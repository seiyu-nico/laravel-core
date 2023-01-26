<?php

namespace SeiyuNico\LaravelCore\Console\Commands;

use Illuminate\Console\GeneratorCommand as GC;
use Illuminate\Support\Str;
use SeiyuNico\LaravelCore\Exceptions\InvalidParameterException;

abstract class GeneratorCommand extends GC
{
    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace): array
    {
        $input = preg_replace('/(Repository|Service)$/', '', $this->argument('name'));
        [$fully_qualified, $model] = $this->parseModel($input);

        if (
            ! class_exists($fully_qualified) && $this->option('model') &&
            $this->option('model') &&
            $this->components->confirm("A {$model} model does not exist. Do you want to generate it?", true)
        ) {
            $this->call('make:model', ['name' => $model]);
        }

        return array_merge($replace, [
            '{{ namespacedModel }}' => $fully_qualified,
            '{{ modelClass }}' => $model,
        ]);
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return array
     *
     * @throws InvalidParameterException
     */
    protected function parseModel($model): array
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidParameterException('Model name contains invalid characters.');
        }

        return $this->generateModel($model);
    }

    /**
     * Generate the model for the given model and classes.
     *
     * @param  string  $model
     * @return array
     */
    protected function generateModel(string $model): array
    {
        $model = ltrim($model, '\\/');

        $model = str_replace('/', '\\', $model);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($model, $rootNamespace)) {
            return $model;
        }

        $fully_qualified = is_dir(app_path('Models')) ? $rootNamespace.'Models\\'.$model : $rootNamespace.$model;

        return [$fully_qualified, $model];
    }
}
