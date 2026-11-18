<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Docente extends Model
{
    protected static string $table = 'docente';
    protected array $fillable = ['id_facultad', 'dni', 'apellidos', 'nombres', 'correo', 'categoria', 'dedicacion', 'estado'];

    public const CATEGORIAS = ['AUXILIAR', 'ASOCIADO', 'PRINCIPAL'];
    public const DEDICACIONES = [
        'DEDICACION EXCLUSIVA - DE',
        'TIEMPO COMPLETO - TC',
        'TIEMPO PARCIAL - TP',
    ];

    public function facultad(): ?Facultad
    {
        return $this->belongsTo(Facultad::class, 'id_facultad');
    }
}
