<?php

namespace ZablockiBros\Mappers\Commands;

use ZablockiBros\Mappers\Commands\Traits\AsksFields;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MapperMakeCommand extends GeneratorCommand
{
    use AsksFields;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:mapper {name} {--fields=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new data mapper';

    /**
     * Execute the console command.
     *
     * @return bool|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if ($this->option('fields')) {
            $this->fields = $this->option('fields');
        }

        if (! $this->fields) {
            $this->askFields();
        }

        if (false === parent::handle()) {
            return false;
        }

        $this->call('make:adapter-interface', [
            'name'     => $this->getNameInput(),
            '--fields' => $this->fields,
        ]);

        $this->call('make:adapter-class', [
            'name'     => $this->getNameInput(),
            '--fields' => $this->fields,
        ]);

        return true;
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = str_replace('DummyClass', $this->getClassName(), $stub);
        $stub = str_replace('DummyAdapterClass', $this->getAdapterClass($name), $stub);
        $stub = str_replace('DummyAdapterFullClass', $this->getAdapterFullClass($name), $stub);
        $stub = $this->replaceFields($stub, $name);

        return $stub;
    }
    /**
     * @param $stub
     * @param $name
     * @return string
     */
    protected function replaceFields($stub, $name)
    {
        return str_replace('DummyFields', $this->getClassDocMethods(), $stub);
    }

    /**
     * @var string
     */
    protected function getAdapterClass($name)
    {
        $name = $this->getBaseClassName();

        return $name . 'Adapter';
    }

    /**
     * @var string
     */
    protected function getAdapterFullClass($name)
    {
        return $name . '\Adapters\\' . $this->getAdapterClass($name);
    }

    /**
     * Get the destination class path.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        $name = str_replace('\\', '/', $name);
        $name = ucwords(camel_case($name));

        return base_path('app/') . $name . '/' . $this->getClassName() . '.php';
    }

    /**
     * @return string
     */
    protected function getClassName()
    {
        return $this->getBaseClassName() . 'Mapper';
    }

    /**
     * @return string
     */
    protected function getBaseClassName()
    {
        $name = trim($this->argument('name'));
        $parts = explode('/', $name);

        return ucwords(camel_case(array_last($parts)));
    }
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/mapper.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Mappers';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the service.'],
        ];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['fields', null, InputOption::VALUE_OPTIONAL, 'Fields to map to'],
        ];
    }
}
