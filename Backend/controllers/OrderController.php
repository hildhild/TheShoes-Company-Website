<?php

require_once './models/Product.php';
require_once './models/Order.php';

// require_once './models/UserInfo.php';
// require_once './models/Category.php';
// require_once './models/ProductRating.php';
// require_once './models/ProductComment.php';
// require_once './models/ProductCategory.php';

class OrderController
{
    /////////////////////////////////////////////////////////////////////////////////////
    // Get Products
    /////////////////////////////////////////////////////////////////////////////////////
    public function getProducts($param, $data)
    {
        $queryParams = array();
        if (isset($_SERVER['QUERY_STRING'])) {
            $queryString = $_SERVER['QUERY_STRING'];
            parse_str($queryString, $queryParams);
        }
        try {
            $product = new Product();

            // Get product by name if exist
            $result = $product->get(
                $queryParams,
                ['product_name', 'max_price', 'min_price', 'category','order_by'],
                []
            );

            $rows = $result->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode(["message" => "Product List fetched", "data" => $rows]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::getProducts: " . $e->getMessage();
            die();
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // Get Product Detail
    /////////////////////////////////////////////////////////////////////////////////////
    public function getSingleProduct($param, $data)
    {
        try {
            $product = new Product();
            $productCategory = new ProductCategory();
            $productRating = new ProductRating();
            $productComment = new ProductComment();

            // Get product
            $result = $product->get(['id' => $param['id']], ['id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "Product does not exist"]);
                die();
            }
            $row = $result->fetch(PDO::FETCH_ASSOC);

            // Get product category
            $result = $productCategory->get(['product_id' => $param['id']], ['product_id'], ['CATEGORY.id', 'CATEGORY.name']);
            $row['categories'] = $result->fetchAll(PDO::FETCH_ASSOC);

            // Get product comment
            $result = $productComment->get(['product_id' => $param['id']], ['product_id']);
            $row['comments'] = $result->fetchAll(PDO::FETCH_ASSOC);

            // Get rating number
            $result = $productRating->get(['product_id' => $param['id']], ['product_id'], ['AVG(rating) as rating_average', 'COUNT(rating) as rating_count']);
            $rating = $result->fetch(PDO::FETCH_ASSOC);
            $row['rating_average'] = $rating['rating_average'];
            $row['rating_count'] = $rating['rating_count'];

            http_response_code(200);
            echo json_encode(["message" => "Blog fetched", "data" => $row]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::getSingleProduct: " . $e->getMessage();
            die();
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // GET BUYING ORDER BASED ON $user_id and order_status = BUYING =>each user have different result
    /////////////////////////////////////////////////////////////////////////////////////
   
    public function get_buying_order($user_id,$createNewBuying){
        $order = new Order();

        // Get the order with buying status to process
        $result =$order->get_buying_order($user_id);
        if ($result->rowCount() == 0  && $createNewBuying==1) {
            //TODO : create new order with buying status and return the order_id
        $order_id = $order->create_buying_order($user_id);
        }else{
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
            $order_id=$rows[0]["order_id"];
        }         
       return $order_id;
    } 
      /////////////////////////////////////////////////////////////////////////////////////
    // ADD  Product to Cart : First will get the buying_order then create order_detail , if cant find , it will create
    // a new buying order 
    /////////////////////////////////////////////////////////////////////////////////////
    public function addProductToCart($param, $data)
    {
        // Checking body data
        if (
            !isset($data['product_id'])
            || !isset($data['size'])
            || !isset($data['color'])
            || !isset($data['thumbnail'])
            || !isset($data['quantity'])
            || !isset($data['price'])
            || !isset($data['product_name'])
            
        ) {
            http_response_code(400);
            echo json_encode(["message" => "Missing name, desciption, price, quantity or at least 1 thumbnail"]);
            return;
        }
        try {
            $product = new Product();

            // Check if product exist
            $result = $product->get(['product_id' => $data['product_id']], ['product_id'], ['product_id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "Product does not exist"]);
                die();
            }
            // Create product
            
            $order = new Order();
            $order_id=$this->get_buying_order($param["user"]["user_id"],1); // in same class , must use $this to call function
            $result = $order->create_order_detail($order_id,$data['product_id'],$data['size'],$data['quantity'],$data['price'],$data['product_name'],$data['thumbnail'],$data['color']);

            http_response_code(200);
            echo json_encode(["message" => "Add To  Cart Successfully"]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::addProduct: " . $e->getMessage();
            die();
        }
    }
       /////////////////////////////////////////////////////////////////////////////////////
    // GET CART with order_detail based on user__id and superTotalMoney
    /////////////////////////////////////////////////////////////////////////////////////
    public function getCart($param, $data)
    {
        try {
            $order = new Order();
            $order_id=$this->get_buying_order($param["user"]["user_id"],1);
            $cart=$order->getCart($order_id); // in same class , must use $this to call function
            http_response_code(200);
            echo json_encode(["message" => "Get Cart Successfully","data" => $cart]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::addProduct: " . $e->getMessage();
            die();
        }
    }
    /////////////////////////////////////////////////////////////////////////////////////
    // GET ORDER LIST for admin 
    /////////////////////////////////////////////////////////////////////////////////////
    public function getOrderList($param, $data)
    {
        try {
            $order = new Order();
            $cart=$order->getOrderList(); // in same class , must use $this to call function
            http_response_code(200);
            echo json_encode(["message" => "Get Order list for Admin Successfully","data" => $cart]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::addProduct: " . $e->getMessage();
            die();
        }
    }
    /////////////////////////////////////////////////////////////////////////////////////
    // Update Product
    /////////////////////////////////////////////////////////////////////////////////////
    public function editOrderStatus($param, $data)
    {
        try {
            // Check if fields exist
            $order = new order();
            if (
                !isset($data['order_id'])
                || !isset($data['order_status'])
            ) {
                http_response_code(400);
                echo json_encode(["message" => "Missing some fields"]);
                return;
            }
            // Not yet check if order_id exist

            // Update product
            $result = $order->editOrderStatus( $data["order_id"],$data["order_status"]);

            http_response_code(200);
            echo json_encode(["message" => "The order updated status successfully"]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::updateProduct: " . $e->getMessage();
            die();
        }
    }
    public function deleteProductInCart($param, $data)
    {
        // Checking body data
        if (
            !isset($data['order_detail_id'])
        ) {
            http_response_code(400);
            echo json_encode(["message" => "Missing order_detail_id"]);
            return;
        }
        try {
            // delete product in cart
            
            $order = new Order();
            $result = $order->delete_product_in_cart($data['order_detail_id']);

            http_response_code(200);
            echo json_encode(["message" => "Delete Item In Cart Successfully"]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::addProduct: " . $e->getMessage();
            die();
        }
    }
    /////////////////////////////////////////////////////////////////////////////////////
    // CREATE ORDER : First get the buying order of a customer => use get_buying_order
    // then add new fields to the existing ORDER record , remember to calculate totalmoney and total quantity
    /////////////////////////////////////////////////////////////////////////////////////
    public function create_order($param, $data){
         // Checking body data
         if (
            !isset($data['email'])
            || !isset($data['user_name'])
            || !isset($data['country'])
            || !isset($data['province'])
            || !isset($data['city'])
            || !isset($data['zip_code'])
            || !isset($data['address'])
            || !isset($data['phone_number'])
            || !isset($data['card_name'])
            || !isset($data['card_number'])
            || !isset($data['card_expiration'])
            || !isset($data['vcc'])
        ){
            http_response_code(400);
            echo json_encode(["message" => "You miss some values"]);
            return;
        }
        $order = new Order();
        //No BUYING Order has been created so you cant checkout
        $order_id=$this->get_buying_order($param["user"]["user_id"],0);
        if(empty($order_id)) 
            {
                http_response_code(404);
                echo json_encode(["message" => "You need to add shoes to your cart first",]);
                return;
            }
        $result = $order->create_order($param["user"]["user_id"],$order_id,$data['email'],$data['user_name'],$data['country'],$data['province'],$data['city'],
        $data['zip_code'],$data['address'],$data['phone_number'],$data['card_name'],$data['card_number'],$data['card_expiration'],$data['vcc']);
        http_response_code(200);
        echo json_encode(["message" => "Checkout Successfully"]);
    }
    public function create_order_details(){
        
    }
    /////////////////////////////////////////////////////////////////////////////////////
    // Update Product
    /////////////////////////////////////////////////////////////////////////////////////
    public function updateProduct($param, $data)
    {
        try {
            // Check if product exist
            $product = new Product();

            // Check if product exist
            $result = $product->get(['product_id' => $param['id']], ['product_id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "Product does not exist"]);
                die();
            }

            // Update product
            $result = $product->update(
                $param['id'],
                $data,
                ['product_name', 'description', 'size', 'price', 'quantity', 'color','thumbnail','category','discount']
            );

            http_response_code(200);
            echo json_encode(["message" => "Product updated successfully"]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::updateProduct: " . $e->getMessage();
            die();
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // Delete Product
    /////////////////////////////////////////////////////////////////////////////////////
    public function deleteProduct($param, $data)
    {
        try {
            $product = new Product();

            // Check if product exist
            $result = $product->get(['product_id' => $param['id']], ['product_id'], ['product_id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "Product does not exist"]);
                die();
            }
            // Delete product
            $product->delete($param['id']);
            //Not yet handle delete comment
            http_response_code(200);
            echo json_encode(["message" => "Product deleted successfully"]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::deleteProduct: " . $e->getMessage();
            die();
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // Comment Product
    /////////////////////////////////////////////////////////////////////////////////////
    public function commentProduct($param, $data)
    {
        // Checking body data
        if (
            !isset($data['content'])
        ) {
            http_response_code(400);
            echo json_encode(["message" => "Missing content"]);
            return;
        }

        try {
            $product = new Product();
            $productComment = new ProductComment();
            $userInfo = new UserInfo();

            // Check if product exist
            $result = $product->get(['id' => $param['id']], ['id'], ['id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "Product does not exist"]);
                die();
            }

            // Check if user exist
            $result = $userInfo->get(['id' => $param['user']['id']], ['id'], ['id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "User does not exist"]);
                die();
            }

            $data['product_id'] = $param['id'];
            $data['user_id'] = $param['user']['id'];


            // Create product comment
            $productComment->create(
                $data,
                ['product_id', 'user_id', 'content']
            );

            http_response_code(200);
            echo json_encode(["message" => "Product comment created successfully"]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::commentProduct: " . $e->getMessage();
            die();
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // Rate Product
    /////////////////////////////////////////////////////////////////////////////////////
    public function rateProduct($param, $data)
    {
        // Checking body data
        if (
            !isset($data['rating'])
        ) {
            http_response_code(400);
            echo json_encode(["message" => "Missing stars"]);
            return;
        }

        try {
            $product = new Product();
            $productRating = new ProductRating();
            $userInfo = new UserInfo();

            // Check if product exist
            $result = $product->get(['id' => $param['id']], ['id'], ['id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "Product does not exist"]);
                die();
            }

            // Check if user exist
            $result = $userInfo->get(['id' => $param['user']['id']], ['id'], ['id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "User does not exist"]);
                die();
            }

            // Check if user already rated
            $result = $productRating->get(['product_id' => $param['id'], 'user_id' => $param['user']['id']], ['product_id', 'user_id']);
            if ($result->rowCount() != 0) {
                http_response_code(400);
                echo json_encode(["message" => "User already rated this product"]);
                die();
            }

            $data['product_id'] = $param['id'];
            $data['user_id'] = $param['user']['id'];


            // Create product comment
            $productRating->create(
                $data,
                ['product_id', 'user_id', 'rating']
            );

            http_response_code(200);
            echo json_encode(["message" => "Product rating created successfully"]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::commentProduct: " . $e->getMessage();
            die();
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // Add Product Category
    /////////////////////////////////////////////////////////////////////////////////////
    public function addProductCategory($param, $data)
    {
        // Checking body data
        if (
            !isset($data['category_id'])
        ) {
            http_response_code(400);
            echo json_encode(["message" => "Missing category_id"]);
            return;
        }

        try {
            $product = new Product();
            $category = new Category();
            $productCategory = new ProductCategory();

            // Check if product exist
            $result = $product->get(['id' => $param['id']], ['id'], ['id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "Product does not exist"]);
                die();
            }

            // Check if category exist
            $result = $category->get(['id' => $data['category_id']], ['id'], ['id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "Category does not exist"]);
                die();
            }

            // Check if product already have this category
            $result = $productCategory->get(['product_id' => $param['id'], 'category_id' => $data['category_id']], ['product_id', 'category_id']);
            if ($result->rowCount() != 0) {
                http_response_code(400);
                echo json_encode(["message" => "Product already have this category"]);
                die();
            }

            $data['product_id'] = $param['id'];

            // Create product category
            $productCategory->create(
                $data,
                ['product_id', 'category_id']
            );

            http_response_code(200);
            echo json_encode(["message" => "Product category created successfully"]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::addProductCategory: " . $e->getMessage();
            die();
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // Delete Product Category
    /////////////////////////////////////////////////////////////////////////////////////
    public function deleteProductCategory($param, $data)
    {
        // Checking body data
        if (
            !isset($data['category_id'])
        ) {
            http_response_code(400);
            echo json_encode(["message" => "Missing category_id"]);
            return;
        }

        try {
            $product = new Product();
            $category = new Category();
            $productCategory = new ProductCategory();

            // Check if product exist
            $result = $product->get(['id' => $param['id']], ['id'], ['id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "Product does not exist"]);
                die();
            }

            // Check if category exist
            $result = $category->get(['id' => $data['category_id']], ['id'], ['id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "Category does not exist"]);
                die();
            }

            $data['product_id'] = $param['id'];

            // Delete product category
            $productCategory->delete(
                $data['product_id'],
                $data['category_id']
            );

            http_response_code(200);
            echo json_encode(["message" => "Product category deleted successfully"]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::deleteProductCategory: " . $e->getMessage();
            die();
        }
    }
}