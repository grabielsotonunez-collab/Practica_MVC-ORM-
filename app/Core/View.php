<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    /**
     * @param array<string, mixed> $datos
     */
    public static function render(
        string $vista,
        array $datos = []
    ): void {
        $archivo = dirname(__DIR__)
            .'/Views/'
            .str_replace('.', DIRECTORY_SEPARATOR, $vista)
            .'.php';

        if (! is_file($archivo)) {
            throw new RuntimeException(
                "No existe la vista: {$vista}"
            );
        }

        $datos['url'] = static fn (string $ruta = ''): string =>
            self::url($ruta);

        extract($datos, EXTR_SKIP);

        require $archivo;
    }

    public static function url(string $ruta = ''): string
    {
        $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';

        $base = str_replace(
            '\\',
            '/',
            dirname($script),
        );

        if ($base === '/' || $base === '.') {
            $base = '';
        }

        return $base.'/'.ltrim($ruta, '/');
    }
}