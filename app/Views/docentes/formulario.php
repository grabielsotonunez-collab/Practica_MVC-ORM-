<?php
use App\Models\Docente;

$e = static fn (mixed $valor): string => htmlspecialchars(
    is_scalar($valor) ? (string) $valor : '', ENT_QUOTES, 'UTF-8'
);
$valor = static fn (string $campo): mixed => $old[$campo] ?? $registro?->$campo ?? '';
$accion = $registro === null ? '/docentes' : '/docentes/'.$registro->id;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Docentes</title>
</head>
<body>
<h1><?= $registro === null ? 'Registrar' : 'Editar' ?> docente</h1>
<p><a href="<?= $e($url('/docentes')) ?>">Volver</a></p>
<?php if ($errores !== []): ?>
<ul>
    <?php foreach ($errores as $error): ?>
    <li><?= $e($error) ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
<form method="post" action="<?= $e($url($accion)) ?>">
    <input type="hidden" name="_token" value="<?= $e($_SESSION['_token']) ?>">
    <?php if ($registro !== null): ?>
    <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>
    <p><label>Facultad
        <select name="id_facultad" required>
            <option value="">Selecciona una facultad</option>
            <?php foreach ($facultades as $facultad): ?>
            <option value="<?= $e($facultad->id) ?>" <?= $e($valor('id_facultad')) === $e($facultad->id) ? 'selected' : '' ?>>
                <?= $e($facultad->nombre) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label></p>
    <p><label>DNI <input type="text" name="dni" maxlength="8" inputmode="numeric" pattern="[0-9]{8}" value="<?= $e($valor('dni')) ?>" required></label></p>
    <p><label>Apellidos <input type="text" name="apellidos" maxlength="100" value="<?= $e($valor('apellidos')) ?>" required></label></p>
    <p><label>Nombres <input type="text" name="nombres" maxlength="100" value="<?= $e($valor('nombres')) ?>" required></label></p>
    <p><label>Correo <input type="email" name="correo" maxlength="150" value="<?= $e($valor('correo')) ?>" required></label></p>
    <p><label>Categoría
        <select name="categoria" required>
            <option value="">Selecciona una opción</option>
            <?php foreach (Docente::CATEGORIAS as $opcion): ?>
            <option value="<?= $e($opcion) ?>" <?= $valor('categoria') === $opcion ? 'selected' : '' ?>><?= $e($opcion) ?></option>
            <?php endforeach; ?>
        </select>
    </label></p>
    <p><label>Dedicación
        <select name="dedicacion" required>
            <option value="">Selecciona una opción</option>
            <?php foreach (Docente::DEDICACIONES as $opcion): ?>
            <option value="<?= $e($opcion) ?>" <?= $valor('dedicacion') === $opcion ? 'selected' : '' ?>><?= $e($opcion) ?></option>
            <?php endforeach; ?>
        </select>
    </label></p>
    <button type="submit">Guardar</button>
</form>
</body>
</html>
