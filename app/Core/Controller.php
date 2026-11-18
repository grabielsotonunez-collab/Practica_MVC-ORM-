<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    /**
     * @param array<string, mixed> $datos
     */
    protected function view(
        string $vista,
        array $datos = []
    ): void {
        View::render($vista, $datos);
    }

    protected function redirect(string $ruta): never
    {
        header('Location: '.View::url($ruta));

        exit;
    }

    protected function verifyCsrf(): void
    {
        $tokenSesion = $_SESSION['_token'] ?? '';
        $tokenFormulario = $_POST['_token'] ?? '';

        $tokenValido = is_string($tokenSesion)
            && is_string($tokenFormulario)
            && $tokenSesion !== ''
            && hash_equals($tokenSesion, $tokenFormulario);

        if (! $tokenValido) {
            http_response_code(419);

            exit('Token CSRF inválido o vencido.');
        }
    }

    protected function abort(
        int $estado,
        string $mensaje
    ): never {
        http_response_code($estado);

        exit($mensaje);
    }

    protected function flash(
        string $clave,
        mixed $valor
    ): void {
        $_SESSION['_flash'][$clave] = $valor;
    }

    protected function pullFlash(
        string $clave,
        mixed $predeterminado = null
    ): mixed {
        $valor = $_SESSION['_flash'][$clave]
            ?? $predeterminado;

        unset($_SESSION['_flash'][$clave]);

        return $valor;
    }

    /**
     * @param array<string, string> $errores
     */
    protected function redirectWithErrors(
        array $errores,
        string $ruta
    ): never {
        $this->flash('errors', $errores);
        $this->flash('old', $_POST);

        $this->redirect($ruta);
    }
}