<?php
$_md5_uploaddir		= 'bc01a2b06b9a37b31d764339dbd0f733';

$upload_file_u		= $_GET['u'];
$upload_file_u		= substr($upload_file_u,strpos($upload_file_u,"x")+1);
$upload_file_u		= base64_decode($upload_file_u);

$upload_file_pole	= array();
$upload_file_pole	= explode("|",$upload_file_u);

$uploaddir = $upload_file_pole[0];

if($_md5_uploaddir != md5($uploaddir)) exit;

$safe_filename = preg_replace(
								array("/\s+/", "/[^-\.\w]+/"),
								array("_", ""),
								trim($_FILES['uploadfile']['name']));

$file = '../../'.$uploaddir.'/'.basename($upload_file_pole[1].'-'.$safe_filename); 
$size = $_FILES['uploadfile']['size'];

if($size>1048576) {
	echo "error file size > 1 MB";
	unlink($_FILES['uploadfile']['tmp_name']);
	exit;
}

if(move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) {
	echo "success";
}else{
	echo "error ".$_FILES['uploadfile']['error']." --- ".$_FILES['uploadfile']['tmp_name']." %%% ".$file."($size)";
}
?>