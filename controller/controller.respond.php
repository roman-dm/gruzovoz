<?php
	Class Respond extends base{
		public function __construct(){
			parent::__construct();
			$this->profile=parent::getProfile();
			Twig_Autoloader::register();
			$loader = new Twig_Loader_Filesystem('app/views/');
		 	//'cache'       => 'compilation_cache',
			$this->view = new Twig_Environment($loader, array(
					    'auto_reload' => true
			));
		}
		public function main(){
			echo $this->view->render('respond.html', array('profile'=>$this->profile[0]));
		}
	}