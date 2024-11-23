<?php

class MysqlAdapter {
    private $config = [];
    protected $Link;
    private $result;

    public function __construct($config) {
        if (count($config) !== 4) {
            throw new InvalidArgumentException("Invalid configuration array. Expected 4 elements: host, username, password, database.");
        }
        $this->config = $config;
    }

    protected function connect() {
        if ($this->Link === null) {
            $this->Link = new mysqli($this->config[0], $this->config[1], $this->config[2], $this->config[3]);

            if (!$this->Link) {
                throw new RuntimeException("Connection failed: " . $this->Link->connect_error);
            }
        }
        return $this->Link;
    }

    private function executeQuery($sql) {   
        if (empty($sql) || !is_string($sql)) {
            throw new InvalidArgumentException("SQL query is empty.");
        }
        //lazy connection 
        $this->connect();       
        $this->result = $this->Link->query($sql);

        if (!$this->result) {
            throw new RuntimeException("Error executing query: " . $sql . " - " . $this->Link->error);
        }

        return $this->result;
    }

    public function select($table, $columns = '*', $conditions = '1', $orderBy = '', $limit = null, $offset = null) {
        
        $sql = "SELECT $columns FROM $table WHERE $conditions";

        if (!empty($orderBy)) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if (!empty($limit)) {
            $sql .= " LIMIT $limit";
            
            if (!empty($offset)) {
                $sql .= " OFFSET $offset";
            }
        }
        
        return $this->executeQuery($sql);
    }

    public function insert($table, array $data) {
        $this->connect();
        $columns = implode(", ", array_keys($data));
        $escaped_values = array_map([$this->Link, 'real_escape_string'], array_values($data));
        $values = implode("', '", $escaped_values);
        $sql = "INSERT INTO $table ($columns) VALUES ('$values')";
        return $this->executeQuery($sql);
    }

    public function update($table, $data, $conditions) {
        $this->connect();
        $set = [];
        foreach ($data as $column => $value) {
            $escaped_value = $this->Link->real_escape_string($value);
            $set[] = "$column = '$escaped_value'";
        }
        $setString = implode(", ", $set);
        $sql = "UPDATE $table SET $setString WHERE $conditions";
        return $this->executeQuery($sql);
    }

    public function delete($table, $conditions) {
        $sql = "DELETE FROM $table WHERE $conditions";
        return $this->executeQuery($sql);
    }
    public function fetch() {
        if ($this->result instanceof mysqli_result) {
            return $this->result->fetch_assoc();
        }
        return False;
    }

    public function fetchAll() {
        if ($this->result instanceof mysqli_result) {
            return $this->result->fetch_all(MYSQLI_ASSOC);
        }
        return False;
    }
    public function rowCount() {
        if ($this->result instanceof mysqli_result) {
            return $this->result->num_rows;
        }
        return 0;
    }
    public function __destruct() {
        if ($this->Link) {
            $this->Link->close();
        }
    }
}