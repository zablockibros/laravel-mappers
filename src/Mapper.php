<?php

namespace ZablockiBros\Mappers;

use Illuminate\Contracts\Support\Arrayable;

abstract class Mapper implements Arrayable
{
    /**
     * @var \ZablockiBros\Mappers\Adapter
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $mapped;

    /**
     * Mapper constructor.
     *
     * @param \ZablockiBros\Mappers\Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->mapped  = $this->mapped();
    }

    /**
     * @return array
     */
    public function mapped(): array
    {
        return $this->adapter->adapt();
    }

    /**
     * @return array
     */
    public function map(): array
    {
        return $this->mapped();
    }

    /**
     * @return array
     */
    public function original(): array
    {
        return $this->adapter->data();
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->mapped[$key] ?? null;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->adapter->{$name}($arguments);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->mapped();
    }
}
