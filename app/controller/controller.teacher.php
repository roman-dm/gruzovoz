<?php
	Class Teacher extends base{
		public function __construct(){
			parent::__construct();
			$this->profile=parent::getProfile();
			Twig_Autoloader::register();
			$loader = new Twig_Loader_Filesystem('app/views/');
		 	//'cache'       => 'compilation_cache',
			$this->view = new Twig_Environment($loader, array(
					    'auto_reload' => true
			));
			$this->model=new User();
			$this->user_list=$this->model->getUser(array("query_type"=>"user_list"));
			foreach ($this->user_list as $user) {
				$this->user_mas[$user["iStatusId"]]["name_group"]=$user["sStatusName"];
				$this->user_mas[$user["iStatusId"]]["name_group_russia"]=$user["sStatusNameRussia"];
				$this->user_mas[$user["iStatusId"]]["group"][]=$user;
			}
			if(!parent::accessPage("teacher")){
				echo $this->view->render('denied.html');
				exit();
			};
		}
		public function main(){
			echo $this->view->render('teacher.html',array('profile'=>$this->profile[0],"user_list"=>$this->user_mas));
		}
		public function detailtask(){
			$this->model=new Task();
			$this->modelUser=new User();
			$mass_task=$this->model->getOneTask($_GET["id_task"]);
			$mass_task[0]["who_see"]=$this->model->getUserWhoSeeTask($_GET["id_task"]);
			$mass_task[0]["respond"]=$this->modelUser->getFio($mass_task[0]["iTaskUserIdOt"]);
			$mass_task[0]["creater"]=$this->modelUser->getFio($mass_task[0]["iTaskUserCreate"]);
			$mass_task[0]["visa"]=$this->modelUser->getFio(abs($mass_task[0]["iVisaTask"]));
			//echo "<pre>"; print_r($mass_task[0]); echo "</pre>";
			echo $this->view->render('detailtask.html',array('profile'=>$this->profile[0],"task_info"=>$mass_task[0]));
		}
		public function detailevent(){
			$this->model=new Event();
			$this->modelUser=new User();
			$mass_event=$this->model->getOneEvent($_GET["id_event"]);
			$mass_event[0]["who_see"]=$this->model->getUserWhoSeeEvent($_GET["id_event"]);
			$mass_event[0]["respond"]=$this->modelUser->getFio($mass_event[0]["iEventUserIdOt"]);
			$mass_event[0]["creater"]=$this->modelUser->getFio($mass_event[0]["iEventUserCreate"]);
			$mass_event[0]["visa"]=$this->modelUser->getFio(abs($mass_event[0]["iVisaEvent"]));
			//echo "<pre>"; print_r($mass_event[0]); echo "</pre>";
			echo $this->view->render('detailevent.html',array('profile'=>$this->profile[0],"event_info"=>$mass_event[0]));
		}
	}