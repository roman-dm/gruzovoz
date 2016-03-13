<?php
	Class Admin{
		public function __construct(){
			Twig_Autoloader::register();
			$loader = new Twig_Loader_Filesystem('app/views/');
		 	//'cache'       => 'compilation_cache',
			$this->view = new Twig_Environment($loader, array(
					    'auto_reload' => true
			));
		}
		public function main(){
			echo $this->view->render('admin.html');
		}
	}