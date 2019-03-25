<?php

	class Grab extends DB_Connect{
		public function __construct($dbo=NULL, $useDate=NULL){
			parent::__construct($dbo);
		}
		public function addGrab($array){
				print_r($array);
				$query = $this->db->prepare("INSERT INTO Grab (iGrabName, iGrabPhone,iGrabCityStart,iGrabRegionStart,iGrabCityEnd,iGrabRegionEnd,sFio) VALUES (:iGrabName,:iGrabPhone,:iGrabCityStart,:iGrabRegionStart,:iGrabCityEnd,:iGrabRegionEnd,:sFio)");
				$query->bindParam('iGrabName', str_replace("(удалена)", "", $array["name"]));
				$query->bindParam('iGrabPhone', $array["phone"]);
				$query->bindParam('iGrabCityStart', $array["city"]);
				$query->bindParam('iGrabRegionStart', $array["region"]);
				$query->bindParam('iGrabCityEnd', $array["city_end"]);
				$query->bindParam('iGrabRegionEnd', $array["region_end"]);
				$query->bindParam('sFio', $array["fio"]);
				// $query->bindParam('iBodyType', $driver["body_type_id"]);
				// $query->bindParam('iCapacity', $driver["capacity"]);
				// $query->bindParam('iVolume', $driver["volume"]);
				//$query->bindParam('iCustomerOrg', isset($driver["organization"]) ? $driver["organization"] : "" );
				$query->execute();	
				//print_r($query->errorInfo());
				return $this->db->lastInsertId();
		}
		public function getParsingCar(){
			$query = $this->db->prepare("SELECT * FROM Parsing_car");
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function addCar($sName,$sCity){
			$query = $this->db->prepare("INSERT INTO car (sName, sCity) VALUES (:sName,:sCity)");
				$query->bindParam('sName', $sName);
				$query->bindParam('sCity', $sCity);
				$query->execute();	
				//print_r($query->errorInfo());
				return $this->db->lastInsertId();
		}
		public function addPhone($sPhoneName,$sPhone,$iCarId){
			$query = $this->db->prepare("INSERT INTO phone (sPhoneName, sPhone,iCarId) VALUES (:sPhoneName, :sPhone, :iCarId)");
				$query->bindParam('sPhoneName', $sPhoneName);
				$query->bindParam('sPhone', $sPhone);
				$query->bindParam('iCarId', $iCarId);
				$query->execute();	
				//print_r($query->errorInfo());
				return $this->db->lastInsertId();
		}
		public function deleteCarGrab($id_cargrab){
			$query = $this->db->prepare("DELETE FROM Parsing_car WHERE iDriverId=:iDriverId AND iSubscriptions=:iSubscriptions");
			$query->bindParam('iDriverId', $id_driver);
			$query->bindParam('iSubscriptions', $id_subscribe);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function getCar(){
			$query = $this->db->prepare("SELECT * FROM car");
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getCargo(){
			$query = $this->db->prepare("SELECT * FROM Grab");
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getPhone($iCarId){
			$query = $this->db->prepare("SELECT sPhoneName,sPhone FROM phone WHERE iCarId=:iCarId");
			$query->bindParam('iCarId', $iCarId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
	}
?>