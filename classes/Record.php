<?php

namespace Csv;

class Record
{
    private $fields;

    public function addField(Field $field)
    {
        $this->fields[] = $field;
    }

    public function columnNames()
    {
        return array_map(function ($field) {
            return $field->name();
        }, $this->fields);
    }

    public function accept(Visitor $visitor)
    {
        $visitor->visitRecord($this);
        foreach ($this->fields as $field) {
            $field->accept($visitor);
        }
    }
}
