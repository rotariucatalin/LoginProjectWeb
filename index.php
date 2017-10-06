<?php
    require_once('config.php');
    require_once('MCrypt/MCrypt.php');
    
    $username           = "";
    $password           = "";
    $id_user            = "";
    $output_array       = array();
    
    $json_post  = json_decode(file_get_contents('php://input'), true);
    $type       = $json_post['type'];
    
/*    if(isset($json_post['username'])) $username = MCrypt::decrypt($json_post['username']);
    if(isset($json_post['password'])) $password = MCrypt::decrypt($json_post['password']);*/
         
    if(isset($json_post['username']))           $username           = $json_post['username'];
    if(isset($json_post['password']))           $password           = $json_post['password'];
    if(isset($json_post['id_user']))            $id_user            = $json_post['id_user'];
    if(isset($json_post['status_message']))     $status_message     = $json_post['status_message'];
    if(isset($json_post['currend_friend']))     $currend_friend     = $json_post['currend_friend'];
    
/*    $type   = "request_data_visibility";
    $id_user    = "1";
    $currend_friend    = "2";*/
    
    switch($type)   {
        case "login":                   login_function($username, $password, $output_array); break;
        case "register":                register_function($username, $password, $output_array); break;
        case "get_all_users":           get_all_users_function($output_array); break;  
        case "get_status_message":      get_status_message($output_array, $id_user); break;  
        case "set_status_message":      set_status_message($output_array, $id_user, $status_message); break;  
        case "get_all_friends":         get_all_friends($output_array, $id_user); break;  
        case "get_profile_allowed":     get_profile_allowed($output_array, $id_user, $currend_friend); break;  
        case "get_friend_async":        get_friend_async($output_array, $id_user, $currend_friend); break;  
        case "add_or_remove_friend":    add_or_remove_friend($output_array, $id_user, $currend_friend); break;  
        case "remove_data_visibility":  remove_data_visibility($output_array, $id_user, $currend_friend); break;  
        case "request_data_visibility": request_data_visibility($output_array, $id_user, $currend_friend); break;  
    }
    
    function login_function($username, $password, $output_array)   {
        global $con;
        
        $select_query       = mysqli_query($con, "SELECT * FROM `users` WHERE `username` = '".$username."' AND `password` = '".MD5($password)."' ");
        while($result_query = mysqli_fetch_array($select_query)) {
            
            $id_user_db        = $result_query['id_user'];
            $username_db       = $result_query['username'];   
            $password_db       = $result_query['password'];
            
            $output_array      = array("id_user" => $id_user_db, "username" => $username_db, "password" => $password_db, "success" => 1, "message" => "Username Found", "user_status" => "Test status" );
        }
        
        if(count($output_array) == 0)
            $output_array   = array("id_user" => 0 ,"username" => "", "password" => "", "success" => "0", "message" => "Username Not Found", "user_status" => "" );
            
        echo json_encode($output_array);
        exit();   
    }
    
    function register_function($username, $password, $output_array)   {
        global $con;
        
        $insert_query       = mysqli_query($con, " INSERT INTO `users`( `id_user`, `username`, `password` ) VALUES ( '', '".$username."', '".MD5($password)."' ) ");
        
        if($insert_query)
            $output_array   = array("success" => "1", "message" => "Username successfully created!" );
        else
            $output_array   = array("success" => "0", "message" => "There was a problem creating this account!" );
            
        echo json_encode($output_array);
        exit();   
    }
    
    function get_all_users_function($output_array)  {
        
        $path   = 'big-cogwheel-outline_318-36658.jpg';
        $base64 = base64_encode($path);
        
        $imagedata = file_get_contents("mc_freddie_color_web.png");
                     // alternatively specify an URL, if PHP settings allow
        $base64 = base64_encode($imagedata);        
        
        for($i = 1; $i <=20; $i++)  {
            $output_array[$i]       = array("id_user" => "$i","username" => "Username-$i", "status_message" => "🙅", "avatar" => "$base64");
        }
        $output_array["total"]      = $i - 1;
        
        echo json_encode($output_array);
        exit();
    }
    
    function get_status_message($output_array, $id_user)   {
        
        global $con;
        
        $select_query       = mysqli_query($con, " SELECT `user_status_message` FROM `users` WHERE `id_user` = '".$id_user."' ");
        $result_select      = mysqli_fetch_array($select_query);
        
        $status_message     = $result_select['user_status_message'];
        
        if($select_query)
            $output_array   = array("success" => "1", "user_status" => "$status_message" );
        else
            $output_array   = array("success" => "0", "user_status" => "There was a problem retreving the status!" );
        
        echo json_encode($output_array);
        exit();  
    }
    
    function set_status_message($output_array, $id_user, $status_message) {
        
        global $con;
        
        $update_query   = mysqli_query($con, " UPDATE `users` SET `user_status_message` = '".$status_message."' WHERE `id_user` = '".$id_user."' ");
        
        if($update_query)
            $output_array   = array("success" => "1", "message" => "Status successfully updated!" );
        else
            $output_array   = array("success" => "0", "message" => "There was a problem updating the status!" );
        
        echo json_encode($output_array);
        exit(); 
    }
    
    function get_all_friends($output_array, $id_user)  {
        
        global $con;
        
        $path   = 'big-cogwheel-outline_318-36658.jpg';
        $base64 = base64_encode($path);
        
        $imagedata = file_get_contents("mc_freddie_color_web.png");
                     // alternatively specify an URL, if PHP settings allow
        $base64 = base64_encode($imagedata);        
        
        $count  = 1;
        
        $select_query       = mysqli_query($con, "SELECT * FROM `friends` LEFT JOIN `users` ON `friends`.`friend_id_user` = `users`.`id_user` WHERE `friends`.`id_user` = '".$id_user."' ");
        while($result_query = mysqli_fetch_array($select_query)){
            
            $friend_id_user = $result_query['friend_id_user'];
            $friend_name    = $result_query['username'];
            $friend_status  = $result_query['user_status_message'];
            
            $output_array[$count]       = array("id_user" => "$friend_id_user","username" => "$friend_name", "status_message" => "$friend_status", "avatar" => "$base64");   
            $count++; 
        }
        $output_array["total"]      = $count;
        
        echo json_encode($output_array);
        exit();
    }
    
    function get_profile_allowed($output_array, $id_user, $currend_friend) {
        global $con;
        
        $check_allowed_existent = mysqli_query($con, " SELECT * FROM `users_privilege` LEFT JOIN `users` ON `users`.`id_user` = `users_privilege`.`id_friend` WHERE `users_privilege`.`id_user` = '".$id_user."' AND `id_friend` = '".$currend_friend."' ");
        $rowcount               = mysqli_num_rows($check_allowed_existent);
        if($rowcount == 0) {
            
            $output_array   = array("success" => "1", "count" => "0" );       
                       
            
        } else {
            
            $result                     = mysqli_fetch_array($check_allowed_existent);
            $pirvilege_allowed          = $result['pirvilege_allowed'];
            /*$output_array['privilege']  = json_decode($pirvilege_allowed, TRUE);*/
            $output_array['success']    = "1";
            $output_array['count']      = "1";
            
            
            foreach(json_decode($pirvilege_allowed, TRUE) AS $index => $value)  {
                
                if($index == "user_profile_name")   $output_array['user_profile_name']      = $result['user_profile_name'];
                if($index == "username")            $output_array['username']               = $result['username'];
                if($index == "user_dob")            $output_array['user_dob']               = $result['user_dob'];
                if($index == "user_status_message") $output_array['user_status_message']    = $result['user_status_message'];
                if($index == "user_gender")         $output_array['user_gender']            = $result['user_gender'];
                if($index == "user_occupation")     $output_array['user_occupation']        = $result['user_occupation'];
                if($index == "user_company")        $output_array['user_company']           = $result['user_company'];
                if($index == "user_nationality")    $output_array['user_nationality']       = $result['user_nationality'];
                if($index == "user_phone")          $output_array['user_phone']             = $result['user_phone'];
                if($index == "user_email")          $output_array['user_email']             = $result['user_email'];
                if($index == "user_website")        $output_array['user_website']           = $result['user_website'];
                if($index == "user_interests")      $output_array['user_interests']         = $result['user_interests'];
                
            }
        }
        
        $select_profile_settings    = mysqli_query($con, " SELECT * FROM `users_profile_settins` LEFT JOIN `users` ON `users_profile_settins`.`id_user` = `users`.`id_user` WHERE `users_profile_settins`.`id_user` = '".$currend_friend."' ");
        while($result               = mysqli_fetch_array($select_profile_settings)) {
            
            if( $result['user_profile_settings_name'] == '1' )          $output_array['user_profile_name']       = $result['user_profile_name'];     
            if( $result['user_profile_settings_username'] == '1' )      $output_array['username']                = $result['username'];     
            if( $result['user_profile_settings_dob'] == '1' )           $output_array['user_dob']                = $result['user_dob'];     
            if( $result['user_profile_settings_status'] == '1' )        $output_array['user_status_message']     = $result['user_status_message'];     
            if( $result['user_profile_settings_gender'] == '1' )        $output_array['user_gender']             = $result['user_gender'];     
            if( $result['user_profile_settings_occupation'] == '1' )    $output_array['user_occupation']         = $result['user_occupation'];     
            if( $result['user_profile_settings_company'] == '1' )       $output_array['user_company']            = $result['user_company'];     
            if( $result['user_profile_settings_nationality'] == '1' )   $output_array['user_nationality']        = $result['user_nationality'];     
            if( $result['user_profile_settings_phone'] == '1' )         $output_array['user_phone']              = $result['user_phone'];     
            if( $result['user_profile_settings_email'] == '1' )         $output_array['user_email']              = $result['user_email'];     
            if( $result['user_profile_settings_website'] == '1' )       $output_array['user_website']            = $result['user_website'];     
            if( $result['user_profile_settings_interests'] == '1' )     $output_array['user_interests']          = $result['user_interests'];     
        }
        echo json_encode($output_array);
        exit();
    }
    
    function get_friend_async($output_array, $id_user, $currend_friend) {
        
        global $con;
        
        $check_friend_async = mysqli_query($con, " SELECT COUNT(*) AS `total` FROM `friends` WHERE `id_user` = '".$id_user."' AND `friend_id_user` = '".$currend_friend."' ");
        $result             = mysqli_fetch_array($check_friend_async);
        
        $output_array       = array( "count" => $result['total'] );
        
        echo json_encode($output_array);
        exit();
    }
    
    function add_or_remove_friend($output_array, $id_user, $currend_friend) {
        
        global $con;
        
        /*
            Check if is friend
        */
        $friend_query   = mysqli_query($con, " SELECT * FROM `friends` WHERE `id_user` = '".$id_user."' AND `friend_id_user` = '".$currend_friend."' ");
        $row_count      = mysqli_num_rows($friend_query);
        if($row_count == 0)  {
            
             $insert_friend = mysqli_query($con, " INSERT INTO `friends`(`id_friend`, `id_user`, `friend_id_user`) VALUES ('', '".$id_user."', '".$currend_friend."') ");
             
             $output_array  = array("success" => "1", "message" => "Friend added successfully");
             echo json_encode($output_array);
             exit();
            
        } else {
            
            $delete_friend  = mysqli_query($con, " DELETE FROM `friends` WHERE `id_user` = '".$id_user."' AND `friend_id_user` = '".$currend_friend."' ");
            $output_array  = array("success" => "1", "message" => "Friend removed successfully");
            echo json_encode($output_array);
            exit();
                
        }
    }
    
    function remove_data_visibility($output_array, $id_user, $currend_friend) {
        
        global $con;
        
        $delete_data_visibility = mysqli_query($con, " DELETE FROM `users_privilege` WHERE `id_user` = '".$id_user."' AND `id_friend` = '".$currend_friend."' ");
        
        $output_array           = array("success" => "1", "message" => "Data visibility removed");
        echo json_encode($output_array);
        exit();
    }
    
    function request_data_visibility($output_array, $id_user, $currend_friend) {
        
        global $con;
        
        $output_array['user_profile_name']      = "";
        $output_array['username']               = "";
        $output_array['user_dob']               = "";
        $output_array['user_status_message']    = "";
        $output_array['user_gender']            = "";
        $output_array['user_occupation']        = "";
        $output_array['user_company']           = "";
        $output_array['user_nationality']       = "";
        $output_array['user_phone']             = "";
        $output_array['user_email']             = "";
        $output_array['user_website']           = "";
        $output_array['user_interests']         = "";
        
        $select_data_visibility = mysqli_query($con, " SELECT * FROM `users_privilege` WHERE `id_user` = '".$id_user."' AND `id_friend` = '".$currend_friend."' ");
        $result                 = mysqli_fetch_array($select_data_visibility);
        
        $pirvilege_allowed      = $result['pirvilege_allowed'];
        $allowed_array          = json_decode($pirvilege_allowed, TRUE);
        if(count($allowed_array) > 0)
            foreach($allowed_array AS $index => $value)
                unset($output_array[$index]);    
        
        $select_profile_settings    = mysqli_query($con, " SELECT * FROM `users_profile_settins` LEFT JOIN `users` ON `users_profile_settins`.`id_user` = `users`.`id_user` WHERE `users_profile_settins`.`id_user` = '".$currend_friend."' ");
        while($result               = mysqli_fetch_array($select_profile_settings)) {
            
            if( $result['user_profile_settings_name'] == '1' )          unset($output_array['user_profile_name']);
            if( $result['user_profile_settings_username'] == '1' )      unset($output_array['username']);
            if( $result['user_profile_settings_dob'] == '1' )           unset($output_array['user_dob']);
            if( $result['user_profile_settings_status'] == '1' )        unset($output_array['user_status_message']);
            if( $result['user_profile_settings_gender'] == '1' )        unset($output_array['user_gender']);
            if( $result['user_profile_settings_occupation'] == '1' )    unset($output_array['user_occupation']);
            if( $result['user_profile_settings_company'] == '1' )       unset($output_array['user_company']);
            if( $result['user_profile_settings_nationality'] == '1' )   unset($output_array['user_nationality']);
            if( $result['user_profile_settings_phone'] == '1' )         unset($output_array['user_phone']);
            if( $result['user_profile_settings_email'] == '1' )         unset($output_array['user_email']);
            if( $result['user_profile_settings_website'] == '1' )       unset($output_array['user_website']);
            if( $result['user_profile_settings_interests'] == '1' )     unset($output_array['user_interests']);
        }
        
        echo json_encode($output_array);
        exit();
    }
?>