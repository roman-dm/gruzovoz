<?php
session_start();
	include_once "app/config/db-cred.inc.php";
	include_once "app/model/Twig/Autoloader.php";
	foreach ($C as $name=>$val){
		define($name,$val);
	}
	$dsn ="mysql:host=".DB_HOST.";dbname=".DB_NAME;
	$dbo = new PDO($dsn,DB_USER,DB_PASS);

		spl_autoload_register(function ($class) {
    		//echo $class."/n/r";
			$filename="app/model/class.".strtolower($class).".inc.php";
			if(strpos($class,"Twig")===false){
				if(file_exists($filename)){
					include_once $filename;
				}
				else{
					$filename=$_SERVER["DOCUMENT_ROOT"]."/app/controller/controller.".strtolower($class).".php";
					include_once $filename;
				}
			}		
		});

	function __autoload($class){
	}
	$test=new Route();