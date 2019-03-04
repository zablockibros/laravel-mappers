<?php

namespace ZablockiBros\Mappers\Traits;

use ZablockiBros\Mappers\Adapter;
use ZablockiBros\Mappers\Mapper;

trait UsesMappers
{
    /**
     * @param string $class
     *
     * @return \ZablockiBros\Mappers\Adapter
     */
    public function adapt(string $class): Adapter
    {
        return new $class($this->dataForAdapter());
    }

    /**
     * @param string $adapterClass
     * @param string $mapperClass
     *
     * @return \ZablockiBros\Mappers\Mapper
     */
    public function mapper(string $adapterClass, string $mapperClass): Mapper
    {
        return new $mapperClass(new $adapterClass($this->dataForAdapter()));
    }

    /**
     * @override
     *
     * @return array
     */
    protected function dataForAdapter(): array
    {
        return $this->toArray();
    }
}
