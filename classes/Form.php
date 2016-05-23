<?php

namespace Csv;

class Form
{
    private $controller;

    private $model;

    private $record;

    private $action;

    private $id;

    public function __construct(Controller $controller, Model $model, array $record, $action)
    {
        $this->controller = $controller;
        $this->model = $model;
        $this->record = $record;
        $this->action = $action;
        $this->id = 1;
    }

    public function render()
    {
        ob_start();
        $this->renderForm();
        return ob_get_clean();
    }

    private function renderForm()
    {
        $action = $this->controller->url($this->action);
        printf('<form class="csv_editform" action="%s" method="POST">', XH_hsc($action));
        printf('<input type="hidden" name="csv_digest" value="%s">', $this->model->digest($this->record));
        foreach ($this->model->columns() as $name => $column) {
            $this->renderField($name, $column);
        }
        $this->renderButtons();
        echo '</form>';
    }

    private function renderField($name, $column)
    {
        echo '<p>';
        $type = isset($column['type']) ? $column['type'] : 'text';
        switch ($type) {
        case 'hidden':
            $this->renderInput($this->id++, $type, $name, $this->record[$name], $column);
            break;
        case 'checkbox':
            $this->renderLabel($this->id, $column['title']);
            $this->renderCheckbox($this->id++, $name, $this->record[$name], $column);
            break;
        case 'select':
            $this->renderLabel($this->id, $column['title']);
            $this->renderSelect($this->id++, $name, $column['values'], $this->record[$name], $column);
            break;
        case 'textarea':
            $this->renderLabel($this->id, $column['title']);
            $this->renderText($this->id++, $name, $this->record[$name], $column);
            break;
        default:
            $this->renderLabel($this->id, $column['title']);
            $this->renderInput($this->id++, $type, $name, $this->record[$name], $column);
        }
        echo '</p>';
    }

    private function renderLabel($id, $text)
    {
        printf('<label for="csv_id%d">%s</label>', $id, XH_hsc($text));
    }

    private function renderCheckbox($id, $name, $value, $column)
    {
        $checked = $value ? 'checked' : '';

        printf('<input name="csv_%s" type="hidden" value="0">', $id, $name);
        printf('<input id="csv_id%d" name="csv_%s" type="checkbox" value="1" %s>', $id, $name, $checked);
    }

    private function renderSelect($id, $name, $options, $value, $column)
    {
        printf('<select id="csv_id%d" name="csv_%s">', $id, $name);
        foreach ($options as $option) {
            $checked = $option == $value ? 'checked' : '';
            printf('<option %s>%s</option>', $checked, XH_hsc($option));
        }
        echo '</select>';
    }

    private function renderText($id, $name, $value, $column)
    {
        $required = isset($column['required']) ? 'required' : '';
        printf('<textarea id="csv_id%d" name="csv_%s" %s>', $id, $name, $required);
        echo XH_hsc($value);
        echo '</textarea>';
    }

    private function renderInput($id, $type, $name, $value, $column)
    {
        $required = isset($column['required']) ? 'required' : '';
        printf(
            '<input id="csv_id%d" name="csv_%s" type="%s" value="%s" %s>',
            $id, $name, $type, XH_hsc($value), $required
        );
    }

    private function renderButtons()
    {
        global $plugin_tx;

        echo '<p>';
        printf('<button>%s</button>', $plugin_tx['csv']['label_save']);
        echo ' ';
        printf('<button type="reset">%s</button>', $plugin_tx['csv']['label_reset']);
        echo '</p>';
        printf(
            '<p><a href="%s">%s</a></p>',
            $this->controller->url('index'),
            $plugin_tx['csv']['label_back']
        );
    }
}
