<?php

namespace Csv;

interface Visitor
{
    public function visitRecord(Record $record);
    
    public function visitDate(DateField $field);

    public function visitCheckbox(CheckboxField $field);

    public function visitSelect(SelectField $field);

    public function visitTextarea(TextareaField $field);

    public function visitHidden(HiddenField $field);

    public function visitText(TextField $field);
}
