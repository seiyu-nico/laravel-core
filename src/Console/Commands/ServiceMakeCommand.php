<?php

namespace SeiyuNico\LaravelCore\Console\Commands;

use Illuminate\Support\Str;
use SeiyuNico\LaravelCore\Exceptions\InvalidParameterException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ServiceMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/../../stubs/Service.stub');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Services';
    }

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
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in the base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name): string
    {
        $replace = [];
        $replace = $this->buildModelReplacements($replace);
        $replace = $this->buildRepositoryReplacements($replace);

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildRepositoryReplacements(array $replace): array
    {
        $input = preg_replace('/Service$/', '', $this->argument('name'));
        [$fully_qualified, $class] = $this->parseRepository($input);
        if (
            ! class_exists($fully_qualified) &&
            $this->option('repository') &&
            $this->components->confirm("A {$class} repository does not exist. Do you want to generate it?", true)
        ) {
            $this->call('make:repository', ['name' => $class.'Repository']);
        }

        return array_merge($replace, [
            '{{ namespacedRepository }}' => $fully_qualified,
            '{{ repositoryClass }}' => $class.'Repository',
        ]);
    }

    /**
     * Get the fully-qualified repository class name.
     *
     * @param  string  $class
     * @return array
     *
     * @throws InvalidParameterException
     */
    protected function parseRepository($class): array
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $class)) {
            throw new InvalidParameterException('Repository name contains invalid characters.');
        }

        return $this->generateRepository($class);
    }

    /**
     * Generate the repository for the given model and classes.
     *
     * @param  string  $class
     * @return array
     */
    protected function generateRepository(string $class): array
    {
        $class = ltrim($class, '\\/');

        $class = str_replace('/', '\\', $class);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($class, $rootNamespace)) {
            return $class;
        }

        $class_repositories = $class.'Repository';

        $fully_qualified = is_dir(app_path('Repositories')) ? $rootNamespace.'Repositories\\'.$class_repositories : $rootNamespace.$class_repositories;

        return [$fully_qualified, $class];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['model', 'm', InputOption::VALUE_NONE, 'Generate a service for the given model'],
            ['repository', 'r', InputOption::VALUE_NONE, 'Generate a service for the given repository'],
        ];
    }
}
