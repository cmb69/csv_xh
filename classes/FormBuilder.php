<?php

namespace Csv;

class FormBuilder implements Visitor
{
    private $sxe;

    private $data;

    public function buildFormFor(Record $record, array $data)
    {
        $this->data = $data;
        $record->accept($this);
        return $this->sxe/*->asXml()*/;
    }

    public function visitRecord(Record $record)
    {
        $this->sxe = new \SimpleXmlElement('<form/>');
        $this->sxe->addAttribute('class', 'csv_editform');
    }

    public function visitDate(DateField $field)
    {
        $p = $this->sxe->addChild('p');
        $this->addLabelTo($p, $field);
        $el = $p->addChild('input');
        $el->addAttribute('type', 'date');
        $this->addNameTo($el, $field);
        $el->addAttribute('value', $this->data[$field->name()]);
    }

    public function visitCheckbox(CheckboxField $field)
    {
        $p = $this->sxe->addChild('p');
        $this->addLabelTo($p, $field);
        $name = 'csv_' . $field->name();
        $el = $p->addChild('input');
        $this->addNameTo($el, $field);
        $el->addAttribute('type', 'hidden');
        $el->addAttribute('value', '');
        $el = $p->addChild('input');
        $this->addNameTo($el, $field);
        $el->addAttribute('type', 'checkbox');
        $el->addAttribute('value', '1');
        if ($this->data[$field->name()])
            $el->addAttribute('checked', 'checked');
    }
    
    public function visitSelect(SelectField $field)
    {
        $p = $this->sxe->addChild('p');
        $this->addLabelTo($p, $field);
        $el = $p->addChild('select');
        $this->addNameTo($el, $field);
        foreach ($field->options() as $option) {
            $child = $el->addChild('option', $option);
            if ($this->data[$field->name()] == $option) {
                $child->addAttribute('selected', 'selected');
            }
        }
    }

    public function visitTextarea(TextareaField $field)
    {
        $p = $this->sxe->addChild('p');
        $this->addLabelTo($p, $field);
        $el = $p->addChild('textarea', $this->data[$field->name()]);
        $this->addNameTo($el, $field);
        if ($field->isRequired()) {
            $el->addAttribute('required', 'required');
        }
    }

    public function visitHidden(HiddenField $field)
    {
        $p = $this->sxe->addChild('p');
        $el = $p->addChild('input');
        $el->addAttribute('type', 'hidden');
        $this->addNameTo($el, $field);
        $el->addAttribute('value', $this->data[$field->name()]);
    }

    public function visitText(TextField $field)
    {
        $p = $this->sxe->addChild('p');
        $this->addLabelTo($p, $field);
        $el = $p->addChild('input');
        $el->addAttribute('type', 'text');
        $this->addNameTo($el, $field);
        $el->addAttribute('value', $this->data[$field->name()]);
    }

    private function addLabelTo(\SimpleXmlElement $sxe, Field $field)
    {
        $sxe->addChild('label', $field->label());
    }

    private function addNameTo(\SimpleXmlElement $sxe, $field)
    {
        $sxe->addAttribute('name', 'csv_' . $field->name());
    }
}
