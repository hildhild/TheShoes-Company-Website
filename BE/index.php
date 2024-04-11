<?php
// Header configuration, do not touch
header('Content-Type: application/json');
// Front end server address
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// Fetch method and URI from request
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Handle  CORS error
if ($httpMethod == "OPTIONS") {
    header("HTTP/1.1 200 OK");
    die();
}

// Load composer (PHP third party package manager)
require './vendor/autoload.php';

require_once './config/Database.php';

// Load .env variable
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


//Router function
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {


    // Auth Group
    $r->addGroup('/{group:auth}', function (FastRoute\RouteCollector $r) {
        $r->addRoute('POST', '/register', 'register');          // xong
        $r->addRoute('POST', '/login', 'login');                //xong
        $r->addRoute('PATCH', '/password', ['requireLogin', 'changePassword']); 
        $r->addRoute('PATCH', '/profile', ['requireLogin', 'changeProfile']);
        $r->addRoute('DELETE', '', ['requireLogin', 'deleteSelf']);
        $r->addRoute('GET', '/','hello');
    });

    // Account Group
    $r->addGroup('/{group:user}', function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/all-user', ['requireAdmin', 'getUsers']);          //xong
        $r->addRoute('GET', '/{id:\d+}', ['requireLogin', 'getSingleUser']);
        $r->addRoute('DELETE', '/{id:\d+}', ['requireAdmin', 'deleteUser']);
        $r->addRoute('GET', '/buying-history', ['requireLogin', 'getBuyingHistory']); //xong
        $r->addRoute('PUT', '/information', ['requireLogin', 'updateUserInformation']); //xong
        $r->addRoute('GET', '/information', ['requireLogin', 'getUserInformation']); //xong

    });
    $r->addGroup('/{group:order}', function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '', ['requireAdmin', 'getUsers']);
        $r->addRoute('GET', '/order-list', ['requireAdmin', 'getOrderList']);
        $r->addRoute('PUT', '/order-status', ['requireAdmin', 'editOrderStatus']);
        $r->addRoute('GET', '/{id:\d+}', ['requireLogin', 'getSingleUser']);
        $r->addRoute('POST', '' ,['requireLogin','get_buying_order']);              //xong
        $r->addRoute('POST', '/addProductToCart',['requireLogin','addProductToCart']);              //xong
        $r->addRoute('GET', '/cart', ['requireLogin', 'getCart']);                  //xong
        $r->addRoute('DELETE', '/cart', ['requireLogin', 'deleteProductInCart']);                  //xong
        $r->addRoute('POST', '/cart', ['requireLogin', 'deleteProductInCart']);                  //xong
        $r->addRoute('POST', '/checkout', ['requireLogin', 'create_order']);                  //xong

    });
    
    // Product Group
    $r->addGroup('/{group:product}', function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '', 'getProducts');  //xong , can get all or get single by name , category,order_by        
        $r->addRoute('GET', '/{id:\d+}', 'getSingleProduct');
        $r->addRoute('POST', '', ['requireAdmin', 'addProduct']); //xong
        $r->addRoute('PATCH', '/{product_id:\d+}', ['requireAdmin', 'updateProduct']);  //xong
        $r->addRoute('DELETE', '', ['requireAdmin', 'deleteProduct']); //xong
        $r->addRoute('POST', '/{product_id:\d+}/comment', ['requireLogin', 'commentProduct']);   // Done
        $r->addRoute('DELETE', '/deletecomment/{comment_id:\d+}', ['requireLogin', 'deleteCommentProduct']);   // Done
        $r->addRoute('POST', '/{id:\d+}/rate', ['requireLogin', 'rateProduct']); 
        $r->addRoute('POST', '/{id:\d+}/category', ['requireAdmin', 'addProductCategory']);
        $r->addRoute('DELETE', '/{id:\d+}/category', ['requireAdmin', 'deleteProductCategory']);
    });

    // Category Group
    $r->addGroup('/{group:category}', function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '', 'getCategories');
        $r->addRoute('POST', '', ['requireAdmin', 'addCategory']);
        $r->addRoute('DELETE', '/{id:\d+}', ['requireAdmin', 'deleteCategory']);
    });

    // News Group
    $r->addGroup('/{group:news}', function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '',  'getNews'); // Done  
        $r->addRoute('GET', '/{news_id:\d+}',   'getSingleNew');    // Done
        $r->addRoute('POST', '', ['requireAdmin', 'addNews']);    // Done     
        $r->addRoute('PATCH', '/{news_id:\d+}', ['requireAdmin', 'updateNews']);     // Done
        $r->addRoute('DELETE', '/{news_id:\d+}', ['requireAdmin', 'deleteNews']);   // Done
        $r->addRoute('POST', '/{news_id:\d+}/comment', ['requireLogin', 'addCommentForNews']);      // Done
        $r->addRoute('DELETE', '/deletecomment/{comment_id:\d+}', ['requireLogin', 'deleteCommentForNews']);      // Done
    }); 

    // Promotion Group
    $r->addGroup('/{group:promotion}', function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '', ['requireAdmin', 'getpromotions']);
        $r->addRoute('POST', '', ['requireAdmin', 'addPromotion']);
        $r->addRoute('DELETE', '/{code:\D+}', ['requireAdmin', 'deletePromotion']);
    });

    // Cart & Order Group Merge Into Cart Only
    $r->addGroup('/{group:cart}', function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '', ['requireLogin', 'getCart']);
        $r->addRoute('GET', '/order', ['requireLogin', 'getOrders']);
        $r->addRoute('GET', '/order/{id:\d+}', ['requireLogin', 'getSingleOrder']);
        $r->addRoute('POST', '/product/{product_id:\d+}', ['requireLogin', 'addProductToCart']);
        $r->addRoute('PATCH', '/product/{product_id:\d+}', ['requireLogin', 'updateProductInCart']);
        $r->addRoute('DELETE', '/product/{product_id:\d+}', ['requireLogin', 'deleteProductInCart']);
        $r->addRoute('POST', '/promotion', ['requireLogin', 'addPromotionToCart']);
        $r->addRoute('DELETE', '/promotion/{code:\w+}', ['requireLogin', 'deletePromotionInCart']);
        $r->addRoute('POST', '/checkout', ['requireLogin', 'checkoutCart']);
        $r->addRoute('PATCH', '/order/{id:\d+}', ['requireAdmin', 'updateOrderStatus']);
    });
});

