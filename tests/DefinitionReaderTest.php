<?php

use org\bovigo\vfs\vfsStream;

class DefinitionReaderTest extends PHPUnit_Framework_TestCase
{
    private $file;

    public function setUp()
    {
        $root = vfsStream::setup();
        $this->file = vfsStream::newFile('foo.json')->at($root);
        $json = <<<EOT
[{
    "name": "description",
    "type": "textarea",
    "label": "Description"
}, {
    "name": "gender",
    "type": "select",
    "label": "Gender",
    "options": ["male", "female"]
}, {
    "name": "secret",
    "type": "hidden"
}, {
    "name": "archived",
    "type": "checkbox"
}, {
    "name": "due_date",
    "type": "date"
}]
EOT;
        $this->file->setContent($json);
    }

    public function testReadReturnsRecord()
    {
        $filename = $this->file->url();
        $reader = new Csv\DefinitionReader($filename);
        $this->assertInstanceOf('Csv\\Record', $reader->read());
    }
}
