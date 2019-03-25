<?php
	class ajax{

		public function main(){
			//parent::__construct($dbo);
			$this->$_POST["type_request"]();
		}
		public function login_user(){
			$this->model=new User();
			$this->userName=$_POST["email_user"];
			$this->userPass=$this->md($_POST["pass_user"]);
			$this->id_user=$this->model->checkUser($this->userName,$this->userPass);
			if(is_numeric($this->id_user[0]["iUserId"])){
				$this->model->loginUser($this->id_user[0]["iUserId"],$this->userName,$this->id_user[0]["iUserStatus"] );
				echo json_encode(array("result"=>"true","page"=>$this->model->getMainPage($this->id_user[0]["iUserId"])));
			}
			else{
				echo "false";
			}
			
		}
		private function md($pass){
			return md5($pass);
		}
		public function processTask(){
			if(isset($_POST["id_task"])) {
				if(!is_numeric($_POST["id_task"])){
					echo json_encode(array("result"=>"false"));
					exit();
				}
			}
			else{
				$mass=json_decode($_POST["aUsers"]);
				if (!in_array($_POST["iTaskUserIdOt"], $mass)) {
			   		$mass[]=$_POST["iTaskUserIdOt"];
				}
				$this->model=new Task();
				$lastinsertId=$this->model->updateAddTask($_POST);
				$this->model->addUserTask($lastinsertId,$mass);
				if($_SESSION["status"]==1){
					$this->model->addVisaTask($lastinsertId, $_SESSION["id"]);
				}
				$mass_files=json_decode($_POST["fFiles"]);
				if(count($mass_files)){
					$this->model->addFilesInTask($lastinsertId,$mass_files);
				}
				echo json_encode(array("id_task"=>$lastinsertId));
			}
		}
		public function processEvent(){
			if(isset($_POST["id_event"])) {
				if(!is_numeric($_POST["id_event"])){
					echo json_encode(array("result"=>"false"));
					exit();
				}
			}
			else{
				$mass=json_decode($_POST["aUsers"]);
				if (!in_array($_POST["iEventUserIdOt"], $mass)) {
			 	  $mass[]=$_POST["iEventUserIdOt"];
				}
				$this->model=new Event();
				if($_SESSION["status"]==1){
					$_POST["visa"]=$_SESSION["id"];
				}
				else{$_POST["visa"]=0;}
				$lastinsertId=$this->model->updateAddEvent($_POST);
				$this->model->addUserEvent($lastinsertId,$mass);
				if($_SESSION["status"]==1){
					$this->model->addVisaEvent($lastinsertId, $_SESSION["id"]);
				}
				$mass_files=json_decode($_POST["fFiles"]);
				if(count($mass_files)){
					$this->model->addFilesInEvent($lastinsertId,$mass_files);
				}
				echo json_encode(array("id_event"=>$lastinsertId));
			}
		}
		public function getUser(){
				$this->model=new User();
				echo json_encode($this->model->getUser($_POST));
		}
		//Запрос календаря
		public function getCurDate(){
			$this->modelTask=new Task();
			$this->modelEvent=new Event();
			$mass_task=$this->modelTask->getTaskInMonth($_POST["month"],$_POST["year"],$_POST["id_user"]);
			foreach ($mass_task as &$value) {
				$value["type"]="task";
			}
			$mass_event=$this->modelEvent->getEventInMonth($_POST["month"],$_POST["year"],$_POST["id_user"]);
			foreach ($mass_event as &$value) {
				$value["type"]="event";
			}
			$two_mass=array_merge_recursive ($this->addDateArray($mass_event,"event"),$this->addDateArray($mass_task,"task"));
			//print_r($this->addDateArray($mass_task,"task"));
			foreach ($two_mass as $key => $value) {
				//echo "<pre>"; echo $value["dCurDate"]."---".date("Y-m-t");echo "</pre>";
				$cur_mon=(int)substr($value["dCurDate"], 5, 2);
				//echo $cur_mon;
				if($cur_mon!=$_POST["month"]){
					unset($two_mass[$key]);
				}
			}
			//echo "<pre>"; print_r($two_mass); echo "</pre>";
			sort($two_mass);
			echo json_encode($two_mass);
		}
		//Запрос ленты
		public function getListEventTask(){
			$this->modelTask=new Task();
			$this->modelEvent=new Event();
			$this->modelUser=new User();
			$mass_task=$this->modelTask->getTasks(10,$_POST["iUserId"]);
			$noactualtask=$this->modelTask->getTaskNoSuccesed($_POST["iUserId"],"all");
			foreach ($noactualtask as &$value) {
				if($value["iVisaTask"]!=0){
					$value["viza"]=$this->modelUser->getFio(abs($value["iVisaTask"]));
					$value["iVisa"]=$value["iVisaTask"];
				}
			}
			foreach ($mass_task as &$value) {
				$value["viza"][0]="";
				$value["type"]="task";
				if($value["iVisaTask"]!=0){
					$value["viza"]=$this->modelUser->getFio(abs($value["iVisaTask"]));
					$value["iVisa"]=$value["iVisaTask"];
				}
			}
			$mass_event=$this->modelEvent->getEvents(10,$_POST["iUserId"]);
			foreach ($mass_event as &$value) {
				$value["viza"][0]="";
				$value["type"]="event";
				if($value["iVisaEvent"]!=0){
					$value["viza"]=$this->modelUser->getFio(abs($value["iVisaEvent"]));
					$value["iVisa"]=$value["iVisaEvent"];
				}
			}
			$result_array=array_merge_recursive($mass_task,$mass_event);
			$p=array();
			foreach ($result_array as &$res) {
				if(isset($res["dEventDateEnd"])){
					$res["dDate"]=strtotime($res["dEventDateEnd"]);
					$p[]=strtotime($res["dEventDateEnd"]);
					$res["iVisa"]=$res["iVisaEvent"];
					unset($res["iVisaEvent"]);
				}
				else{
					$res["dDate"]=strtotime($res["dTaskDateEnd"]);
					$p[]=strtotime($res["dTaskDateEnd"]);
					$res["iVisa"]=$res["iVisaTask"];
					unset($res["iVisaTask"]);
				}
			}
			// echo "<pre>";
			// print_r($result_array);
			// echo "</pre>";
			if(count($result_array)>0){
				array_multisort($p, SORT_NUMERIC, $result_array);	
			}
			echo json_encode(array("all"=>$result_array,"tasks"=>$mass_task,"events"=>$mass_event,"noactualtask"=>$noactualtask));
		}
		private function addDateArray($mass, $type){
			$result=array();
			$value["type"]=$type;
			foreach ($mass as $value) {
				if($type=="event"){
					$begin = new DateTime( $value["dEventDateStart"] );
					$end = new DateTime( $value["dEventDateEnd"] );
				}
				else{
					$begin = new DateTime( $value["dTaskDateStart"] );
					$end = new DateTime( $value["dTaskDateEnd"] );
				}
				$begin = $begin->modify( '+1 day' ); 

				$interval = new DateInterval('P1D');
				$daterange = new DatePeriod($begin, $interval ,$end);

				foreach($daterange as $date){
					if($type=="event"){
				    	$result[]=array("type"=>$type,"iEventUserIdOt"=>$value["iEventUserIdOt"],"sEventName"=>$value["sEventName"],"dCurDate"=>$date->format("Y-m-d"),"iEventId"=>$value["iEventId"]);
					}
					else{
				    	$result[]=array("type"=>$type,"iTaskUserIdOt"=>$value["iTaskUserIdOt"],"sTaskName"=>$value["sTaskName"],"dCurDate"=>$date->format("Y-m-d"),"iTaskId"=>$value["iTaskId"]);						
					}
				}
				if($type=="event"){
					if($value["dEventDateStart"]==$value["dEventDateEnd"]){
						$value["type"]=$type;
						$value["dCurDate"]=$value["dEventDateStart"];
						$result[]=$value;
					}
					else{
						$value["dCurDate"]=$value["dEventDateStart"];
						unset($value["dEventDateStart"]);
						$result[]=$value;
						$value["dCurDate"]=$value["dEventDateEnd"];
						unset($value["dEventDateEnd"]);
						$result[]=$value;
					}
				}
				else{
					if($value["dTaskDateStart"]==$value["dTaskDateEnd"]){
						$value["dCurDate"]=$value["dTaskDateStart"];
						$result[]=$value;
				}
				else{
						$value["dCurDate"]=$value["dTaskDateStart"];
						unset($value["dTaskDateStart"]);
						$result[]=$value;
						$value["dCurDate"]=$value["dTaskDateEnd"];
						unset($value["dTaskDateEnd"]);
						$result[]=$value;
				}	
				}
			}

			//echo "<pre>";
			//print_r($result);
			//echo "</pre>";
			return $result;
		}
		//загрузка файла
		public function uploadFile(){
			$allowed = array('png', 'jpg', 'gif','zip','doc','docx','txt','pdf','xlsx','xls','docs','rar');
			//print_r($_FILES);
			if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){

				$extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);
				if(!in_array(strtolower($extension), $allowed)){
					echo '{"status":"error"}';
					exit;
				}
				//print_r($_FILES['upl']['tmp_name'], 'uploads_folder/')
				$sult="lyc.alexkam";
				$hash=md5($sult.time());
				$new_name='upload_folder/'.$hash;
				mkdir($new_name, 0777);
				if(move_uploaded_file($_FILES['upl']['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/".$new_name."/".$_FILES['upl']['name'])){
					$arr=array("status"=>"success","fold"=>$hash,"name"=>$_FILES['upl']['name']);
					echo json_encode($arr);
					exit;
				}
			}
			echo '{"status":"error"}';
			exit;
		}
		public function addVisaTask(){
			$this->model=new Task();
			$this->model->addVisaTask($_POST["idtask"], $_POST["idUser"]);
		}
		public function addVisaEvent(){
			$this->model=new Event();
			$this->model->addVisaEvent($_POST["idevent"], $_POST["idUser"]);
		}
		public function processNews(){
			$mass=json_decode($_POST["aUsers"]);
			$this->model=new News();
			$lastinsertId=$this->model->updateAddNews($_POST);
			$this->model->addUserNews($lastinsertId,$mass);
			$mass_files=json_decode($_POST["fFiles"]);
			if(count($mass_files)>0){
				$this->model->addFilesInNews($lastinsertId,$mass_files);
			}
		}
		public function delete_item(){
			$types = explode("-", $_POST["typeiditem"]);
			print_r($types);
			if($types[0]=="task"){
				$this->modelTask=new Task();
				$this->modelTask->deleteTask($types[1]);
			}
			else if($types[0]="event"){
				$this->modelEvent=new Event();
				$this->modelEvent->deleteEvent($types[1]);
			}
		}
		public function get_one_task(){
			$this->modelTask=new Task();
			$mass_task=$this->modelTask->getOneTask($_POST["idTask"]);
			$mass_task[0]["who_see"]=$this->modelTask->getUserWhoSeeTask($_POST["idTask"]);
			echo json_encode($mass_task);
		}
		public function get_one_event(){
			$this->modelEvent=new Event();
			$mass_event=$this->modelEvent->getOneEvent($_POST["idEvent"]);
			$mass_event[0]["who_see"]=$this->modelEvent->getUserWhoSeeEvent($_POST["idEvent"]);
			echo json_encode($mass_event);
		}
		public function getListEventTaskUser(){
			$this->modelTask=new Task();
			$this->modelEvent=new Event();
			$this->modelUser=new User();
			$mass_task=$this->modelTask->getTasksWhereCreate(10,$_POST["iUserId"]);
			$mass_event=$this->modelEvent->getTasksWhereEvent(10,$_POST["iUserId"]);
			foreach ($mass_event as &$value) {
				$value["type"]="event";
			}
			foreach ($mass_task as &$value) {
				$value["type"]="task";	
			}
			$result_array=array_merge_recursive($mass_task,$mass_event);
			foreach ($result_array as &$res) {
				if(isset($res["dEventDateEnd"])){
					$res["dDate"]=strtotime($res["dEventDateEnd"]);
					$p[]=strtotime($res["dEventDateEnd"]);
				}
				else{
					$res["dDate"]=strtotime($res["dTaskDateEnd"]);
					$p[]=strtotime($res["dTaskDateEnd"]);
				}
			}
			if(count($result_array)>0){
				array_multisort($p, SORT_NUMERIC, $result_array);
			}
			echo json_encode(array("all"=>$result_array,"tasks"=>$mass_task,"events"=>$mass_event));
		}
		public function taskDone(){
			$this->model=new Task();
			$this->model->addCompleteTask($_POST["idTask"]);
		}
		//Редактирование новостей-задач-событий
		public function update_oneten(){
			$edit_mass=array();
			switch ($_POST["type"]) {
				case 'event':
					$this->model=new Event();
					$properties=json_decode($_POST["list"]);
					if((isset($properties->ot)) && is_numeric($properties->ot)){
						$edit_mass["iEventUserIdOt"]=$properties->ot;
					}
					if((isset($properties->dataend)) && (!empty($properties->dataend))){
						$edit_mass["dEventDateEnd"]=$properties->dataend;
					}
					if((isset($properties->name)) && !empty($properties->name)){
						$edit_mass["sEventName"]=$properties->name;
					}
					if((isset($properties->desc)) && !empty($properties->desc)){
						$edit_mass["sEventDesc"]=$properties->desc;
					}
					if((isset($properties->user_list)) && !empty($properties->user_list)){
						$this->model->deleteEventUsers($_POST["idTen"]);
						$this->model->addUserEvent($_POST["idTen"],$properties->user_list);
					}
					if(count($edit_mass)>0){
						echo $this->model->editEvent($edit_mass,$_POST["idTen"]);
					}	
					break;

				case 'task':
					$this->model=new Task();
					$properties=json_decode($_POST["list"]);
					if((isset($properties->ot)) && is_numeric($properties->ot)){
						$edit_mass["iTaskUserIdOt"]=$properties->ot;
					}
					if((isset($properties->dataend)) && (!empty($properties->dataend))){
						$edit_mass["dTaskDateEnd"]=$properties->dataend;
					}
					if((isset($properties->name)) && !empty($properties->name)){
						$edit_mass["sTaskName"]=$properties->name;
					}
					if((isset($properties->desc)) && !empty($properties->desc)){
						$edit_mass["sTaskDesc"]=$properties->desc;
					}
					if((isset($properties->user_list)) && !empty($properties->user_list)){
						$this->model->deleteTaskUsers($_POST["idTen"]);
						$this->model->addUserTask($_POST["idTen"],$properties->user_list);
					}
					if(count($edit_mass)>0){
						echo $this->model->editTask($edit_mass,$_POST["idTen"]);
					}	
					break;

				case 'news':
					# code...
					break;

				default:
					# code...
					break;
			}
		}
		public function getCountEventTask(){
			$events=array();
			$this->model=new Event();
			$tasks=array();
			$this->modelTask=new Task();
			$events["all"]=$this->model->getEventAllMonth($_SESSION["id"],"count");
			$events["actual"]=$this->model->getEventActual($_SESSION["id"],"count");
			$events["noactual"]=$this->model->getEventNoActual($_SESSION["id"],"count");
			$tasks["all"]=$this->modelTask->getTaskAllMonth($_SESSION["id"],"count");
			$tasks["actual"]=$this->modelTask->getTaskActual($_SESSION["id"],"count");
			$tasks["success"]=$this->modelTask->getTaskSuccesed($_SESSION["id"],"count");
			$tasks["nosuccess"]=$this->modelTask->getTaskNoSuccesed($_SESSION["id"],"count");
			echo json_encode(array("events"=>$events,"tasks"=>$tasks));
		}
		public function addSubject(){
			$white_list=array("physics","mathematics","russian","informatics");
			if(in_array($_POST["subject"],$white_list)){
				$this->model=new Monitoring();
				if($this->model->addSubject($_POST)){
					echo json_encode(array("result"=>"true","subject"=>$_POST["subject"],"value-sub"=>$_POST["value"]));
				}
			}
		}
		public function add_mark(){
			$this->model=new Monitoring();
			$this->model->uploadSub($_POST["id"],$_POST["sub"],$_POST["mark"]);
			echo json_encode(array("result"=>true, "info"=>$this->model->getMonitOneStudent($_POST["id"])));
		}
		public function add_recomend(){
			$this->model=new Monitoring();
			$this->model->uploadRecomend($_POST["id"],$_POST["recomend"]);
			echo json_encode(array("result"=>true, "info"=>$this->model->getMonitOneStudent($_POST["id"])));
		}
	}