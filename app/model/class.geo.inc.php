<?php

	class Geo extends DB_Connect{
		public function __construct($dbo=NULL, $useDate=NULL){
			parent::__construct($dbo);
		}
		public function getCountryById($countyId){
			$query = $this->db->prepare("SELECT * FROM Countries WHERE iCountryId=:id");
			$query->bindParam('id', $countyId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getListCountries(){
			$query = $this->db->prepare("SELECT iCountryId as id,iCountyName as name, iCountyCode as phone_code FROM Countries");
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function addRegion($region){
			$query = $this->db->prepare("INSERT INTO Regions (iRegionVkid, iRegionName,iRegionCountry) VALUES (:iRegionVkid, :iRegionName,1)");
				$query->bindParam('iRegionVkid', $region["id"]);
				$query->bindParam('iRegionName', $region["title"]);
				$query->execute();	
				return $this->db->lastInsertId();
		}
		public function getRegion($region){
			$query = $this->db->prepare("SELECT * FROM Regions WHERE iRegionName=:region");
			$query->bindParam('region', $region);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
	}
?>