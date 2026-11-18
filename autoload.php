<?php

declare(strict_types=1);

spl_autoload_register(function (string $clase): void {
    $prefijo = 'App\\';
    $directorioBase = __DIR__.'/app/';

    if (! str_starts_with($clase, $prefijo)) {
        return;
    }

    $claseRelativa = substr($clase, strlen($prefijo));

    $archivo = $directorioBase
        .str_replace('\\', DIRECTORY_SEPARATOR, $claseRelativa)
        .'.php';

    if (is_file($archivo)) {
        require_once $archivo;
    }
});