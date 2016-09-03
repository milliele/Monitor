<?php
if(isset($_POST['target']))
{
	header('Content-Type: application/pdf;charset=UTF-8');
	if(isset($_POST['name'])) $name = $_POST['name'];
	else $name = $_POST['target'];
	header('Content-Disposition: attachment; filename='.$name);
	readfile($_POST['target']);
}
?>
