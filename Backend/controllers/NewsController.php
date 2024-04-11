<?php

require_once './models/News.php';
require_once './models/NewsComment.php';
 
class NewsController
{
    public function getNews($param, $data)
    {
        $queryParams = [];
        $allowedKeys = [];
        $select = ['news_id' ,'created_at', 'updated_at', 'image_url' , 'title', 'content'];

        try {
            $News = new News();
            $result = $News->get($queryParams, $allowedKeys, $select);
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
      
            http_response_code(200);
            echo json_encode(["message" => "News List fetched Successfully", "data" => $rows]);
        
        } catch (PDOException $e) {
            echo "Unknown error in NewsController::getNews: " . $e->getMessage();
            die();
        }
    }

    public function getSingleNew($param, $data)
    {
        $news_id = $param["news_id"];
        $allowedKeys = ['news_id'];
        $select = ['news_id' ,'created_at', 'updated_at', 'image_url' , 'title', 'content'];

        $News = new News();
        $NewsComment = new NewsComment();
        $result = $News->get(['news_id' => $news_id], $allowedKeys, $select);
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        if($result->rowCount() == 0) {

            http_response_code(404);
            echo json_encode(["message" => "No News found"]);

        } else {
            // get Comment for News
            $result = $NewsComment->get(['news_id' => $news_id], [] , ['comment_id', 'news_id' , 'avatar_url' , 'user_id' , 'user_name' , 'created_at', 'updated_at', 'title' , 'content']);
            $cmtrows = $result->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode(["message" => "News fetched Successfully", "data" => $rows , 'commnents' => $cmtrows]);

        }
    }

    public function addCommentForNews($param, $data)
    {
        if (!isset($data['user_id']) || !isset($data['content']) || !isset($data['title']) || !isset($data['user_name']) || !isset($data['avatar_url']))
        {
            http_response_code(400);
            echo json_encode(["message" => "Missing user_id, content, title, image_url or name"]);
            return;
        }

        // Check news exist
        $News = new News();
        $news_id = $param["news_id"];

        $result = $News->get(['news_id' => $news_id], ['news_id'], ['news_id']);
        if ($result->rowCount() == 0) {

            http_response_code(400);
            echo json_encode(["message" => "News does not exist"]);
            die();
        }

        // add news_id into data
        $data['news_id'] = $param['news_id'];

        $allowedKeys = ['avatar_url' , 'title', 'content' , 'user_name' , 'user_id' , 'news_id'];

        try {

            $NewsComment = new NewsComment();
            $result = $NewsComment->create(
                $data,
                $allowedKeys
            );
            
            http_response_code(200);
            echo json_encode(["message" => "Comment for News created successfully"]);

        } catch (PDOException $e) {

            echo "Unknown error in (comment for news) NewsController:: addNews: " . $e->getMessage();
            die();
            
        }
    }

     public function deleteCommentForNews($param, $data)
    { 
       
        $NewsComment = new NewsComment();
        $comment_id = $param["comment_id"];
 
        $result = $NewsComment->get(['comment_id' => $comment_id], ['comment_id'], ['comment_id']);
        if ($result->rowCount() == 0) {

            http_response_code(400);
            echo json_encode(["message" => "News_Comment does not exist"]);
            die();

        }

        try{

            $NewsComment->delete($comment_id);
            http_response_code(200);
            echo json_encode(["message" => "News_Comment deleted successfully"]);

        } catch (PDOException $e) {
     
            echo "Unknown error in NewsController::deleteCommentForNews : " . $e->getMessage();
            die();

        }
    }
 

    public function addNews($param, $data)
    {
        if (!isset($data['user_id'])
            || !isset($data['content'])
            || !isset($data['title'])
            || !isset($data['image_url'])) 
        {
            http_response_code(400);
            echo json_encode(["message" => "Missing user_id, content, title, image_url"]);
            return;
        }

        $allowedKeys = ['image_url' , 'title', 'content'];

        try {
            $News = new News();
            $result = $News->create(
                $data,
                $allowedKeys
            );
            
            http_response_code(200);
            echo json_encode(["message" => "News created successfully"]);
        } catch (PDOException $e) {
            echo "Unknown error in NewsController:: addNews: " . $e->getMessage();
            die();
        }
    }
 
    public function updateNews($param, $data)
    {
        if (!isset($param['news_id']) || empty($data)) {
            http_response_code(400);
            echo json_encode(["message" => "Missing news_id or update data"]);
            return;
        }

        $allowedKeys = ['image_url', 'title', 'content'];

        try {

            $News = new News();
            $news_id = $param["news_id"];

           // Check if News exist
            $News = new News();
            $result = $News->get(['news_id' => $news_id], ['news_id'], ['news_id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "News does not exist"]);
                die();
            }

            $News->update($news_id, $data, $allowedKeys);

            http_response_code(200);
            echo json_encode(["message" => "News updated successfully"]);
            
        } catch (PDOException $e) {

            echo "Unknown error in NewsController::updateNews: " . $e->getMessage();
            die();

        }
    }

    
    public function deleteNews($param, $data)
    {   
        try {

            $news_id = $param["news_id"];
            $allowedKeys = ['news_id'];
            $select = ['news_id' ,'created_at', 'updated_at', 'image_url' , 'title', 'content'];
            
            // Check if News exist
            $News = new News();
            $result = $News->get(['news_id' => $news_id], ['news_id'], ['news_id']);
            if ($result->rowCount() == 0) {
                http_response_code(400);
                echo json_encode(["message" => "News does not exist"]);
                die();
            }

            // Delete News
            $News->delete($news_id);
 
            http_response_code(200);
            echo json_encode(["message" => "News deleted successfully"]);

        } catch (PDOException $e) {
     
            echo "Unknown error in NewsController::deleteNews : " . $e->getMessage();
            die();

        }
    }

    
}