<?php

namespace Csv;

abstract class Field
{
    private $name;

    private $label;

    public function __construct($name, $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    public function name()
    {
        return $this->name;
    }

    public function label()
    {
        return $this->label;
    }

    public function isRequired()
    {
        return true;
    }

    abstract public function accept(Visitor $visitor);
}
