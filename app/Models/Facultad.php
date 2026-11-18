<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Facultad extends Model
{
    protected static string $table = 'facultad';
    protected array $fillable = ['nombre', 'abreviatura', 'estado'];

    public function docentes(): array
    {
        return $this->hasMany(Docente::class, 'id_facultad');
    }
}
