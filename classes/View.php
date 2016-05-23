<?php

namespace Csv;

class View
{
    public static $id = 0;

    private $controller;

    private $template;

    private $data;

    public function __construct($controller, $template, array $data)
    {
        global $pth;

        $this->controller = $controller;
        $this->template = "{$pth['folder']['plugins']}csv/views/$template.php";
        $this->data = $data;
    }

    public function render()
    {
        extract($this->data);
        ob_start();
        include $this->template;
        return ob_get_clean();
    }

    protected function lang($key, $args = [], $count = null)
    {
        return $this->controller->lang($key, $args, $count);
    }

    protected function url($action = null, $id = null)
    {
        return $this->controller->url($action, $id);
    }
}
