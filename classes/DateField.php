<?php

namespace Csv;

class DateField extends Field
{
    public function accept(Visitor $visitor)
    {
        $visitor->visitDate($this);
    }
}
