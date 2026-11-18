<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Docente;
use App\Models\Facultad;

final class DocenteController extends Controller
{
    public function index(): void
    {
        $this->view('docentes.index', ['registros' => Docente::where('estado', 1)]);
    }

    public function create(): void
    {
        $this->formulario(null);
    }

    public function store(): never
    {
        $this->verifyCsrf();
        Docente::create($this->validar('/docentes/crear'));
        $this->redirect('/docentes');
    }

    public function edit(int $id): void
    {
        $this->formulario($this->buscar($id));
    }

    public function update(int $id): never
    {
        $this->verifyCsrf();
        $registro = $this->buscar($id);
        $registro->update($this->validar("/docentes/{$id}/editar"));
        $this->redirect('/docentes');
    }

    public function deactivate(int $id): never
    {
        $this->verifyCsrf();
        $this->buscar($id)->update(['estado' => 0]);
        $this->redirect('/docentes');
    }

    private function buscar(int $id): Docente
    {
        $registro = Docente::find($id);
        if ($registro === null || (int) $registro->estado !== 1) {
            $this->abort(404, 'Registro activo no encontrado.');
        }

        return $registro;
    }

    private function formulario(?Docente $registro): void
    {
        $this->view('docentes.formulario', [
            'registro' => $registro,
            'errores' => $this->pullFlash('errors', []),
            'old' => $this->pullFlash('old', []),
            'facultades' => Facultad::all(),
        ]);
    }

    private function validar(string $ruta): array
    {
        $datos = [];
        $errores = [];

        foreach (['dni' => 8, 'apellidos' => 100, 'nombres' => 100, 'correo' => 150] as $campo => $maximo) {
            $valor = $_POST[$campo] ?? '';
            $datos[$campo] = is_string($valor) ? trim($valor) : '';

            if ($datos[$campo] === '' || mb_strlen($datos[$campo]) > $maximo) {
                $errores[$campo] = "Revisa el campo {$campo} (máximo {$maximo} caracteres).";
            }
        }

        if (!preg_match('/^[0-9]{8}$/D', $datos['dni'])) {
            $errores['dni'] = 'El DNI debe tener ocho dígitos.';
        }

        if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores['correo'] = 'Ingresa un correo válido.';
        }

        $idFacultad = filter_var($_POST['id_facultad'] ?? null, FILTER_VALIDATE_INT);
        if ($idFacultad === false || $idFacultad < 1 || Facultad::find($idFacultad) === null) {
            $errores['id_facultad'] = 'Selecciona una facultad existente.';
        }
        $datos['id_facultad'] = $idFacultad;

        foreach (['categoria' => Docente::CATEGORIAS, 'dedicacion' => Docente::DEDICACIONES] as $campo => $opciones) {
            $valor = $_POST[$campo] ?? '';
            if (!in_array($valor, $opciones, true)) {
                $errores[$campo] = "Selecciona una opción válida para {$campo}.";
            }
            $datos[$campo] = $valor;
        }

        if ($errores !== []) {
            $this->redirectWithErrors($errores, $ruta);
        }

        return $datos;
    }
}
