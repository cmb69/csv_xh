<?php

namespace Csv;

class Table2
{
    private $filename;

    private $columns;

    public function __construct($filename, array $columns)
    {
        $this->filename = $filename;
        $this->columns = $columns;
    }

    public function select(/*$columns*/)
    {
        $iterator = new ReadIterator($this->filename, $this->columns);
        if (func_num_args()) {
            $iterator = new SelectIterator($iterator, func_get_args());
        }
        return new Query($iterator);
    }

    public function delete()
    {

    }

    public function insert(array $values)
    {
        $record = array();
        foreach ($this->columns as $column) {
            $record[$column] = $values[$column];
        }
        $stream = fopen($this->filename, 'a');
        flock($stream, LOCK_EX);
        fputcsv($stream, $record);
        flock($stream, LOCK_UN);
        fclose($stream);
    }

    public function update(array $values)
    {
        return new Updater($this->columns, $values);
    }
}

class Updater
{
    private $columns;

    private $values;

    public function __construct(array $columns, array $values)
    {
        $this->columns = $columns;
        $this->values = $values;
    }
}

class Query
{
    private $iterator;

    public function __construct($iterator)
    {
        $this->iterator = $iterator;
    }

    public function where(callable $filter)
    {
        $this->iterator = new \CallbackFilterIterator($this->iterator, $filter);
        return $this;
    }

    public function limit($offset, $count)
    {
        $this->iterator = new \LimitIterator($this->iterator, $offset, $count);
        return $this;
    }

    public function fetch()
    {
        $result = iterator_to_array($this->iterator);
        $this->iterator = null;
        return $result;
    }
}

class SelectIterator implements \Iterator
{
    private $iterator;

    private $columns;

    public function __construct(\Iterator $iterator, array $columns)
    {
        $this->iterator = $iterator;
        $this->columns = array_flip($columns);
    }

    public function current()
    {
        return array_intersect_key($this->iterator->current(), $this->columns);
    }

    public function key()
    {
        return $this->iterator->key();
    }

    public function next()
    {
        return $this->iterator->next();
    }

    public function rewind()
    {
        return $this->iterator->rewind();
    }

    public function valid()
    {
        return $this->iterator->valid();
    }
}



class ReadIterator implements \Iterator
{
    private $stream;

    private $columns;

    private $current;

    public function __construct($filename, array $columns)
    {
        $this->columns = $columns;
        $this->stream = fopen($filename, 'r');
        flock($this->stream, LOCK_SH);
    }

    public function __destruct()
    {
        flock($this->stream, LOCK_UN);
        fclose($this->stream);
    }

    public function current()
    {
        return array_combine($this->columns, $this->current);
    }

    public function key()
    {
        return $this->current[0];
    }

    public function next()
    {
        $this->current = fgetcsv($this->stream);
    }

    public function rewind()
    {
        rewind($this->stream);
        $this->next();
    }

    public function valid()
    {
        return $this->current !== false;
    }
}


class WriteIterator implements \Iterator
{
    private $stream;

    private $temp;

    private $columns;

    public function __construct($filename, array $columns)
    {
        $this->columns = $columns;
        $this->stream = fopen($filename, 'r+');
        flock($this->stream, LOCK_EX);
        $this->temp = fopen('php://temp', 'w+');
    }

    public function __destruct()
    {
        fclose($this->temp);
        flock($this->stream, LOCK_UN);
        fclose($this->stream);
    }

    public function current()
    {
        return array_combine($this->columns, $this->current);
    }

    public function key()
    {
        return $this->current[0];
    }

    public function next()
    {
        $this->current = fgetcsv($this->stream);
    }

    public function rewind()
    {
        rewind($this->stream);
        $this->next();
    }

    public function valid()
    {
        return $this->current !== false;
    }
}

interface Criterion
{
    public function __invoke(array $row);
}

class IsEqual implements Criterion
{
    private $column;

    private $value;

    public function __construct($column, $value)
    {
        $this->column = $column;
        $this->value = $value;
    }

    public function __invoke(array $row)
    {
        return $row[$this->column] == $this->value;
    }
}

class IsLike implements Criterion
{
    private $column;

    private $value;

    public function __construct($column, $value)
    {
        $this->column = $column;
        $this->value = $value;
    }

    public function __invoke(array $row)
    {
        return fnmatch($this->value, $row[$this->column]);
    }
}

class IsBetweenAnd implements Criterion
{
    private $column;

    private $from;

    private $to;

    public function __construct($column, $from, $to)
    {
        $this->column = $column;
        $this->from = $from;
        $this->to = $to;
    }

    public function __invoke(array $row)
    {
        $value = $row[$this->column];
        return $value >= $this->from && $value <= $this->to;
    }
}

class IsIn implements Criterion
{
    private $column;

    private $values;

    public function __construct($column, $values)
    {
        $this->column = $column;
        $this->values = $values;
    }

    public function __invoke(array $row)
    {
        return in_array($row[$this->column], $this->values);
    }
}
