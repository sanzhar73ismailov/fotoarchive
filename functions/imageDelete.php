<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 02.11.2017
 * Time: 19:01
 */
require_once "db.php";

$images_id = $_POST['id'];

//print_r($_POST);die;

if(!$images_id){
    return false;
}
else{
    $photo_select = $conn->query("DELETE FROM fotos WHERE id = '".$images_id."'");

    echo json_encode(['result'=>true]);
}