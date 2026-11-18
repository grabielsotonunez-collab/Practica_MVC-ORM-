<?php
$e = static fn (mixed $valor): string => htmlspecialchars(
    is_scalar($valor) ? (string) $valor : '', ENT_QUOTES, 'UTF-8'
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Docentes</title>
</head>
<body>
<h1>Docentes</h1>
<p><a href="<?= $e($url('/facultades')) ?>">Facultades</a>
    | <a href="<?= $e($url('/docentes/crear')) ?>">Registrar docente</a></p>
<?php if ($registros === []): ?>
    <p>No hay registros activos.</p>
<?php else: ?>
<table border="1">
    <thead>
        <tr><th>Facultad</th><th>DNI</th><th>Apellidos</th><th>Nombres</th><th>Correo</th><th>Categoría</th><th>Dedicación</th><th>Acciones</th></tr>
    </thead>
    <tbody>
        <?php foreach ($registros as $registro): ?>
        <tr>
            <td><?= $e($registro->facultad()?->nombre ?? '') ?></td>
            <td><?= $e($registro->dni) ?></td>
            <td><?= $e($registro->apellidos) ?></td>
            <td><?= $e($registro->nombres) ?></td>
            <td><?= $e($registro->correo) ?></td>
            <td><?= $e($registro->categoria) ?></td>
            <td><?= $e($registro->dedicacion) ?></td>
            <td>
                <a href="<?= $e($url('/docentes/'.$registro->id.'/editar')) ?>">Editar</a>
                <form method="post" action="<?= $e($url('/docentes/'.$registro->id)) ?>">
                    <input type="hidden" name="_token" value="<?= $e($_SESSION['_token']) ?>">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit">Eliminar</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
</body>
</html>
