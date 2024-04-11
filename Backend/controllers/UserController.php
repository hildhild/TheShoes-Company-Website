<?php

require_once './models/User.php';
require_once './models/Order.php';

// require_once './models/UserInfo.php';

// Controller for Admin only, no need for more detail queries
class UserController
{
    /////////////////////////////////////////////////////////////////////////////////////
    // Get Users
    /////////////////////////////////////////////////////////////////////////////////////
    public function getUsers($param, $data)
    {
        try {
            $user = new User();
            $result = $user->getAllUsers();
            $result = $result->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode(['message' => 'User list fetched', 'data' => $result]);
        } catch (PDOException $e) {
            echo "Unknown error in UserController::getUsers: " . $e->getMessage();
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // Get User Detail
    /////////////////////////////////////////////////////////////////////////////////////
    public function getSingleUser($param, $data)
    {
        // Filter priviledge
        if ($param['user']['role'] != 'ADMIN' && $param['user']['id'] != $param['id']) {
            http_response_code(403);
            echo json_encode(['message' => 'Non-admin user can only get their own detail']);
            return;
        }

        try {
            $user = new User();
            $userInfo = new UserInfo();

            $result = $user->get(['id' => $param['id']], ['id'], ['id', 'role', 'email']);
            if ($result->rowCount() == 0) {
                http_response_code(404);
                echo json_encode(['message' => 'User not found']);
                return;
            }
            $row = $result->fetch(PDO::FETCH_ASSOC);

            $result = $userInfo->get(
                ['id' => $row['id']],
                [],
                ['name', 'image_url', 'birth_date', 'phone', 'address']
            );
            $row['info'] = $result->fetch(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode(['message' => 'User detail fetched', 'data' => $row]);
        } catch (PDOException $e) {
            echo "Unknown error in UserController::getSingleUser: " . $e->getMessage();
        }
    }
    public function getBuyingHistory($param, $data)
    {
        // Filter priviledge
        // if ($param['user']['role'] != 'ADMIN' && $param['user']['id'] != $param['id']) {
        //     http_response_code(403);
        //     echo json_encode(['message' => 'Non-admin user can only get their own detail']);
        //     return;
        // }

        try {
            $user = new user();
            $result = $user->get_buying_history($param['user']['user_id']);
            http_response_code(200);
            echo json_encode(['message' => 'History Order Fetched Successfully', 'data' => $result]);
        } catch (PDOException $e) {
            echo "Unknown error in UserController::getSingleUser: " . $e->getMessage();
        }
    }
    public function deleteUser($param, $data)
    {
        try {
            $user = new user();
            $result = $user->delete($param['id']);
            http_response_code(200);
            echo json_encode(['message' => 'Delete User Information Successfully']);
        } catch (PDOException $e) {
            echo "Unknown error in UserController::getSingleUser: " . $e->getMessage();
        }
    }
    public function getUserInformation($param, $data)
    {
        try {
            $user = new user();
            $result = $user->get_user_information($param['user']['user_id']);
            http_response_code(200);
            echo json_encode(['message' => 'Get User Information Successfully', 'data' => $result]);
        } catch (PDOException $e) {
            echo "Unknown error in UserController::getSingleUser: " . $e->getMessage();
        }
    }
     /////////////////////////////////////////////////////////////////////////////////////
    // Update User Detail
    /////////////////////////////////////////////////////////////////////////////////////
    public function updateUserInformation($param, $data)
    {
        try {
            $user = new user();
            $result = $user->update($param['user']['user_id'],$data);
         

            http_response_code(200);
            echo json_encode(["message" => "The user information updated successfully"]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::updateProduct: " . $e->getMessage();
            die();
        }
    }
}