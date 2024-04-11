<?php

require_once './config/Database.php';

$connection = Database::getInstance()->getConnection();

class News
{
    public function get($queryParams, $allowedKeys = [], $select = [])
    {
        global $connection;
        if (!empty($allowedKeys)) {
            $queryParams = array_intersect_key($queryParams, array_flip($allowedKeys));
        }  

        $selectClause = empty($select) ? '*' : implode(', ', $select);
     
        $conditions = [];
        foreach ($queryParams as $key => $value) {
            $conditions[] = "$key = '$value'";
        }
        
        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $query = "SELECT $selectClause FROM News $whereClause ORDER BY News.updated_at DESC";

        try {

            $result = $connection->query($query);
            return $result;

        } catch (PDOException $e) {

            echo "Unknown error in News::get: " . $e->getMessage();
            
        }
    }

    public function create($data, $allowedKeys = [])
    {
        global $connection;
        if (!empty($allowedKeys)) {
            $data = array_intersect_key($data, array_flip($allowedKeys));
        }

        $columns = implode(', ', array_keys($data));
        $values = "'" . implode("', '", $data) . "'";
        
        $query = "INSERT INTO News ($columns) VALUES ($values)";
        // echo "create News query : " . $query;
        
        try {

            $result = $connection->query($query);
            return $result;

        } catch (PDOException $e) {

            echo "Unknown error in NewsS::create: " . $e->getMessage();
        }
    }

  public function update($id, $data, $allowedKeys = [])
    {
        global $connection;
        if (!empty($allowedKeys)) {
            $data = array_intersect_key($data, array_flip($allowedKeys));
        }

        $updates = [];
        foreach ($data as $key => $value) {
            $updates[] = "$key = '$value'";
        }

        $query = "UPDATE News SET " . implode(", ", $updates) . " WHERE news_id = '$id'";
       
        try {
            
            $result = $connection->query($query);
            return $result;

        } catch (PDOException $e) {

            echo "Unknown error in News::update: " . $e->getMessage();

        }
    }

     
    public function delete($id)
    {
        global $connection;

        $query = "DELETE FROM News WHERE news_id = '$id'";
       
        try {

            $result = $connection->query($query);
            return $result;

        } catch (PDOException $e) {

            echo "Unknown error in News::delete: " . $e->getMessage();

        }
    }

}