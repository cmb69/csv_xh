<?php

namespace Csv;

class HiddenField extends Field
{
    public function __construct($name)
    {
        parent::__construct($name, null);
    }

    public function accept(Visitor $visitor)
    {
        $visitor->visitHidden($this);
    }
}

