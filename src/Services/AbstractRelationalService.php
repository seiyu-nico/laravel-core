<?php

namespace SeiyuNico\LaravelCore\Services;

use Illuminate\Database\Eloquent\Model;
use SeiyuNico\LaravelCore\Exceptions\InvalidParameterException;
use SeiyuNico\LaravelCore\Repositories\AbstractRelationalRepository;

/**
 * @property AbstractRelationalRepository $repository
 */
abstract class AbstractRelationalService extends AbstractService
{
    public function __construct(AbstractRelationalRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * @param  array  $attributes
     * @return Model
     *
     * @throws InvalidParameterException
     */
    public function create(array $attributes): Model
    {
        try {
            $model = $this->repository->createWith($attributes);
        } catch (\Throwable $e) {
            $this->throwException($e);
        }

        return $model;
    }

    /**
     * @param  int  $id
     * @param  array  $attributes
     * @return Model
     *
     * @throws InvalidParameterException
     */
    public function update(int $id, array $attributes): Model
    {
        try {
            $model = $this->repository->updateWith($id, $attributes);
        } catch (\Throwable $e) {
            $this->throwException($e);
        }

        return $model;
    }

    /**
     * @param  \Throwable  $e
     * @return void
     *
     * @throws InvalidParameterException
     */
    protected function throwException(\Throwable $e): void
    {
        throw new InvalidParameterException('不正なパラメータ', previous: $e);
    }
}
