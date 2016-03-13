<?php
	class Main extends base{
		public $mess;
		public function __construct(){
			parent::__construct();
			$this->profile=parent::getProfile();
		}
		public function main(){
			$this->mess="";
			if(!empty($_SESSION["mess"])){
				$this->mess=$_SESSION["mess"];
				unset($_SESSION["mess"]);
				session_unregister('mess');
			}
			echo $this->view->render('index.html',array("mess"=>$this->mess));
		}
	}