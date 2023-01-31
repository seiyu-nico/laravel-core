<?php

namespace SeiyuNico\LaravelCore\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use SeiyuNico\LaravelCore\Exceptions\InvalidParameterException;
use SeiyuNico\LaravelCore\Traits\Models\HasImagesObservable;

abstract class AbstractRelationalRepository extends AbstractEloquentRepository
{
    /**
     * @param  array  $payload
     * @return Model
     */
    public function createWith(array &$payload): Model
    {
        $model = $this->model;
        $model->fill($payload)->save();

        return $this->createChild($this->model, $payload);
    }

    /**
     * @param  Model  $model
     * @param  array  $payload
     * @return Model
     */
    protected function createChild(Model $model, array &$payload): Model
    {
        $relations = get_self_class_methods($model::class);

        foreach ($relations as $relation) {
            if (! isset($payload[$relation])) {
                continue;
            }
            $relationClass = $model->{$relation}();

            if ($relationClass instanceof BelongsToMany) {
                $relationClass->sync($payload[$relation]);

                continue;
            }

            if ($relationClass instanceof HasOne) {
                /** @var Model $child */
                $child = $model->{$relation};
                $child->fill($payload[$relation])->save();
                $primary_key = $child->getKeyName();
                $payload[$relation][$primary_key] = $child->{$primary_key};
                $this->createChild($child, $payload[$relation]);

                continue;
            }

            if ($relationClass instanceof HasMany && ! empty($payload[$relation])) {
                $primary_key = $relationClass->getModel()->getKeyName();
                foreach ($payload[$relation] as &$child_data) {
                    /** @var Model $child_model */
                    $child_model = $model->{$relation}()->create([$primary_key => $child_data[$primary_key] ?? null, ...$child_data]);
                    $child_data[$primary_key] = $child_model->{$primary_key};
                    $this->createChild($child_model, $child_data);
                }
            }
        }

        return $model;
    }

    /**
     * @param  int|string  $id
     * @param  array  $payload
     * @return Model
     *
     * @throws InvalidParameterException
     */
    public function updateWith(int|string $id, array &$payload): Model
    {
        /** @var Model|HasImagesObservable $model */
        $model = $this->update($id, $payload);

        $relations = get_self_class_methods($this->model::class);

        foreach ($relations as $relation) {
            if (! isset($payload[$relation])) {
                continue;
            }
            $data = collect($payload[$relation]);
            $relationClass = $model->{$relation}();

            if ($relationClass instanceof BelongsToMany) {
                $relationClass->sync($payload[$relation]);

                continue;
            }

            /** @var Model|\Illuminate\Database\Eloquent\Collection|Model[] $child */
            $child = $model->{$relation};

            if ($relationClass instanceof HasOne) {
                $child->fill($payload[$relation])->save();
                $primary_key = $child->getKeyName();
                if (! isset($payload[$relation][$primary_key])) {
                    $payload[$relation][$primary_key] = $child->{$primary_key};
                }

                continue;
            }

            // 1:x
            $data_ids = $data->pluck('id');
            $child->whereIn('id', $child->pluck('id')->diff($data_ids))
                ->each(fn (Model $model) => $model->delete());

            if (! empty($payload[$relation])) {
                foreach ($payload[$relation] as &$child_data) {
                    $child_model = $model->{$relation}()->updateOrCreate(['id' => $child_data['id'] ?? null], $child_data);
                    if (! isset($child_data['id'])) {
                        $child_data['id'] = $child_model->id;
                    }
                }
            }
        }

        return $model;
    }
}
