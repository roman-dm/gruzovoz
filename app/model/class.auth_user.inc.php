<?php

	class Auth_User extends DB_Connect{
		private $_useDate;
		private $_m;
		private $_y;
		private $_daysInMonth;
		private $_startDay;
		public function __construct($dbo=NULL, $useDate=NULL){
			parent::__construct($dbo);
		}
		public function get_token($token,$type){
			if($type=="any"){
				$str="sToken=:sToken OR sGuestToken=:sToken";
			}
			elseif($type=="guest"){
				$str="sGuestToken=:sToken";
			}
			elseif($type=="main"){
				$str="sToken=:sToken";
			}
			$query = $this->db->prepare("SELECT * FROM Device WHERE $str");
			$query->bindParam('sToken', $token);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function get_token_guest($token){
			$query = $this->db->prepare("SELECT * FROM Device WHERE sGuestToken=:sGuestToken");
			$query->bindParam('sGuestToken', $token);
			$query->execute();
			//print_r($query->errorInfo());	
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function check_device_id($sDeviceUin){
			$query = $this->db->prepare("SELECT sToken as count FROM Device WHERE sDeviceUin=:sDeviceUin");
			$query->bindParam('sDeviceUin', $sDeviceUin);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function check_device($iDeviceId){
			//echo $iDeviceId;
			$query = $this->db->prepare("SELECT COUNT(*) as count FROM Customer,Driver WHERE Customer.iDeviceId=:iDeviceId OR Driver.iDeviceId=:iDeviceId");
			$query->bindParam('iDeviceId', $iDeviceId[0]["iDeviceId"]);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function get_info_token($uin,$sToken){
			$query = $this->db->prepare("REPLACE INTO Device (sDeviceUin,sGuestToken) VALUES (:uin,:sGuestToken)");
			$query->bindParam('sGuestToken', $sToken);
			$query->bindParam('uin', $uin);	
			//$query->bindParam('TypeUser', $type);	
			$query->execute();
			//print_r($query->errorInfo());	
			return $query->rowCount();
			
		}
		public function addRegistrationSite($sToken,$phone,$type_user,$sCode){
			$query = $this->db->prepare("INSERT INTO Device (sDeviceUin,sToken,sSmsNumber,sCode,TypeReg,TypeUser,dDateSend) VALUES (:sToken,:sToken,:sSmsNumber,:sCode,1,:TypeUser,Now())");
			$query->bindParam('sToken', $sToken);
			$query->bindParam('sSmsNumber', $phone);
			$query->bindParam('sCode', $sCode);
			$query->bindParam('TypeUser', $type_user);
			$query->execute();	
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		
		public function update_code_and_phone($sToken,$sSmsNumber,$sCode){
			//echo $sToken;
			$query = $this->db->prepare("UPDATE Device SET iStatus=0, sSmsNumber=:sSmsNumber,sCode=:sCode, dDateSend=ADDDATE(NOW(), INTERVAL 2 MINUTE) WHERE sGuestToken=:sGuestToken");
			$query->bindParam('sGuestToken', $sToken);
			$query->bindParam('sSmsNumber', $sSmsNumber);
			$query->bindParam('sCode', $sCode);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function update_status($sToken){
			$query = $this->db->prepare("UPDATE Device SET iStatus=1 WHERE sToken=:sToken");
			$query->bindParam('sToken', $sToken);
			$query->execute();
			return $query->rowCount();
		}
		public function update_status_site_phone($sPhone){
			$query = $this->db->prepare("UPDATE Device SET iStatus=1 WHERE sSmsNumber=:sSmsNumber");
			$query->bindParam('sSmsNumber', $sPhone);
			$query->execute();
			return $query->rowCount();
		}
		public function update_main_token($sToken,$sGuestToken,$sCode){
			$query = $this->db->prepare("UPDATE Device SET sToken=:sToken WHERE sGuestToken=:sGuestToken AND sCode=:sCode");
			$query->bindParam('sGuestToken', $sGuestToken);
			$query->bindParam('sCode', $sCode);
			$query->bindParam('sToken', $sToken);
			$query->execute();
			return $query->rowCount();
		}
		public function update_email($sEmail,$iDeviceId,$sEmailCode){
			$query = $this->db->prepare("UPDATE Device SET sEmail=:sEmail,sEmailCode=:sEmailCode WHERE iDeviceId=:iDeviceId");
			$query->bindParam('iDeviceId', $iDeviceId);
			$query->bindParam('sEmail', $sEmail);
			$query->bindParam('sEmailCode', $sEmailCode);
			$query->execute();
			return $query->rowCount();
		}
		public function update_email_status_site($sEmail){
			$query = $this->db->prepare("UPDATE Device SET iStatusEmail=1 WHERE sEmail=:sEmail");
			$query->bindParam('sEmail', $sEmail);
			$query->execute();
			return $query->rowCount();
		}
		public function update_status_site($sSmsNumber){
			$query = $this->db->prepare("UPDATE Device SET iStatus=1 WHERE sSmsNumber=:sSmsNumber");
			$query->bindParam('sSmsNumber', $sSmsNumber);
			$query->execute();
			return $query->rowCount();
		}
		public function update_type_user($iDeviceId,$type){
			$query = $this->db->prepare("UPDATE Device SET TypeUser=:TypeUser WHERE iDeviceId=:iDeviceId");
			$query->bindParam('TypeUser', $type);
			$query->bindParam('iDeviceId', $iDeviceId);
			$query->execute();
			return $query->rowCount();
		}
		public function check_code($sCode,$sToken){
			// echo $sCode;
			// echo $sToken;
			$query = $this->db->prepare("SELECT * FROM Device WHERE sGuestToken=:sGuestToken AND sCode=:sCode");
			$query->bindParam('sGuestToken', $sToken);
			$query->bindParam('sCode', $sCode);
			$query->execute();
			return $query->rowCount();
		}
		public function check_code_site($sPhone,$sCode){
			// echo $sPhone;
			$query = $this->db->prepare("SELECT * FROM Device WHERE sCode=:sCode AND sSmsNumber=:sPhone");
			$query->bindParam('sCode', $sCode);
			$query->bindParam('sPhone', $sPhone);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function check_code_site_email($sEmail,$sEmailCode){
			$query = $this->db->prepare("SELECT * FROM Device WHERE sEmailCode=:sEmailCode AND sEmail=:sEmail");
			$query->bindParam('sEmailCode', $sEmailCode);
			$query->bindParam('sEmail', $sEmail);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function check_code_email($sEmail,$sCode){
			$query = $this->db->prepare("SELECT * FROM Device WHERE sEmailCode=:sEmailCode AND sEmail=:sEmail");
			$query->bindParam('sEmailCode', $sCode);
			$query->bindParam('sEmail', $sEmail);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function getIdToken($token){
			$query = $this->db->prepare("SELECT iDeviceId FROM Device WHERE sGuestToken=:sToken OR sToken=:sToken");
			$query->bindParam('sToken', $token);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function checkPhone($phone){
			$query = $this->db->prepare("SELECT iDeviceId FROM Device WHERE sSmsNumber=:sPhone");
			$query->bindParam('sPhone', $phone);
			$query->execute();
			//echo $phone;
			if($query->rowCount()==0){
				return true;
			}
			else{
				return false;
			}
		}
		public function getInfoDevice($id_device){
			$query = $this->db->prepare("SELECT * FROM Device WHERE iDeviceId=:iDeviceId");
			$query->bindParam('iDeviceId', $id_device);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function updatePhoneAndStatus($id_device,$phone){
			$query = $this->db->prepare("UPDATE Device SET iStatus=0,sSmsNumber=:sSmsNumber,sToken='' WHERE iDeviceId=:iDeviceId");
			$query->bindParam('sSmsNumber', $phone);
			$query->bindParam('iDeviceId', $id_device);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function updateCodePhone($id_device,$phone,$code){
			$query = $this->db->prepare("UPDATE Device SET sCode=:sCode,sSmsNumber=:sSmsNumber WHERE iDeviceId=:iDeviceId");
			$query->bindParam('sSmsNumber', $phone);
			$query->bindParam('sCode', $code);
			$query->bindParam('iDeviceId', $id_device);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function getTypeUser($iDeviceId){
			//echo $iDeviceId;
			$query = $this->db->prepare("SELECT iDriverId,iCustomerId FROM Customer,Driver WHERE Customer.iDeviceId=:iDeviceId OR Driver.sHashImage=:iDeviceId");
			$query->bindParam('iDeviceId', $iDeviceId);
			$query->execute();
			//echo $query->rowCount();
			if($query->rowCount()==0){
				return true;
			}
			else{
				return false;
			}
		}
		public function checkImageHash($hash){
			//echo $iDeviceId;
			$query = $this->db->prepare("SELECT * FROM Customer,Driver WHERE Customer.sHashImage=:sHashImage OR Driver.sHashImage=:sHashImage");
			$query->bindParam('sHashImage', $hash);
			$query->execute();
			//echo $query->rowCount();
			if($query->rowCount()==0){
				return true;
			}
			else{
				return false;
			}
		}
	}
?>