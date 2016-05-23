<?php

namespace Csv;

class TextField extends Field
{
    public function accept(Visitor $visitor)
    {
        $visitor->visitText($this);
    }
}
