<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Facultad;

final class FacultadController extends Controller
{
    public function index(): void
    {
        $this->view('facultades.index', ['registros' => Facultad::where('estado', 1)]);
    }

    public function create(): void
    {
        $this->formulario(null);
    }

    public function store(): never
    {
        $this->verifyCsrf();
        Facultad::create($this->validar('/facultades/crear'));
        $this->redirect('/facultades');
    }

    public function edit(int $id): void
    {
        $this->formulario($this->buscar($id));
    }

    public function update(int $id): never
    {
        $this->verifyCsrf();
        $registro = $this->buscar($id);
        $registro->update($this->validar("/facultades/{$id}/editar"));
        $this->redirect('/facultades');
    }

    public function deactivate(int $id): never
    {
        $this->verifyCsrf();
        $this->buscar($id)->update(['estado' => 0]);
        $this->redirect('/facultades');
    }

    private function buscar(int $id): Facultad
    {
        $registro = Facultad::find($id);
        if ($registro === null || (int) $registro->estado !== 1) {
            $this->abort(404, 'Registro activo no encontrado.');
        }

        return $registro;
    }

    private function formulario(?Facultad $registro): void
    {
        $this->view('facultades.formulario', [
            'registro' => $registro,
            'errores' => $this->pullFlash('errors', []),
            'old' => $this->pullFlash('old', []),
        ]);
    }

    private function validar(string $ruta): array
    {
        $datos = [];
        $errores = [];

        foreach (['nombre' => 120, 'abreviatura' => 20] as $campo => $maximo) {
            $valor = $_POST[$campo] ?? '';
            $datos[$campo] = is_string($valor) ? trim($valor) : '';

            if ($datos[$campo] === '' || mb_strlen($datos[$campo]) > $maximo) {
                $errores[$campo] = "Revisa el campo {$campo} (máximo {$maximo} caracteres).";
            }
        }

        if ($errores !== []) {
            $this->redirectWithErrors($errores, $ruta);
        }

        return $datos;
    }
}
