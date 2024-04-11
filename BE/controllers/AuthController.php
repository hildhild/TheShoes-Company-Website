<?php

require_once './models/User.php';
// require_once './models/UserInfo.php';

use Firebase\JWT\JWT;
            // || isset($data['phone']) && strlen($data['phone']) > 10
            // $image_url = $data['image_url'] ?? null;
class AuthController
{

    public function register($param, $data)
    {
        // Checking body data
        if (!isset($data['user_name']) || !isset($data['password']) || !isset($data['email'])) {
            http_response_code(400);
            echo json_encode(["message" => "Missing email, password, or name "]);
            return;
        }
        // Validate the data
        $user_name = $data['user_name'];
        $email = $data['email'];
        $password = $data['password'];
        $confirm_password = $data['confirm_password'];

        if (
            filter_var($email, FILTER_VALIDATE_EMAIL) === false
            || strlen($password) < 6
        ) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid email or password"]);
            return;
        }
        if($password != $confirm_password)
        {
            http_response_code(400);
            echo json_encode(["message" => "Password and Confirmed Passwored are not matched"]);
            return;
        }

        try {   
            $user = new User();

            $result = $user->get(['email' => $email], ['email'], ['user_id', 'email']);
            // Check if email already exists
            if ($result->rowCount() > 0) {
                http_response_code(400);
                echo json_encode(["message" => "Email already exists"]);
                die();
            }
            // Hash password
            $password = password_hash($password, PASSWORD_BCRYPT);

            // Create user, then associated user_info
            $user->create(['email' => $email, 'password' => $password,'role'=>'CUSTOMER','user_name'=>$user_name]);
            $newUserId = $user->get(['email' => $email], ['email'], ['user_id', 'role'])
                ->fetch(PDO::FETCH_ASSOC);
           
            // Create JWT
            $row = [
                'id' => $newUserId['user_id'],
                'email' => $email,
                'role' => $newUserId['role'],
            ];
            $jwt = JWT::encode($row, $_ENV['SECRECT_KEY'], 'HS256');


            http_response_code(200);
            echo json_encode(["message" => "User created successfully", "token" => $jwt, "data" => $row]);
        } catch (PDOException $e) {
            echo "Unknown error in AuthController::register: " . $e->getMessage();
            die();
        }
    }
    /////////////////////////////////////////////////////////////////////////////////////
    // Login
    /////////////////////////////////////////////////////////////////////////////////////
    public function login($param, $data)
    {
        // Checking body data
        if (!isset($data['password']) || !isset($data['email'])) {
            http_response_code(400);
            echo json_encode(["message" => "Missing email or password"]);
            return;
        }
        // Validate the data
        $email = $data['email'];
        $password = $data['password'];

        if (
            filter_var($email, FILTER_VALIDATE_EMAIL) === false
            || strlen($password) < 6
        ) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid email or password"]);
            return;
        }

        try {
            $user = new User();

            $result = $user->get(['email' => $email], ['email'], ['user_id', 'user_name' , 'email', 'password', 'role']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "Account does not exist"]);
                die();
            }
            $row = $result->fetch(PDO::FETCH_ASSOC);        //fetch để lấy array data dạng key - value

            // Check password
            if (!password_verify($password, $row['password'])) {        // php function : password_verify
                http_response_code(400);
                echo json_encode(["message" => "Wrong password"]);
                die();
            }

            // Attached data for client side
            

            // Create JWT
            unset($row['password']);
            $jwt = JWT::encode($row, $_ENV['SECRECT_KEY'], 'HS256');

            http_response_code(200);
            echo json_encode(["message" => "User login successfully", "token" => $jwt, "data" => $row]);
        } catch (PDOException $e) {
            echo "Unknown error in AuthController::register: " . $e->getMessage();
            die();
        }
    }
    public function test($param, $data)
{
    echo "TEST";
}

    /////////////////////////////////////////////////////////////////////////////////////
    // Change Profile
    /////////////////////////////////////////////////////////////////////////////////////
  
}