<?php
//session_start();
class route extends base
{
	public $method_name;
	public $controller;
	public function __construct()
	{
		parent::__construct();
		$old_routes=explode('?', $_SERVER['REQUEST_URI']);
		$routes = explode('/', $old_routes[0]);
		if($routes[1]=="api"){
			header("Content-type: application/json");
		}
		$this->addLogAutorization();
		if(empty($routes[1])){
			$routes[1]="main";
		}
		if(isset($_GET["test1"])){
			$count_r=count($routes)-2;
			$line="app/config/route_".$count_r.".json";
			//echo $line;
			$array_routes=json_decode(file_get_contents($line),true);
		}
		else{
			$array_routes=json_decode(file_get_contents("app/config/route.json"),true);
		}
		$count_ing=array();
		if(isset($_GET["test1"])){
			foreach ($array_routes as $item){
				// print_r($item);
				// print_r($routes);
			if(!isset($routes[2])){
				if($item["action1"]==$routes[1] && !isset($item["action2"])){
					$method_name=$item["method"];
					$this->controller=$item["controller"];
					break;
				}
			}
			else{
				//print_r($item);
				$count_sop=0;
				// $routes = array_diff($routes, array(''));
				// // if(!empty($routes[0])){
				// // 	unset($routes[0]);
				// // }
				// // if(!empty($routes[1])){
				// // 	unset($routes[1]);
				// // }
				// // asort($routes);
				// print_r($routes);
				foreach($routes as $key=>$route){
					//echo $item["action".$key]."==".$route."<br>";
					if(!empty($item["action".$key]) && !empty($route) && $item["action".$key]==$route){
						//echo "<br>";
						$count_sop++;
						//echo $count_sop;
						//echo $item["action".$key]."==".$route."</br>";
						// echo "<br>";
					}	
					else if($item["action".$key]=="int" && is_numeric($route)){
						$count_ing[]=$route;
						$count_sop++;
					}
				// echo "Кол-во совпаданией".$count_sop."</br>";
				// echo "Кол-во роутов".count($routes)."</br>";
					//print_R($count_ing);
				if(count($routes)-2==$count_sop){
					//echo $item["method"];
						$method_name=$item["method"];
						$this->controller=$item["controller"];
						break;
					}
				}
			}
		}
		}
		else{
		foreach ($array_routes as $item){
			if(!isset($routes[2])){
				if($item["action1"]==$routes[1] && !isset($item["action2"])){
					$method_name=$item["method"];
					$this->controller=$item["controller"];
					break;
				}
			}
			else{
				//print_r($item);
				$count_sop=0;
				// $routes = array_diff($routes, array(''));
				// // if(!empty($routes[0])){
				// // 	unset($routes[0]);
				// // }
				// // if(!empty($routes[1])){
				// // 	unset($routes[1]);
				// // }
				// // asort($routes);
				// print_r($routes);
				foreach($routes as $key=>$route){
					if(isset($_GET["test1"])){
						//echo $count_sop;
					}
					if(!empty($item["action".$key]) && !empty($route) && $item["action".$key]==$route){
						//echo "<br>";
						$count_sop++;
						//echo $item["action".$key]."==".$route."</br>";
						// echo "<br>";
					}	
					else if($item["action".$key]=="int" && is_numeric($route)){
						$count_ing[]=$route;
						$count_sop++;
					}
				// echo "Кол-во совпаданией".$count_sop."</br>";
				// echo "Кол-во роутов".count($routes)."</br>";
					//print_R($count_ing);
				if(count($routes)-2==$count_sop){
					//echo $item["method"];
						$method_name=$item["method"];
						$this->controller=$item["controller"];
						break;
					}
				}
			}
		}
		}
		if(isset($method_name) && !empty($method_name)){
			if(isset($_GET["time"])){
				echo microtime(true); 
			}
			$model= new Main();
			//print_r($model);
			$controller = new $this->controller;
			//print_r($count_ing);
			if(count($count_ing)>0){
				$controller->$method_name($count_ing);
			}
			else{
				$controller->$method_name();
			}
		}
		else{
			//echo $_SESSION["status"];
			if((isset($_SESSION["status"])) && (!empty($_SESSION["status"])) ){
				$access_array=array(1=>"root",2=>"teacher",3=>"respond",4=>"admin");
				$main_page=$access_array[$_SESSION["status"]];
			}
			//echo $main_page;
			echo $this->view->render('404.html',array("profile"=>$main_page));
		}
	}
	private function addLogAutorization(){
		$name = "log/".date("H")."-".date("d-m-y").".txt";
		$type="";
		$headers = getallheaders();
		if(isset($headers["x-auth-token"])){
			$type="app | token=".$headers["x-auth-token"];
		}
		else if(isset($_SESSION["type_user"])){
			$type="site";
		}
		$param=array();
		$param=parent::getDate();
		if(count($param)==0){
			$param=parent::getFormData();
		}
		if(count($param)==0 && count($_POST)>0){
			$param=$_POST;
		}
		$string_param="";
		if(count($param)>0){
			foreach ($param as $key => $value) {
				$string_param.=$key."= ".$value." ";
			}
		}
		else{
			$string_param="Параметров нет";
		}
		//если файла нету... тогда
			$fp = fopen($name, "a+"); // ("r" - считывать "w" - создавать "a" - добовлять к тексту), мы создаем файл
			$string=date("d-m H:i:s")." ".$_SERVER['REQUEST_URI']." | ".$type." | ".$_SERVER['REQUEST_METHOD']." | \r\n";
			$string.="Параметры: | ".$string_param." |";
			$string.="...............................................................................................................................\r\n";
			fwrite($fp, $string);
			fclose ($fp);
		//fopen("/log/".$name.".txt", "a+"); 
	}
}
?>