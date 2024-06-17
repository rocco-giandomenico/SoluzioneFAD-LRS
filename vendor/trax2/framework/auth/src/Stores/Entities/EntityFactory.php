<?php

namespace Trax\Auth\Stores\Entities;

use Trax\Repo\Contracts\ModelFactoryContract;

class EntityFactory implements ModelFactoryContract
{
    /**
     * Return the model class.
     *
     * @return string
     */
    public static function modelClass(): string
    {
        return Entity::class;
    }

    /**
     * Create a new model instance given some data.
     *
     * @param array  $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function make(array $data)
    {
        $modelClass = self::modelClass();
        $entity = new $modelClass;

        // Generated UUID.
        $entity->uuid = (string) \Str::uuid();

        // Required name.
        $entity->name = $data['name'];

        // Set default meta because there is no default value in the model.
        $entity->meta = empty($data['meta']) ? [] : $data['meta'];

        // Nullable owner_id.
        if (isset($data['owner_id'])) {
            $entity->owner_id = $data['owner_id'];
        }

        // Nullable type.
        if (isset($data['type'])) {
            $entity->type = $data['type'];
        }

        // Nullable parent ID.
        if (isset($data['parent_id'])) {
            $entity->parent_id = $data['parent_id'];
        }

        return $entity;
    }

    /**
     * Prepare data before recording (used for bulk insert).
     *
     * @param array  $data
     * @return array
     */
    public static function prepare(array $data)
    {
        return $data;
    }

    /**
     * Update an existing model instance, given some data.
     *
     * @param \Illuminate\Database\Eloquent\Model  $model
     * @param array  $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function update($model, array $data)
    {
        $model->update($data);
        return $model;
    }

    /**
     * Duplicate an existing model in the database, given some data.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function duplicate($model, array $data = [])
    {
        $copy = $model->replicate()->fill($data);
        $copy->save();
        return $copy;
    }
}
