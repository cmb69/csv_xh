<?php

namespace Csv;

class SelectField extends Field
{
    private $options;

    public function __construct($name, $label, array $options)
    {
        parent::__construct($name, $label);
        $this->options = $options;
    }

    public function options()
    {
        return $this->options;
    }

    public function accept(Visitor $visitor)
    {
        $visitor->visitSelect($this);
    }
}
