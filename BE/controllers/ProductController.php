<?php

require_once './models/Product.php';
require_once './models/ProductComment.php';
// require_once './models/UserInfo.php';
// require_once './models/Category.php';
// require_once './models/ProductRating.php';
// require_once './models/ProductCategory.php';

class ProductController
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
            foreach ($rows as &$row) {
                $thumbnails = explode(',', $row['combined_thumbnails']);
                $sizes = explode(',', $row['combined_sizes']);
            
                // Add thumbnails and sizes to their respective arrays
                $row["thumbnails"] = $thumbnails;
                $row["sizes"] = $sizes;
                //delete fields
                unset($row['combined_thumbnails']);
                unset($row['combined_sizes']);
            }
            

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
            // $productCategory = new ProductCategory();
            // $productRating = new ProductRating();
            $productComment = new ProductComment();

            // Get product
            $result = $product->get(['product_id' => $param['id']], ['product_id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "Product does not exist"]);
                die();
            }
            $row = $result->fetch(PDO::FETCH_ASSOC);
            //explode the strings to array for easier get from FE
           { $thumbnails = explode(',', $row['combined_thumbnails']);
                $sizes = explode(',', $row['combined_sizes']);
            
                // Add thumbnails and sizes to their respective arrays
                $row["thumbnails"] = $thumbnails;
                $row["sizes"] = $sizes;
                //delete fields
                unset($row['combined_thumbnails']);
                unset($row['combined_sizes']);
           }
            // Get product category
            // $result = $productCategory->get(['product_id' => $param['id']], ['product_id'], ['CATEGORY.id', 'CATEGORY.name']);
            // $row['categories'] = $result->fetchAll(PDO::FETCH_ASSOC);

            // Get product comment
            $result = $productComment->get(['product_id' => $param['id']], ['product_id']);
            $row['comments'] = $result->fetchAll(PDO::FETCH_ASSOC);

            // Get rating number
            // $result = $productRating->get(['product_id' => $param['id']], ['product_id'], ['AVG(rating) as rating_average', 'COUNT(rating) as rating_count']);
            // $rating = $result->fetch(PDO::FETCH_ASSOC);
            // $row['rating_average'] = $rating['rating_average'];
            // $row['rating_count'] = $rating['rating_count'];

            http_response_code(200);
            echo json_encode(["message" => "Single Product fetched", "data" => $row]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::getSingleProduct: " . $e->getMessage();
            die();
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // Create Product
    /////////////////////////////////////////////////////////////////////////////////////
    public function addProduct($param, $data)
    {
        // Checking body data
        if (
            !isset($data['product_name'])
            || !isset($data['price'])
            || !isset($data['description'])
            || !isset($data['category'])
            || !isset($data['size'])    
            || !isset($data['quantity'])
            || !isset($data['color'])
            || !isset($data['thumbnail'])
        ) {
            http_response_code(400);
            echo json_encode(["message" => "Missing name, desciption, price, quantity or at least 1 thumbnail"]);
            return;
        }
        try {
            $product = new Product();

            // Create product
            $result = $product->create(
                $data,
                ['product_name', 'description', 'size', 'price', 'quantity', 'color','thumbnail','category','discount']
            );

            http_response_code(200);
            echo json_encode(["message" => "Product created successfully"]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::addProduct: " . $e->getMessage();
            die();
        }
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
            $result = $product->get(['product_id' => $param['product_id']], ['product_id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "Product does not exist"]);
                die();
            }

            // Update product
            $result = $product->update(
                $param['product_id'],
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
            $result = $product->getEasy($data['product_id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "Product does not exist"]);
                die();
            }
            $row = $result->fetch(PDO::FETCH_ASSOC);
            // Delete product
            $product->delete($data['product_id']);
            //Not yet handle delete comment
            http_response_code(200);
            echo json_encode(["message" => "Product deleted successfully"]);
        } catch (PDOException $e) {
            echo "Unknown error in ProductController::deleteProduct: " . $e->getMessage();
            die();
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // Comment Product   - Minh Hieu
    /////////////////////////////////////////////////////////////////////////////////////
    public function commentProduct($param, $data)
    {
        if (!isset($data['user_id']) || !isset($data['content']) || !isset($data['title']) || !isset($data['user_name']))
        {
            http_response_code(400);
            echo json_encode(["message" => "Missing user_id, content, title, image_url or name"]);
            return;
        }

        // Check if product exist
        $product = new Product();
        $result = $product->getEasy($param['product_id']);
        if ($result->rowCount() == 0) {
            http_response_code(400);
            echo json_encode(["message" => "Product does not exist"]);
            die();
        }
 
        // add product_id into data
        $data['product_id'] = $param['product_id'];

        $allowedKeys = ['avatar_url' , 'title', 'content' , 'user_name' , 'user_id' , 'product_id'];

        try {

            $ProductComment = new ProductComment();
            $result = $ProductComment->create(
                $data,
                $allowedKeys
            );
            
            http_response_code(200);
            echo json_encode(["message" => "Comment for Product created successfully"]);

        } catch (PDOException $e) {

            echo "Unknown error in (comment for product) NewsController:: commentProduct: " . $e->getMessage();
            die();
            
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // Delete Comment of Product  - Minh Hieu
    /////////////////////////////////////////////////////////////////////////////////////
     public function deleteCommentProduct($param, $data)
    { 
       
        $ProductComment = new ProductComment();
        $comment_id = $param["comment_id"];
 
        $result = $ProductComment->get(['comment_id' => $comment_id], ['comment_id'], ['comment_id']);
        if ($result->rowCount() == 0) {

            http_response_code(400);
            echo json_encode(["message" => "Product_Comment does not exist"]);
            die();

        }

        try{
            
            $ProductComment->delete($comment_id);
            http_response_code(200);
            echo json_encode(["message" => "Product_Comment deleted successfully"]);

        } catch (PDOException $e) {
     
            echo "Unknown error in ProductController::deleteCommentProduct : " . $e->getMessage();
            die();

        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // Rate Product
    /////////////////////////////////////////////////////////////////////////////////////
    // public function rateProduct($param, $data)
    // {
    //     // Checking body data
    //     if (
    //         !isset($data['rating'])
    //     ) {
    //         http_response_code(400);
    //         echo json_encode(["message" => "Missing stars"]);
    //         return;
    //     }

    //     try {
    //         $product = new Product();
    //         $productRating = new ProductRating();
    //         $userInfo = new UserInfo();

    //         // Check if product exist
    //         $result = $product->get(['id' => $param['id']], ['id'], ['id']);
    //         if ($result->rowCount() == 0) {
    //             http_response_code(400);
    //             echo json_encode(["message" => "Product does not exist"]);
    //             die();
    //         }

    //         // Check if user exist
    //         $result = $userInfo->get(['id' => $param['user']['id']], ['id'], ['id']);
    //         if ($result->rowCount() == 0) {
    //             http_response_code(400);
    //             echo json_encode(["message" => "User does not exist"]);
    //             die();
    //         }

    //         // Check if user already rated
    //         $result = $productRating->get(['product_id' => $param['id'], 'user_id' => $param['user']['id']], ['product_id', 'user_id']);
    //         if ($result->rowCount() != 0) {
    //             http_response_code(400);
    //             echo json_encode(["message" => "User already rated this product"]);
    //             die();
    //         }

    //         $data['product_id'] = $param['id'];
    //         $data['user_id'] = $param['user']['id'];


    //         // Create product comment
    //         $productRating->create(
    //             $data,
    //             ['product_id', 'user_id', 'rating']
    //         );

    //         http_response_code(200);
    //         echo json_encode(["message" => "Product rating created successfully"]);
    //     } catch (PDOException $e) {
    //         echo "Unknown error in ProductController::commentProduct: " . $e->getMessage();
    //         die();
    //     }
    // }

    /////////////////////////////////////////////////////////////////////////////////////
    // Add Product Category
    /////////////////////////////////////////////////////////////////////////////////////
    // public function addProductCategory($param, $data)
    // {
    //     // Checking body data
    //     if (
    //         !isset($data['category_id'])
    //     ) {
    //         http_response_code(400);
    //         echo json_encode(["message" => "Missing category_id"]);
    //         return;
    //     }

    //     try {
    //         $product = new Product();
    //         $category = new Category();
    //         $productCategory = new ProductCategory();

    //         // Check if product exist
    //         $result = $product->get(['id' => $param['id']], ['id'], ['id']);
    //         if ($result->rowCount() == 0) {
    //             http_response_code(400);
    //             echo json_encode(["message" => "Product does not exist"]);
    //             die();
    //         }

    //         // Check if category exist
    //         $result = $category->get(['id' => $data['category_id']], ['id'], ['id']);
    //         if ($result->rowCount() == 0) {
    //             http_response_code(400);
    //             echo json_encode(["message" => "Category does not exist"]);
    //             die();
    //         }

    //         // Check if product already have this category
    //         $result = $productCategory->get(['product_id' => $param['id'], 'category_id' => $data['category_id']], ['product_id', 'category_id']);
    //         if ($result->rowCount() != 0) {
    //             http_response_code(400);
    //             echo json_encode(["message" => "Product already have this category"]);
    //             die();
    //         }

    //         $data['product_id'] = $param['id'];

    //         // Create product category
    //         $productCategory->create(
    //             $data,
    //             ['product_id', 'category_id']
    //         );

    //         http_response_code(200);
    //         echo json_encode(["message" => "Product category created successfully"]);
    //     } catch (PDOException $e) {
    //         echo "Unknown error in ProductController::addProductCategory: " . $e->getMessage();
    //         die();
    //     }
    // }

    /////////////////////////////////////////////////////////////////////////////////////
    // Delete Product Category
    /////////////////////////////////////////////////////////////////////////////////////
    // public function deleteProductCategory($param, $data)
    // {
    //     // Checking body data
    //     if (
    //         !isset($data['category_id'])
    //     ) {
    //         http_response_code(400);
    //         echo json_encode(["message" => "Missing category_id"]);
    //         return;
    //     }

    //     try {
    //         $product = new Product();
    //         $category = new Category();
    //         $productCategory = new ProductCategory();

    //         // Check if product exist
    //         $result = $product->get(['id' => $param['id']], ['id'], ['id']);
    //         if ($result->rowCount() == 0) {
    //             http_response_code(400);
    //             echo json_encode(["message" => "Product does not exist"]);
    //             die();
    //         }

    //         // Check if category exist
    //         $result = $category->get(['id' => $data['category_id']], ['id'], ['id']);
    //         if ($result->rowCount() == 0) {
    //             http_response_code(400);
    //             echo json_encode(["message" => "Category does not exist"]);
    //             die();
    //         }

    //         $data['product_id'] = $param['id'];

    //         // Delete product category
    //         $productCategory->delete(
    //             $data['product_id'],
    //             $data['category_id']
    //         );

    //         http_response_code(200);
    //         echo json_encode(["message" => "Product category deleted successfully"]);
    //     } catch (PDOException $e) {
    //         echo "Unknown error in ProductController::deleteProductCategory: " . $e->getMessage();
    //         die();
    //     }
    // }
}