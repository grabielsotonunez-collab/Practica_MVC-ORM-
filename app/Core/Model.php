<?php

declare(strict_types=1);

namespace App\Core;

use LogicException;
use PDO;
use RuntimeException;

abstract class Model
{
    protected static string $table;

    protected static string $primaryKey = 'id';

    /**
     * Campos que pueden registrarse o actualizarse.
     *
     * @var array<int, string>
     */
    protected array $fillable = [];

    /**
     * Valores del registro.
     *
     * @var array<string, mixed>
     */
    protected array $attributes = [];

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Recupera todos los registros.
     *
     * @return array<int, static>
     */
    public static function all(): array
    {
        $tabla = static::identifier(static::$table);

        $consulta = Database::connection()->query(
            "SELECT * FROM {$tabla}"
        );

        $filas = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $modelos = [];

        foreach ($filas as $fila) {
            $modelos[] = new static($fila);
        }

        return $modelos;
    }

    /**
     * Busca por clave primaria.
     */
    public static function find(int $id): ?static
    {
        $tabla = static::identifier(static::$table);
        $clavePrimaria = static::identifier(static::$primaryKey);

        $consulta = Database::connection()->prepare(
            "SELECT * FROM {$tabla}
             WHERE {$clavePrimaria} = :id
             LIMIT 1"
        );

        $consulta->execute([
            'id' => $id,
        ]);

        $fila = $consulta->fetch(PDO::FETCH_ASSOC);

        return $fila === false
            ? null
            : new static($fila);
    }

    /**
     * Busca registros por una columna.
     *
     * @return array<int, static>
     */
    public static function where(
        string $columna,
        mixed $valor
    ): array {
        $tabla = static::identifier(static::$table);
        $columnaSegura = static::identifier($columna);

        $consulta = Database::connection()->prepare(
            "SELECT * FROM {$tabla}
             WHERE {$columnaSegura} = :valor"
        );

        $consulta->execute([
            'valor' => $valor,
        ]);

        $filas = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $modelos = [];

        foreach ($filas as $fila) {
            $modelos[] = new static($fila);
        }

        return $modelos;
    }

    public static function countWhere(
        string $columna,
        mixed $valor
    ): int {
        $tabla = static::identifier(static::$table);
        $columnaSegura = static::identifier($columna);

        $consulta = Database::connection()->prepare(
            "SELECT COUNT(*) FROM {$tabla}
             WHERE {$columnaSegura} = :valor"
        );

        $consulta->execute([
            'valor' => $valor,
        ]);

        return (int) $consulta->fetchColumn();
    }

    /**
     * Registra un modelo.
     *
     * @param array<string, mixed> $datos
     */
    public static function create(array $datos): static
    {
        $modelo = new static();

        $datosPermitidos = $modelo->onlyFillable($datos);

        if ($datosPermitidos === []) {
            throw new LogicException(
                'No se recibieron campos permitidos.'
            );
        }

        $tabla = static::identifier(static::$table);
        $columnas = array_keys($datosPermitidos);

        $columnasSql = array_map(
            static fn (string $columna): string =>
                static::identifier($columna),
            $columnas,
        );

        $parametrosSql = array_map(
            static fn (string $columna): string =>
                ':'.$columna,
            $columnas,
        );

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $tabla,
            implode(', ', $columnasSql),
            implode(', ', $parametrosSql),
        );

        $conexion = Database::connection();
        $consulta = $conexion->prepare($sql);
        $consulta->execute($datosPermitidos);

        $id = (int) $conexion->lastInsertId();
        $registro = static::find($id);

        if ($registro === null) {
            throw new RuntimeException(
                'El registro fue creado, pero no pudo recuperarse.'
            );
        }

        return $registro;
    }

    /**
     * Actualiza el modelo actual.
     *
     * @param array<string, mixed> $datos
     */
    public function update(array $datos): bool
    {
        $datosPermitidos = $this->onlyFillable($datos);

        if ($datosPermitidos === []) {
            return false;
        }

        $id = $this->getKey();

        if ($id === null) {
            throw new LogicException(
                'No se puede actualizar un modelo sin identificador.'
            );
        }

        $asignaciones = [];

        foreach (array_keys($datosPermitidos) as $columna) {
            $columnaSegura = static::identifier($columna);
            $asignaciones[] = "{$columnaSegura} = :{$columna}";
        }

        $tabla = static::identifier(static::$table);
        $clavePrimaria = static::identifier(static::$primaryKey);

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = :__id',
            $tabla,
            implode(', ', $asignaciones),
            $clavePrimaria,
        );

        $parametros = $datosPermitidos;
        $parametros['__id'] = $id;

        $consulta = Database::connection()->prepare($sql);
        $resultado = $consulta->execute($parametros);

        if ($resultado) {
            $this->attributes = array_replace(
                $this->attributes,
                $datosPermitidos,
            );
        }

        return $resultado;
    }

    /**
     * Elimina físicamente el modelo.
     */
    public function delete(): bool
    {
        $id = $this->getKey();

        if ($id === null) {
            throw new LogicException(
                'No se puede eliminar un modelo sin identificador.'
            );
        }

        $tabla = static::identifier(static::$table);
        $clavePrimaria = static::identifier(static::$primaryKey);

        $consulta = Database::connection()->prepare(
            "DELETE FROM {$tabla}
             WHERE {$clavePrimaria} = :id"
        );

        return $consulta->execute([
            'id' => $id,
        ]);
    }

    /**
     * Relación uno a muchos.
     *
     * @param class-string<Model> $modeloRelacionado
     * @return array<int, Model>
     */
    protected function hasMany(
        string $modeloRelacionado,
        string $claveForanea
    ): array {
        return $modeloRelacionado::where(
            $claveForanea,
            $this->getKey(),
        );
    }

    /**
     * Relación pertenece a.
     *
     * @param class-string<Model> $modeloRelacionado
     */
    protected function belongsTo(
        string $modeloRelacionado,
        string $claveForanea
    ): ?Model {
        $idRelacionado = $this->attributes[$claveForanea] ?? null;

        if ($idRelacionado === null) {
            return null;
        }

        return $modeloRelacionado::find((int) $idRelacionado);
    }

    public function getKey(): int|string|null
    {
        return $this->attributes[static::$primaryKey] ?? null;
    }

    public function __get(string $atributo): mixed
    {
        return $this->attributes[$atributo] ?? null;
    }

    public function __isset(string $atributo): bool
    {
        return array_key_exists($atributo, $this->attributes);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * @param array<string, mixed> $datos
     * @return array<string, mixed>
     */
    private function onlyFillable(array $datos): array
    {
        return array_intersect_key(
            $datos,
            array_flip($this->fillable),
        );
    }

    private static function identifier(string $nombre): string
    {
        if (! preg_match(
            '/^[a-zA-Z_][a-zA-Z0-9_]*$/',
            $nombre
        )) {
            throw new LogicException(
                'Identificador SQL no permitido.'
            );
        }

        return '`'.$nombre.'`';
    }
}