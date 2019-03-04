<?php

namespace ZablockiBros\Mappers\Commands;

use ZablockiBros\Mappers\Commands\Traits\AsksFields;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AdapterMakeCommand extends GeneratorCommand
{
    use AsksFields;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:adapter-class {name} {--fields=} {--interface=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new data adapter for given mapper';

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

        $this->line('Creating adapter interface...');

        $this->call('make:adapter-interface', [
            'name'     => $this->getNameInput(),
            '--fields' => $this->fields ?? '',
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
        $stub = str_replace('DummyAbstractClass', $this->getAbstractClassName(), $stub);
        $stub = str_replace('DummyAbstractFullClass', $this->getNamespace($name) . '\\' . $this->getAbstractClassName(), $stub);
        //dd($this->getStubFieldMethods());
        $stub = str_replace('DummyStubMethods', $this->getStubFieldMethods(), $stub);

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

        return base_path('app/') . $path . '/Adapters/' . $this->getClassName() . '.php';
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

    /**
     * Get the class name from name input.
     *
     * @return string
     */
    protected function getAbstractClassName()
    {
        $name = $this->option('interface') ?? $this->getClassName();

        if (starts_with('Abstract', $name)) {
            return $name;
        }

        return 'Abstract' . $name;
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
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/adapter.stub';
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
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['fields', null, InputOption::VALUE_OPTIONAL, 'Fields to map to'],
            ['interface', null, InputOption::VALUE_OPTIONAL, 'Interface to map to'],
        ];
    }
}
