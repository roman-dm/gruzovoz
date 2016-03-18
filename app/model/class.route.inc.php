<?php
//session_start();
class route extends base
{
	public $method_name;
	public $controller;
	public function __construct()
	{
		parent::__construct();
		$routes = explode('/', $_SERVER['REQUEST_URI']);
		if(empty($routes[1])){
			$routes[1]="main";
		}
		$array_routes=json_decode(file_get_contents("app/config/route.json"));
		foreach ($array_routes as $item){
			if(!isset($routes[2])){
				if($item->action==$routes[1] && !isset($item->action2)){
					$method_name=$item->method;
					$this->controller=$item->controller;
					break;
				}
			}
			else{
				if($item->action==$routes[1] && $item->action2==$routes[2]){
					$method_name=$item->method;
					$this->controller=$item->controller;
				}
			}
		}
		if(isset($method_name) && !empty($method_name)){
			$controller = new $this->controller;
			$controller->$method_name();
		}
		else{
			echo $_SESSION["status"];
			if((isset($_SESSION["status"])) && (!empty($_SESSION["status"])) ){
				$access_array=array(1=>"root",2=>"teacher",3=>"respond",4=>"admin");
				$main_page=$access_array[$_SESSION["status"]];
			}
			echo $main_page;
			echo $this->view->render('404.html',array("profile"=>$main_page));
		}
	}
}
?>