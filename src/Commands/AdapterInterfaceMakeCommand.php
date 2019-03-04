<?php

namespace ZablockiBros\Mappers\Commands;

use ZablockiBros\Mappers\Commands\Traits\AsksFields;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AdapterInterfaceMakeCommand extends GeneratorCommand
{
    use AsksFields;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:adapter-interface {name} {--fields}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new data adapter interface';

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

        return parent::handle();
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
        $stub = str_replace('DummyClass', $this->getAbstractClassName(), $stub);
        $stub = str_replace('DummyFields', '', $stub);
        //$stub = str_replace('DummyFields', $this->getAbstractFieldMethods(), $stub);
        $stub = str_replace('DummyDocMethods', $this->getClassDocMethods(), $stub);
        $stub = str_replace('DummyMapArrayKeys', $this->getMapArrayFields(), $stub);

        return $stub;
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
        $path = str_replace($this->getClassName(), '', $name);

        return base_path('app/') . $path . '/Adapters/' . $this->getAbstractClassName() . '.php';
    }

    /**
     * Get the full namespace for a given class, without the class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return $name . '\Adapters';
    }

    /**
     * Get the class name from name input.
     *
     * @return string
     */
    protected function getClassName()
    {
        $name = ucwords(camel_case($this->getAdapterName()));

        if (strpos($name, 'Adapter') === false) {
            $name = $name . 'Adapter';
        }

        return $name;
    }

    /**]
     * @return string
     */
    protected function getAbstractClassName()
    {
        $name = $this->getClassName();

        if (strpos($name, 'Abstract') === false) {
            $name = 'Abstract' . $name;
        }

        return $name;
    }

    /**
     * @return string
     */
    protected function getAdapterName()
    {
        $name = trim($this->argument('name'));
        $parts = explode('/', $name);

        return array_last($parts);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/abstract-adapter.stub';
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
