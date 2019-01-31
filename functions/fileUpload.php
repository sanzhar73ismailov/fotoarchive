<?php
require_once "db.php";
echo json_encode($_POST);
//echo json_encode($_FILES);
die;
$checkSize =  getimagesize($_FILES["fileToUpload"]["tmp_name"]);
if(!empty($checkSize)){
//    die(md5(rand()));
    if($checkSize[0] < 2048 && $checkSize[1] < 2048 ){
        echo json_encode(['error'=>'Загруженный размер должен быть больше, чем 2048']);die;
    }
    else if($checkSize[0] > 2048 && $checkSize[1] < 2048 || $checkSize[0] > 2048 && $checkSize[1] < 2048){
        $first_name = $_POST["first_name"];
        $last_name = $_POST["last_name"];
        $date_name = $_POST["date_name"];
        $area_name = $_POST["area_name"];
        $target_dir = "../uploads/directories/";
        $target_file = $target_dir . $_FILES["fileToUpload"]["name"];
        $imgName = md5(rand());
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
//        die($imageFileType);
        if (!file_exists("$target_dir".$area_name)) {
            mkdir("$target_dir".$area_name, 0777, true);
            // Check if image file is a actual image or fake image
            if(isset($_POST["submit"])) {
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if($check !== false) {
                    echo "File is an image - " . $check["mime"] . ".";
                    $uploadOk = 1;
                } else {
                    echo "File is not an image.";
                    $uploadOk = 0;
                }
            }
//// Check file size
//            if ($_FILES["fileToUpload"]["size"] > 500000) {
//                echo "Sorry, your file is too large.";
//                $uploadOk = 0;
//            }
// Allow certain file formats
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
            } else {

                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], "$target_dir$area_name/".$imgName.".".$imageFileType)) {
                    $checking = $conn->query("SELECT * FROM foto_upload WHERE area_name = '$area_name'");
                    $check_result = $checking->fetch_array();
                    $file_name = "uploads/directories/$area_name/".$imgName.".".$imageFileType;
                    if($check_result["first_name"] == $first_name && $check_result["last_name"] == $last_name && $check_result["date_name"] == $date_name && $check_result["area_name"] == $area_name){
                        $select = $conn->query("SELECT id FROM foto_upload WHERE area_name = '$area_name'");
                        $result = $select->fetch_array();
                        $id = $result["id"];
                        $conn->query("INSERT INTO fotos (foto_upload_id, foto_name)
                    VALUES ( '$id','$file_name' )");
                        $photo_select = $conn->query("SELECT * FROM fotos WHERE foto_upload_id = '".$id."'");
                        $photo_result = $photo_select->fetch_all();

                        echo json_encode(['status'=>200,'resp'=>$photo_result]);die;
                    }else{
                        $sql = $conn->query("INSERT INTO foto_upload (first_name, last_name, date_name, area_name)
                    VALUES ('$first_name','$last_name','$date_name','$area_name') ");
                        $select = $conn->query("SELECT id FROM foto_upload WHERE area_name = '$area_name'");
                        $result = $select->fetch_array();
                        $id = $result["id"];
                        $conn->query("INSERT INTO fotos (foto_upload_id, foto_name)
                    VALUES ( '$id','$file_name' )");
                        $photo_select = $conn->query("SELECT * FROM fotos WHERE foto_upload_id = '".$id."'");
                        $photo_result = $photo_select->fetch_all();

                        echo json_encode(['status'=>200,'resp'=>$photo_result]);die;
                    }
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        }
        else{
            if(isset($_POST["submit"])) {
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if($check !== false) {
                    echo "File is an image - " . $check["mime"] . ".";
                    $uploadOk = 1;
                } else {
                    echo "File is not an image.";
                    $uploadOk = 0;
                }
            }
// Check file size
//            if ($_FILES["fileToUpload"]["size"] > 500000) {
//                echo "Sorry, your file is too large.";
//                $uploadOk = 0;
//          }
// Allow certain file formats
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], "$target_dir$area_name/".$imgName.".".$imageFileType)) {
                    $checking = $conn->query("SELECT * FROM foto_upload WHERE area_name = '$area_name'");
                    $check_result = $checking->fetch_array();
                    $file_name = "uploads/directories/$area_name/".$imgName.".".$imageFileType;
                    if($check_result["first_name"] == $first_name && $check_result["last_name"] == $last_name && $check_result["date_name"] == $date_name && $check_result["area_name"] == $area_name){
                        $select = $conn->query("SELECT id FROM foto_upload WHERE area_name = '$area_name'");
                        $result = $select->fetch_array();
                        $id = $result["id"];
                        $conn->query("INSERT INTO fotos (foto_upload_id, foto_name)
                    VALUES ( '$id','$file_name' )");
                        $photo_select = $conn->query("SELECT * FROM fotos WHERE foto_upload_id = '".$id."'");
                        $photo_result = $photo_select->fetch_all();

                        echo json_encode(['status'=>200,'resp'=>$photo_result]);die;
                    }else{
                        $sql = $conn->query("INSERT INTO foto_upload (first_name, last_name, date_name, area_name)
                    VALUES ('$first_name','$last_name','$date_name','$area_name') ");
                        $select = $conn->query("SELECT id FROM foto_upload WHERE area_name = '$area_name'");
                        $result = $select->fetch_array();
                        $id = $result["id"];
                        $conn->query("INSERT INTO fotos (foto_upload_id, foto_name)
                    VALUES ( '$id','$file_name' )");
                        $photo_select = $conn->query("SELECT * FROM fotos WHERE foto_upload_id = '".$id."'");
                        $photo_result = $photo_select->fetch_all();

                        echo json_encode(['status'=>200,'resp'=>$photo_result]);die;
                    }
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        }
    }
}
else{
    echo json_encode(['error'=>'Пожалуйста заполните все поля']);die;
}
?>