// echo $uri;
// Strip query string (?foo=bar) and decode URI
// To access query string, use $_GET['foo']

if (false !== $pos = strpos($uri, '?')) {    //may be this is for pathn params
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Route handler
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
// var_dump($routeInfo);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(["message" => 'API NOT FOUND']);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo json_encode(["message" => 'Method is not allowed']);
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];               // catch the path , not include group : ex : login
        $vars = $routeInfo[2];                  // array :  ex : $vars[group]= "auth"
        $json = file_get_contents('php://input');       // get the json passed to it 
        $data = array();
        if (!empty($json)) {
            $data = json_decode($json, true);       // change json type to php array: [color=>'grey','description'=>'Hello my friend']
        }
        // Call middleware
        include_once './middlewares/Middleware.php';

        foreach ((array) $handler as $function) { 
            // echo   $handler;   
            switch ($function) {
                case 'requireLogin':
                    Middleware::requireLogin($vars);   
                    //$vars passed by reference => get  $vars['user'] = [
                    //     'user_id' => $row['id'],
                    //     'email' => $row['email'],
                    //     'role' => $row['role']
                    // ];
                    break;
                case 'requireAdmin':
                    Middleware::requireAdmin($vars); //$vars passed by reference 
                    break;
                default:
                    $controllerName = ucfirst($vars['group']) . 'Controller';
                    // echo $controllerName;
                    require './controllers/' . $controllerName . '.php';
                    $controller = new $controllerName();
                    $controller->$function($vars, $data); 
                    break;
            }
        }
        break;
}