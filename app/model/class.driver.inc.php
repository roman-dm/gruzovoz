<?php

	class Driver extends DB_Connect{
		public function __construct($dbo=NULL, $useDate=NULL){
			parent::__construct($dbo);
		}
		public function AddRegistrationDriver($driver,$id_device){
				//print_r($driver);
				$query = $this->db->prepare("INSERT INTO Driver (sDriverName, iCountryId,iCityId,sDriverPhone,sCarName,iCarType,iBodyType,iCapacity,iVolume,sLoadTypeTop,sLoadTypeRear,sLoadTypeSide,sDriverSpecialization,iLoaders,iRate,iRateInCity,iDeviceId,iRegionId) VALUES (:sDriverName,:iCountryId,:iCityId,:sDriverPhone,:sCarName,:iCarType,:iBodyType,:iVolume,:iCapacity,:sLoadTypeTop,:sLoadTypeRear,:sLoadTypeSide,:sDriverSpecialization,:iLoaders,:iRate,:iRateInCity,:iDeviceId,:iRegionId)");
				$query->bindParam('sDriverName', $driver["name"]);
				$query->bindParam('iCountryId', $driver["country_id"]);
				$query->bindParam('iCityId', $driver["city_id"]);
				$query->bindParam('sDriverPhone', $driver["phone"]);
				$query->bindParam('sCarName', $driver["car_name"]);
				$query->bindParam('iCarType', $driver["car_type"]);
				$query->bindParam('iBodyType', $driver["body_type_id"]);
				$query->bindParam('iCapacity', $driver["capacity"]);
				$query->bindParam('iVolume', $driver["volume"]);
				$query->bindParam('sLoadTypeTop', isset($driver["load_type_top"]) ? $driver["load_type_top"] : "");
				$query->bindParam('sLoadTypeRear',isset($driver["load_type_rear"]) ? $driver["load_type_rear"] : "");
				$query->bindParam('sLoadTypeSide', isset($driver["load_type_side"]) ? $driver["load_type_side"] : "");
				$query->bindParam('sDriverSpecialization', $driver["driver_specialization"]);
				$query->bindParam('iLoaders', isset($driver["loaders"]) ? $driver["loaders"] : "");
				$query->bindParam('iRate', isset($driver["rate_intercity"]) ? $driver["rate_intercity"] : "");
				$query->bindParam('iRateInCity', isset($driver["rate_in_city"]) ? $driver["rate_in_city"] : "");
				$query->bindParam('iDeviceId', $id_device[0]["iDeviceId"]);
				$query->bindParam('iRegionId', $driver["iRegionVkid"]);
				//$query->bindParam('iCustomerOrg', isset($driver["organization"]) ? $driver["organization"] : "" );
				$query->execute();	
				//print_r($query->errorInfo());
				return $this->db->lastInsertId();
		}
		public function getBodyType(){
			$query = $this->db->prepare("SELECT * FROM Body_type");
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function findDriver($id_device){
			$query = $this->db->prepare("SELECT iDriverId FROM Driver WHERE iDeviceId=:iDeviceId");
			$query->bindParam('iDeviceId', $id_device[0]["iDeviceId"]);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function respondDriverOrder($id_order,$id_driver,$driver){
			$query = $this->db->prepare("INSERT INTO Driver_order (iOrder, iDriver,iPrice,sCurrency,dTimeResponse) VALUES (:iOrder, :iDriver,:iPrice,:sCurrency,NOW())");
			$query->bindParam('iOrder', $id_order);
			$query->bindParam('iDriver',$id_driver);
			$query->bindParam('iPrice',isset($driver["price"]) ? $driver["price"] : "");
			$query->bindParam('sCurrency', isset($driver["currency"]) ? $driver["currency"] : "");
			$query->execute();
			//print_r($query->errorInfo());
			return $this->db->lastInsertId();
		}
		public function deleteCheckDriver($order_id,$id_driver){
			$query = $this->db->prepare("DELETE FROM Driver_order WHERE iOrder=:iOrderId AND iDriver=:iDriver");
			$query->bindParam('iOrderId', $order_id);
			$query->bindParam('iDriver', $id_driver);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->execute();
		}
		public function checkRespond($id_order,$id_driver){
			$query = $this->db->prepare("SELECT COUNT(*) as count FROM Driver_order WHERE iOrder=:iOrder AND iDriver=:iDriver");
			$query->bindParam('iOrder', $id_order);
			$query->bindParam('iDriver',$id_driver);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getRespondDriver($id_order,$dTimeResponse,$type){
			// echo $id_order;
			//echo $dTimeResponse;
			$select="*";
			if($type=="count"){
				$select="COUNT(*) as count";
			}
			//echo "SELECT $select FROM `Driver_order` ds LEFT JOIN Driver d ON ds.iDriver=d.iDriverId WHERE ds.iOrder=:iOrder AND dTimeResponse<:dTimeResponse";
			$query = $this->db->prepare("SELECT $select FROM `Driver_order` ds LEFT JOIN Driver d ON ds.iDriver=d.iDriverId WHERE ds.iOrder=:iOrder AND ds.dTimeResponse<:dTimeResponse");
			$query->bindParam('iOrder', $id_order);
			$query->bindParam('dTimeResponse', $dTimeResponse);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getNewRespondDriver($id_order,$dTimeResponse,$type){
			//echo $id_order;
			$select="*";
			$where='';
			if($type=="count"){
				$select="COUNT(*) as count";
			}
			else if($type=="called"){
				$where=" AND ds.iGetPhoneDriver=1";
			}
			//echo $id_order;
			$query = $this->db->prepare("SELECT $select FROM `Driver_order` ds LEFT JOIN Driver d ON ds.iDriver=d.iDriverId WHERE ds.iOrder=:iOrder $where AND ds.dTimeResponse>:dTimeResponse");
			$query->bindParam('iOrder', $id_order);
			$query->bindParam('dTimeResponse', $dTimeResponse);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function changeDriver($orderId,$id_driver){
			//echo $orderId;
			// echo $orderId;
			// echo $id_driver;
			$query = $this->db->prepare("UPDATE  Orders SET iDriverId=:iDriverId, dEditTime=NOW() WHERE iOrderid=:orderId");
			$query->bindParam('orderId', $orderId);
			$query->bindParam('iDriverId', $id_driver);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function refuseDriver($orderId){
			$query = $this->db->prepare("UPDATE  Orders SET iDriverId=-1 WHERE iOrderid=:orderId");
			$query->bindParam('orderId', $orderId);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function updateRate($RateCoulmn,$iDriverId){
			//echo $RateCoulmn; echo $iDriverId;
			$query = $this->db->prepare("UPDATE  Rating_driver SET $RateCoulmn=$RateCoulmn+1 WHERE iDriverId=:iDriverId");
			$query->bindParam('iDriverId', $iDriverId);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();	
		}
		public function addRating($iDriverId){
			$query = $this->db->prepare("INSERT INTO Rating_driver (iDriverId) VALUES (:iDriverId)");
			$query->bindParam('iDriverId', $iDriverId);
			$query->execute();
			//print_r($query->errorInfo());
			return $this->db->lastInsertId();
		}
		public function getRating($iDriverId){
			$query = $this->db->prepare("SELECT * FROM Rating_driver WHERE iDriverId=:iDriverId");
			$query->bindParam('iDriverId', $iDriverId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getOtklik($iDriverId,$arr){
			//print_r($arr);
			$limit_str="";
			if(!isset($arr["offset"])){
				$limit_str.="LIMIT 0";
			}
			else{
				$limit_str.="LIMIT ".$arr["offset"];
			}
			if(!isset($arr["limit"])){
				$limit_str.=",1000";
			}
			else{
				$limit_str.=",".$arr["limit"];
			}
			//echo "SELECT * FROM Orders,Driver_order WHERE Orders.iOrderid=Driver_order.iOrder AND Driver_order.iDriver=:iDriverId and Orders.iStatus=0 and Orders.iDriverId=0 ".$limit_str;
			$query = $this->db->prepare("SELECT * FROM Orders,Driver_order WHERE Orders.iOrderid=Driver_order.iOrder AND Driver_order.iDriver=:iDriverId and Orders.iStatus=0 and Orders.iDriverId=0 ".$limit_str);
			$query->bindParam('iDriverId', $iDriverId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function rateOrder($orderId,$rating){
			//echo $orderId;
			$query = $this->db->prepare("UPDATE  Orders SET iRatingDriver=:iRatingDriver WHERE iOrderid=:orderId");
			$query->bindParam('orderId', $orderId);
			$query->bindParam('iRatingDriver', $rating);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function getProfileDriver($id_driver){
		//	echo $id_driver[0]["iDriverId"];
			$query = $this->db->prepare("SELECT * FROM Driver d LEFT JOIN Body_type dt ON dt.iBodyTypeId=d.iBodyType WHERE d.iDriverId=:iDriverId");
			$query->bindParam('iDriverId', $id_driver[0]["iDriverId"]);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getCallsDriver($iDriverId,$arr){
			$limit="";
			if(!isset($arr["offset"])){
				$limit.="LIMIT 0";
			}
			else{
				$limit.="LIMIT ".$arr["offset"];
			}
			if(!isset($arr["limit"])){
				$limit.=",1000";
			}
			else{
				$limit.=",".$arr["limit"];
			}
			$query = $this->db->prepare("SELECT  calls_customer.iDriverId, calls_customer.iCustomerId  as id,  calls_customer.dCustomerCallsDate as date_call,  calls_customer.iUnId, 0 as cus, Customer.sHashImage as hash,Customer.iCustomerName  as name FROM calls_customer LEFT JOIN Customer ON calls_customer.iCustomerId=Customer.iCustomerId WHERE calls_customer.iDriverId=58 UNION ALL SELECT calls_driver.iDriverId,calls_driver.iCustomerId as id, calls_driver.dDriverCallsDate as date_call, calls_driver.iUnId, 1 as cus, Customer.sHashImage as hash,Customer.iCustomerName as name  FROM calls_driver LEFT JOIN Customer ON calls_driver.iCustomerId=Customer.iCustomerId WHERE calls_driver.iDriverId=58 Order By date_call DESC $limit");
			$query->bindParam('iDriverId', $iDriverId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function update_profile_driver($idd,$order){
			//print_r($idd);
			$str="";
			if(isset($order["name"])){
				$str.="sDriverName=:sDriverName, ";
			}
			if(isset($order["phone"])){
				$str.="sDriverPhone=:sDriverPhone, ";
			}
			if(isset($order["avatar"])){
				$str.="sAvatar=:sAvatar, ";
			}
			if(isset($order["avatar_hash"])){
				$str.="sHashImage=:sHashImage, ";
			}
			if(isset($order["country_id"])){
				$str.="iCountryId=:iCountryId, ";
			}
			if(isset($order["city_id"])){
				$str.="iCityId=:iCityId, ";
			}
			if(isset($order["car_name"])){
				$str.="sCarName=:sCarName, ";
			}
			if(isset($order["car_type"])){
				$str.="iCarType=:iCarType, ";
			}
			if(isset($order["body_type_id"])){
				$str.="iBodyType=:iBodyType, ";
			}
			if(isset($order["capacity"])){
				$str.="iCapacity=:iCapacity, ";
			}
			if(isset($order["volume"])){
				$str.="iVolume=:iVolume, ";
			}
			if(isset($order["load_type_top"])){
				$str.="sLoadTypeTop=:sLoadTypeTop, ";
			}
			if(isset($order["load_type_rear"])){
				$str.="sLoadTypeRear=:sLoadTypeRear, ";
			}
			if(isset($order["load_type_side"])){
				$str.="sLoadTypeSide=:sLoadTypeSide, ";
			}
			if(isset($order["driver_specialization"])){
				$str.="sDriverSpecialization=:sDriverSpecialization, ";
			}
			if(isset($order["loaders"])){
				$str.="iLoaders=:iLoaders, ";
			}
			if(isset($order["rate_intercity"])){
				$str.="iRate=:iRate, ";
			}
			if(isset($order["rate_in_city"])){
				$str.="iRateInCity=:iRateInCity, ";
			}
			if(isset($order["rating"])){
				$str.="iRating=:iRating, ";
			}
			if(isset($order["notifications"])){
				$str.="sNotification=:sNotification, ";
			}
			
			$str=substr($str, 0,-2);
			$query = $this->db->prepare("UPDATE Driver SET $str,dEditTime=NOW() WHERE iDriverId=:iDriverId");
			//echo "UPDATE Driver SET $str WHERE iDriverId=:iDriverId";
			if(isset($order["name"])){
				$query->bindParam('sDriverName', $order["name"]);
			}
			if(isset($order["phone"])){
				$query->bindParam('sDriverPhone', $order["phone"]);
			}
			if(isset($order["avatar"])){
				$query->bindParam('sAvatar', $order["avatar"]);
			}
			if(isset($order["avatar_hash"])){
				$query->bindParam('sHashImage', $order["avatar_hash"]);
			}
			if(isset($order["country_id"])){
				$query->bindParam('iCountryId', $order["country_id"]);
			}
			if(isset($order["city_id"])){
				$query->bindParam('iCityId', $order["city_id"]);
			}
			if(isset($order["car_name"])){
				$query->bindParam('sCarName', $order["car_name"]);
			}
			if(isset($order["car_type"])){
				$query->bindParam('iCarType', $order["car_type"]);
			}
			if(isset($order["body_type_id"])){
				$query->bindParam('iBodyType', $order["body_type_id"]);
			}
			if(isset($order["capacity"])){
				$query->bindParam('iCapacity', $order["capacity"]);
			}
			if(isset($order["volume"])){
				$query->bindParam('iVolume', $order["volume"]);
			}
			if(isset($order["load_type_top"])){
				$query->bindParam('sLoadTypeTop', $order["load_type_top"]);
			}
			if(isset($order["load_type_rear"])){
				$query->bindParam('sLoadTypeRear', $order["load_type_rear"]);
			}
			if(isset($order["load_type_side"])){
				$query->bindParam('sLoadTypeSide', $order["load_type_side"]);
			}
			if(isset($order["driver_specialization"])){
				$query->bindParam('sDriverSpecialization', $order["driver_specialization"]);
			}
			if(isset($order["loaders"])){
				$query->bindParam('iLoaders', $order["loaders"]);
			}
			if(isset($order["rating"])){
				$query->bindParam('iRating', $order["rating"]);
			}
			if(isset($order["rate_intercity"])){
				$query->bindParam('iRate', $order["rate_intercity"]);
			}
			if(isset($order["rate_in_city"])){
				$query->bindParam('iRateInCity', $order["rate_in_city"]);
			}
			if(isset($order["notifications"])){
				$query->bindParam('sNotification', $order["notifications"]);
			}
			$query->bindParam('iDriverId', $idd);
			$query->execute();
			return $query->rowCount();
		}
		public function getDriver($arr,$type){
			//print_r($arr["body_types_ids"]);
			$body_types_ids=$arr['body_types_ids'];
			$limit="";
			$where="AND";
			$select="";
			if(isset($arr["id"])){
				$where.=" AND iDriverId=:iDriverId";
			}
			if(isset($arr["city_id"])){
				$where.=" AND iCityId=:iCityId";
			}
			if(isset($arr["driver_specialization"])){
				$where.=" AND sDriverSpecialization=:sDriverSpecialization";
			}
			if(isset($arr["loaders"])){
				$where.=" AND iLoaders=:iLoaders";
			}
			if(isset($arr["region_id"])){
				$where.=" AND iRegionId=:iRegionId";
			}
			if(isset($arr["min_ton"])){
				$where.=" AND iCapacity>:min_ton";
			}
			if(isset($arr["max_ton"])){
				$where.=" AND iCapacity<:max_ton";
			}
			if(isset($arr["body_types_ids"])){
				$where.=" AND iBodyType IN ($body_types_ids)";
			}
			if(!isset($arr["offset"])){
				$limit.="LIMIT 0";
			}
			else{
				$limit.="LIMIT ".$arr["offset"];
			}
			if(!isset($arr["limit"])){
				$limit.=",1000";
			}
			else{
				$limit.=",".$arr["limit"];
			}
			if($type=="count"){
				$select="COUNT(*) as count";
			}
			else if($type=="app" || $type=="site"){
				$select="*";
			}
			// if($where!=""){
			// 	$where.=" AND";
			// }	
			$where=substr($where, 4);
			$query = $this->db->prepare("SELECT $select FROM Driver WHERE iDeleteStatus = 0 $where $limit");
			//echo "SELECT $select FROM Driver WHERE iDeleteStatus = 0 $where $limit";
			if(isset($arr["id"])){
				$query->bindParam('iDriverId', $arr["id"]);
			}
			if(isset($arr["city_id"])){
				$query->bindParam('iCityId', $arr["city_id"]);
			}	
			if(isset($arr["driver_specialization"])){
				$query->bindParam('sDriverSpecialization', $arr["driver_specialization"]);
			}
			if(isset($arr["loaders"])){
				$query->bindParam('iLoaders', $arr["loaders"]);
			}
			if(isset($arr["min_ton"])){
				$query->bindParam('min_ton', $arr["min_ton"]);
			}
			if(isset($arr["max_ton"])){
				$query->bindParam('max_ton', $arr["max_ton"]);
			}
			if(isset($arr["region_id"])){
				$query->bindParam('iRegionId', $arr["region_id"]);
			}
			if(isset($arr["body_types_ids"])){
				//$query->bindParam('iBodyType', $arr["body_types_ids"]);
			}
			if(isset($arr["id"])){
				//echo "yes";
				$query->bindParam('iDriverId', $arr["id"]);
			}
			$query->execute();
			//print_r($query->errorInfo());
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getOrdersDriver($id_driver,$type,$arr){
			//echo $type;
			$limit_str="";
			if(!isset($arr["offset"])){
				$limit_str.="LIMIT 0";
			}
			else{
				$limit_str.="LIMIT ".$arr["offset"];
			}
			if(!isset($arr["limit"])){
				$limit_str.=",1000";
			}
			else{
				$limit_str.=",".$arr["limit"];
			}
			if($type=="all"){
				$str="";
			}
			else if($type=="current"){
				$str="AND iStatus=0";
			}
			else if($type=="old"){
				$str="AND iStatus=1";
			}
			//echo "SELECT  * FROM Orders WHERE iDriverId=:iDriverId $str $limit_str";
			$query = $this->db->prepare("SELECT  * FROM Orders WHERE iDriverId=:iDriverId $str $limit_str");
			$query->bindParam('iDriverId', $id_driver);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function arraySortDriverApp($arr){
			$new_array["id"]=$arr["iDriverId"];
	        $new_array["name"]=$arr["sDriverName"];
	        $link="";
			if(!empty($arr["sHashImage"])){
				$link="/get_image/?image=".$arr["sHashImage"];
			}
			$new_array["avatar"]=$link;
	        $new_array["car_name"]=$arr["sCarName"];
	        $new_array["rating"]=$arr["iRating"];
	        $new_array["rate_in_city"]=$arr["iRate"];
	        $new_array["rate_intercity"]=$arr["iRateInCity"];
	        $new_array["loaders"]=$arr["iLoaders"];
	        $new_array["capacity"]=$arr["iCapacity"];
	        return $new_array;
		}
		public function arraySortDriver($arr){
			$new_array=array();
			$new_array["id"]=$arr["iDriverId"];
	        $new_array["name"]=$arr["sDriverName"];
	        $link="";
			if(!empty($arr["sHashImage"])){
				$link="/get_image/?image=".$arr["sHashImage"];
			}
	        $new_array["avatar"]=$link;
	        $new_array["car_name"]=$arr["sCarName"];
	        $new_array["rating"]=$arr["iRating"];
	        $new_array["rate"]=$arr["iRate"];
	        $new_array["loaders"]=$arr["iLoaders"];
	        $new_array["capacity"]=$arr["iCapacity"];
	        $new_array["volume"]=$arr["iVolume"];
	        $new_array["load_type_top"]=$arr["sLoadTypeTop"];
	        $new_array["load_type_rear"]=$arr["sLoadTypeRear"];
	        $new_array["load_type_side"]=$arr["sLoadTypeSide"];
	        $new_array["driver_specialization"]=$arr["sDriverSpecialization"];
	        $new_array["car_type"]=$arr["iCarType"];
	        return $new_array;
		}
		public function updateStatusGetPhone($id_order,$id_driver){
			//echo $id_order;echo $id_driver;
			$query = $this->db->prepare("UPDATE  Driver_order SET iGetPhoneDriver=1 WHERE iOrder=:orderId AND iDriver=:iDriver");
			$query->bindParam('orderId', $id_order);
			$query->bindParam('iDriver', $id_driver);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function getHashImage($hash){
			$query = $this->db->prepare("SELECT sAvatar as avatar, iDriverId as id FROM Driver WHERE sHashImage=:sHashImage");
			$query->bindParam('sHashImage', $hash);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getInfoResponse($id_order,$id_driver){
			// echo $id_order;
			// echo $id_driver;
			$query = $this->db->prepare("SELECT iPrice FROM `Driver_order` WHERE iOrder=:iOrder AND iDriver=:iDriver");
			$query->bindParam('iOrder', $id_order);
			$query->bindParam('iDriver', $id_driver);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getInfoBodyType($idBodyTypes){
			//echo $idBodyTypes;
			$query = $this->db->prepare("SELECT * FROM `Body_type` WHERE iBodyTypeId=:idBodyTypes");
			$query->bindParam('idBodyTypes', $idBodyTypes);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function updateCalls($id_customer,$id_driver){
			$unId=$id_customer."-".$id_driver;
			$query = $this->db->prepare("REPLACE INTO calls_driver (iCustomerId,iDriverId,dDriverCallsDate,iUnId) VALUES (:iCustomerId,:iDriverId,NOW(),:iUnId)");
			$query->bindParam('iCustomerId', $id_customer);
			$query->bindParam('iDriverId', $id_driver);
			$query->bindParam('iUnId', $unId);
			$query->execute();
			//print_r($query->errorInfo());
			return $this->db->lastInsertId();
		}
	}
?>