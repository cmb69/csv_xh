<?php

use org\bovigo\vfs\vfsStream;

class Table2Test extends PHPUnit_Framework_TestCase
{
    /** @var Csv\Table2 */
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
        $this->table = new Csv\Table2($file->url(), $columns);
    }

    public function testFindAll()
    {
        $expected = [
            1 => ['id' => '1', 'last_name' => 'Becker', 'first_name' => 'Christoph'],
            2 => ['id' => '2', 'last_name' => 'Keil', 'first_name' => 'Hartmut'],
            3 => ['id' => '3', 'last_name' => 'Ziesing', 'first_name' => 'Frank']
        ];
        $actual = $this->table->select()->fetch();
        $this->assertEquals($expected, $actual);
    }

    public function testSelect()
    {
        $expected = [
            1 => ['last_name' => 'Becker'],
            2 => ['last_name' => 'Keil'],
            3 => ['last_name' => 'Ziesing']
        ];
        $actual = $this->table->select('last_name')->fetch();
        $this->assertEquals($expected, $actual);
    }

    public function testLimit()
    {
        $expected = [
            2 => ['id' => '2', 'last_name' => 'Keil', 'first_name' => 'Hartmut'],
        ];
        $actual = $this->table->select()->limit(1, 1)->fetch();
        $this->assertEquals($expected, $actual);
    }

    public function testFindById()
    {
        $expected = [2 => ['id' => '2', 'last_name' => 'Keil', 'first_name' => 'Hartmut']];
        $actual = $this->table->select()->where(function ($row) {return $row['id'] == 2;})->fetch();
        $this->assertEquals($expected, $actual);
    }
    
    public function testIsEqual()
    {
        $expected = [2 => ['id' => '2', 'last_name' => 'Keil', 'first_name' => 'Hartmut']];
        $crit = new Csv\IsEqual('id', 2);
        $actual = $this->table->select()->where($crit)->fetch();
        $this->assertEquals($expected, $actual);
    }

    public function testIsLike()
    {
        $expected = [3 => ['id' => '3', 'last_name' => 'Ziesing', 'first_name' => 'Frank']];
        $crit = new Csv\IsLike('last_name', 'Z*sin?');
        $actual = $this->table->select()->where($crit)->fetch();
        $this->assertEquals($expected, $actual);
    }

    public function testIsBetweenAnd()
    {
        $expected = [
            1 => ['id' => '1', 'last_name' => 'Becker', 'first_name' => 'Christoph'],
            2 => ['id' => '2', 'last_name' => 'Keil', 'first_name' => 'Hartmut']
        ];
        $crit = new Csv\IsBetweenAnd('id', 1, 2);
        $actual = $this->table->select()->where($crit)->fetch();
        $this->assertEquals($expected, $actual);
    }

    public function testIsIn()
    {
        $expected = [
            1 => ['id' => '1', 'last_name' => 'Becker', 'first_name' => 'Christoph'],
            2 => ['id' => '2', 'last_name' => 'Keil', 'first_name' => 'Hartmut']
        ];
        $crit = new Csv\IsIn('first_name', ['Christoph', 'Hartmut']);
        $actual = $this->table->select()->where($crit)->fetch();
        $this->assertEquals($expected, $actual);
    }

    public function testMultipleWhereClauses()
    {
        $expected = [
            1 => ['id' => '1', 'last_name' => 'Becker', 'first_name' => 'Christoph'],
        ];
        $crit1 = new Csv\IsEqual('last_name', 'Becker');
        $crit2 = new Csv\IsEqual('first_name', 'Christoph');
        $actual = $this->table->select()->where($crit1)->where($crit2)->fetch();
        $this->assertEquals($expected, $actual);
    }

    public function testInsert()
    {
        $new = ['id' => '4', 'last_name' => 'Irmler', 'first_name' => 'Holger'];
        $this->table->insert($new);
        $crit = new Csv\IsEqual('id', 4);
        $actual = $this->table->select()->where($crit)->fetch();
        $this->assertEquals($new, $actual[4]);
    }

    public function _testUpdate()
    {
        $new = ['id' => '1', 'last_name' => 'Becker', 'first_name' => 'Christoph Michael'];
        $this->table->update($new);
        $crit = new Csv\IsEqual('id', 1);
        $actual = $this->table->select()->where($crit)->fetch();
        $this->assertEquals($new, $actual[1]);
    }
    
    public function _testDelete()
    {
        $this->assertTrue($this->table->delete(1, '884e65bcc730d0210d4f1acd8c98f52f'));
        $this->assertNull($this->table->findById(1));
    }
}
