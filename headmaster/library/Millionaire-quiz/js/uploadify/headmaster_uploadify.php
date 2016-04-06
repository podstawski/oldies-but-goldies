<?php
    if (!empty($_FILES)) {    
        $tempFile = $_FILES['Filedata']['tmp_name'];
        $targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
        $targetFile = str_replace('//','/',$targetPath) . $_POST['hash'] . '.jpg';
        $targetFile_BIG = str_replace('//','/',$targetPath) . $_POST['hash'] . '_big.jpg';
        // $targetFile = '/home/headmaster/headmaster.jestemprzedsiebiorczy.pl/SVN/public/uploads/' . $_POST['hash'] . '.jpg';
        move_uploaded_file($tempFile,$targetFile);
        include('resize-class.php');
	    $resizeObj = new resize($targetFile);
		$resizeObj->resizeImage(1024,1024);
        $resizeObj->saveImage($targetFile_BIG, 80);
    	$resizeObj->resizeImage(560,348);
        $resizeObj->saveImage($targetFile, 80);
        echo str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
    }
?>
