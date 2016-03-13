<?php

	class Event extends DB_Connect{
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
		public function updateAddEvent($event){
				print_r($event);
				$query = $this->db->prepare("INSERT INTO events (sEventName, iEventUserIdOt,sEventDesc,dEventDateStart,dEventDateEnd,iEventUserCreate,iVisaEvent) VALUES (:sEventName, :iEventUserIdOt,:sEventDesc,:dEventDateStart,:dEventDateEnd,:iEventUserCreate,:iVisaEvent)");
				$query->bindParam('sEventName', $event["sEventName"]);
				$query->bindParam('sEventDesc', $event["sEventDesc"]);
				$query->bindParam('dEventDateStart', $event["dEventDateStart"]);
				$query->bindParam('dEventDateEnd', $event["dEventDateEnd"]);
				$query->bindParam('iEventUserIdOt', $event["iEventUserIdOt"]);
				$query->bindParam('iEventUserCreate', $event["iEventUserCreate"]);
				$query->bindParam('iVisaEvent', $event["visa"]);		
				$query->execute();	
				return $this->db->lastInsertId();
		}
		public function addUserEvent($lastinsertId,$mass){
			$query = $this->db->prepare("INSERT INTO events_users (iEventId, iUserId) VALUES (:iEventId, :iUserId)");
			foreach ($mass as $value) {
				$query->bindParam('iEventId', $lastinsertId);
				$query->bindParam('iUserId', $value);
				$query->execute();
			}	
		}
		public function getEventInMonth($month,$year,$iUserId){
			$query = $this->db->prepare("SELECT events.iEventUserIdOt,events.sEventName,events.dEventDateStart,events.dEventDateEnd,events.iEventId FROM events_users, events WHERE events_users.iEventId=events.iEventId and ((month(events.dEventDateStart)=:month AND year(events.dEventDateStart)=:year) or month(events.dEventDateEnd)=:month AND year(events.dEventDateEnd)=:year) AND events_users.iUserId=:iUserId");
			$query->bindParam('month', $month);
			$query->bindParam('year', $year);
			$query->bindParam('iUserId', $iUserId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getEvents($query_type,$iUserId){
			$sql='SELECT ';
			switch ($query_type) {
				case 10:
					$rows='iVisaEvent,events.iEventUserIdOt,events.sEventName,events.dEventDateStart,events.dEventDateEnd,events.iEventId,users.sUserName, users.sUserSecondName, users.sUserThirdName  ';
					$tables='events_users, events,users ';
					$dop='WHERE events_users.iEventId=events.iEventId AND events_users.iUserId=:iUserId and events.iEventUserCreate=users.iUserId and events.dEventDateEnd >= CURDATE() ORDER BY events.dEventDateEnd asc LIMIT 0, 10';
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
		public function getOneEvent($iEventId){
			$query = $this->db->prepare("SELECT * FROM events WHERE events.iEventId=:iEventId");
			$query->bindParam('iEventId', $iEventId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getUserWhoSeeEvent($iEventId){
			$query = $this->db->prepare("SELECT users.sUserName,users.sUserSecondName, users.sUserThirdName FROM events_users,users WHERE events_users.iEventId=:iEventId and events_users.iUserId=users.iUserId");
			$query->bindParam('iEventId', $iEventId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}	
		public function addVisaEvent($iEventId, $iVisaEvent){
			$query = $this->db->prepare("UPDATE events SET iVisaEvent=:iVisaEvent WHERE iEventId=:iEventId");
			$query->bindParam('iVisaEvent', $iVisaEvent);
			$query->bindParam('iEventId', $iEventId);
			$query->execute();
		}
		public function deleteEvent($iEventId){
			echo $iEventId;
			$query = $this->db->prepare("DELETE FROM events WHERE iEventId=:iEventId");
			$query->execute(array(":iEventId"=>$iEventId));		
		}
		public function getTasksWhereEvent($query_type,$iUserId){
			$sql='SELECT ';
			switch ($query_type) {
				case 10:
					$rows='sEventName,dEventDateEnd,iEventId ';
					$tables='events ';
					$dop='WHERE iEventUserCreate=:iUserId and dEventDateEnd >= CURDATE()  ORDER BY dEventDateEnd asc';
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
		public function addFilesInEvent($lastinsertId,$mass){
			$query = $this->db->prepare("INSERT INTO events_files (iEventId, sEventFilesName,sEventFilesPath) VALUES (:iEventId, :sEventFilesName, :sEventFilesPath)");
			foreach ($mass as $value) {
				echo"<pre>"; print_r($value->fold); echo"</pre>";
				$query->bindParam('iEventId', $lastinsertId);
				$query->bindParam('sEventFilesName', $value->name);
				$query->bindParam('sEventFilesPath', $value->fold);
				$query->execute();
			}	
		}
		public function getFilesInEvent($iEventId){
			$query = $this->db->prepare("SELECT sEventFilesName, sEventFilesPath FROM events_files WHERE events_files.iEventId=:iEventId");
			$query->bindParam('iEventId', $iEventId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getEventAllMonth($iUserId){
			$query = $this->db->prepare("SELECT events.iVisaEvent,events.iEventUserIdOt,events.sEventName,events.dEventDateStart,events.dEventDateEnd,events.iEventId,events.iEventUserCreate,users.sUserName,users.sUserSecondName,users.sUserThirdName FROM events_users, events, users WHERE events.iEventUserCreate=users.iUserId AND events_users.iEventId=events.iEventId AND events_users.iUserId=:iUserId");
			$query->bindParam('iUserId', $iUserId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getEventActual($iUserId){
			$query = $this->db->prepare("SELECT events.iVisaEvent,events.iEventUserIdOt,events.sEventName,events.dEventDateStart,events.dEventDateEnd,events.iEventId,events.iEventUserCreate,users.sUserName,users.sUserSecondName,users.sUserThirdName FROM events_users, events, users WHERE events.iEventUserCreate=users.iUserId AND events_users.iEventId=events.iEventId AND events_users.iUserId=:iUserId AND events.dEventDateEnd >= CURDATE()");
			$query->bindParam('iUserId', $iUserId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getEventNoActual($iUserId){
			$query = $this->db->prepare("SELECT events.iVisaEvent,events.iEventUserIdOt,events.sEventName,events.dEventDateStart,events.dEventDateEnd,events.iEventId,events.iEventUserCreate,users.sUserName,users.sUserSecondName,users.sUserThirdName FROM events_users, events, users WHERE events.iEventUserCreate=users.iUserId AND events_users.iEventId=events.iEventId AND events_users.iUserId=:iUserId AND events.dEventDateEnd < CURDATE()");
			$query->bindParam('iUserId', $iUserId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
	}
?>