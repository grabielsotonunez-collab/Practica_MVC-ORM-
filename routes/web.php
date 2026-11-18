<?php

declare(strict_types=1);

use App\Controllers\DocenteController;
use App\Controllers\FacultadController;

$router->get('/', [FacultadController::class, 'index']);

$router->get('/facultades', [FacultadController::class, 'index']);
$router->get('/facultades/crear', [FacultadController::class, 'create']);
$router->post('/facultades', [FacultadController::class, 'store']);
$router->get('/facultades/{id}/editar', [FacultadController::class, 'edit']);
$router->put('/facultades/{id}', [FacultadController::class, 'update']);
$router->delete('/facultades/{id}', [FacultadController::class, 'deactivate']);

$router->get('/docentes', [DocenteController::class, 'index']);
$router->get('/docentes/crear', [DocenteController::class, 'create']);
$router->post('/docentes', [DocenteController::class, 'store']);
$router->get('/docentes/{id}/editar', [DocenteController::class, 'edit']);
$router->put('/docentes/{id}', [DocenteController::class, 'update']);
$router->delete('/docentes/{id}', [DocenteController::class, 'deactivate']);
