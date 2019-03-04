<?php

namespace ZablockiBros\Mappers;

use Illuminate\Support\Fluent;

abstract class Adapter
{
    /**
     * @var \Illuminate\Support\Fluent
     */
    protected $data;

    /**
     * @var array
     */
    protected $adapted;

    /**
     * Adapter constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = new Fluent($data);
        $this->adapt();
    }

    /**
     * @param $key
     *
     * @return null
     */
    public function __get($key)
    {
        return $this->adapted[$key] ?? null;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed|null
     */
    public function __call($name, $arguments)
    {
        return $this->adapted[snake_case($name)] ?? null;
    }

    /**
     * @override
     *
     * @return array
     */
    public function mapping(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function adapt(): array
    {
        return $this->adapted = collect($this->mapping())
            ->mapWithKeys(function ($dotPath, $key) {
                return [
                    $key => $this->mapValue($dotPath),
                ];
            })
            ->toArray();
    }

    /**
     * @return array
     */
    public function adapted(): array
    {
        return $this->adapted;
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->data->toArray();
    }

    /**
     * @param string $class
     *
     * @return \ZablockiBros\Mappers\Mapper
     */
    public function mapInto(string $class): Mapper
    {
        return new $class($this);
    }

    /**
     * @param string $class
     *
     * @return \ZablockiBros\Mappers\Mapper
     */
    public function mapper(string $class): Mapper
    {
        return $this->mapInto($class);
    }

    /**
     * @param $dotPath
     */
    private function mapValue($dotPath)
    {
        if (is_null($dotPath)) {
            return $dotPath;
        }

        if (is_array($dotPath)) {
            return $dotPath;
        }

        if (! is_string($dotPath)) {
            return $dotPath;
        }

        return $this->data->get($dotPath)
            ?? array_dot($this->data->toArray())[$dotPath]
            ?? $dotPath
            ?? null;
    }
}
