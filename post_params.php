<?php
    $data_array = $_POST['data_array'];
    $secret = $_POST['secret'];
    $nickname = $_POST['nickname'];
    $src_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

    // if (!$humid) $humid = "NULL";

    if ($secret != 'e96cfe7eb8b48d6b5c492de81383275fce7a8bc743eccde62c6efce7aacba1e9') {
        die("context: " .print_r($_POST, 1) . " access denied.");
    }  
    
 ?>