<?php
$e = static fn (mixed $valor): string => htmlspecialchars(
    is_scalar($valor) ? (string) $valor : '', ENT_QUOTES, 'UTF-8'
);
$valor = static fn (string $campo): mixed => $old[$campo] ?? $registro?->$campo ?? '';
$accion = $registro === null ? '/facultades' : '/facultades/'.$registro->id;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Facultades</title>
</head>
<body>
<h1><?= $registro === null ? 'Registrar' : 'Editar' ?> facultad</h1>
<p><a href="<?= $e($url('/facultades')) ?>">Volver</a></p>
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
    <p><label>Nombre <input type="text" name="nombre" maxlength="120" value="<?= $e($valor('nombre')) ?>" required></label></p>
    <p><label>Abreviatura <input type="text" name="abreviatura" maxlength="20" value="<?= $e($valor('abreviatura')) ?>" required></label></p>
    <button type="submit">Guardar</button>
</form>
</body>
</html>
