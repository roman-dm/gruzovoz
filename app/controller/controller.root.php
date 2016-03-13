<?php
	Class Root extends base{
		public $profile;
		public $user_list;
		public $user_mas=array();
		public function __construct(){
			parent::__construct();
			$this->profile=parent::getProfile();
			if(!parent::accessPage("root")){
				echo $this->view->render('denied.html');
				exit();
			};
			$this->model=new User();
			$this->user_list=$this->model->getUser(array("query_type"=>"user_list"));
			foreach ($this->user_list as $user) {
				$this->user_mas[$user["iStatusId"]]["name_group"]=$user["sStatusName"];
				$this->user_mas[$user["iStatusId"]]["name_group_russia"]=$user["sStatusNameRussia"];
				$this->user_mas[$user["iStatusId"]]["group"][]=$user;
			}
		}
		public function main(){
			$this->model=new News();
			$news_mass=$this->model->getNews(10,$_SESSION['id']);
			//echo "<pre>"; print_r($news_mass); echo "</pre>";
			echo $this->view->render('root.html',array('profile'=>$this->profile[0],"news_mass"=>$news_mass,"user_list"=>$this->user_mas));
		
			//print_r($this->profile[0]);
			//echo "<pre>"; print_r($this->user_mas); echo "</pre>";

		}
		public function ten(){
			echo $this->view->render('ten.html',array('profile'=>$this->profile[0],"user_list"=>$this->user_mas));
		}
		public function tasks(){
			$tasks=array();
			$this->modelTask=new Task();
			$tasks["all"]=$this->modelTask->getTaskAllMonth($_SESSION["id"]);
			$tasks["actual"]=$this->modelTask->getTaskActual($_SESSION["id"]);
			$tasks["success"]=$this->modelTask->getTaskSuccesed($_SESSION["id"]);
			$tasks["nosuccess"]=$this->modelTask->getTaskNoSuccesed($_SESSION["id"]);
			echo $this->view->render('tasks.html',array('profile'=>$this->profile[0],"user_list"=>$this->user_mas,"tasks"=>$tasks));
		//echo "<pre>"; print_r($tasks); echo "</pre>";
		}
		public function events(){
			$events=array();
			$this->model=new Event();
			$events["all"]=$this->model->getEventAllMonth($_SESSION["id"]);
			$events["actual"]=$this->model->getEventActual($_SESSION["id"]);
			$events["noactual"]=$this->model->getEventNoActual($_SESSION["id"]);
			echo $this->view->render('events.html',array('profile'=>$this->profile[0],"user_list"=>$this->user_mas,"events"=>$events));
			//echo "<pre>"; print_r($events); echo "</pre>";
		}
		public function news(){
			echo $this->view->render('news.html',array('profile'=>$this->profile[0],"user_list"=>$this->user_mas));
		}
		public function info(){
			echo $this->view->render('info.html',array('profile'=>$this->profile[0],"user_list"=>$this->user_mas));
		}
		public function detailnews(){
			echo parent::accessEventTaskNews("news",$_SESSION["id"],$_GET["id_news"]);
			$this->model=new News();
			$this->modelUser=new User();
			$mass_news=$this->model->getOneNews($_GET["id_news"]);
			$mass_news[0]["creater"]=$this->modelUser->getFio($mass_news[0]["iNewsUserCreate"]);
			$mass_news[0]["files"]=$this->model->getFilesInNews($_GET["id_news"]);
			//echo "<pre>"; print_r($mass_news); echo "</pre>";
			echo $this->view->render('detailnews.html',array('profile'=>$this->profile[0],"news_info"=>$mass_news));
		}
		public function detailtask(){
			$this->model=new Task();
			$this->modelUser=new User();
			$mass_task=$this->model->getOneTask($_GET["id_task"]);
			$mass_task[0]["who_see"]=$this->model->getUserWhoSeeTask($_GET["id_task"]);
			$mass_task[0]["respond"]=$this->modelUser->getFio($mass_task[0]["iTaskUserIdOt"]);
			$mass_task[0]["creater"]=$this->modelUser->getFio($mass_task[0]["iTaskUserCreate"]);
			$mass_task[0]["visa"]=$this->modelUser->getFio(abs($mass_task[0]["iVisaTask"]));
			$mass_task[0]["files"]=$this->model->getFilesInTask($_GET["id_task"]);
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
			$mass_event[0]["files"]=$this->model->getFilesInEvent($_GET["id_event"]);
			//echo "<pre>"; print_r($mass_event[0]); echo "</pre>";
			echo $this->view->render('detailevent.html',array('profile'=>$this->profile[0],"event_info"=>$mass_event[0]));
		}
	}