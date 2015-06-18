<?
if($_GET['format'] == 'protobuf_raw'){
	readfile('GetGameData.raw');
}elseif($_GET['format'] == 'json'){
	header("Content-type: application/json");
	echo file_get_contents('GetGameData.json');
}
?>