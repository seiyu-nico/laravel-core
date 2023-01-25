<?php

namespace SeiyuNico\LaravelCore\Services;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use SeiyuNico\LaravelCore\Exceptions\InvalidParameterException;
use SeiyuNico\LaravelCore\Exceptions\NotFoundException;

/**
 * @template TModel as Model
 */
interface InterfaceService
{
    /**
     * @param  non-empty-array<int, string>  $columns
     * @param  array<int|string, string|Closure>  $relations
     * @return Collection<int, TModel>
     */
    public function all(
        array $columns = ['*'],
        array $relations = []
    ): Collection;

    /**
     * @param  int  $id
     * @return TModel
     *
     * @throws NotFoundException
     */
    public function findById(int $id): Model;

    /**
     * @param  array<string, mixed>  $attributes
     * @return TModel
     *
     * @throws InvalidParameterException
     */
    public function create(array $attributes): Model;

    /**
     * @param  int  $id
     * @param  array<string, mixed>  $attributes
     * @return TModel
     *
     * @throws InvalidParameterException
     */
    public function update(
        int $id,
        array $attributes
    ): Model;

    /**
     * @param  int  $id
     * @return bool
     *
     * @throws InvalidParameterException
     */
    public function delete(int $id): bool;

    /**
     * @param  BelongsToMany<TModel>  $belongsToMany
     * @param  array<string|mixed>  $attributes
     * @param  string  $key
     * @return void
     */
    public static function attachIfKeyExist(
        BelongsToMany $belongsToMany,
        array $attributes,
        string $key
    ): void;

    /**
     * @param  BelongsToMany<TModel>  $belongsToMany
     * @param  array<string|mixed>  $attributes
     * @param  string  $key
     * @return void
     */
    public static function syncIfKeyExist(
        BelongsToMany $belongsToMany,
        array $attributes,
        string $key
    ): void;
}
