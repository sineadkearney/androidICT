<?php
echo("test");
if(!empty($_POST['data'])){
$data = $_POST['data'];
$fname = "links.xml";//generates random name

// $file = fopen("upload/" .$fname, 'w');//creates new file
$file = fopen("links.xml", 'w');//creates new file
fwrite($file, $data);
fclose($file);
}
?>