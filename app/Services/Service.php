<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

abstract class Service
{
    /**
     * @param  Model  $model  Instancia del modelo inyectada por el constructor de la clase hija.
     */
    public function __construct(protected Model $model) {}

    public function all()
    {
        return $this->model->where('company_id', $this->companyId())->get();
    }

    public function find(int $id)
    {
        return $this->model->where('company_id', $this->companyId())->findOrFail($id);
    }

    protected function companyId(): int
    {
        return auth()->user()->company_id;
    }

    public function create(array $data)
    {
        $data['company_id'] = $data['company_id'] ?? $this->companyId();

        return $this->model->create($data);
    }

    /**
     * Gestiona la persistencia de datos (Creación/Actualización) bajo una transacción atómica.
     *
     * @param  array  $data  Datos recibidos de la petición.
     * @param  Model  $model  Instancia del modelo a procesar.
     * @return void
     */
    protected function saveOrUpdate(array $data, Model $model)
    {
        return DB::transaction(function () use ($data, $model) {
            $data = $this->beforeSave($data, $model);
            $model->fill($data);
            $model->save();

            return $model;
        });
    }

    /**
     * Gancho (Hook) para manipular los datos antes de ser guardados.
     * Debe ser sobrescrito en la clase hija para lógica específica (ej. unset, hash, etc).
     *
     * @return array
     */
    protected function beforeSave(array $data, Model $model)
    {
        return $data;
    }

    public function changeActive($newActive, Model $model)
    {
        return DB::transaction(function () use ($newActive, $model) {
            $active = filter_var($newActive, FILTER_VALIDATE_BOOLEAN);
            $model->is_active = $active;
            $model->save();

            return $model;
        });
    }

    public function getAllActive()
    {
        return $this->model->where('company_id', $this->companyId())->where('is_active', 1)->get();
    }

    /**
     * Encripta una contraseña usando Bcrypt.
     */
    protected function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    /**
     * Verifica si una contraseña coincide con su hash.
     */
    protected function checkPassword(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }
}
