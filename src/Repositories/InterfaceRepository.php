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
 */
interface InterfaceRepository
{
    /**
     * 全部のデータを取得する.
     *
     * @param  non-empty-array<int, string>  $columns
     * @param  array<int|string, string|Closure>  $relations
     * @return Collection<int, TModel>
     */
    public function all(array $columns = ['*'], array $relations = []): Collection;

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
    ): Collection;

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
    ): LengthAwarePaginator;

    /**
     * 指定した条件に合ってるデータを取得する
     *
     * @param  non-empty-array<int, string>  $columns
     * @param  array<int|string, mixed>  $conditions
     * @param  array<int|string, string|Closure>  $relations
     * @param  ?Closure  $callback
     * @return TModel|null
     */
    public function findOneWithConditions(array $columns = ['*'], array $conditions = [], array $relations = [], ?Closure $callback = null): ?Model;

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
    ): Model;

    /**
     * モデルを作成する
     *
     * @param  array<string, mixed>  $payload
     * @return TModel
     *
     * @throws InvalidParameterException
     */
    public function create(array $payload): Model;

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
    ): Model;

    /**
     * データを挿入または更新する
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $values
     * @return TModel
     */
    public function updateOrCreate(array $attributes, array $values): Model;

    /**
     * IDを使用して、データを削除する
     *
     * @param  int|string  $id
     * @return bool
     *
     * @throws InvalidParameterException
     */
    public function deleteById(
        int|string $id
    ): bool;
}
