<?php

namespace Csv;

class Controller
{
    private $model;

    private $filename;

    public function __construct(Model $model, $filename)
    {
        global $pth;

        $this->model = $model;
        $this->filename = "{$pth['folder']['content']}$filename";
    }

    public function processIndex()
    {
        $records = $this->model->findAll();
        return (new ListView($this, $this->model, $records))->render();


        $columns = $this->model->columns();
        $view = new View($this, 'list', compact('records', 'columns'));
        return $view->render();
    }

    public function processCreate()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $record = $this->model->newRecord();
            return (new Form($this, $this->model, $record, 'create'))->render();
        } else {
            $posted = $this->recordFromPost();
            //return (new Form($this, $this->model, $posted, 'create'))->render();
            $this->model->insert($posted);
            $this->redirect($this->url('index'));
        }
    }

    private function validate($record)
    {
        $errors = array();

    }

    public function processUpdate()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $id = isset($_GET['csv_id']) ? stsl($_GET['csv_id']) : '';
            $reader = new DefinitionReader("{$this->filename}.json");
            $record = $reader->read();
            $table = new Table($this->filename, $record->columnNames());
            $data = $table->findById($id);
            //$record = $this->model->findById($id);
            if (isset($data)) {
                $fb = new FormBuilder();
                $form = $fb->buildFormFor($record, $data);
                return $form->asXml();
                return (new Form($this, $this->model, $record, 'update'))->render();
            } else {
                $this->redirect($this->url('index'));
            }
        } else {
            $posted = $this->recordFromPost();
            $digest = stsl($_POST['csv_digest']);
            if ($this->model->update($posted, $digest)) {
                $this->redirect($this->url('index'));
            } else {
                return (new Form($this, $this->model, $posted, 'update'))->render();
            }
        }
    }

    private function recordFromPost()
    {
        $posted = array();
        foreach ($this->model->columns() as $name => $column) {
            $posted[$name] = stsl($_POST["csv_$name"]);
        }
        return $posted;
    }

    public function processDelete()
    {
        //if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        //    $id = isset($_GET['csv_id']) ? stsl($_GET['csv_id']) : '';
        //    $record = $this->model->findById($id);
        //    return (new DetailView($record))->render();
        //} else {
        $id = isset($_GET['csv_id']) ? stsl($_GET['csv_id']) : '';
        $this->model->delete($id);
        $this->redirect($this->url('index'));
        //}
    }

    private function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    public function url($action = null, $id = null)
    {
        global $sn, $su;

        $url = "$sn?$su";
        if (isset($action)) {
            $url .= "&csv_action=$action";
        }
        if (isset($id)) {
            $url .= "&csv_id=$id";
        }
        return $url;
    }
}
