<?php

namespace Csv;

class TextareaField extends Field
{
    public function accept(Visitor $visitor)
    {
        $visitor->visitTextarea($this);
    }
}
