<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Eloquent para la tabla "candidates".
 *
 * Este modelo vive en la capa de Infraestructura.
 * Solo representa datos de la base y no contiene ninguna lógica de dominio.
 */
class CandidateModel extends Model
{
    protected $table = 'candidates';

    /**
     * Permite asignación masiva en el repositorio.
     */
    protected $fillable = [
        'full_name',
        'email',
        'years_experience',
        'cv_text',
        'status',
    ];

    /**
     * Laravel manejará automáticamente created_at y updated_at.
     */
    public $timestamps = true;

    /**
     * Tipos de casting opcionales para asegurar tipos correctos.
     */
    protected $casts = [
        'years_experience' => 'integer',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];
}
