<?php

namespace Csv;

class Validator implements Visitor
{
    public function visitRecord(Record $record)
    {

    }

    public function visitCheckbox(CheckboxField $field)
    {
        $name = 'csv_' . $field->name();
        if ($field->isRequired() && empty($_POST[$name]))
            throw new \Exception('REQUIRED');
    }

    public function visitTextarea(TextareaField $field)
    {

    }

    public function visitHidden(HiddenField $field)
    {

    }
}
