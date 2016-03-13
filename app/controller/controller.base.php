<?php
	class base extends DB_Connect{
		public $view;
		public $access;
		public function __construct(){
			Twig_Autoloader::register();
			$loader = new Twig_Loader_Filesystem('app/views/');
		 	//'cache'       => 'compilation_cache',
			$this->view = new Twig_Environment($loader, array(
					    'auto_reload' => true
			));
			if(!$this->getSession()){
				if($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]!=$_SERVER["SERVER_NAME"]."/"){
					$_SESSION["mess"]="<span style='color:red'>Ошибка авторизации, пожалуйста введи логин и пароль</span>";
					header("Location:http://".$_SERVER["SERVER_NAME"]);
				}
			}
			$this->access=new access();
		}
		public function getProfile(){
			//include_once $_SERVER["DOCUMENT_ROOT"]."/app/model/class.user.inc.php";
			$this->model=new user();
			$profile=$this->model->getOneUser("profile", $_SESSION["id"]);
			$pr_routes = explode('/', $_SERVER['REQUEST_URI']);
			$new_arr = array_diff($pr_routes, array(''));
			if(isset($new_arr[2])){
				$profile[0]["currect_page"]=$new_arr[2];
			}
			else{
				$profile[0]["currect_page"]="";
			}
			return $profile;
		}
		public function getSession(){
			if(isset($_SESSION["id"])){
				return true;
			}
			else{
				return false;
			}
		}
		public function logout(){
			$_SESSION["mess"]="<span style='color:green'>Сеанс закончен!</span>";
			header("Location:http://".$_SERVER["SERVER_NAME"]);

		}
		public function accessPage($currentPage){
			$access_array=array(1=>"root",2=>"teacher",3=>"respond",4=>"admin");
			$status=$this->access->getStatus($_SESSION["id"]);
			if($access_array[$status[0]["iUserStatus"]]==$currentPage){
				return true;
			}
			else{
				return false;
			}
		}
	}