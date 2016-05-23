<?php

namespace Csv;

class CheckboxField extends Field
{
    public function accept(Visitor $visitor)
    {
        $visitor->visitCheckbox($this);
    }
}
