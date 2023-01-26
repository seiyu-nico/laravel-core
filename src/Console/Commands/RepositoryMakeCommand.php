<?php

namespace SeiyuNico\LaravelCore\Console\Commands;

use Illuminate\Support\Str;
use SeiyuNico\LaravelCore\Exceptions\InvalidParameterException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RepositoryMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/../../stubs/Repository.stub');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Repositories';
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
        $replace = $this->buildServiceReplacements($replace);

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
    protected function buildServiceReplacements(array $replace): array
    {
        $input = preg_replace('/Repository$/', '', $this->argument('name'));
        [$fully_qualified, $class] = $this->parseService($input);
        if (! class_exists($fully_qualified) && $this->option('service') && $this->components->confirm("A {$class} service does not exist. Do you want to generate it?", true)) {
            $this->call('make:service', ['name' => $class.'Service']);
        }

        return array_merge($replace, [
            '{{ namespacedService }}' => $fully_qualified,
            '{{ serviceClass }}' => $class.'Service',
        ]);
    }

    /**
     * Get the fully-qualified service class name.
     *
     * @param  string  $class
     * @return array
     *
     * @throws InvalidParameterException
     */
    protected function parseService($class): array
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $class)) {
            throw new InvalidParameterException('Service name contains invalid characters.');
        }

        return $this->generateService($class);
    }

    /**
     * Generate the service for the given model and classes.
     *
     * @param  string  $class
     * @return array
     */
    protected function generateService(string $class): array
    {
        $class = ltrim($class, '\\/');

        $class = str_replace('/', '\\', $class);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($class, $rootNamespace)) {
            return $class;
        }

        $class_service = $class.'Service';

        $fully_qualified = is_dir(app_path('Services')) ? $rootNamespace.'Services\\'.$class_service : $rootNamespace.$class_service;

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
            ['model', 'm', InputOption::VALUE_NONE, 'Generate a repository for the given model'],
            ['service', 's', InputOption::VALUE_NONE, 'Generate a repository for the given service'],
        ];
    }
}
