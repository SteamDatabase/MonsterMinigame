<?php 
if($_GET['format'] == 'protobuf_raw'){
	readfile('GetPlayerNames.raw');
}elseif($_GET['format'] == 'json'){
	header("Content-type: application/json");
	echo file_get_contents('GetPlayerNames.json');
}

?>