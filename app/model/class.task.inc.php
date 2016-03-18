<?php

	class Task extends DB_Connect{
		private $_useDate;
		private $_m;
		private $_y;
		private $_daysInMonth;
		private $_startDay;
		public function __construct($dbo=NULL, $useDate=NULL){
			parent::__construct($dbo);
			if(isset($useDate)){
				$this->_useDate=$useDate;
			}
			else {
				$this->_useDate=date("Y-m-d H:i:s");
			}
			$ts=strtotime($this->_useDate);
			$this->_m=date("m",$ts);
			$this->_y=date("Y",$ts);
			$this->_daysInMonth=cal_days_in_month(CAL_GREGORIAN, $this->_m, $this->_m);
			$ts=mktime(0,0,0,$this->_m,$this->_y);
			$this->_startDay=date("w",$ts);
		}
		public function updateAddTask($event){
				$query = $this->db->prepare("INSERT INTO tasks (sTaskName,sTaskDesc,dTaskDateStart,dTaskDateEnd,iTaskUserCreate,iTaskUserIdOt) VALUES (:sTaskName,:sTaskDesc,:dTaskDateStart,:dTaskDateEnd,:iTaskUserCreate,:iTaskUserIdOt)");
				$query->bindParam('sTaskName', $event["sTaskName"]);
				$query->bindParam('sTaskDesc', $event["sTaskDesc"]);
				$query->bindParam('dTaskDateStart', $event["dTaskDateStart"]);
				$query->bindParam('dTaskDateEnd', $event["dTaskDateEnd"]);
				$query->bindParam('iTaskUserCreate', $event["iTaskUserCreate"]);	
				$query->bindParam('iTaskUserIdOt', $event["iTaskUserIdOt"]);		
				$query->execute();	
				return $this->db->lastInsertId();
		}
		public function addUserTask($lastinsertId,$mass){
			$query = $this->db->prepare("INSERT INTO tasks_users (iTaskId, iUserId) VALUES (:iTaskId, :iUserId)");
			foreach ($mass as $value) {
				$query->bindParam('iTaskId', $lastinsertId);
				$query->bindParam('iUserId', $value);
				$query->execute();
			}	
		}
		public function getTaskNoSuccesed($iUserId){
			$query = $this->db->prepare("SELECT tasks.iVisaTask,tasks.iTaskUserIdOt,tasks.sTaskName,tasks.dTaskDateStart,tasks.dTaskDateEnd,tasks.iTaskId,users.sUserName,users.sUserSecondName,users.sUserThirdName FROM tasks_users, tasks, users WHERE  tasks.iTaskUserCreate=users.iUserId AND tasks_users.iTaskId=tasks.iTaskId AND tasks_users.iUserId=:iUserId AND tasks.iStateTask=0 AND tasks.dTaskDateEnd < CURDATE()");
			$query->bindParam('iUserId', $iUserId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getTaskSuccesed($iUserId){
			$query = $this->db->prepare("SELECT tasks.iVisaTask,tasks.iTaskUserIdOt,tasks.sTaskName,tasks.dTaskDateStart,tasks.dTaskDateEnd,tasks.iTaskId,users.sUserName,users.sUserSecondName,users.sUserThirdName FROM tasks_users, tasks, users WHERE tasks.iTaskUserCreate=users.iUserId AND  tasks_users.iTaskId=tasks.iTaskId AND tasks_users.iUserId=:iUserId AND tasks.iStateTask=1");
			$query->bindParam('iUserId', $iUserId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getTaskActual($iUserId){
			$query = $this->db->prepare("SELECT tasks.iVisaTask,tasks.iTaskUserIdOt,tasks.sTaskName,tasks.dTaskDateStart,tasks.dTaskDateEnd,tasks.iTaskId,users.sUserName,users.sUserSecondName,users.sUserThirdName FROM tasks_users, tasks, users WHERE  tasks.iTaskUserCreate=users.iUserId AND  tasks_users.iTaskId=tasks.iTaskId AND tasks_users.iUserId=:iUserId AND tasks.dTaskDateEnd >= CURDATE() AND tasks.iStateTask=0");
			$query->bindParam('iUserId', $iUserId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getTaskAllMonth($iUserId){
			$query = $this->db->prepare("SELECT tasks.iVisaTask,tasks.iTaskUserIdOt,tasks.sTaskName,tasks.dTaskDateStart,tasks.dTaskDateEnd,tasks.iTaskId,tasks.iTaskUserCreate,users.sUserName,users.sUserSecondName,users.sUserThirdName FROM tasks_users, tasks, users WHERE tasks.iTaskUserCreate=users.iUserId AND tasks_users.iTaskId=tasks.iTaskId AND tasks_users.iUserId=:iUserId");
			$query->bindParam('iUserId', $iUserId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getTaskInMonth($month,$year,$iUserId){
			$query = $this->db->prepare("SELECT tasks.iTaskUserIdOt,tasks.sTaskName,tasks.dTaskDateStart,tasks.dTaskDateEnd,tasks.iTaskId FROM tasks_users, tasks WHERE tasks_users.iTaskId=tasks.iTaskId and ((month(tasks.dTaskDateStart)=:month AND year(tasks.dTaskDateStart)=:year) or month(tasks.dTaskDateEnd)=:month AND year(tasks.dTaskDateEnd)=:year) AND tasks_users.iUserId=:iUserId AND tasks.iStateTask=0");
			$query->bindParam('month', $month);
			$query->bindParam('year', $year);
			$query->bindParam('iUserId', $iUserId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getTasksWhereCreate($query_type,$iUserId){
			$sql='SELECT ';
			switch ($query_type) {
				case 10:
					$rows='sTaskName,dTaskDateEnd,iTaskId ';
					$tables='tasks ';
					$dop='WHERE iTaskUserCreate=:iUserId and dTaskDateEnd >= CURDATE()  ORDER BY dTaskDateEnd asc';
					break;
				case "all":
					$rows='sTaskName,dTaskDateEnd,iTaskId ';
					$tables='tasks ';
					$dop='WHERE iTaskUserCreate=:iUserId and dTaskDateEnd >= CURDATE()  ORDER BY dTaskDateEnd asc';
					break;
				default:
					# code...
					break;
			}
			$query = $this->db->prepare($sql.$rows.'FROM '.$tables.$dop);
			$query->bindParam('iUserId', $iUserId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getTasks($query_type,$iUserId){
			$sql='SELECT ';
			switch ($query_type) {
				case 10:
					$rows='tasks.iVisaTask,tasks.iTaskUserIdOt,tasks.sTaskName,tasks.dTaskDateStart,tasks.dTaskDateEnd,tasks.iTaskId,users.sUserName, users.sUserSecondName, users.sUserThirdName  ';
					$tables='tasks_users, tasks,users ';
					$dop='WHERE tasks.iStateTask=0 AND tasks_users.iTaskId=tasks.iTaskId AND tasks_users.iUserId=:iUserId and tasks.iTaskUserCreate=users.iUserId and tasks.dTaskDateEnd >= CURDATE()  ORDER BY tasks.dTaskDateEnd asc LIMIT 0, 10';
					break;
				case "all":
					$rows='tasks.iVisaTask,tasks.iTaskUserIdOt,tasks.sTaskName,tasks.dTaskDateStart,tasks.dTaskDateEnd,tasks.iTaskId,users.sUserName, users.sUserSecondName, users.sUserThirdName  ';
					$tables='tasks_users, tasks,users ';
					$dop='WHERE tasks_users.iTaskId=tasks.iTaskId AND tasks_users.iUserId=:iUserId and tasks.iTaskUserCreate=users.iUserId ORDER BY tasks.dTaskDateEnd';
					break;
				default:
					# code...
					break;
			}
			$query = $this->db->prepare($sql.$rows.'FROM '.$tables.$dop);
			$query->bindParam('iUserId', $iUserId);
			$query->execute();
			//var_dump($query);
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getOneTask($iTaskId){
			$query = $this->db->prepare("SELECT * FROM tasks WHERE tasks.iTaskId=:iTaskId");
			$query->bindParam('iTaskId', $iTaskId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getUserWhoSeeTask($iTaskId){
			$query = $this->db->prepare("SELECT users.sUserName,users.sUserSecondName, users.sUserThirdName FROM tasks_users,users WHERE tasks_users.iTaskId=:iTaskId and tasks_users.iUserId=users.iUserId");
			$query->bindParam('iTaskId', $iTaskId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function addVisaTask($iTaskId, $iVisaTask){
			$query = $this->db->prepare("UPDATE tasks SET iVisaTask=:iVisaTask WHERE iTaskId=:iTaskId");
			$query->bindParam('iVisaTask', $iVisaTask);
			$query->bindParam('iTaskId', $iTaskId);
			$query->execute();
		}
		public function deleteTask($iTaskId){
			$query = $this->db->prepare("DELETE FROM tasks WHERE iTaskId=:iTaskId");
			$query->execute(array(":iTaskId"=>$iTaskId));		
		}
		public function addCompleteTask($iTaskId){
			$query = $this->db->prepare("UPDATE tasks SET iStateTask=1 WHERE iTaskId=:iTaskId");
			$query->bindParam('iTaskId', $iTaskId);
			$query->execute();
		}
		public function addFilesInTask($lastinsertId,$mass){
			$query = $this->db->prepare("INSERT INTO tasks_files (iTasksId, sTaskFilesName,sTaskFilesPath) VALUES (:iTasksId, :sTaskFilesName, :sTaskFilesPath)");
			foreach ($mass as $value) {
				$query->bindParam('iTasksId', $lastinsertId);
				$query->bindParam('sTaskFilesName', $value->name);
				$query->bindParam('sTaskFilesPath', $value->fold);
				$query->execute();
			}	
		}
		public function getFilesInTask($iTasksId){
			$query = $this->db->prepare("SELECT sTaskFilesName, sTaskFilesPath FROM tasks_files WHERE tasks_files.iTasksId=:iTasksId");
			$query->bindParam('iTasksId', $iTasksId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
	}
?>