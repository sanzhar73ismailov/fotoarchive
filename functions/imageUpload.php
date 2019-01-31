<?php
require_once "db.php";

$images_id = $_POST['data'];

if(!$images_id){
    return false;
}
else{
    $photo_select = $conn->query("SELECT * FROM fotos WHERE foto_upload_id = '".$images_id."'");
    $result = mysqli_fetch_all($photo_select);
    echo json_encode(['photo'=>$result]);
}