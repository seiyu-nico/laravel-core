<?php

namespace SeiyuNico\LaravelCore\Traits\Models;

use SeiyuNico\LaravelCore\Observers\HasImagesObserver;

trait HasImagesObservable
{
    /**
     * $imageColumnsに定義したカラムが画像カラムとして扱われる
     * デフォルトは次の通り
     *
     * protected array $imageColumns = [
     *     'images',
     * ];
     */

    /** @var array|string[] */
    private array $temporaryImages = [];

    /** @var array|string[] */
    private array $originalImages = [];

    public static function bootHasImagesObservable(): void
    {
        self::observe(HasImagesObserver::class);
    }

    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->temporaryImages)) {
            return $this->temporaryImages[$key];
        }

        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->getImageColumnsList(), true)) {
            if (is_array($value)) {
                $this->temporaryImages[$key] = $value;
                $this->attributes[$key] = '[]';
            } else {
                $this->attributes[$key] = $value;
            }

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * @param  string  $key
     * @return void
     */
    public function setOriginalImages(string $key): void
    {
        $this->originalImages[$key] = $this->getOriginal($key);
    }

    /**
     * @param  string  $key
     * @return mixed|string|null
     */
    public function getOriginalImages(string $key): mixed
    {
        return $this->originalImages[$key] ?? null;
    }

    /**
     * @param  string  $column
     * @return bool
     */
    public function isImageColumn(string $column): bool
    {
        return in_array($column, $this->getImageColumnsList(), true);
    }

    /**
     * @param  string  $column
     * @return bool
     */
    public function usingImages(string $column): bool
    {
        return isset($this->temporaryImages[$column]);
    }

    /**
     * @param  string  $column
     * @return void
     */
    public function clearImages(string $column): void
    {
        unset($this->temporaryImages[$column]);
    }

    private function getImageColumnsList()
    {
        return $this->imageColumns ?? [
            'images',
        ];
    }

    /**
     * @param  string  $column
     * @return string
     */
    abstract public function buildImagePath(string $column): string;
}
