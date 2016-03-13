<?php
if(isset($_GET["y"])){
	//$_POST["id_event"]="1=1";
	$_POST["sEventName"]="test";
	// $_POST["sEventDesc"]="comment";
	// $_POST["dEventDateStart"]="2016-02-20";
	// $_POST["dEventDateEnd"]="2016-02-20";
	// $_POST["iEventUserIdOt"]=1;
	$_POST["type_request"]="processEvent";
	//$_POST["query_type"]='user_list';
}
include "app/int.inc.php";
//$test=new route();
?>