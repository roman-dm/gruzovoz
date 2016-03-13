<?php
	class user extends DB_Connect{
		public function __construct($dbo=NULL, $useDate=NULL){
			parent::__construct($dbo);
		}
		public function checkUser($sUserLogin,$sUserPass){
			$query = $this->db->prepare('SELECT iUserId, iUserStatus FROM users WHERE sUserLogin=:sUserLogin and sUserPass=:sUserPass');
			//echo 'yes';
			$query->bindParam('sUserLogin', $sUserLogin);
			$query->bindParam('sUserPass', $sUserPass);
			$query->execute();
			return $query->fetchAll();
		}
		public function loginUser($iUserId,$sUserLogin,$sStatus){
			$_SESSION['login']=$sUserLogin;
			$_SESSION['id']=$iUserId;
			$_SESSION['status']=$sStatus;
		}
		public function getMainPage($iUserId){
			$query = $this->db->prepare('SELECT status.sMainPage FROM users,status WHERE iUserId=:iUserId and users.iUserStatus=status.iStatusId');
			//echo 'yes';
			$query->bindParam('iUserId', $iUserId);
			$query->execute();
			return $query->fetchColumn();
		}
		public function getUser($users){
			$sql='SELECT ';
			switch ($users['query_type']) {
				case 'user_list':
					$rows='status.sStatusNameRussia, users.sUserSecondName,users.sUserName,users.iUserId,status.sStatusName, status.iStatusId,users.sUserThirdName  ';
					$tables='users LEFT JOIN status ON users.iUserStatus = status.iStatusId ';
					$dop='ORDER BY users.iUserStatus asc';
					break;
				
				default:
					# code...
					break;
			}
			$query = $this->db->prepare($sql.$rows.'FROM '.$tables.$dop);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getFio($iUserId){
			$query = $this->db->prepare("SELECT  users.sUserName, users.sUserSecondName, users.sUserThirdName FROM users WHERE users.iUserId=:iUserId");
			$query->bindParam('iUserId', $iUserId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getOneUser($type_user, $iUserId){
			$sql='SELECT ';
			switch ($type_user) {
				case 'profile':
					$rows='status.sMainPage, users.iUserId, users.sUserName, users.sUserSecondName, users.sUserLogin,users.sUserThirdName ';
					$tables='users, status ';
					$dop='WHERE iUserId=:iUserId and status.iStatusId=users.iUserStatus';
				break;
				}
				$query = $this->db->prepare($sql.$rows.'FROM '.$tables.$dop);
				$query->bindParam('iUserId', $iUserId);
				$query->execute();
				return $query->fetchAll(PDO::FETCH_ASSOC);
		}
	}