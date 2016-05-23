<?php

namespace Csv;

class DefinitionReader
{
    private $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function read()
    {
        $record = new Record();
        $columns = json_decode(file_get_contents($this->filename));
        foreach ($columns as $column) {
            $type = isset($column->type) ? $column->type : 'text';
            $class = "Csv\\{$type}Field";
            $label = isset($column->label) ? $column->label : '';
            if ($type == "select") {
                $field = new $class($column->name, $label, $column->options);
            } else {
                $field = new $class($column->name, $label);
            }
            $record->addField($field);
        }
        return $record;
    }
}
