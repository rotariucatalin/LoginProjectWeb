<?php
    require_once('config.php');
    require_once('MCrypt/MCrypt.php');
    
    $username           = "";
    $password           = "";
    $output_array       = array();
    
    $json_post  = json_decode(file_get_contents('php://input'), true);
    $type       = $json_post['type'];
    
/*    if(isset($json_post['username'])) $username = MCrypt::decrypt($json_post['username']);
    if(isset($json_post['password'])) $password = MCrypt::decrypt($json_post['password']);*/
    
    if(isset($json_post['username'])) $username = $json_post['username'];
    if(isset($json_post['password'])) $password = $json_post['password'];
    
    switch($type)   {
        case "login":       login_function($username, $password, $output_array); break;
        case "register":    register_function($username, $password, $output_array); break;
            
    }
    
    function login_function($username, $password, $output_array)   {
        global $con;
        
        $select_query       = mysqli_query($con, "SELECT * FROM `users` WHERE `username` = '".$username."' AND `password` = '".MD5($password)."' ");
        while($result_query = mysqli_fetch_array($select_query)) {
            
            $id_user_db        = $result_query['id_user'];
            $username_db       = $result_query['username'];   
            $password_db       = $result_query['password'];
            
            $output_array      = array("id_user" => $id_user_db, "username" => $username_db, "password" => $password_db, "success" => 1, "message" => "Username Found" );
        }
        
        if(count($output_array) == 0)
            $output_array   = array("id_user" => 0 ,"username" => "", "password" => "", "success" => "0", "message" => "Username Not Found" );
            
        echo json_encode($output_array);
        exit();   
    }
    
    function register_function($username, $password, $output_array)   {
        global $con;
        
        $insert_query       = mysqli_query($con, " INSERT INTO `users`( `id_user`, `username`, `password` ) VALUES ( '', '".$username."', '".$password."' ) ");
        
        if($insert_query)
            $output_array   = array("success" => "1", "message" => "Username successfully created!" );
        else
            $output_array   = array("success" => "0", "message" => "There was a problem creating this account!" );
            
        echo json_encode($output_array);
        exit();   
    }
?>