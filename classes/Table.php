<?php

namespace Csv;

class Table
{
    private $filename;

    private $columns;

    public function __construct($filename, array $columns)
    {
        $this->filename = $filename;
        $this->columns = $columns;
    }

    public function findAll()
    {
        $result = array();
        $stream = fopen($this->filename, 'r');
        flock($stream, LOCK_SH);
        while (($record = fgetcsv($stream)) !== false) {
            $result[] = array_combine($this->columns, $record);
        }
        flock($stream, LOCK_UN);
        fclose($stream);
        return $result;
    }

    public function findById($id)
    {
        $result = null;
        $stream = fopen($this->filename, 'r');
        flock($stream, LOCK_SH);
        while (($record = fgetcsv($stream)) !== false) {
            if ($record[0] == $id) {
                $result = array_combine($this->columns, $record);
            }
        }
        flock($stream, LOCK_UN);
        fclose($stream);
        return $result;
    }

    public function insert(array $newRecord)
    {
        $stream = fopen($this->filename, 'a');
        flock($stream, LOCK_EX);
        fputcsv($stream, $newRecord);
        flock($stream, LOCK_UN);
        fclose($stream);
    }

    public function update($newRecord, $digest)
    {
        $ok = true;
        $stream = fopen($this->filename, 'r+');
        flock($stream, LOCK_EX);
        $temp = fopen('php://temp', 'c+');
        while (($record = fgetcsv($stream)) !== false) {
            if ($record[0] != $newRecord['id']) {
                fputcsv($temp, $record);
            } else {
                if ($digest == $this->digest($record)) {
                    fputcsv($temp, $newRecord);
                } else {
                    $ok = false;
                    break;
                }
            }
        }
        if ($ok) {
            rewind($stream);
            rewind($temp);
            stream_copy_to_stream($temp, $stream);
        }
        fclose($temp);
        flock($stream, LOCK_UN);
        if ($ok) {
            ftruncate($stream, ftell($stream));
        }
        fclose($stream);
        return $ok;
    }

    public function delete($id, $digest)
    {
        $ok = true;
        $stream = fopen($this->filename, 'r+');
        flock($stream, LOCK_EX);
        $temp = fopen('php://temp', 'c+');
        while (($record = fgetcsv($stream)) !== false) {
            if ($record[0] != $id) {
                fputcsv($temp, $record);
            } else {
                if ($digest != $this->digest($record)) {
                    var_dump($record);
                    $ok = false;
                    break;
                }
            }
        }
        if ($ok) {
            rewind($stream);
            rewind($temp);
            stream_copy_to_stream($temp, $stream);
        }
        fclose($temp);
        flock($stream, LOCK_UN);
        if ($ok) {
            ftruncate($stream, ftell($stream));
        }
        fclose($stream);
        return $ok;
    }

    private function digest(array $record)
    {
        return md5(serialize(array_values($record)));
    }
}
