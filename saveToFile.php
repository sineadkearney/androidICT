<?php
$q=$_GET["q"];

if(!empty($_POST['data'])){
$data = $_POST['data'];
$fname = "links".$q.".xml";

// $file = fopen("upload/" .$fname, 'w');//creates new file
$file = fopen($fname , 'w');//creates new file
fwrite($file, $data);
fclose($file);
}
?>