<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 04.11.2017
 * Time: 13:48
 */
require_once "db.php";

$data = json_decode($_POST['data']);

/*

  Array
(
    [0] => stdClass Object
        (
            [category_number] => 1
            [category_name] => 11
            [image_number] => 1
            [photo_name] => uploads/directories/wetwett/03563342055be6220e63583085516728.jpg
            [photo_id] => 45
        )

    [1] => stdClass Object
        (
            [category_number] => 1
            [category_name] => 11
            [image_number] => 1
            [photo_name] => uploads/directories/wetwett/762977dfefdbd915ee7527aa563d2bb4.jpg
            [photo_id] => 45
        )

    [2] => stdClass Object
        (
            [category_number] => 2
            [category_name] => 22
            [image_number] => 2
            [photo_name] => uploads/directories/wetwett/bea5b83d3a056039813089e7aa7f7e9a.jpg
            [photo_id] => 45
        )

    [3] => stdClass Object
        (
            [category_number] => 2
            [category_name] => 22
            [image_number] => 2
            [photo_name] => uploads/directories/wetwett/4ddcf81ffc4375a8ebbb7c8fe80896b8.jpg
            [photo_id] => 45
        )

)

 */

//print_r($data);
//var_dump($data);
$imgCount = count($data);
$photoID = $data[0]->photo_id;

$photo_select = $conn->query("SELECT * FROM fotos WHERE id = '".$photoID."'");
$result = mysqli_fetch_assoc($photo_select);
$foto_upload_id = $result['foto_upload_id'];
//echo $foto_upload_id;
//var_dump($result);
$categories = array();

foreach ($data  as $img){
    $categories[] =  (array) $img;
}

//print_r($categories);
$catCount = array();
foreach ($categories as $category){
    $catCount[] = $category['category_number'];
}
$newCatCount = array_unique($catCount);
//print_r($newCatCount);

$lastArray = array();

foreach ($newCatCount as $catNum){

    foreach ($categories as $category){
        if($category['category_number'] == $catNum){
            $lastArray[$catNum][] = $category;
        }
    }
}

//print_r($lastArray);

foreach ($lastArray as $catNum){
    foreach ($catNum as $key => $value){
        
    }
}

$files_to_zip = array();

create_zip();


function create_zip($files = array(),$destination = '',$overwrite = false) {
    //if the zip file already exists and overwrite is false, return false
    if(file_exists($destination) && !$overwrite) { return false; }
    //vars
    $valid_files = array();
    //if files were passed in...
    if(is_array($files)) {
        //cycle through each file
        foreach($files as $file) {
            //make sure the file exists
            if(file_exists($file)) {
                $valid_files[] = $file;
            }
        }
    }
    //if we have good files...
    if(count($valid_files)) {
        //create the archive
        $zip = new ZipArchive();
        if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        //add the files
        foreach($valid_files as $file) {
            $zip->addFile($file,$file);
        }
        //debug
        //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

        //close the zip -- done!
        $zip->close();

        //check to make sure the file exists
        return file_exists($destination);
    }
    else
    {
        return false;
    }
}