<?php

namespace ZablockiBros\Mappers\Commands\Traits;

trait AsksFields
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * @return void
     */
    protected function askFields(): void
    {
        if ($this->option('fields')) {
            $this->fields = $this->option('fields');

            return;
        }

        $this->fields = $this->ask('List fields you want the adapter to map to (space separated)');
    }

    /**
     * @return array
     */
    protected function getFields(): array
    {
        $fields = trim($this->fields);
        $fields = str_replace(',', ' ', $fields);
        $fields = explode(' ', $fields);

        return collect($fields)
            ->map(function ($field) {
                return trim($field);
            })
            ->filter(function ($field) {
                return ! empty($field);
            })
            ->toArray();
    }

    /**
     * @return array|string
     */
    protected function getClassDocMethods()
    {
        $fields = $this->getFields();

        $dummyFields = [];

        foreach ($fields as $field) {
            $method = camel_case(trim($field));
            $replace = <<<EOT
 * @method mixed $method()
EOT;

            $dummyFields[] = $replace;
        }

        $dummyFields = implode(PHP_EOL, $dummyFields);

        if (! empty($dummyFields)) {
            $dummyFields = <<<EOT
/**
 * Override these methods for specific adapters
 * 
$dummyFields
 */
EOT;

            $dummyFields = PHP_EOL.$dummyFields;
        }

        return $dummyFields;
    }

    /**
     * @return array|string
     */
    protected function getAbstractFieldMethods()
    {
        $fields = $this->getFields();

        $dummyFields = [];

        foreach ($fields as $field) {
            $method = camel_case(trim($field));
            $replace = <<<EOT
    /**
     * @return mixed
     */
     abstract public function $method();
EOT;

            $dummyFields[] = $replace;
        }

        $dummyFields = implode(PHP_EOL.PHP_EOL, $dummyFields);

        if (! empty($dummyFields)) {
            $dummyFields = PHP_EOL.PHP_EOL.$dummyFields;
        }

        return $dummyFields;
    }

    /**
     * @return array|string
     */
    protected function getStubFieldMethods()
    {

        $fields = $this->getFields();

        $dummyFields = [];

        foreach ($fields as $field) {
            $method = camel_case(trim($field));
            $return = '$this->data[\'' . snake_case(trim($field)) . '\'];';
            $replace = <<<EOT
    /**
     * @return mixed
     */
     public function $method()
     {
        return $return
     }
EOT;

            $dummyFields[] = $replace;
        }

        $dummyFields = implode(PHP_EOL.PHP_EOL, $dummyFields);

        if (! empty($dummyFields)) {
            $dummyFields = PHP_EOL.$dummyFields;
        }

        return $dummyFields;
    }

    /**
     * @return array|string
     */
    protected function getMapArrayFields()
    {
        $fields = $this->getFields();

        $dummyFields = [];

        foreach ($fields as $field) {
            $array_key = snake_case(trim($field));
            $methodKey = '$this->' . camel_case(trim($field)) . '()';

            $replace = <<<EOT
            '$array_key' => $methodKey,
EOT;

            $dummyFields[] = $replace;
        }

        $dummyFields = implode(PHP_EOL, $dummyFields);

        if (! empty($dummyFields)) {
            $dummyFields = <<<EOT
$dummyFields
EOT;
        } else {
            $dummyFields = <<<EOT
            'sample_mapper_key' => 'adapter.dot.path',
EOT;

        }

        return $dummyFields;
    }
}
