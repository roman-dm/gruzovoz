<?php

	class subscription_driver  extends DB_Connect{
		public function __construct($dbo=NULL, $useDate=NULL){
			parent::__construct($dbo);
		}
		public function AddSubscription($subscription,$id_driver){
				//print_r($subscription);
				$query = $this->db->prepare("INSERT INTO Driver_subscriptions (sPaymentMethod,sPaymentType, iStartId, iFinishId, sOrderType, dStartDate, dFinishDate, iMinKillo, iMaxKillo, sActive,sNotifications,iDriverId) VALUES (:sPaymentMethod,:sPaymentType,:iStartId,:iFinishId,:sOrderType,:dStartDate,:dFinishDate,:iMinKillo,:iMaxKillo,:sActive,:sNotifications,:iDriverId)");
				$query->bindParam('sPaymentMethod', $subscription["payment_method"]);
				$query->bindParam('sPaymentType', $subscription["payment_type"]);
				$query->bindParam('iStartId', $subscription["start_id"]);
				$query->bindParam('iFinishId', isset($subscription["finish_id"]) ? $subscription["finish_id"] : 0);
				$query->bindParam('sOrderType', $subscription["order_type"]);
				$query->bindParam('dStartDate', isset($subscription["start_date"]) ? $subscription["start_date"] : "");
				$query->bindParam('dFinishDate', isset($subscription["finish_date"]) ? $subscription["finish_date"] : "");
				$query->bindParam('iMinKillo', isset($subscription["min_killo"]) ? $subscription["min_killo"] : 0);
				$query->bindParam('iMaxKillo', isset($subscription["max_killo"]) ? $subscription["max_killo"] : 0);
				$query->bindParam('sActive',isset($subscription["active"]) ? $subscription["active"] : "");
				$query->bindParam('sNotifications', isset($subscription["notifications"]) ? $subscription["notifications"] : "");
				$query->bindParam('iDriverId', $id_driver);
				$query->execute();	
				//print_r($query->errorInfo());
				return $this->db->lastInsertId();
		}
		public function getOneSubscribe($id_subscribe){
			$query = $this->db->prepare("SELECT * FROM Driver_subscriptions WHERE iSubscriptions=:iSubscriptions AND iStatusId=0");
			$query->bindParam('iSubscriptions', $id_subscribe);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function getSubscriptionDriver($id_driver,$type){
			$str="";
			if($type=="count"){
				$str="COUNT(*) as count";
			}
			else{
				$str="*";
			}
			$query = $this->db->prepare("SELECT $str FROM Driver_subscriptions WHERE iDriverId=:iDriverId AND iStatusId=0");
			$query->bindParam('iDriverId', $id_driver);
			$query->execute();
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function deleteSubscribe($id_subscribe,$id_driver){
			$query = $this->db->prepare("DELETE FROM Driver_subscriptions WHERE iDriverId=:iDriverId AND iSubscriptions=:iSubscriptions");
			$query->bindParam('iDriverId', $id_driver);
			$query->bindParam('iSubscriptions', $id_subscribe);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function update_subscription($idd,$subscription){
			$str="";
			// print_r($subscription);
			// print_r($idd);
			if(isset($subscription["payment_method"])){
				$str.="sPaymentMethod=:sPaymentMethod, ";
			}
			if(isset($subscription["payment_type"])){
				$str.="sPaymentType=:sPaymentType, ";
			}
			if(isset($subscription["start_id"])){
				$str.="iStartId=:iStartId, ";
			}
			if(isset($subscription["finish_id"])){
				$str.="iFinishId=:iFinishId, ";
			}
			if(isset($subscription["order_type"])){
				$str.="sOrderType=:sOrderType, ";
			}
			if(isset($subscription["start_date"])){
				$str.="dStartDate=:dStartDate, ";
			}
			if(isset($subscription["finish_date"])){
				$str.="dFinishDate=:dFinishDate, ";
			}
			if(isset($subscription["min_killo"])){
				$str.="iMinKillo=:iMinKillo, ";
			}
			if(isset($subscription["max_killo"])){
				$str.="iMaxKillo=:iMaxKillo, ";
			}
			if(isset($subscription["active"])){
				$str.="sActive=:sActive, ";
			}
			if(isset($subscription["notifications"])){
				$str.="sNotifications=:sNotifications, ";
			}
			$str=substr($str, 0,-2);
			$query = $this->db->prepare("UPDATE Driver_subscriptions SET $str,dEditTime=NOW() WHERE iSubscriptions=:iSubscriptions AND iDriverId=:iDriverId");
			//echo "UPDATE Driver_subscriptions SET $str,dEditTime=NOW() WHERE iSubscriptions=:iSubscriptions AND iDriverId=:iDriverId";
			if(isset($subscription["payment_method"])){
				$query->bindParam('sPaymentMethod', $subscription["payment_method"]);
			}
			if(isset($subscription["payment_type"])){
				$query->bindParam('sPaymentType', $subscription["payment_type"]);
			}
			if(isset($subscription["start_id"])){
				$query->bindParam('iStartId', $subscription["start_id"]);
			}
			if(isset($subscription["finish_id"])){
				$query->bindParam('iFinishId', $subscription["finish_id"]);
			}
			if(isset($subscription["order_type"])){
				$query->bindParam('sOrderType', $subscription["order_type"]);
			}
			if(isset($subscription["start_date"])){
				$query->bindParam('dStartDate', $subscription["start_date"]);
			}
			if(isset($subscription["finish_date"])){
				$query->bindParam('dFinishDate', $subscription["finish_date"]);
			}
			if(isset($subscription["min_killo"])){
				$query->bindParam('iMinKillo', $subscription["min_killo"]);
			}
			if(isset($subscription["max_killo"])){
				$query->bindParam('iMaxKillo', $subscription["max_killo"]);
			}
			if(isset($subscription["active"])){
				$query->bindParam('sActive', $subscription["active"]);
			}
			if(isset($subscription["notifications"])){
				$query->bindParam('sNotifications', $subscription["notifications"]);
			}
			$query->bindParam('iDriverId', $idd);
			$query->bindParam('iSubscriptions', $subscription["id"]);
			$query->execute();
			//print_r($query->errorInfo());
			return $query->rowCount();
		}
		public function getDriver($arr,$type){
			//print_r($arr);
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
			if(isset($arr["region_id"])){
				$query->bindParam('iRegionId', $arr["region_id"]);
			}
			if(isset($arr["id"])){
				//echo "yes";
				$query->bindParam('iDriverId', $arr["id"]);
			}
			$query->execute();
			//print_r($query->errorInfo());
			return $query->fetchAll(PDO::FETCH_ASSOC);
		}
		public function arraySortSubsribe($arr,$type){
			//print_r($arr);
			$this->model_order = new Order();
			$this->model_geo = new Geo();
			$new_array=array();
			$new_array["id"]=$arr["iSubscriptions"];
	        $new_array["payment_type"]=$arr["sPaymentType"];
	        $new_array["payment_method"]=$arr["sPaymentMethod"];
	        $new_array["order_type"]=$arr["sOrderType"];
	       	$new_array["min_killo"]=$arr["iMinKillo"];
	       	$new_array["active"]=$arr["sActive"];
	       	$new_array["notifications"]=$arr["sNotifications"];
	       	if($type=="app"){
	       		if($arr["dStartDate"]=="0000-00-00 00:00:00"){
	       			$new_array["start_date"]="";
	       		}
	       		else{
	       			$dt = new DateTime($arr["dStartDate"]);
 					$new_array["start_date"]=$dt->format(DATE_ISO8601);
 				}
	       	}
	       	else{
	       		if($arr["dStartDate"]=="0000-00-00 00:00:00"){
	       			$new_array["start_date"]=null;
	       			}
	       		else{
	       			$new_array["start_date"]=date("d-m-Y",strtotime($arr["dStartDate"]));
	       		}
	       	}
	       	$new_array["max_killo"]=$arr["iMaxKillo"];
 				if($type=="app"){
 					if($arr["dFinishDate"]=="0000-00-00 00:00:00"){
	       				$new_array["finish_date"]="";
		       		}
		       		else{
						$dt = new DateTime($arr["dFinishDate"]);
	 					$new_array["finish_date"]=$dt->format(DATE_ISO8601);
	 				}
 				}
 				else{
 					if($arr["dFinishDate"]=="0000-00-00 00:00:00"){
	       				$new_array["finish_date"]=null;
	       			}
	       			else{
	        			$new_array["finish_date"]=date("d-m-Y",strtotime($arr["dFinishDate"]));
	        		}
 				}
	        if(isset($arr["iStartId"]) && $arr["iStartId"]!=0){
 						//echo "yes"; echo $value["iStartCityId"];
 						//echo $value["iStartCityId"];
	 					$start_city_info=$this->model_order->getCity($arr["iStartId"]);
	 					if(isset($start_city_info["region"])){
							$info_region=$this->model_geo->getRegion($start_city_info["region"]);
							$info_region[0]["typeRegion"]=true;
						}
						else{
							$start_city_info["region"]="";
							$info_region[0]["iRegionVkid"]="";
							$info_region[0]["typeRegion"]=false;
						}
	 					$new_array["start_city"]=array("id"=>$start_city_info["id"],"name"=>$start_city_info["title"],"parent_name"=>$start_city_info["region"],"parent_id"=>$info_region[0]["iRegionVkid"],"region"=>$info_region[0]["typeRegion"],"country_name"=>"Россия","country_id"=>1);
 					}
 					if(isset($arr["iFinishId"]) && $arr["iFinishId"]!=0){
 						//echo $value["iFinishCityId"];
 						$finish_city_info=$this->model_order->getCity($arr["iFinishId"]);
	 					//print_r($finish_city_info);
	 					if(isset($finish_city_info["region"])){
							$info_region_finish=$this->model_geo->getRegion($finish_city_info["region"]);
							$info_region_finish[0]["typeRegion"]=true;
						}
						else{
							//echo "yes";
							$finish_city_info["region"]="";
							$info_region_finish[0]["iRegionVkid"]="";
							$info_region_finish[0]["typeRegion"]=false;
							//print_R($info_region_finish);
						}
	 					//print_r($info_region_finish);
						//$info_region_finish=$this->model_geo->getRegion($finish_city_info["region"]);
						$new_array["finish_city"]=array(
							"id"=>$finish_city_info["id"],
							"name"=>$finish_city_info["title"],
							"parent_name"=>$finish_city_info["region"],
							"parent_id"=>$info_region_finish[0]["iRegionVkid"],
							"region"=>$info_region_finish[0]["typeRegion"],
							"country_name"=>"Россия",
							"country_id"=>1);
					}
	        return $new_array;
		}
	}
?>