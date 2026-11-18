<?php

declare(strict_types=1);

use App\Core\Router;

/*
 * Inicia la sesión para mensajes temporales y CSRF.
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

/*
 * Carga automáticamente las clases.
 */
require dirname(__DIR__).'/autoload.php';

/*
 * Crea un token CSRF para los formularios.
 */
if (
    ! isset($_SESSION['_token'])
    || ! is_string($_SESSION['_token'])
) {
    $_SESSION['_token'] = bin2hex(
        random_bytes(32)
    );
}

/*
 * Registra las rutas.
 */
$router = new Router();

require dirname(__DIR__).'/routes/web.php';

/*
 * Procesa la solicitud actual.
 */
$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/',
);