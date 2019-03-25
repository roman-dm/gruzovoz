<?php
Class admin_customer extends base{
	public $profile;
	public $user_list;
	public $user_mas=array();
	public function __construct(){
		parent::__construct();
		$this->profile_user["current_page"]=$this->current_page[1];
		if(isset($_SESSION["type_user"])){
			$this->profile_user["status"]=$_SESSION["type_user"];
		}
			//$this->profile=parent::getProfile();
			// if(!parent::accessPage("root")){
			// 	echo $this->view->render('denied.html');
			// 	exit();
			// };
			// $this->model=new User();
			// $this->user_list=$this->model->getUser(array("query_type"=>"user_list"));
			// foreach ($this->user_list as $user) {
			// 	$this->user_mas[$user["iStatusId"]]["name_group"]=$user["sStatusName"];
			// 	$this->user_mas[$user["iStatusId"]]["name_group_russia"]=$user["sStatusNameRussia"];
			// 	$this->user_mas[$user["iStatusId"]]["group"][]=$user;
			// }
	}
	public function main(){
		$this->model_driver=new Driver();
		$list_driver=$this->model_driver->getDriver(array("limit"=>3),"site");
			//print_r($list_driver);
		$new_array=array();
		foreach ($list_driver as $key => $value) {
			$new_array[]=$this->model_driver->arraySortDriver($value);
		}
				//print_r($this->profile_user);
		echo $this->view->render('customer_main.html',array("list_driver"=>$new_array,"profile"=>$this->profile_user));
			//print_r($this->profile[0]);
		echo "<pre>"; print_r($new_array); echo "</pre>";

	}
	public function get_left_profile(){
		parent::checkToken("main");
		$this->model_customer=new Customer();
		$this->model_order=new Order();
		$this->model_geo= new Geo();
		$this->model_auth= new Auth_User();
		$id_device=parent::getIdDevice();
			//print_r($id_device);
		$id_customer=$this->model_customer->findCustomer($id_device);
				//print_r($_SESSION);
				//выгружаем все заказы
		$info_order=$this->model_customer->getOrder($ar,"count");
				// print_r($id_customer);
		$profile=$this->model_customer->getProfile($id_customer[0]["iCustomerId"]);
		$device=$this->model_auth->getInfoDevice($profile[0]["iDeviceId"]);
				//print_r($device[0]["dDateSend"]);
		$new_profile["name"]=$profile[0]["iCustomerName"];
				//$new_profile["avatar"]=$profile[0]["iCustomerPhone"];
		$link="";
		if(!empty($profile[0]["sHashImage"])){
			$link="/get_image_phone/?image=".$profile[0]["sHashImage"];
		}
		$new_profile["avatar"]=$link;
				//$new_profile["avatar"]=$profile[0]["iCustomerAvatar"];
		$new_profile["rating"]=$profile[0]["iRating"];
		if(empty($profile[0]["sNotifications"])){
			$new_profile["notifications"]="false";
		}
		else{
			$new_profile["notifications"]=$profile[0]["sNotifications"];
		}
		$link="";
		if(!empty($profile[0]["sHashImage"])){
			$link="/get_image_phone/?image=".$arr["sHashImage"];
		}
		echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("profile"=>$new_profile)));
	}
	public function add_avatar(){
		parent::checkToken("main");
		$this->model_customer=new Customer();
		$id_device=parent::getIdDevice();
		$id_customer=$this->model_customer->findCustomer($id_device);
		$image=parent::getDateImage();
		$md5_link=md5(microtime());
		$customer=array();
		// print_r($id_customer);
		$id=$id_customer[0]["iCustomerId"];
		$link_to_image="/images/customer/".$id."/l_".$md5_link.".jpg";
		$link_to_small_image="/images/customer/".$id."/s_".$md5_link.".jpg";
		if(!is_dir($_SERVER["DOCUMENT_ROOT"]."/images/customer/".$id."/")) mkdir($_SERVER["DOCUMENT_ROOT"]."/images/customer/".$id."/");
		$handle = fopen($_SERVER["DOCUMENT_ROOT"].$link_to_image, "w+");
		$new_cus=explode("Content-Type: image/jpeg",$image);
		$new_cus2=explode("----------------------------",$new_cus[1]);
			        // print_r($new_cus2[0]);
		fwrite($handle,substr($new_cus2[0], 4));
		fclose($handle);
		$customer["avatar"]=$md5_link.".jpg";
		$customer["avatar_hash"]=$md5_link;
		if(parent::img_resize($_SERVER["DOCUMENT_ROOT"].$link_to_image, $_SERVER["DOCUMENT_ROOT"].$link_to_small_image, 100, 0)!=1){
		    			//echo parent::json_encode_cyr(array("result"=> false));
		    			//exit();
		}
		if(!empty($profile[0]["iCustomerAvatar"])){
			unlink($_SERVER["DOCUMENT_ROOT"]."/images/customer/".$id_customer[0]["iCustomerId"]."/s_".$profile[0]["iCustomerAvatar"]);
			unlink($_SERVER["DOCUMENT_ROOT"]."/images/customer/".$id_customer[0]["iCustomerId"]."/l_".$profile[0]["iCustomerAvatar"]);
		}
		$result=$this->model_customer->update_profile_customer($customer,$id_customer[0]["iCustomerId"]);
		if($result==1){
			echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>(object)array()));
		}
		else{
			echo parent::json_encode_cyr(array("code"=> 23,"message"=> "Не удалось изменить профиль", "data"=> (object)array()));
		}
	}
	public function get_profile(){
		parent::checkToken("main");
		$this->model_customer=new Customer();
		$this->model_order=new Order();
		$this->model_geo= new Geo();
		$this->model_auth= new Auth_User();
		$id_device=parent::getIdDevice();
		$id_customer=$this->model_customer->findCustomer($id_device);
		if(isset($_GET["debug"])){
			header("Content-type: text/plain");
			echo "yes";
		}
		if($_SERVER['REQUEST_METHOD']=="GET"){
				//print_r($_SESSION);
				//выгружаем все заказы
			$info_order=$this->model_customer->getOrder($ar,"count");
				// print_r($id_customer);
			$profile=$this->model_customer->getProfile($id_customer[0]["iCustomerId"]);
			$device=$this->model_auth->getInfoDevice($profile[0]["iDeviceId"]);
				//print_r($device[0]["dDateSend"]);
			$new_profile["name"]=$profile[0]["iCustomerName"];
			if($device[0]["sSmsNumber"][0]=="8" || $device[0]["sSmsNumber"][0]=="7"){
				$device[0]["sSmsNumber"]=substr($device[0]["sSmsNumber"], 1);
			}
			else if($device[0]["sSmsNumber"][0]=="+" && $device[0]["sSmsNumber"][1]=="7"){
				$device[0]["sSmsNumber"]=substr($device[0]["sSmsNumber"], 2);
			}
			$new_profile["phone"]=$device[0]["sSmsNumber"];
			$new_profile["phone_confirmed"]=$device[0]["iStatus"]==1 ? "true" : "false";
			$new_profile["organization"]=$profile[0]["iCustomerOrg"];
				//$new_profile["avatar"]=$profile[0]["iCustomerPhone"];
			$link="";
			if(!empty($profile[0]["sHashImage"])){
				$link="/get_image_phone/?image=".$profile[0]["sHashImage"];
			}
			$new_profile["avatar"]=$link;
				//$new_profile["avatar"]=$profile[0]["iCustomerAvatar"];
			$new_profile["rating"]=$profile[0]["iRating"];
			if(empty($profile[0]["sNotifications"])){
				$new_profile["notifications"]="false";
			}
			else{
				$new_profile["notifications"]=$profile[0]["sNotifications"];
			}
			$link="";
			if(!empty($profile[0]["sHashImage"])){
				$link="/get_image_phone/?image=".$arr["sHashImage"];
			}
			if($profile[0]["iCustomerCity"]!=0){
				$start_city_info=$this->model_order->getCity($profile[0]["iCustomerCity"]);
				if(isset($start_city_info["region"])){
					$info_region=$this->model_geo->getRegion($start_city_info["region"]);
					$info_region[0]["typeRegion"]=true;
				}
				else{
					$start_city_info["region"]="";
					$info_region[0]["iRegionVkid"]="";
					$info_region[0]["typeRegion"]=false;
				}
						//$info_region=$this->model_geo->getRegion($start_city_info["region"]);
				$new_profile["city"]=array(
					"id"=>$start_city_info["id"],
					"name"=>trim($start_city_info["title"]),
					"parent_name"=>trim($start_city_info["region"]),
					"parent_id"=>$info_region[0]["iRegionVkid"],
					"region"=>$info_region[0]["typeRegion"],
					"country_name"=>"Россия",
					"country_id"=>1);
			}
			echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("profile"=>$new_profile)));
		}
		else if($_SERVER['REQUEST_METHOD']=="PATCH"){
			$customer=parent::getFormData();
			$profile=$this->model_customer->getProfile($id_customer[0]["iCustomerId"]);
 				//print_r($profile);
			$device_info=$this->model_auth->getInfoDevice($profile[0]["iDeviceId"]);
				//print_r($customer);
			if(isset($customer["phone"])){
				$new_phone=str_replace(array(')','('," ","-"),"",$customer["phone"]);
				if($new_phone[0]=="+" && $new_phone[1]=="7"){
					$code_country="+7";
					$new_phone=substr($new_phone, 2);
				}
				else if($new_phone[0]=="8"){
					$code_country="+7";
					$new_phone=substr($new_phone, 1);
				}
				else if($new_phone[0]=="9"){
					$code_country="+7";
				}
				$phone=$code_country.$new_phone;
				if($phone!=$device_info[0]["sSmsNumber"] && strlen($phone)>5){
						//print_r($customer);
					$this->model_auth->updatePhoneAndStatus($profile[0]["iDeviceId"],$phone);
				}
				if(isset($_GET["debug"])){
					print_r($customer);
					print_r($id_customer);
					print_r($customer);
				}
			}
			$result=$this->model_customer->update_profile_customer($customer,$id_customer[0]["iCustomerId"]);
 				//print_r($result);
			if($result==1){
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>(object)array()));
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 23,"message"=> "Не удалось изменить профиль", "data"=> (object)array()));
			}
		}
			// echo $this->view->render('get_one_customer.html',array("all_profile"=>$new_profile,"profile"=>$this->profile_user));
			// echo "<pre>"; 
			// 	print_r($new_profile);
			// echo "</pre>";
	}
	public function get_one_customer($id_customer){
		$this->model=new Customer();
		$this->model_order=new Order();
		$this->model_geo= new Geo();
		$this->model_auth= new Auth_User();
				//print_r($_SESSION);
				//выгружаем все заказы
		$device[0]["iDeviceId"]=$_SESSION["id_user"];
				//print_r($id_device);
		$ar["customer_id"]=$id_customer[0];
		$info_order=$this->model->getOrder($ar,"count");
		$profile=$this->model->getProfile($id_customer[0]);
		$device=$this->model_auth->getInfoDevice($profile[0]["iDeviceId"]);
				//print_r($device[0]["dDateSend"]);
		$new_profile["name"]=$profile[0]["iCustomerName"];
		$new_profile["date_enter"]=$device[0]["dDateSend"];
		$new_profile["organization"]=$profile[0]["iCustomerOrg"];
				//$new_profile["avatar"]=$profile[0]["iCustomerPhone"];
		$new_profile["country_id"]=1;
		$new_profile["count"]=$info_order[0]["count"];
		$new_profile["avatar"]=$profile[0]["iCustomerAvatar"];
		$new_profile["rating"]=$profile[0]["iRating"];
		if($profile[0]["iCustomerCity"]!=0){
			$start_city_info=$this->model_order->getCity($profile[0]["iCustomerCity"]);
			$info_region=$this->model_geo->getRegion($start_city_info["region"]);
			$new_profile["city"]=array(
				"id"=>$start_city_info["id"],
				"name"=>trim($start_city_info["title"]),
				"parent_name"=>trim($start_city_info["region"]),
				"parent_id"=>$info_region[0]["iRegionVkid"],
				"region"=>true,
				"country_name"=>"Россия",
				"country_id"=>1);
		}
		echo $this->view->render('get_one_customer.html',array("all_profile"=>$new_profile,"profile"=>$this->profile_user));
		echo"all_profile";
		echo "<pre>";
		print_r($new_profile);
		echo "</pre>";
		echo"profile";
		echo "<pre>";
		print_r($this->profile_user);
		echo "<pre>";

	}
	public function get_profile_customer(){
		$this->model=new Customer();
		$profile=$this->model->getProfile($_POST["id_customer"]);
		$new_profile["name"]=$profile[0]["iCustomerName"];
		$link="";
		if(!empty($profile[0]["sHashImage"])){
			$link="/get_image/?image=".$profile[0]["sHashImage"];
		}
		$new_profile["avatar"]=$link;
		echo parent::json_encode_cyr(array("result"=>true,"profile"=>$new_profile,"id_customer"=>$_POST["id_customer"]));
	}
	public function get_phone(){
		parent::checkSession();
		$this->model=new Customer();
		$this->model_auth=new Auth_user();
		if($_SESSION["type_user"]!="driver"){
			echo parent::json_encode_cyr(array("result"=> false));
		}
		else{
			$info_order=$this->model->getOrder(array("id"=>$_POST["id"]),"site");
			$profile_user=$this->model->getProfile($info_order[0]["iCustomerID"]);
				//print_r($profile_user[0]["iDeviceId"]);
			$device=$this->model_auth->getInfoDevice($profile_user[0]["iDeviceId"]);
			echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "phone"=>$device[0]["sSmsNumber"]));

		}
	}
	public function getPhoneDriver($id_driver){
		parent::checkToken("main");
		$this->model_driver=new Driver();
		$this->model_customer=new Customer();
		$this->model_auth=new Auth_user();
			// $info_order=$this->model_customer->getOrder(array("id"=>$id_order[0]),"site");
			// //print_r($info_order);
			// if($info_order[0]["iDriverId"]<1){
			// 	echo parent::json_encode_cyr(array("code"=> 31,"message"=> "Не удалось получить телефон водителя", "data"=> (object)array()));
			// 	exit();
			// }
		$id_device=parent::getIdDevice();
		$id_customer=$this->model_customer->findCustomer($id_device);
			//print_r($id_device);
		if(count($id_customer)==0){
			echo parent::json_encode_cyr(array("code"=> 9,"message"=> "Проблемы с получением телефонного номера", "data"=> (object)array()));
			exit();
		}
			//print_r($info_order);
		$driver[0]["iDriverId"]=$id_driver[0];
		$profile=$this->model_driver->getProfileDriver($driver);
		if(count($profile)==0){
			echo parent::json_encode_cyr(array("code"=> 26,"message"=> "Водитель не найден", "data"=> (object)array()));
			exit();
		}
			//print_r($id_customer);
		$result=$this->model_customer->updateCalls($id_customer[0]["iCustomerId"],$driver[0]["iDriverId"]);
		$device=$this->model_auth->getInfoDevice($profile[0]["iDeviceId"]);
		echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("phone"=>$device[0]["sSmsNumber"])));
	}
	public function delete_avatar(){
		parent::checkToken("main");
		$this->model_customer=new Customer();
		$id_device=parent::getIdDevice();
		$id_customer=$this->model_customer->findCustomer($id_device);
		$profile=$this->model_customer->getProfile($id_customer[0]["iCustomerId"]);
			//print_r($id_device);
		$id=$this->model_customer->update_profile_customer(array("avatar"=>"","avatar_hash"=>""),$id_customer[0]["iCustomerId"]);
		if($id==1){
			if(!empty($profile[0]["iCustomerAvatar"]) && file_exists($_SERVER["DOCUMENT_ROOT"]."/images/customer/".$id_customer[0]["iCustomerId"]."/s_".$profile[0]["iCustomerAvatar"])){
				unlink($_SERVER["DOCUMENT_ROOT"]."/images/customer/".$id_customer[0]["iCustomerId"]."/s_".$profile[0]["iCustomerAvatar"]);
				unlink($_SERVER["DOCUMENT_ROOT"]."/images/customer/".$id_customer[0]["iCustomerId"]."/l_".$profile[0]["iCustomerAvatar"]);
			}
			echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> (object)array()));
		}
		else{
			echo parent::json_encode_cyr(array("code"=> 23,"message"=> "Не удалось изменить профиль", "data"=> (object)array()));
		}
	}
	public function getCountDriver(){
		$this->model_driver=new Driver();
		$this->model_customer=new Customer();
		$id_device=parent::getIdDevice();
		$id_customer=$this->model_customer->findCustomer($id_device);
		if(count($id_customer)==0){
			echo parent::json_encode_cyr(array("code"=> 37,"message"=> "Заказчик не найден", "data"=> (object)array()));
			exit();
		}
		if(count($_POST)>0 && !isset($_POST["region_id"])){
			echo parent::json_encode_cyr(array("code"=> 38,"message"=> "Нет выбран регион в фильтре", "data"=> (object)array()));
			exit();
		}
		if(count($_POST)==0){
			$this->model_order=new order();
			$this->model_geo=new Geo();
			$profile=$this->model_customer->getProfile($id_customer[0]["iCustomerId"]);
			if($profile[0]["iCustomerCity"]==1 || $profile[0]["iCustomerCity"]==2){
				$_POST["city_id"]=$profile[0]["iCustomerCity"];
			}
			else if($profile[0]["iCustomerCity"]>2){
				$city_info=$this->model_order->getCity($profile[0]["iCustomerCity"]);
				$info_region_finish=$this->model_geo->getRegion($city_info["region"]);
				if(!empty($info_region_finish[0]["iRegionVkid"])){
					$_POST["region_id"]=$info_region_finish[0]["iRegionVkid"];
				}
			}
		}
		if($_POST["region_id"]==1 || $_POST["region_id"]==2){
			$_POST["city_id"]=$_POST["region_id"];
			unset($_POST["region_id"]);
		}
		unset($_POST["limit"]);
		unset($_POST["offset"]);
		if(isset($_POST["body_types_ids"]) && !empty($_POST["body_types_ids"])){
			$body_types_ids=json_decode($_POST["body_types_ids"],true);
			$_POST["body_types_ids"]=implode(",",$body_types_ids);
		}
		$count=$this->model_driver->getDriver($_POST,"count");
		echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> array("count"=>$count[0]["count"])));
	}
	public function getDriver(){
		parent::checkToken("any");
		$this->model_driver=new Driver();
		$this->model_customer=new Customer();
		$id_device=parent::getIdDevice();
		$id_customer=$this->model_customer->findCustomer($id_device);
		$headers = getallheaders();
		if(($headers["X-Auth-Token"]!=$this->token[0]["sToken"]) && ($headers["x-auth-token"]!=$this->token[0]["sToken"]) && ($_POST["offest"]+$_POST["limit"])>30){
			echo parent::json_encode_cyr(array("code"=> 39,"message"=> "Превышен лимит просмотра водителей", "data"=> (object)array()));exit();
		}
		if(count($id_customer)==0){
			echo parent::json_encode_cyr(array("code"=> 37,"message"=> "Заказчик не найден", "data"=> (object)array()));
			exit();
		}
		if(count($_POST)>0 && !isset($_POST["region_id"])){
			echo parent::json_encode_cyr(array("code"=> 38,"message"=> "Нет выбран регион в фильтре", "data"=> (object)array()));
			exit();
		}
		if(count($_POST)==0){
			$this->model_order=new order();
			$this->model_geo=new Geo();
			$profile=$this->model_customer->getProfile($id_customer[0]["iCustomerId"]);
			if($profile[0]["iCustomerCity"]==1 || $profile[0]["iCustomerCity"]==2){
				$_POST["city_id"]=$profile[0]["iCustomerCity"];
			}
			else if($profile[0]["iCustomerCity"]>2){
				$city_info=$this->model_order->getCity($profile[0]["iCustomerCity"]);
				$info_region_finish=$this->model_geo->getRegion($city_info["region"]);
				if(!empty($info_region_finish[0]["iRegionVkid"])){
					$_POST["region_id"]=$info_region_finish[0]["iRegionVkid"];
				}
			}
		}
		if($_POST["region_id"]==1 || $_POST["region_id"]==2){
			$_POST["city_id"]=$_POST["region_id"];
			unset($_POST["region_id"]);
		}
		if(isset($_POST["body_types_ids"]) && !empty($_POST["body_types_ids"])){
			$body_types_ids=json_decode($_POST["body_types_ids"],true);
			$_POST["body_types_ids"]=implode(",",$body_types_ids);
		}
		$list_driver=$this->model_driver->getDriver($_POST,"app");
		if(($headers["X-Auth-Token"]!=$this->token[0]["sToken"]) && ($headers["x-auth-token"]!=$this->token[0]["sToken"]) && count($list_driver)>0){
			echo parent::json_encode_cyr(array("code"=> 39,"message"=> "Превышен лимит просмотра заказов", "data"=> (object)array()));exit();
		}
			//print_r($_POST);
		$new_array=array();
		foreach ($list_driver as $key => $value) {
			$new_array[]=$this->model_driver->arraySortDriverApp($value);
		}
		unset($_POST["limit"]);
		unset($_POST["offset"]);
		$count=$this->model_driver->getDriver($_POST,"count");
		echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> array("drivers"=>$new_array,"total"=>$count[0]["count"])));
	}
	public function close_order(){
		parent::checkSession();
		$this->model=new Customer();
		$info_order=$this->model->getOrder(array("id"=>$_POST["id_order"]),"site");
		if($info_order[0]["iDriverId"]==0){
			echo parent::json_encode_cyr(array("result"=>false,"error"=>"Водитель не выбран"));
		}
			//print_r($info_order);
		if(parent::checkRating($_POST["rating"],"driver",$info_order[0]["iDriverId"])){
			echo parent::json_encode_cyr(array("result"=>true));
		}
		else{
			echo parent::json_encode_cyr(array("result"=>false));
		}
			//$idd=$this->model_driver->rateOrder($id_order[0],$_POST["rating"]);
	}
}