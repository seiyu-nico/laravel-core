<?php

namespace SeiyuNico\LaravelCore\Observers;

use Illuminate\Database\Eloquent\Model;
use SeiyuNico\LaravelCore\Exceptions\S3DeleteFailException;
use SeiyuNico\LaravelCore\Exceptions\S3UploadFailException;
use SeiyuNico\LaravelCore\Traits\Models\HasImagesObservable;

class HasImagesObserver
{
    /**
     * 作成時にバイナリをアップロードする
     *
     * @param  Model&HasImagesObservable  $model
     * @return void
     *
     * @throws S3UploadFailException
     */
    public function creating(Model $model): void
    {
        $this->createOrUpdate($model);
    }

    /**
     * 更新時にバイナリをアップロードする
     *
     * @param  Model&HasImagesObservable  $model
     * @return void
     *
     * @throws S3UploadFailException
     */
    public function saving(Model $model): void
    {
        $this->createOrUpdate($model);
    }

    /**
     * 更新時にバイナリをアップロードする
     *
     * @param  Model&HasImagesObservable  $model
     * @return void
     *
     * @throws S3UploadFailException
     */
    public function updating(Model $model): void
    {
        $this->createOrUpdate($model);
    }

    /**
     * @param  Model&HasImagesObservable  $model
     * @return void
     *
     * @throws S3UploadFailException
     */
    private function createOrUpdate(Model $model): void
    {
        if (! method_exists($model, 'isImageColumn')) {
            return;
        }

        $current = $model->getAttributes();

        foreach ($current as $column => $value) {
            if (! $model->isImageColumn($column) || ! $model->usingImages($column)) {
                continue;
            }
            $model->setOriginalImages($column);

            $images = $model->{$column};
            $base_path = $model->buildImagePath($column);
            $model->{$column} = json_encode($this->uploadImages($base_path, $images));
            $model->clearImages($column);
        }
    }

    /**
     * 変更前後を比較して削除された画像をS3から削除する
     *
     * @param  Model&HasImagesObservable  $model
     * @return void
     */
    public function updated(Model $model): void
    {
        if (! method_exists($model, 'isImageColumn')) {
            return;
        }

        $current = $model->getAttributes();
        $deleted = [];

        foreach ($current as $column => $value) {
            if (! $model->isImageColumn($column) || ! $model->isDirty($column)) {
                continue;
            }
            $decoded = is_null($value) ? [] : json_decode($value);
            $deleted = array_merge($deleted, array_diff($model->getOriginalImages($column) ?? [], $decoded));
        }
        $this->deleteImages($deleted);
    }

    /**
     * レコード削除時にS3の画像を削除する
     *
     * @param  Model  $model
     * @return void
     */
    public function deleted(Model $model): void
    {
        if (! method_exists($model, 'isImageColumn')) {
            return;
        }

        $current = $model->getOriginal();

        $deleted = [];

        foreach ($current as $column => $value) {
            if (! $model->isImageColumn($column)) {
                continue;
            }
            $value = $value ?? [];
            $deleted = array_merge($deleted, $value);
        }
        $this->deleteImages($deleted);
    }

    /**
     * @param  string  $base_path
     * @param  array  $images
     * @return array
     *
     * @throws S3UploadFailException
     */
    private function uploadImages(string $base_path, array $images): array
    {
        return array_map(function ($image) use ($base_path) {
            if (is_string($image)) {
                return $image;
            }
            $result = \Storage::disk('s3')->putFile($base_path, $image);

            if ($result === false) {
                throw new S3UploadFailException($base_path);
            }

            return '/'.$result;
        }, $images);
    }

    /**
     * @param  array  $images
     * @return void
     */
    private function deleteImages(array $images): void
    {
        foreach ($images as $image) {
            try {
                \Storage::disk('s3')->delete($image);
            } catch (\Throwable $e) {
                report(new S3DeleteFailException($image, $e));
            }
        }
    }
}
