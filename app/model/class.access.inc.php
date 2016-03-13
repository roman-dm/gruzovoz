<?php
	class access extends DB_Connect{
		public function __construct($dbo=NULL, $useDate=NULL){
			parent::__construct($dbo);
		}
		public function getStatus($currentId){
			$query = $this->db->prepare('SELECT iUserStatus FROM users WHERE iUserId=:iUserId');
			//echo 'yes';
			$query->bindParam('iUserId', $currentId);
			$query->execute();
			return $query->fetchAll();
		}
		public function accessEventTaskNews($table,$iUserId,$element){
			echo $table." ".$iUserId." ".$element;
			switch ($table) {
				case 'task':
					$query = $this->db->prepare('SELECT * FROM tasks_users WHERE iUserId=:iUserId AND iTaskId=:iTaskId');
					$query->bindParam('iUserId', $iUserId);
					$query->bindParam('iTaskId', $element);
					break;
				case 'event':
					$query = $this->db->prepare('SELECT * FROM events_users WHERE iUserId=:iUserId AND iEventId=:iTaskId');
					$query->bindParam('iUserId', $iUserId);
					$query->bindParam('iEventId', $element);
					break;
				case 'news':
					$query = $this->db->prepare('SELECT * FROM news_users WHERE iUserId=:iUserId AND iNewsId=:iNewsId');
					$query->bindParam('iUserId', $iUserId);
					$query->bindParam('iNewsId', $element);
					break;
			}
			$query->execute();
			var_dump($query);
			return $query->rowCount();
	}
}