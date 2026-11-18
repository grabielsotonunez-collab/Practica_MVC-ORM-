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
    <title>Facultades</title>
</head>
<body>
<h1>Facultades</h1>
<p><a href="<?= $e($url('/docentes')) ?>">Docentes</a>
    | <a href="<?= $e($url('/facultades/crear')) ?>">Registrar facultad</a></p>
<?php if ($registros === []): ?>
    <p>No hay registros activos.</p>
<?php else: ?>
<table border="1">
    <thead>
        <tr><th>Nombre</th><th>Abreviatura</th><th>Acciones</th></tr>
    </thead>
    <tbody>
        <?php foreach ($registros as $registro): ?>
        <tr>
            <td><?= $e($registro->nombre) ?></td>
            <td><?= $e($registro->abreviatura) ?></td>
            <td>
                <a href="<?= $e($url('/facultades/'.$registro->id.'/editar')) ?>">Editar</a>
                <form method="post" action="<?= $e($url('/facultades/'.$registro->id)) ?>">
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
