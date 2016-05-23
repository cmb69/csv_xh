<?php

use org\bovigo\vfs\vfsStream;

class TableTest extends PHPUnit_Framework_TestCase
{
    /** @var Csv\Table */
    private $table;

    public function setUp()
    {
        $root = vfsStream::setup();
        $file = vfsStream::newFile('test.csv')->at($root);
        $csv = <<<EOT
1,Becker,Christoph
2,Keil,Hartmut
3,Ziesing,Frank

EOT;
        $file->setContent($csv);
        $columns = ['id', 'last_name', 'first_name'];
        $this->table = new Csv\Table($file->url(), $columns);
    }

    public function testFindAll()
    {
        $expected = [
            ['id' => '1', 'last_name' => 'Becker', 'first_name' => 'Christoph'],
            ['id' => '2', 'last_name' => 'Keil', 'first_name' => 'Hartmut'],
            ['id' => '3', 'last_name' => 'Ziesing', 'first_name' => 'Frank']
        ];
        $this->assertEquals($expected, $this->table->findAll());
    }

    public function testFindById()
    {
        $expected = ['id' => '2', 'last_name' => 'Keil', 'first_name' => 'Hartmut'];
        $this->assertEquals($expected, $this->table->findById(2));
    }

    public function testInsert()
    {
        $new = ['id' => '4', 'last_name' => 'Irmler', 'first_name' => 'Holger'];
        $this->table->insert($new);
        $this->assertEquals($new, $this->table->findById('4'));
    }

    public function testUpdate()
    {
        $new = ['id' => '1', 'last_name' => 'Becker', 'first_name' => 'Christoph Michael'];
        $this->assertTrue($this->table->update($new, '884e65bcc730d0210d4f1acd8c98f52f'));
        $this->assertEquals($new, $this->table->findById(1));
    }
    
    public function testDelete()
    {
        $this->assertTrue($this->table->delete(1, '884e65bcc730d0210d4f1acd8c98f52f'));
        $this->assertNull($this->table->findById(1));
    }
}
