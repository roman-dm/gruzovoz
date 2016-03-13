<?php

	class News extends DB_Connect{
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
		public function updateAddNews($news){
				$query = $this->db->prepare("INSERT INTO news (sNewsName,sNewsDetail,iNewsUserCreate,dNewsCreate) VALUES (:sNewsName,:sNewsDetail,:iNewsUserCreate,NOW())");
				$query->bindParam('sNewsName', $news["sNewsName"]);
				$query->bindParam('sNewsDetail', $news["sNewsDetail"]);
				$query->bindParam('iNewsUserCreate', $news["iNewsUserCreate"]);	
				$query->execute();	
				return $this->db->lastInsertId();	
		}
		public function addUserNews($lastinsertId,$mass){
			$query = $this->db->prepare("INSERT INTO news_users (iNewsId, iUserId) VALUES (:iNewsId, :iUserId)");
			foreach ($mass as $value) {
				$query->bindParam('iNewsId', $lastinsertId);
				$query->bindParam('iUserId', $value);
				$query->execute();
			}	
		}
		public function getNews($query_type,$iUserId){
			$sql='SELECT ';
			switch ($query_type) {
				case 10:
					$rows='news.sNewsName,news.sNewsDetail, news.iNewsId, news.dNewsCreate ';
					$tables='news, news_users ';
					$dop='WHERE news_users.iNewsId=news.iNewsId AND news_users.iUserId=:iUserId';
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
		public function getOneNews($iNewsId){
			$query = $this->db->prepare("SELECT * FROM news WHERE news.iNewsId=:iNewsId");
			$query->bindParam('iNewsId', $iNewsId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getFilesInNews($iNewsId){
			$query = $this->db->prepare("SELECT sNewsFilesName, sNewsFilesPath FROM news_files WHERE news_files.iNewsId=:iNewsId");
			$query->bindParam('iNewsId', $iNewsId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function addFilesInNews($lastinsertId,$mass){
			$query = $this->db->prepare("INSERT INTO news_files (iNewsId, sNewsFilesName,sNewsFilesPath) VALUES (:iNewsId, :sNewsFilesName, :sNewsFilesPath)");
			foreach ($mass as $value) {
				echo"<pre>"; print_r($value->fold); echo"</pre>";
				$query->bindParam('iNewsId', $lastinsertId);
				$query->bindParam('sNewsFilesName', $value->name);
				$query->bindParam('sNewsFilesPath', $value->fold);
				$query->execute();
			}	
		}
	}
?>