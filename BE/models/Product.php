<?php
require_once './config/Database.php';

$connection = Database::getInstance()->getConnection();

class Product
{
    public function get($queryParams, $allowedKeys = [], $select = [])
    {
        global $connection;

        if (!empty($allowedKeys)) {
            $queryParams = array_intersect_key($queryParams, array_flip($allowedKeys));
        }

        $selectClause = empty($select) ? '*' : implode(', ', $select);

        $conditions = [];
        $orderClause="";
        foreach ($queryParams as $key => $value) {
            switch ($key):
                case 'product_name':
                    $conditions[] = "Products.product_name LIKE '%$value%'";
                    break;
                case 'max_price':
                    $conditions[] = "Products.price <= $value";
                    break;
                case 'min_price':
                    $conditions[] = "Products.price >= $value";
                    break;
                case 'category':
                    $conditions[] = "Products.category='$value'";
                    break;
                case 'order_by':
                    $lowercaseValue = strtolower($value);
                    if($lowercaseValue!="asc" && $lowercaseValue!="desc")
                    {
                     $orderClause =  "ORDER BY Products.price ASC";
                    }
                    else{
                     $orderClause =  "ORDER BY Products.price $value";
                    }
                    break;
                default:
                    $conditions[] = "Products.$key=$value";
            endswitch;
        }
        
        //get all the product base on condition and include thumbnail and sizes
        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $query = "  SELECT Products.*,
                        GROUP_CONCAT(DISTINCT Thumbnails.thumbnail) AS combined_thumbnails,
                        GROUP_CONCAT(DISTINCT Sizes.size) AS combined_sizes
                    FROM Products 
                    LEFT JOIN Thumbnails ON Products.product_id = Thumbnails.product_id
                    LEFT JOIN Sizes ON Products.product_id = Sizes.product_id
                    $whereClause
                    GROUP BY Products.product_id
                    $orderClause;";
        try {
            $result = $connection->prepare($query);

            $result->execute();

            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in PRODUCT::get: " . $e->getMessage();
        }
    }
    // getEasy is used to get a product by product_id
    public function getEasy($product_id)
    {
        global $connection;
        if (
            empty($product_id)
        ) {
            http_response_code(400);
            echo json_encode(["message" => "Missing product_id"]);
            return;
        }
        $query = "SELECT * FROM Products WHERE product_id=$product_id";
        
        try{
            $result = $connection->prepare($query);
            $result->execute();
            return $result;
        }catch (PDOException $e) {
            echo "Unknown error in PRODUCTS::create: " . $e->getMessage();
        }
    }
    public function create($data, $allowedKeys = [])
    {
        global $connection;
        if (!empty($allowedKeys)) {
            $data = array_intersect_key($data, array_flip($allowedKeys));
        }
        
        $thumbnail = $data['thumbnail'];
        $size = $data['size'];
        //remove thumbnail and size form data
        unset($data['thumbnail']);
        unset($data['size']);
        
        $keys = array_keys($data);
        $values = array_values($data);
        $query = "INSERT INTO Products (" . implode(", ", $keys) . ") VALUES ('" . implode("', '", $values) . "')";
        try {
            $result = $connection->prepare($query);
            $result->execute();
            $lastInsertedId = $connection->lastInsertId();
            //Store the thumbnail
            foreach ($thumbnail as $value) {
            $query_thumbnail= "INSERT INTO Thumbnails (product_id,thumbnail) VALUES (" . $lastInsertedId . ",'" . $value . "')" ;       // dont forget that thumnail is string so must add ' '

             $a = $connection->prepare($query_thumbnail);
             $a->execute();
            }
            
            // Store the size
            foreach ($size as $value) {
                $query_size= "INSERT INTO Sizes (product_id,size) VALUES (" . $lastInsertedId . "," . $value . ")" ;
                 $b = $connection->prepare($query_size);
                 $b->execute();
                }
            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in PRODUCTS::create: " . $e->getMessage();
        }
    }

    public function update($product_id, $data, $allowedKeys = [])
    {
        global $connection;

        if (!empty($allowedKeys)) {
            $data = array_intersect_key($data, array_flip($allowedKeys));
        }
        
        $thumbnail = $data['thumbnail'];
        $size = $data['size'];
        //remove thumbnail and size form data
        unset($data['thumbnail']);
        unset($data['size']);
        
        foreach ($data as $key => $value) {
            $updates[] = "$key='$value'";
        }
        
        $query = "UPDATE Products SET " . implode(", ", $updates) . " WHERE product_id=$product_id";
        

        try {
            $result = $connection->prepare($query);

            $result->execute();
            
            //delete the thumbnail
            $query_thumbnail_delete= "DELETE FROM Thumbnails WHERE product_id=$product_id";     // dont forget that thumnail is string so must add ' '
    
            $delete_thumbnail = $connection->prepare($query_thumbnail_delete);
            $delete_thumbnail->execute();

            //update the thumbnail     
            foreach ($thumbnail as $value) {
                $query_thumbnail_update= "INSERT INTO Thumbnails (product_id,thumbnail) VALUES ($product_id,'$value')" ;       // dont forget that thumnail is string so must add ' '
    
                 $update_thumbnail = $connection->prepare($query_thumbnail_update);
                 $update_thumbnail->execute();
                }
            // //delete the size
            $query_size_delete= "DELETE FROM Sizes WHERE product_id='$product_id'";     // dont forget that thumnail is string so must add ' '
    
            $delete_size = $connection->prepare($query_size_delete);
            $delete_size->execute();

            //update the size     
            foreach ($size as $value) {
                $query_size_update= "INSERT INTO Sizes (product_id,size) VALUES ($product_id,$value)" ;
                
                $update_size = $connection->prepare($query_size_update);
                $update_size->execute();
                }
                // echo "asdas";
                    
            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in PRODUCT::update: " . $e->getMessage();
        }
    }
    //DELETE Product, Size, Thumbnail ,Order_detail
    public function delete($product_id)
    {
        global $connection;

        $query = "DELETE FROM Products WHERE product_id='$product_id'";
        $query_size_delete= "DELETE FROM Sizes WHERE product_id='$product_id'"; 
        $query_thumbnail_delete= "DELETE FROM Thumbnails WHERE product_id=$product_id";     
        $query_order_details_delete= "DELETE FROM Order_Details WHERE product_id=$product_id";     
        try {
            //delete shoes
            $result = $connection->prepare($query);
            $result->execute();
            //delete thumbnail
            $delete_thumbnail = $connection->prepare($query_thumbnail_delete);
            $delete_thumbnail->execute();
            //delete size
            $delete_size = $connection->prepare($query_size_delete);
            $delete_size->execute();
             //delete order_detail
             $delete_order_detail = $connection->prepare($query_order_details_delete);
             $delete_order_detail->execute();
            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in PRODUCT::delete: " . $e->getMessage();
        }
    }
}