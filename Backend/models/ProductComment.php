<?php

require_once './config/Database.php';

$connection = Database::getInstance()->getConnection();

class ProductComment
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

        $query = "SELECT $selectClause FROM Product_Comments $whereClause ORDER BY Product_Comments.updated_at DESC";

        try {

            $result = $connection->query($query);
            return $result;

        } catch (PDOException $e) {

            echo "Unknown error in Product_Comments::get: " . $e->getMessage();
            
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
        
        $query = "INSERT INTO Product_Comments ($columns) VALUES ($values)";
        
        try {

            $result = $connection->query($query);
            return $result;

        } catch (PDOException $e) {

            echo "Unknown error in Product_Comments::create: " . $e->getMessage();
        }
    }

     public function delete($comment_id)
    {
        global $connection;

        $query = "DELETE FROM Product_Comments WHERE comment_id = '$comment_id'";
   
        try {

            $result = $connection->query($query);
            return $result;

        } catch (PDOException $e) {

            echo "Unknown error in Product_Comments::delete: " . $e->getMessage();

        }
    }
}