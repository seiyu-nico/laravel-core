<?php

namespace SeiyuNico\LaravelCore\Services;

use SeiyuNico\LaravelCore\Exceptions\InvalidParameterException;
use SeiyuNico\LaravelCore\Exceptions\NotFoundException;
use SeiyuNico\LaravelCore\Repositories\InterfaceRepository;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @template TModel of Model
 * @template TRepository of InterfaceRepository<TModel>
 * @implements InterfaceService<TModel>
 */
abstract class AbstractService implements InterfaceService
{
    /**
     * @param InterfaceRepository<TModel> $repository
     * @phpstan-param TRepository $repository
     */
    public function __construct(protected readonly InterfaceRepository $repository)
    {
    }

    /**
     * @param non-empty-array<int, string> $columns
     * @param array<int|string, string|Closure> $relations
     * @return Collection<int, TModel>
     */
    public function all(
        array $columns = ['*'],
        array $relations = []
    ): Collection {
        return $this->repository->all($columns, $relations);
    }

    /**
     * @param int $id
     * @return TModel
     * @throws NotFoundException
     */
    public function findById(int $id): Model
    {
        try {
            return $this->repository->findById($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException("'id:{$id}'は見つかりませんでした。", previous: $e);
        }
    }

    /**
     * @param array<string, mixed> $attributes
     * @return TModel
     * @throws InvalidParameterException
     */
    public function create(array $attributes): Model
    {
        return $this->repository->create($attributes);
    }

    /**
     * @param int $id
     * @param array<string, mixed> $attributes
     * @return TModel
     * @throws InvalidParameterException
     */
    public function update(
        int $id,
        array $attributes
    ): Model {
        return $this->repository->update($id, $attributes);
    }

    /**
     * @param int $id
     * @return bool
     * @throws InvalidParameterException
     */
    public function delete(int $id): bool
    {
        return $this->repository->deleteById($id);
    }

    /**
     * @param BelongsToMany<TModel> $belongsToMany
     * @param array<string|mixed> $attributes
     * @param string $key
     * @return void
     */
    public static function attachIfKeyExist(
        BelongsToMany $belongsToMany,
        array $attributes,
        string $key
    ): void {
        if (array_key_exists($key, $attributes)) {
            $belongsToMany->attach($attributes[$key]);
        }
    }

    /**
     * @param BelongsToMany<TModel> $belongsToMany
     * @param array<string|mixed> $attributes
     * @param string $key
     * @return void
     */
    public static function syncIfKeyExist(
        BelongsToMany $belongsToMany,
        array $attributes,
        string $key
    ): void {
        if (array_key_exists($key, $attributes)) {
            $belongsToMany->sync($attributes[$key]);
        }
    }
}
