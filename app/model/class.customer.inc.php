<?php

	class Customer extends DB_Connect{
		public function __construct($dbo=NULL, $useDate=NULL){
			parent::__construct($dbo);
		}
		public function addOrder($order,$customer_id){
				//print_r($customer_id);
				$query = $this->db->prepare("INSERT INTO Orders (sCargoName, iCargWeight,sWeightUnit,iPrice,sCurrencyTypePrice,sPaymentMethod,iStartCityId,iFinishCityId,dStartDate,dFinishDate,tComment,iCustomerID,iDriverId,iCanWrite,iCapacityId,iCanCall) VALUES (:sCargoName, :iCargWeight,:sWeightUnit,:iPrice,:sCurrencyTypePrice,:sPaymentMethod,:iStartCityId,:iFinishCityId,:dStartDate,:dFinishDate,:tComment,:iCustomerID,:iDriverId,:iCanWrite,:iCapacityId,:iCanCall)");
				$query->bindParam('sCargoName', $order["cargo_name"]);
				//$query->bindParam('iCustomerOrg', isset($order["organization"]) ? $order["organization"] : "" );
				$query->bindParam('iCargWeight', $order["cargo_weight"]);	
				$query->bindParam('sWeightUnit', $order["weight_unit"]);
				$query->bindParam('iPrice', $order["price"]);
				$query->bindParam('sCurrencyTypePrice', $order["currency"]);
				$query->bindParam('sPaymentMethod', $order["payment_method"]);
				$query->bindParam('iStartCityId', $order["start_city_id"]);
				$query->bindParam('iFinishCityId', $order["finish_city_id"]);
				$query->bindParam('dStartDate', $order["start_date"]);
				$query->bindParam('dFinishDate', $order["finish_date"]);
				$query->bindParam('tComment', isset($order["comment"]) ? $order["comment"] : "");
				$query->bindParam('iCustomerID', $customer_id[0]["iCustomerId"]);
				$query->bindValue('iDriverId', "-1");
				$query->bindParam('iCapacityId', isset($order["capacity"]) ? $order["capacity"] : "");
				$query->bindParam('iCanCall', isset($order["can_call"]) ? $order["can_call"] : "");
				$query->bindParam('iCanWrite', isset($order["can_write"]) ? $order["can_write"] : "");
				$query->execute();	
				//print_r($query->errorInfo());
				return $this->db->lastInsertId();
		}
		public function AddRegistrationCustomer($customer,$iDeviceId){
				//print_r($customer);
				$query = $this->db->prepare("INSERT INTO Customer (iCustomerName, iCustomerOrg,iCustomerPhone,iCustomerCountry,iCustomerCity,iDeviceId,iCustomerAvatar) VALUES (:iCustomerName, :iCustomerOrg,:iCustomerPhone,:iCustomerCountry,:iCustomerCity,:iDeviceId,:iCustomerAvatar)");
				$query->bindParam('iCustomerName', $customer["name"]);
				$query->bindParam('iCustomerOrg', isset($customer["organization"]) ? $customer["organization"] : "" );
				$query->bindParam('iCustomerAvatar', isset($customer["avatar"]) ? $customer["avatar"] : "" );
				$query->bindParam('iCustomerPhone', $customer["phone"]);	
				$query->bindParam('iCustomerCountry', $customer["country_id"]);
				$query->bindParam('iCustomerCity', $customer["city_id"]);
				$query->bindParam('iDeviceId', $iDeviceId[0]["iDeviceId"]);
				$query->execute();	
				//print_r($query->errorInfo());
				return $this->db->lastInsertId();
		}
		public function rateOrder($orderId,$rating){
			$query = $this->db->prepare("UPDATE  Orders SET iRatingCustomer=:iRatingCustomer WHERE iOrderid=:orderId");
			$query->bindParam('orderId', $orderId);
			$query->bindParam('iRatingCustomer', $rating);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function updateRate($RateCoulmn,$iCustomerId){
			$query = $this->db->prepare("UPDATE  Rating_customer SET $RateCoulmn=$RateCoulmn+1 WHERE iCustomerId=:iCustomerId");
			$query->bindParam('iCustomerId', $iCustomerId);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();	
		}
		public function addCustomer ($id_customer,$id_device){
			$query = $this->db->prepare("INSERT INTO Device_customer (IDeviceID, iCustomerId) VALUES (:IDeviceID, :iCustomerId)");
				$query->bindParam('IDeviceID', $id_device);
				$query->bindParam('iCustomerId', $id_customer);
				$query->execute();	
			return $this->db->lastInsertId();
		}
		public function deleteOrder($orderId,$iCustomerId){
			//echo $orderId;
			$query = $this->db->prepare("UPDATE  Orders SET iDeleteStatus=1 WHERE iOrderid=:orderId AND iCustomerId=:iCustomerId");
			$query->bindParam('orderId', $orderId);
			$query->bindParam('iCustomerId', $iCustomerId);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function getRating($iCustomerId){
			$query = $this->db->prepare("SELECT * FROM Rating_customer WHERE iCustomerId=:iCustomerId");
			$query->bindParam('iCustomerId', $iCustomerId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getBodyOrders($order_id){
			$query = $this->db->prepare("SELECT bto.iBodyTypeId,bt.iBodyTypeName FROM Body_types_orders bto
				LEFT JOIN Body_type bt ON bt.iBodyTypeId=bto.iBodyTypeId WHERE iOrderId=:iOrderId");
			$query->bindParam('iOrderId', $order_id);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function addRating($iCustomerId){
			$query = $this->db->prepare("INSERT INTO Rating_customer (iCustomerId) VALUES (:iCustomerId)");
			$query->bindParam('iCustomerId', $iCustomerId);
			$query->execute();
			//print_r($query->errorInfo());
			return $this->db->lastInsertId();
		}
		public function deleteBodyTypes($order_id){
			$query = $this->db->prepare("DELETE FROM Body_types_orders WHERE iOrderId=:iOrderId");
			$query->bindParam('iOrderId', $order_id);
			$query->execute();
			//print_r($query->errorInfo());
			return $this->db->lastInsertId();
		}
		public function addBodyTypes($body_id,$order_id){
			$query = $this->db->prepare("INSERT INTO Body_types_orders (iBodyTypeId,iOrderId) VALUES (:iBodyTypeId,:iOrderId)");
			$query->bindParam('iBodyTypeId', $body_id);
			$query->bindParam('iOrderId', $order_id);
			$query->execute();
			//print_r($query->errorInfo());
			return $this->db->lastInsertId();
		}
		public function checkOrder($id_order){
			$query = $this->db->prepare("SELECT COUNT(*) as count FROM Orders WHERE iOrderid=:orderId");
			$query->bindParam('orderId', $id_order);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getOrder($arr,$type){
			if(isset($_GET["debug"])){
			print_r($arr);
			}
			$limit="";
			$where="AND";
			$select="";
			$join="";
			if(isset($arr["payment_method"])){
				$where.=" AND Orders.sPaymentMethod=:sPaymentMethod";
			}
			if(isset($arr["payment_type"])){
				if($arr["payment_type"]=="fixed"){
					$where.=" AND Orders.iPrice>0";
				}
				else if($arr["payment_type"]=="open"){
					$where.=" AND Orders.iPrice=0";
				}
			}
			if(isset($arr["start_id"])){
				$where.=" AND Orders.iStartCityId=:iStartCityId";
			}
			if(isset($arr["finish_id"])){
				$where.=" AND Orders.iFinishCityId=:iFinishCityId";
			}
			// if(isset($arr["order_type"])){
			// 	$where.=" AND iFinishCityId=:iFinishCityId";
			// }
			if(isset($arr["start_date"]) && !empty($arr["start_date"])){
				$where.=" AND Orders.dStartDate>:dStartDate";
			}
			if(isset($arr["finish_date"])){
				$where.=" AND Orders.dFinishDate<:dFinishDate";
			}
			if(isset($arr["min_killo"])){
				$where.=" AND Orders.iCargWeight>:iCargWeight_min";
			}
			if(isset($arr["max_killo"])){
				$where.=" AND Orders.iCargWeight<:iCargWeight_max";
			}
			if(isset($arr["id"])){
				$where.=" AND Orders.iOrderid=:iOrderid";
			}
			if(isset($arr["customer_id"])){
				$where.=" AND Orders.iCustomerID=:iCustomerID";
			}
			if(isset($arr["driver_id"])){
				$where.=" AND Orders.iDriverId=:iDriverId";
			}
			if(isset($arr["body_types_ids"])){
				$where.=" AND Body_types_orders.iBodyTypeId IN (:iBodyTypeId)";
			}
			if(isset($arr["body_types_ids"])){
				$join.=" LEFT JOIN Body_types_orders ON Body_types_orders.iOrderId=Orders.iOrderid";
			}
			if(isset($arr["status_id"])){
				$where.=" AND Orders.iStatus=:iStatus";
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
			$query = $this->db->prepare("SELECT $select FROM Orders $join WHERE Orders.iDeleteStatus = 0 $where $limit");
			if(isset($_GET["debug"])){
				echo "SELECT $select FROM Orders $join WHERE Orders.iDeleteStatus = 0 $where $limit";
			}
			if(isset($arr["payment_method"])){
				$query->bindParam('sPaymentMethod', $arr["payment_method"]);
			}
			if(isset($arr["start_id"])){
				$query->bindParam('iStartCityId', $arr["start_id"]);
			}	
			if(isset($arr["finish_id"])){
				$query->bindParam('iFinishCityId', $arr["finish_id"]);
			}
			if(isset($arr["start_date"]) && !empty($arr["start_date"])){
				$query->bindParam('dStartDate', $arr["start_date"]);
			}
			if(isset($arr["finish_date"])){
				$query->bindParam('dFinishDate', $arr["finish_date"]);
			}
			if(isset($arr["status_id"])){
				$query->bindParam('iStatus', $arr["status_id"]);
			}
			if(isset($arr["min_killo"])){
				$query->bindParam('iCargWeight_min', $arr["min_killo"]);
			}
			if(isset($arr["max_killo"])){
				$query->bindParam('iCargWeight_max', $arr["max_killo"]);
			}
			if(isset($arr["body_types_ids"])){
				$query->bindParam('iBodyTypeId', $arr["body_types_ids"]);
			}
			if(isset($arr["id"])){
				//echo "yes";
				$query->bindParam('iOrderid', $arr["id"]);
			}
			if(isset($arr["customer_id"])){
				$query->bindParam('iCustomerID', $arr["customer_id"]);
			}
			if(isset($arr["driver_id"])){
				$query->bindParam('iDriverId', $arr["driver_id"]);
			}
			$query->execute();
				//print_r($query->errorInfo());
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function update_order($idd,$order){
			//print_r($order["dTimeLast"]);
			$str="";
			if(isset($order["cargo_name"])){
				$str.="sCargoName=:sCargoName, ";
			}
			if(isset($order["cargo_weight"])){
				$str.="iCargWeight=:iCargWeight, ";
			}
			if(isset($order["dTimeLast"])){
				$str.="dLastGetDriver=NOW(), ";
			}
			if(isset($order["weight_unit"])){
				$str.="sWeightUnit=:sWeightUnit, ";
			}
			if(isset($order["price"])){
				$str.="iPrice=:iPrice, ";
			}
			if(isset($order["currency"])){
				$str.="sCurrencyTypePrice=:sCurrencyTypePrice, ";
			}
			if(isset($order["payment_method"])){
				$str.="sPaymentMethod=:sPaymentMethod, ";
			}
			if(isset($order["start_city_id"])){
				$str.="iStartCityId=:iStartCityId, ";
			}
			if(isset($order["driver_id"])){
				$str.="iDriverId=:iDriverId, ";
			}
			if(isset($order["finish_city_id"])){
				$str.="iFinishCityId=:iFinishCityId, ";
			}
			if(isset($order["start_date"])){
				$str.="dStartDate=:dStartDate, ";
			}
			if(isset($order["finish_date"])){
				$str.="dFinishDate=:dFinishDate, ";
			}
			if(isset($order["capacity"])){
				$str.="iCapacityId=:iCapacityId, ";
			}
			if(isset($order["can_call"])){
				$str.="iCanCall=:iCanCall, ";
			}
			if(isset($order["can_write"])){
				$str.="iCanWrite=:iCanWrite, ";
			}
			if(isset($order["can_write"])){
				$str.="iCanWrite=:iCanWrite, ";
			}
			if(isset($order["status_id"])){
				$str.="iStatus=:iStatus, ";
			}
			if(isset($order["comment"])){
				$str.="tComment=:tComment, ";
			}
			$str=substr($str, 0,-2);
			$query = $this->db->prepare("UPDATE Orders SET $str,dEditTime=NOW() WHERE iOrderid=:iOrderid");
			//echo "UPDATE Orders SET $str,dEditTime=NOW() WHERE iOrderid=:iOrderid";
			if(isset($order["cargo_name"])){
				$query->bindParam('sCargoName', $order["cargo_name"]);
			}
			if(isset($order["cargo_weight"])){
				$query->bindParam('iCargWeight', $order["cargo_weight"]);
			}
			if(isset($order["weight_unit"])){
				$query->bindParam('sWeightUnit', $order["weight_unit"]);
			}
			if(isset($order["price"])){
				$query->bindParam('iPrice', $order["price"]);
			}
			if(isset($order["currency"])){
				$query->bindParam('sCurrencyTypePrice', $order["currency"]);
			}
			if(isset($order["payment_method"])){
				$query->bindParam('sPaymentMethod', $order["payment_method"]);
			}
			if(isset($order["start_city_id"])){
				$query->bindParam('iStartCityId', $order["start_city_id"]);
			}
			if(isset($order["finish_city_id"])){
				$query->bindParam('iFinishCityId', $order["finish_city_id"]);
			}
			if(isset($order["start_date"])){
				$query->bindParam('dStartDate', $order["start_date"]);
			}
			if(isset($order["finish_date"])){
				$query->bindParam('dFinishDate', $order["finish_date"]);
			}
			if(isset($order["capacity"])){
				$query->bindParam('iCapacityId', $order["capacity"]);
			}
			if(isset($order["can_call"])){
				$query->bindParam('iCanCall', $order["can_call"]);
			}
			if(isset($order["can_write"])){
				$query->bindParam('iCanWrite', $order["can_write"]);
			}
			if(isset($order["driver_id"])){
				$query->bindParam('iDriverId', $order["iDriverId"]);
			}
			if(isset($order["status_id"])){
				$query->bindParam('iStatus', $order["status_id"]);
			}
			if(isset($order["comment"])){
				$query->bindParam('tComment', $order["comment"]);
			}
			$query->bindParam('iOrderid', $idd);
			$query->execute();
			return $query->rowCount();
		}
		public function findCustomer($id_device){
			//print_r($id_device[0]["iDeviceId"]);
			$query = $this->db->prepare("SELECT iCustomerId FROM Customer WHERE iDeviceId=:iDeviceId");
			$query->bindParam('iDeviceId', $id_device[0]["iDeviceId"]);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getProfile($iCustomerID){
			$query = $this->db->prepare("SELECT * FROM Customer WHERE iCustomerId=:iCustomerId");
			$query->bindParam('iCustomerId', $iCustomerID);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getHashImage($hash){
			$query = $this->db->prepare("SELECT iCustomerAvatar as avatar, iCustomerId as id FROM Customer WHERE sHashImage=:sHashImage");
			$query->bindParam('sHashImage', $hash);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getCallsCustomer($iCustomerId,$arr){
			//echo $iCustomerId;
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
			$query = $this->db->prepare("SELECT  calls_customer.iDriverId as id, calls_customer.iCustomerId,  calls_customer.dCustomerCallsDate as date_call,  calls_customer.iUnId, 1 as cus, Driver.sHashImage as hash,Driver.sDriverName  as name FROM calls_customer LEFT JOIN Driver ON calls_customer.iDriverId=Driver.iDriverId WHERE calls_customer.iCustomerId=:iCustomerId UNION ALL SELECT calls_driver.iDriverId as id, calls_driver.iCustomerId as id_customer, calls_driver.dDriverCallsDate as date_call, calls_driver.iUnId, 0 as cus, Driver.sHashImage as hash,Driver.sDriverName as name  FROM calls_driver LEFT JOIN Driver ON calls_driver.iDriverId=Driver.iDriverId WHERE iCustomerId=:iCustomerId Order By date_call DESC $limit");
			$query->bindParam('iCustomerId', $iCustomerId);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function updateCountViews($id_order){
			$query = $this->db->prepare("UPDATE  Orders SET iViewsCount=iViewsCount+1 WHERE iOrderid=:iOrderid");
			$query->bindParam('iOrderid', $id_order);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function updateCalls($id_customer,$id_driver){
			$unId=$id_customer."-".$id_driver;
			$query = $this->db->prepare("REPLACE INTO calls_customer (iCustomerId,iDriverId,dCustomerCallsDate,iUnId) VALUES (:iCustomerId,:iDriverId,NOW(),:iUnId)");
			$query->bindParam('iCustomerId', $id_customer);
			$query->bindParam('iDriverId', $id_driver);
			$query->bindParam('iUnId', $unId);
			$query->execute();
			//print_r($query->errorInfo());
			return $this->db->lastInsertId();
		}
		public function update_profile_customer($customer,$idd){
			$str="";
			if(isset($customer["email"])){
				$str.="sEmail=:sEmail, ";
			}
			if(isset($customer["avatar"])){
				$str.="iCustomerAvatar=:iCustomerAvatar, ";
			}
			if(isset($customer["avatar_hash"])){
				$str.="sHashImage=:sHashImage, ";
			}
			if(isset($customer["name"])){
				$str.="iCustomerName=:iCustomerName, ";
			}
			if(isset($customer["organization"])){
				$str.="iCustomerOrg=:iCustomerOrg, ";
			}
			if(isset($customer["phone"])){
				$str.="iCustomerPhone=:iCustomerPhone, ";
			}
			if(isset($customer["country_id"])){
				$str.="iCustomerCountry=:iCustomerCountry, ";
			}
			if(isset($customer["city_id"])){
				$str.="iCustomerCity=:iCustomerCity, ";
			}
			if(isset($customer["rating"])){
				$str.="iRating=:iRating, ";
			}
			if(isset($customer["notifications"])){
				$str.="sNotifications=:sNotifications, ";
			}
			$str=substr($str, 0,-2);
			$query = $this->db->prepare("UPDATE Customer SET $str,dEditTime=NOW() WHERE iCustomerId=:iCustomerId");
			//echo "UPDATE Orders SET $str WHERE iOrderid=:iOrderid";
			if(isset($customer["email"])){
				$query->bindParam('sEmail', $customer["email"]);
			}
			if(isset($customer["avatar"])){
				$query->bindParam('iCustomerAvatar', $customer["avatar"]);
			}
			if(isset($customer["name"])){
				$query->bindParam('iCustomerName', $customer["name"]);
			}
			if(isset($customer["organization"])){
				$query->bindParam('iCustomerOrg', $customer["organization"]);
			}
			if(isset($customer["phone"])){
				$query->bindParam('iCustomerPhone', $customer["phone"]);
			}
			if(isset($customer["country_id"])){
				$query->bindParam('iCustomerCountry', $customer["country_id"]);
			}
			if(isset($customer["city_id"])){
				$query->bindParam('iCustomerCity', $customer["city_id"]);
			}
			if(isset($customer["avatar_hash"])){
				$query->bindParam('sHashImage', $customer["avatar_hash"]);
			}
			if(isset($customer["rating"])){
				$query->bindParam('iRating', $customer["rating"]);
			}
			if(isset($customer["notifications"])){
				$query->bindParam('sNotifications', $customer["notifications"]);
			}
			$query->bindParam('iCustomerId', $idd);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
	}
?>