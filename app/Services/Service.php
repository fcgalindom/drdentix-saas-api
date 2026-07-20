<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

abstract class Service
{
    /**
     * @param  Model  $model  Instancia del modelo inyectada por el constructor de la clase hija.
     */
    public function __construct(protected Model $model) {}

    /**
     * Recupera todos los registros de la entidad.
     *
     * @return Collection
     */
    /**
     * Recupera todos los registros de la entidad.
     *
     * @return Collection
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Busca un registro por su clave primaria o lanza una excepción si no existe.
     *
     * @return Model|ModelNotFoundException
     */
    public function find(int $id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Crea un nuevo registro mediante asignación masiva.
     *
     * @param  array  $data  Datos a persistir.
     * @return Model
     */
    public function create(array $data)
    {
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

    /**
     * Cambia el estado de activación lógica de un registro.
     *
     * @param  mixed  $newActive  Valor que representa el nuevo estado (true/false, 1/0, 'on').
     * @return void
     */
    public function changeActive($newActive, Model $model)
    {
        DB::transaction(function () use ($newActive, $model) {
            $active = filter_var($newActive, FILTER_VALIDATE_BOOLEAN);
            $model->is_active = $active;
            $model->save();

            return $model;
        });
    }

    /**
     * Recupera todos los registros que se encuentran en estado activo.
     *
     * @return Collection
     */
    public function getAllActive()
    {
        $model = $this->model->where('is_active', 1)->get();

        return $model;
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
