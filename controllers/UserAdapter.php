<?php
require_once '../models/mysqladapter.php';
require_once '../config/db.php';

class UserAdapter extends MysqlAdapter
{
    private $table = 'users';

    public function __construct()
    {
        global $config;
        parent::__construct($config);
    }

    public function addUser(array $data)
    {
        return $this->insert($this->table, $data);
    }
    public function getUsers($orderBy = '', $limit = null, $offset = null)
    {
        $this->select($this->table, '*', '1', $orderBy, $limit, $offset);
        return $this->fetchAll();
    }
    
    public function getUser($id)
    {
        $this->select($this->table, '*', "id = " . intval($id));
        return $this->fetch();
    }

    public function updateUser($id, array $data)
    {
        return $this->update($this->table, $data, "id = " . intval($id));
    }

    public function deleteUser($id)
    {
        return $this->delete($this->table, "id = " . intval($id));
    }

    public function searchUsers($searchTerm)
    {
        $this->connect();
        $searchTerm = $this->Link->real_escape_string($searchTerm);
        $condition = "name LIKE '%$searchTerm%' OR email LIKE '%$searchTerm%'";
        return $this->select($this->table, '*', $condition);
    }

}