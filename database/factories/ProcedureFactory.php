<?php

namespace Database\Factories;

use App\Models\Procedure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Procedure>
 */
class ProcedureFactory extends Factory
{
    private static array $procedureNames = [
        'Limpieza dental',
        'Extracción simple',
        'Extracción quirúrgica',
        'Endodoncia unirradicular',
        'Endodoncia multirradicular',
        'Ortodoncia - control mensual',
        'Implante dental unitario',
        'Corona de porcelana',
        'Puente fijo (por unidad)',
        'Blanqueamiento dental',
        'Carillas de porcelana',
        'Periodoncia - raspaje y alisado',
        'Radiografía panorámica',
        'Radiografía periapical',
        'Selladores dentales',
        'Prótesis total removible',
        'Prótesis parcial removible',
        'Cirugía de terceros molares',
        'Tratamiento de conducto',
        'Empaste con resina',
        'Empaste con amalgama',
        'Revisión general',
        'Brackets metálicos - colocación',
        'Brackets estéticos - colocación',
        'Férula de descarga',
        'Gingivectomía',
        'Apicectomía',
        'Injerto óseo',
        'Elevación de seno maxilar',
        'Fluorización',
        'Calza dental',
        'Exodoncia',
        'Cirugía periodontal',
        'Rehabilitación oral',
        'Diseño de sonrisa',
        'Perfilamiento labial',
        'Toxina botulínica',
        'Ácido hialurónico',
        'Guarda oclusal',
        'Placa de relajación',
        'Cementación de brackets',
        'Retiro de brackets',
        'Controles de ortodoncia',
        'Mantenedor de espacio',
        'Pulpotomía',
        'Pulpectomía',
        'Coronas en acero',
        'Resinas preventivas',
        'Detartraje supragingival',
        'Aplicación de flúor',
    ];

    private static int $nameIndex = 0;

    public function definition(): array
    {
        $name = self::$procedureNames[self::$nameIndex % count(self::$procedureNames)];
        self::$nameIndex++;

        return [
            'company_id' => 1,
            'name' => $name,
            'duration' => fake()->numberBetween(10, 120),
            'state' => 'Activo',
        ];
    }
}
