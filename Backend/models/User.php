<?php
require_once './config/Database.php';

$connection = Database::getInstance()->getConnection();

class User
{

    //GET history order with status != buying . 
    public function get_buying_history($user_id){
        global $connection;
        //becareful with checkout_at , it should be the current time when this code runs
        $query= "SELECT *,created_at AS checkout_at FROM Orders WHERE order_status !='buying' AND user_id=$user_id ORDER BY checkout_at DESC";
        try {
            
            $result = $connection->prepare($query);
            $result->execute();
            //GET ALL ORDERS != BUYING, lấy thằng gần nhất đang shipping trc 
            $row["order"] = $result->fetchALL(PDO::FETCH_ASSOC);
            for($i=0;$i<count($row["order"]);$i++)
            {
                $order_id=$row["order"][$i]['order_id'];
                //GET order_details in each order
                $query_order_detail="SELECT * FROM Order_Details WHERE order_id=$order_id";
                $order_detail = $connection->prepare($query_order_detail);
                $order_detail->execute();
                //assign order_details to items
                $row["order"][$i]["items"]=$order_detail->fetchALL(PDO::FETCH_ASSOC);
                //lay cai order_detail rồi tạo 1 biến array gán vào
            }
            return $row;
        } catch (PDOException $e) {
            echo "Unknown error in PRODUCT::get: " . $e->getMessage();
        }
    } 
    public function get_user_information($user_id){
        global $connection;
        $query= "SELECT * FROM Users WHERE user_id=$user_id";
        try {
            
            $result = $connection->prepare($query);
            $result->execute();
            return $result->fetchALL(PDO::FETCH_ASSOC);
           
        } catch (PDOException $e) {
            echo "Unknown error in PRODUCT::get: " . $e->getMessage();
        }
    } 
    public function get($queryParams, $allowedKeys = [], $select = [])
    {
        global $connection;
        if (!empty($allowedKeys)) {
            $queryParams = array_intersect_key($queryParams, array_flip($allowedKeys));
        }
        //array_flip : đổi vị trí của key và value
        // array_intersect_key: lấy các element trong $queryParams sao cho các key trong $queryParams tồn tại trong array_flip($allowedKeys)
        //queryParams => dùng cho where clause kiểu để phân biệt, allowedKeys chỉ cần set key giống query param, $select lấy các field nào ra 
        $selectClause = empty($select) ?  '*' : implode(', ', $select); //implode is used to create string from array
        $conditions = [];                  //declare array
        foreach ($queryParams as $key => $value) {
            $conditions[] = "$key='$value'";    //push "..." to $conditions
        }
        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $query = "SELECT $selectClause FROM Users $whereClause";
        // echo $query;
        try {
            $result = $connection->prepare($query);

            $result->execute();     
            //$result is an object(PDOStatement), if you want to access the value after get
            // you need to use its method : fetch,fetchAll
            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in USERS::get: " . $e->getMessage();
        }
    }

    public function create($data, $allowedKeys = [])
    {
        global $connection;

        if (!empty($allowedKeys)) {
            $data = array_intersect_key($data, array_flip($allowedKeys));
        }

        $keys = array_keys($data);
        $values = array_values($data);
        $query = "INSERT INTO Users (" . implode(", ", $keys) . ") VALUES ('" . implode("', '", $values) . "')";

        try {
            $result = $connection->prepare($query);

            $result->execute();

            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in USERS::create: " . $e->getMessage();
        }
    }

    public function update($id, $data, $allowedKeys = [])
    {
        global $connection;

        // if (!empty($allowedKeys)) {
        //     $data = array_intersect_key($data, array_flip($allowedKeys));
        // }

        foreach ($data as $key => $value) {
            $updates[] = "$key='$value'";
        }

        $query = "UPDATE Users SET " . implode(", ", $updates) . " WHERE user_id='$id'";

        try {
            $result = $connection->prepare($query);

            $result->execute();

            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in USERS::update: " . $e->getMessage();
        }
    }

    public function delete($id)
    {
        global $connection;
        $query = "DELETE FROM Users WHERE user_id='$id'";
        try {
            $result = $connection->prepare($query);

            $result->execute();

            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in USERS::delete: " . $e->getMessage();
        }
    }

    public function getAllUsers()
    {
        global $connection;

        $query = "SELECT *  FROM Users WHERE role!='ADMIN'";

        try {
            $result = $connection->prepare($query);

            $result->execute();

            return $result;
        } catch (PDOException $e) {
            echo "Unknown error in USERS::get: " . $e->getMessage();
        }
    }
}