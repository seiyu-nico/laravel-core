<?php

namespace SeiyuNico\LaravelCore\Repositories;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use SeiyuNico\LaravelCore\Exceptions\InvalidParameterException;

/**
 * @template TModel of Model
 *
 * @implements InterfaceRepository<TModel>
 */
abstract class AbstractEloquentRepository implements InterfaceRepository
{
    /**
     * BaseRepository constructor.
     *
     * @phpstan-param TModel $model
     * @param Model $model
     */
    public function __construct(protected readonly Model $model)
    {
    }

    /**
     * 全部のデータを取得する.
     *
     * @param  non-empty-array<int, string>  $columns
     * @param  array<int|string, string|Closure>  $relations
     * @return Collection<int, TModel>
     */
    public function all(
        array $columns = ['*'],
        array $relations = []
    ): Collection {
        return $this->model->with($relations)->get($columns);
    }

    /**
     * 指定した条件に合ってるデータを取得する
     *
     * @param  non-empty-array<int, string>  $columns
     * @param  array<int|string, mixed>  $conditions
     * @param  array<int|string, string|Closure>  $relations
     * @param  array<string, string>  $orders
     * @param  ?int  $limit
     * @param  ?Closure  $callback
     * @return Collection<int, TModel>
     */
    public function findWithConditions(
        array $columns = ['*'],
        array $conditions = [],
        array $relations = [],
        array $orders = [],
        ?int $limit = null,
        ?Closure $callback = null
    ): Collection {
        $model = $this->model->with($relations)->where($conditions);
        foreach ($orders as $key => $direction) {
            $model->orderBy($key, $direction);
        }

        if ($limit) {
            $model->limit($limit);
        }

        return $model->select($columns)->getOr(fn () => is_null($callback) ? [] : call_user_func($callback));
    }

    /**
     * 指定した条件に合ってるデータを取得する(ページネーション)
     *
     * @param  non-empty-array<int, string>  $columns
     * @param  array<int|string, mixed>|Closure  $conditions
     * @param  array<int|string, string|Closure>  $relations
     * @param  array<string, string>  $orders
     * @param  int  $per_page
     * @param  int  $page
     * @return LengthAwarePaginator<TModel>
     */
    public function findWithConditionsAndPagination(
        array $columns = ['*'],
        array|Closure $conditions = [],
        array $relations = [],
        array $orders = [],
        int $per_page = 15,
        int $page = 1
    ): LengthAwarePaginator {
        $query = $this->model->with($relations);
        if (is_array($conditions)) {
            $query->where($conditions);
        } else {
            call_user_func($conditions, $query);
        }
        $query->select($columns);
        foreach ($orders as $key => $direction) {
            $query->orderBy($key, $direction);
        }

        return $query->paginate(perPage: $per_page, page: $page);
    }

    /**
     * 指定した条件に合ってるデータを取得する
     *
     * @param  non-empty-array<int, string>  $columns
     * @param  array<int|string, mixed>  $conditions
     * @param  array<int|string, string|Closure>  $relations
     * @param  ?Closure  $callback
     * @return TModel|null
     */
    public function findOneWithConditions(
        array $columns = ['*'],
        array $conditions = [],
        array $relations = [],
        ?Closure $callback = null
    ): ?Model {
        return $this->model
            ->with($relations)
            ->where($conditions)
            ->select($columns)
            ->firstOr(fn () => is_null($callback) ? null : call_user_func($callback));
    }

    /**
     * モデルを作成する
     *
     * @param  array<string, mixed>  $payload
     * @return TModel
     */
    public function create(array $payload): Model
    {
        return $this->model->create($payload)->fresh();
    }

    /**
     * データをアップデートする
     *
     * @param  int|string  $id
     * @param  array<string, mixed>  $payload
     * @return TModel
     *
     * @throws InvalidParameterException
     */
    public function update(
        int|string $id,
        array $payload
    ): Model {
        $model = $this->model->find($id);

        if (is_null($model)) {
            throw new InvalidParameterException('不正なID');
        }

        $result = $model->update($payload);

        if (! $result) {
            throw new InvalidParameterException('不正なパラメータ');
        }

        return $model;
    }

    /**
     * データを挿入または更新する
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $values
     * @return TModel
     */
    public function updateOrCreate(
        array $attributes,
        array $values
    ): Model {
        return $this->model->updateOrCreate($attributes, $values);
    }

    /**
     * IDを使用して、データを削除する
     *
     * @param  int|string  $id
     * @return true
     *
     * @throws InvalidParameterException
     */
    public function deleteById(
        int|string $id
    ): bool {
        if (! $this->findById($id)->delete()) {
            throw new InvalidParameterException('不正なID');
        }

        return true;
    }

    /**
     * IDを使用して、１行のデータを取得する
     *
     * @param  int|string  $id
     * @param  non-empty-array<int, string>  $columns
     * @param  array<int|string, string|Closure>  $relations
     * @param  array<int, string>  $appends
     * @return TModel
     *
     * @throws ModelNotFoundException
     */
    public function findById(
        int|string $id,
        array $columns = ['*'],
        array $relations = [],
        array $appends = []
    ): Model {
        return $this->model->query()->select($columns)->with($relations)->findOrFail($id)->append($appends);
    }
}
