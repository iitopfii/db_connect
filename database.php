<?php

class Database
{

    protected $host;
    protected $user;
    protected $pass;
    protected $dbname;
    public $mysqli;

    function __construct($host, $user, $pass, $dbname)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbname = $dbname;
        $this->mysqli = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        $this->mysqli->query("SET NAMES UTF8");
    }


    public function execute($cmd, $res = 0)
    {
        $query = $this->mysqli->query("$cmd");
        if ($this->mysqli->error) {
            $this->resError("Error message: " . $this->mysqli->error);
        } else {
            if ($res) {
                $data = [];
                while ($res = $query->fetch_object()) {
                    array_push($data, $res);
                }
                $this->resSuccess($data);
            } else {
                $this->resSuccess('Query Success!');
            }
        }
    }

    public function select(array $attributes, $table, $where = '')
    {
        $attr = "";
        $f = true;
        if ($attributes[0] == '*') {
            $attr = "*";
        } else {
            foreach ($attributes as $key) {
                if ($f) {
                    $attr .= "$key";
                    $f = false;
                } else {
                    $attr .= ",$key";
                }
            }
        }
        if ($where) {
            $where = "WHERE $where";
        }
        $sql = "SELECT $attr FROM $table  $where";
        $this->execute($sql, 1);
    }

    public function insert($attributes, $table)
    {
        $attr = "";
        $val = "";
        $f = true;
        foreach ($attributes as $key => $value) {
            if ($f) {
                $attr .= "$key";
                $val .= "'$value'";
                $f = false;
            } else {
                $attr .= ",$key";
                $val .= ",'$value'";
            }
        }
        $sql = "INSERT INTO $table($attr) VALUES($val)";
        $this->execute($sql);
    }

    public function update($attributes, $table, $where = '')
    {

        $attr = "";
        $val = "";
        $f = true;
        foreach ($attributes as $key => $value) {
            if ($f) {
                $val .= "$key ='$value'";
                $f = false;
            } else {
                $val .= ",$key ='$value'";
            }
        }

        if ($where) {
            $where = "WHERE $where";
        }
        $sql = "UPDATE $table SET $val $where";
        $this->execute($sql);
    }

    public function delete($table, $where = '')
    {
        if ($where) {
            $where = "WHERE $where";
        }
        $sql = "DELETE FROM $table $where";
        $this->execute($sql);
    }


    protected function resSuccess($res)
    {
        echo json_encode(['data' => $res, 'error' => 0], JSON_UNESCAPED_UNICODE);
    }

    protected function resError($message = 'Bad request')
    {
        echo json_encode(['data' => $message, 'error' => 1], JSON_UNESCAPED_UNICODE);
    }
}
