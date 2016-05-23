<?php

namespace Csv;

class ListView
{
    private $controller;

    private $model;

    private $records;

    public function __construct(Controller $controller, Model $model, array $records)
    {
        $this->controller = $controller;
        $this->model = $model;
        $this->records = $records;
    }

    public function render()
    {
        global $plugin_tx;

        $this->emitJavaScript();
        ob_start();
        $this->renderTable();
        printf(
            '<p><a href="%s">%s</a></p>',
            XH_hsc($this->controller->url('create')),
            $plugin_tx['csv']['label_new']
        );
        return ob_get_clean();
    }

    private function emitJavaScript()
    {
        global $bjs, $pth;

        $dir = "{$pth['folder']['plugins']}csv/lib/tablefilter/";
        $bjs .= <<<EOT
<script type="text/javascript" src="{$dir}tablefilter.js"></script>
<script type="text/javascript">
addEventListener("load", function () {
console.log('here')
    var tf = new TableFilter(document.querySelector(".csv_table"), {
        base_path: "$dir",
        //paging: true,
        enable_default_theme: true,
        help_instructions: false,
        extensions: [{
            name: "sort"
        }]
    });
    tf.init();
}, false);
</script>
EOT;
    }

    private function renderTable()
    {
        echo '<table class="csv_table">';
        $this->renderThead();
        $this->renderTbody();
        echo '</table>';
    }

    private function renderThead()
    {
        echo '<thead>';
        foreach ($this->model->columns() as $column) {
            if (!isset($column['type']) || $column['type'] != 'hidden') {
                echo '<th>';
                echo XH_hsc($column['title']);
                echo '</th>';
            }
        }
        echo '</thead>';
    }

    private function renderTbody()
    {
        echo '<tbody>';
        foreach ($this->records as $record) {
            $this->renderRecord($record);
        }
        echo '</tbody>';
    }

    private function renderRecord($record)
    {
        global $plugin_tx;

        echo '<tr>';
        foreach ($this->model->columns() as $name => $column) {
            if (!isset($column['type']) || $column['type'] != 'hidden') {
                echo '<td>';
                echo XH_hsc($record[$name]);
                echo '</td>';
            }
        }
        echo '<td>';
        printf(
            '<a href="%s">%s</a>',
            XH_hsc($this->controller->url('update', $record['id'])),
            $plugin_tx['csv']['label_edit']
        );
        echo ' ';
        printf(
            '<a href="%s">%s</a>',
            XH_hsc($this->controller->url('delete', $record['id'])),
            $plugin_tx['csv']['label_delete']
        );
        echo '</td>';
        echo '</tr>';
    }
}
