<?php
Class admin_driver extends base{
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
			// $this->user_list=$this->model->getUser(array("query_type"=>"user_list"));
			// foreach ($this->user_list as $user) {
			// 	$this->user_mas[$user["iStatusId"]]["name_group"]=$user["sStatusName"];
			// 	$this->user_mas[$user["iStatusId"]]["name_group_russia"]=$user["sStatusNameRussia"];
			// 	$this->user_mas[$user["iStatusId"]]["group"][]=$user;
			// }
	}
	public function main(){
		$this->model_customer=new Customer();
		$this->model_order=new Order();
		$this->model_main=new Main();
		$this->profile=array("status"=>$_SESSION["type_user"]);
		$list_order=$this->model_customer->getOrder(array("limit"=>6,"status_id"=>0),"site");
				// echo "<pre>";
				// print_r($list_order);
				// echo "</pre>";
		$new_array_order=$this->model_main->arrayResort($list_order);
				//print_r($this->profile_user);
		echo $this->view->render('driver_main.html',array("list_order"=>$new_array_order,"profile"=>$this->profile_user));
		echo "<pre>"; print_r($new_array_order); echo "</pre>";

	}
	public function get_drivers(){
		echo $this->view->render('get_drivers.html',array("profile"=>$this->profile_user));
	}
	public function get_one_driver($id_driver){
		$this->model=new Driver();
		$this->model_order=new Order();
		$this->model_geo=new Geo();
		$this->model_customer=new Customer();
		$this->model_auth=new Auth_User();
		$id_driver_new[0]["iDriverId"]=$id_driver[0];
		$profile=$this->model->getProfileDriver($id_driver_new);
				//print_r($id_driver[0]);
		$ar["driver_id"]=$id_driver[0];
		$info_order=$this->model_customer->getOrder($ar,"count");
				//print_r($info_order);
		$device=$this->model_auth->getInfoDevice($profile[0]["iDeviceId"]);
				//print_r($profile);
		$new_profile=array();
		$new_profile["name"]=$profile[0]["sDriverName"];
		$new_profile["count"]=$info_order[0]["count"];
		$new_profile["date_enter"]=$device[0]["dDateSend"];
		$new_profile["phone"]=$profile[0]["sDriverPhone"];
		$link="";
		if(!empty($profile[0]["sHashImage"])){
			$link="/get_image/?image=".$profile[0]["sHashImage"];
		}
		$new_profile["avatar"]=$link;
		$new_profile["phone_confirmed"]=0;
		$new_profile["car_name"]=$profile[0]["sCarName"];
		$new_profile["car_type"]=$profile[0]["iCarType"];
		$new_profile["body_type_id"]=$profile[0]["iBodyType"];
		$new_profile["capacity"]=$profile[0]["iCapacity"];
		$new_profile["volume"]=$profile[0]["iVolume"];
		$new_profile["load_type_top"]=$profile[0]["sLoadTypeTop"];
		$new_profile["load_type_rear"]=$profile[0]["sLoadTypeRear"];
		$new_profile["load_type_side"]=$profile[0]["sLoadTypeSide"];
		$new_profile["driver_specialization"]=$profile[0]["sDriverSpecialization"];
		$new_profile["loaders"]=$profile[0]["iLoaders"];
		$new_profile["rate"]=$profile[0]["iRate"];
		if($profile[0]["iCityId"]!=0){
			$start_city_info=$this->model_order->getCity($profile[0]["iCityId"]);
			$info_region=$this->model_geo->getRegion($start_city_info["region"]);
			$new_profile["city"]=array(
				"id"=>$start_city_info["id"],
				"name"=>$start_city_info["title"],
				"parent_name"=>$start_city_info["region"],
				"parent_id"=>$info_region[0]["iRegionVkid"],
				"region"=>true,
				"country_name"=>"Россия",
				"country_id"=>1);
		}
		else{
			$new_profile["city"]=0;
		}
		echo $this->view->render('get_one_driver.html',array("all_profile"=>$new_profile,"profile"=>$this->profile_user));
		echo"all_profile";
		echo "<pre>";
		print_r($new_profile);
		echo "</pre>";
		echo"profile";
		echo "<pre>";
		print_r($this->profile_user);
		echo "<pre>";

	}
	public function orders_catalogue(){
		parent::checkToken("any");
		$this->model=new driver();
		$this->model_customer=new Customer();
		$this->model->order=new order();
		$this->model_geo=new Geo();
		$this->model_auth=new Auth_user();
			// if( (!isset($_POST["payment_method"]) || empty($_POST["payment_method"])) || (!isset($_POST["payment_type"]) || empty($_POST["payment_type"])) || (!isset($_POST["start_id"]) || empty($_POST["start_id"])) || (!isset($_POST["start_date"]) || empty($_POST["start_date"]))){
			// 	echo parent::json_encode_cyr(array("code"=> 7,"message"=> "Проблемы с выгрузкой каталога заказов", "data"=> ""));
			// 	exit();
			// }
		$headers = getallheaders();
			//print_r($headers);
			//echo $headers["x-auth-token"]."---";
			//echo $this->token[0]["sToken"];
		$_POST["driver_id"]=0;
		$_POST["status_id"]=0;
		$_POST["limit"]=$_GET["limit"];
		$_POST["offset"]=$_GET["offset"];
		if(($headers["X-Auth-Token"]!=$this->token[0]["sToken"]) && ($headers["x-auth-token"]!=$this->token[0]["sToken"]) && ($_POST["offest"]+$_POST["limit"])>30){
			echo parent::json_encode_cyr(array("code"=> 35,"message"=> "Превышен лимит просмотра заказов", "data"=> (object)array()));exit();
		}
		$this->model_auth->get_token_guest($token);
			//print_r($this->token);
		if(isset($_POST["start_date"]) && !empty($_POST["start_date"])){
			$_POST["start_date"]=date('Y-m-d H:i:s', strtotime(substr($_POST["start_date"],0,10)));
		}
		if(isset($_POST["finish_date"]) && !empty($_POST["finish_date"])){
			$_POST["finish_date"]=date('Y-m-d H:i:s', strtotime(substr($_POST["finish_date"],0,10)));
		}
		if(isset($_POST["body_types_ids"]) && !empty($_POST["body_types_ids"])){
			$body_types_ids=json_decode($_POST["body_types_ids"],true);
			$_POST["body_types_ids"]=implode(",",$body_types_ids);
		}
			//print_r($_POST);
		$list_order=$this->model_customer->getOrder($_POST,"app");
		$new_list_order=array();
		if(count($list_order)>0){
			foreach ($list_order as $key => $value) {
				$new_list_order_element=array();
				if ($value["dStartDate"] != "0000-00-00 00:00:00") { 
					$dt = new DateTime($value["dStartDate"]);
					$new_list_order_element["start_date"]=$dt->format(DATE_ISO8601);
				} else { 
					$new_list_order_element["start_date"]="";
				}
					// echo $value["dFinishDate"];
					// echo "yes";
				if ($value["dFinishDate"] != "0000-00-00 00:00:00" || !isset($value["dFinishDate"])) { 
						//echo $value["dFinishDate"];
					$dt = new DateTime($value["dFinishDate"]);
					$new_list_order_element["finish_date"]=$dt->format(DATE_ISO8601);}
					else{
						$new_list_order_element["finish_date"]="";
					}
					$new_list_order_element["id"]=$value["iOrderid"];
					$new_list_order_element["cargo_name"]=$value["sCargoName"];
					if($value["sWeightUnit"]=="ton"){
						$new_list_order_element["cargo_weight"]=$value["iCargWeight"]/1000;
					}
					else{
						$new_list_order_element["cargo_weight"]=$value["iCargWeight"];
					}
					//$new_list_order_element["cargo_weight"]=$value["iCargWeight"];
					$new_list_order_element["weight_unit"]=$value["sWeightUnit"];
					$new_list_order_element["price"]=$value["iPrice"];
					$new_list_order_element["status"]=$value["iStatus"];
					$new_list_order_element["currency"]=$value["sCurrencyTypePrice"];
					$new_list_order_element["payment_method"]=$value["sPaymentMethod"];
					$new_list_order_element["capacity"]=$value["iCapacityId"];
					$list_body_types=$this->model_customer->getBodyOrders($value["iOrderid"]);
					if(count($list_body_types)>0){
						$new_list_body=array();
						foreach ($list_body_types as $key => $value) {
							$new_list_body[]=array("id"=>$value["iBodyTypeId"],"name"=>$value["iBodyTypeName"]);
						}
						$new_list_order_element["body_types"]=$new_list_body;
					}
					else{
						$new_list_order_element["body_types"]=null;
					}
					//$start_city_info=$this->model->order->getCity($value["iStartCityId"]);
					//$info_region=$this->model_geo->getRegion($start_city_info["region"]);
 					// $new_list_order_element["start_city"]=array("id"=>$start_city_info["id"],"name"=>$start_city_info["title"],"parent_name"=>$start_city_info["region"],"parent_id"=>$info_region[0]["iRegionVkid"],"region"=>true,"country_name"=>"Россия","country_id"=>1);
 					// $finish_city_info=$this->model->order->getCity($info_order[0]["iFinishCityId"]);
 					// //print_r($finish_city_info);
					// $info_region_finish=$this->model_geo->getRegion($finish_city_info["region"]);
					// $new_list_order_element["finish_city"]=array("id"=>$finish_city_info["id"],"name"=>$finish_city_info["title"],"parent_name"=>$finish_city_info["region"],"parent_id"=>$info_region_finish[0]["iRegionVkid"],"region"=>true,"country_name"=>"Россия","country_id"=>1);

					$start_city_info=$this->model->order->getCity($value["iStartCityId"]);
					if(isset($start_city_info["region"])){
						$info_region=$this->model_geo->getRegion($start_city_info["region"]);
						$info_region[0]["typeRegion"]=true;
					}
					else{
						$start_city_info["region"]="";
						$info_region[0]["iRegionVkid"]="";
						$info_region[0]["typeRegion"]=false;
					}
 					//print_r($info_order[0]["iFinishCityId"]);
					$new_list_order_element["start_city"]=array("id"=>$start_city_info["id"],"name"=>$start_city_info["title"],"parent_name"=>$start_city_info["region"],"parent_id"=>$info_region[0]["iRegionVkid"],"region"=>$info_region[0]["typeRegion"],"country_name"=>"Россия","country_id"=>1);
					$finish_city_info=$this->model->order->getCity($value["iFinishCityId"]);
 					//print_r($finish_city_info["region"]);
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
 					//print_r($finish_city_info);
					$new_list_order_element["finish_city"]=array("id"=>$finish_city_info["id"],"name"=>$finish_city_info["title"],"parent_name"=>$finish_city_info["region"],"parent_id"=>$info_region_finish[0]["iRegionVkid"],"region"=>$info_region_finish[0]["typeRegion"],"country_name"=>"Россия","country_id"=>1);

					$new_list_order[]=$new_list_order_element;
					//print_r($value);
				}
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("orders"=>$new_list_order,"total"=>count($new_list_order))));
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("orders"=>array(),"total"=>0)));
			}
		}
		public function orders_catalogue_count(){
			parent::checkToken("any");
			$this->model=new driver();
			$this->model_customer=new Customer();
			$this->model->order=new order();
			$this->model_geo=new Geo();
			$this->model_auth=new Auth_user();
			// if( (!isset($_POST["payment_method"]) || empty($_POST["payment_method"])) || (!isset($_POST["payment_type"]) || empty($_POST["payment_type"])) || (!isset($_POST["start_id"]) || empty($_POST["start_id"])) || (!isset($_POST["start_date"]) || empty($_POST["start_date"]))){
			// 	echo parent::json_encode_cyr(array("code"=> 7,"message"=> "Проблемы с выгрузкой каталога заказов", "data"=> ""));
			// 	exit();
			// }
			$headers = getallheaders();
			//print_r($headers);
			//echo $headers["x-auth-token"]."---";
			//echo $this->token[0]["sToken"];
			$_POST["driver_id"]=0;
			$_POST["status_id"]=0;
			if(($headers["X-Auth-Token"]!=$this->token[0]["sToken"]) && ($headers["x-auth-token"]!=$this->token[0]["sToken"]) && ($_POST["offest"]+$_POST["limit"])>30){
				echo parent::json_encode_cyr(array("code"=> 35,"message"=> "Превышен лимит просмотра заказов", "data"=> (object)array()));exit();
			}
			$this->model_auth->get_token_guest($token);
			//print_r($this->token);
			if(isset($_POST["start_date"]) && !empty($_POST["start_date"])){
				$_POST["start_date"]=date('Y-m-d H:i:s', strtotime(substr($_POST["start_date"],0,10)));
			}
			if(isset($_POST["finish_date"]) && !empty($_POST["finish_date"])){
				$_POST["finish_date"]=date('Y-m-d H:i:s', strtotime(substr($_POST["finish_date"],0,10)));
			}
			if(isset($_POST["body_types_ids"]) && !empty($_POST["body_types_ids"])){
				$body_types_ids=json_decode($_POST["body_types_ids"],true);
				$_POST["body_types_ids"]=implode(",",$body_types_ids);
			}
			//$list_order=$this->model_customer->getOrder($_POST,"app");
			$list_order=$this->model_customer->getOrder($_POST,"count");
			echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("count"=>$list_order[0]["count"])));
		}
		public function get_order ($id_order){
			parent::checkToken("main");
			$this->model_geo=new Geo();
			$this->model_customer=new Customer();
			$this->model_driver=new Driver();
			$this->model_order=new order();
			$id_device=parent::getIdDevice();
			$id_driver=$this->model_driver->findDriver($id_device);
			if(count($id_driver)==0){
				echo parent::json_encode_cyr(array("code"=> 8,"message"=> "Проблемы с получением информации по заказу", "data"=> (object)array()));exit();
			}
			if(!empty($id_order[0]) && is_numeric($id_order[0])){
				$info_order=$this->model_customer->getOrder(array("id"=>$id_order[0]),"site");
				if(count($info_order)==0){
					echo parent::json_encode_cyr(array("code"=> 8,"message"=> "Проблемы с получением информации по заказу", "data"=> (object)array()));exit();
				}
				$new_array=array();
				$new_array["id"]=$info_order[0]["iOrderid"];
				$new_array["cargo_name"]=$info_order[0]["sCargoName"];
				$new_array["cargo_weight"]=$info_order[0]["iCargWeight"];
				$new_array["weight_unit"]=$info_order[0]["sWeightUnit"];
				$new_array["price"]=$info_order[0]["iPrice"];
				$new_array["currency"]=$info_order[0]["sCurrencyTypePrice"];
				$new_array["payment_type"]=$info_order[0]["sPaymentMethod"];
				$new_array["comment"]=$info_order[0]["tComment"];
				$new_array["rating"]=$info_order[0]["iRatingDriver"];
				$new_array["status"]=$info_order[0]["iStatus"];
				if($info_order[0]["dStartDate"]!="0000-00-00 00:00:00"){
					$dt = new DateTime($info_order[0]["dStartDate"]);
					$new_array["start_date"]=$dt->format(DATE_ISO8601);
				}
				else{
					$new_array["start_date"]="";
				}
				if($info_order[0]["dFinishDate"]!="0000-00-00 00:00:00"){
					$dt1 = new DateTime($info_order[0]["dFinishDate"]);
					$new_array["finish_date"]=$dt1->format(DATE_ISO8601);
				}
				else{
					$new_array["finish_date"]="";
				}
				$start_city_info=$this->model_order->getCity($info_order[0]["iStartCityId"]);
				if(isset($start_city_info["region"])){
					$info_region=$this->model_geo->getRegion($start_city_info["region"]);
					$info_region[0]["typeRegion"]=true;
				}
				else{
					$start_city_info["region"]="";
					$info_region[0]["iRegionVkid"]="";
					$info_region[0]["typeRegion"]=false;
				}
 					//print_r($info_order[0]["iFinishCityId"]);
				$new_array["start_city"]=array("id"=>$start_city_info["id"],"name"=>$start_city_info["title"],"parent_name"=>$start_city_info["region"],"parent_id"=>$info_region[0]["iRegionVkid"],"region"=>$info_region[0]["typeRegion"],"country_name"=>"Россия","country_id"=>1);
				if($info_order[0]["iFinishCityId"]>0){
					$finish_city_info=$this->model_order->getCity($info_order[0]["iFinishCityId"]);
 					//print_r($finish_city_info["region"]);
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
 					//print_r($finish_city_info);
					$new_array["finish_city"]=array("id"=>$finish_city_info["id"],"name"=>$finish_city_info["title"],"parent_name"=>$finish_city_info["region"],"parent_id"=>$info_region_finish[0]["iRegionVkid"],"region"=>$info_region_finish[0]["typeRegion"],"country_name"=>"Россия","country_id"=>1);
				}
				else{
					$new_array["finish_city"]=array();
				}
				$customer_info=$this->model_customer->getProfile($info_order[0]["iCustomerID"]);
				$link="";
				if(!empty($customer_info[0]["sHashImage"])){
					$link="/get_image_phone/?image=".$customer_info[0]["sHashImage"];
				}
				$new_array["customer"]=array(
					"id"=>$customer_info[0]["iCustomerId"],
					"name"=>$customer_info[0]["iCustomerName"],
					"organization"=>$customer_info[0]["iCustomerOrg"],
					"avatar"=>$link,
					"rating"=>$customer_info[0]["iRating"]
				);
				$this->model_customer->updateCountViews($info_order[0]["iOrderid"]);
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> $new_array));
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 8,"message"=> "Проблемы с получением информации по заказу", "data"=> (object)array()));
			}
		}
		public function get_phone_customer ($id_customer){
			parent::checkToken("main");
			$this->model_driver=new Driver();
			$this->model_customer=new Customer();
			$this->model_auth=new Auth_user();
			$id_device=parent::getIdDevice();
			$id_driver=$this->model_driver->findDriver($id_device);
			//print_r($id_device);
			if(count($id_driver)==0){
				echo parent::json_encode_cyr(array("code"=> 9,"message"=> "Проблемы с получением телефонного номера", "data"=> (object)array()));
				exit();
			}
			//print_r($info_order);
			$profile=$this->model_customer->getProfile($id_customer[0]);
			if(count($profile)==0){
				echo parent::json_encode_cyr(array("code"=> 37,"message"=> "Заказчик не найден", "data"=> (object)array()));
				exit();
			}
			$result=$this->model_driver->updateCalls($id_customer[0],$id_driver[0]["iDriverId"]);
			// $profile=$this->model_customer->getProfile($id_customer[0]);
			$device=$this->model_auth->getInfoDevice($profile[0]["iDeviceId"]);
			echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("phone"=>$device[0]["sSmsNumber"])));
		}
		public function registration_driver(){
			parent::checkToken("guest");
			$this->model_driver=new Driver();
			$this->model_customer=new Customer();
			$this->model_order=new order();
			$this->model_geo=new Geo();
			$device=parent::getIdDevice();
			$id_customer_new=$this->model_customer->findCustomer($device);
			$id_driver=$this->model_driver->findDriver($device);
			if(count($id_customer_new)>0 || count($id_driver)>0){
				echo parent::json_encode_cyr(array("code"=> 24,"message"=> "На данное устройство уже зарегистрирован аккаунт", "data"=> (object)array()));
				exit();
			}
			$finish_city_info=$this->model_order->getCity($_POST["city_id"]);
			$info_region_finish=$this->model_geo->getRegion($finish_city_info["region"]);
			//print_r($info_region_finish);
			//exit();
			if((isset($_POST["intercity_driver"]) && $_POST["intercity_driver"]==true) && (isset($_POST["in_city_driver"]) && $_POST["in_city_driver"]==true)){
				$_POST["driver_specialization"]="both";
			}
			else if((isset($_POST["intercity_driver"]) && $_POST["intercity_driver"]==true) && (!isset($_POST["in_city_driver"]) && $_POST["in_city_driver"]==false)){
				$_POST["driver_specialization"]="intercity";
			}
			else if((isset($_POST["in_city_driver"]) && $_POST["in_city_driver"]==true) && (!isset($_POST["intercity_driver"]) && $_POST["intercity_driver"]==false)){
				$_POST["driver_specialization"]="in_city";
			}
			if($_POST["driver_specialization"]!="in_city" && $_POST["driver_specialization"]!="both"){
				//echo "yes";
				$_POST["loaders"]="";
			}
			//print_r($_POST);
			if(!empty($info_region_finish[0]["iRegionVkid"])){
				$_POST["iRegionVkid"]=$info_region_finish[0]["iRegionVkid"];
			}
			else{
				$_POST["iRegionVkid"]=0;
			}
			if(isset($_POST["load_type"]) && !empty($_POST["load_type"])){
				$load_type=json_decode($_POST["load_type"],true);
				if(!is_array($load_type) || count($load_type)==0){
					echo parent::json_encode_cyr(array("code"=> 18,"message"=> "Не выбран не один тип загрузки1", "data"=> (object)array()));
					exit();
				}
				//print_r($load_type);
				if(isset($load_type["top"]) && $load_type["top"]=='true'){
					$_POST["load_type_top"]=true;
				}
				if(isset($load_type["side"]) && $load_type["side"]=='true'){
					$_POST["load_type_side"]=true;
				}
				if(isset($load_type["rear"]) && $load_type["rear"]=='true'){
					$_POST["load_type_rear"]=true;
				}
				//print_r($_POST);
			}
			if(!isset($_POST["load_type_top"]) && !isset($_POST["load_type_side"]) && !isset($_POST["load_type_rear"])){
				echo parent::json_encode_cyr(array("code"=> 18,"message"=> "Не выбран не один тип загрузки", "data"=> (object)array()));
				exit();
			}
			$error=0;
			if(isset($_POST["load_type_top"]) && $_POST["load_type_top"]=='true'){$error+=1;}
			if(isset($_POST["load_type_rear"]) && $_POST["load_type_rear"]=='true'){$error+=1;}
			if(isset($_POST["load_type_side"]) && $_POST["load_type_side"]=='true'){$error+=1;}
			if($error==0){
				echo parent::json_encode_cyr(array("code"=> 18,"message"=> "Не выбран не один тип загрузки", "data"=>(object)array()));
				exit();
			}
			$id=$this->model_driver->AddRegistrationDriver($_POST,parent::getIdDevice());
			$content_file=$_POST["avatar"];
			$_POST["avatar"]="";
			if (isset($content_file) && strlen($content_file)>0) {
				$md5_link=md5(microtime());
				$link_to_image="/images/driver/".$id."/l_".$md5_link.".jpg";
				$link_to_small_image="/images/driver/".$id."/s_".$md5_link.".jpg";
				if(!is_dir($_SERVER["DOCUMENT_ROOT"]."/images/driver/".$id."/")) mkdir($_SERVER["DOCUMENT_ROOT"]."/images/driver/".$id."/");
				$handle = fopen($_SERVER["DOCUMENT_ROOT"].$link_to_image, "w+");
				fwrite($handle, $content_file);
				fclose($handle);
				$_POST["avatar"]=$md5_link.".jpg";
				if(parent::img_resize($_SERVER["DOCUMENT_ROOT"].$link_to_image, $_SERVER["DOCUMENT_ROOT"].$link_to_small_image, 100, 0)!=1){
		    			//echo parent::json_encode_cyr(array("result"=> false));
		    			//exit();
				}
			}
			$hash_image=substr(md5(microtime() . rand(0, 9999)), 0, 25);
			$new_hash=$this->checkHash($hash_image);
			//echo $hash_image;
			if (isset($content_file) && strlen($content_file)>0) {
				$id_files=$this->model1->update_profile_driver($id,array("avatar"=>$_POST["avatar"],"avatar_hash"=>$new_hash));
			}
			if($id>0){
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> (object)array()));
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 10,"message"=> "Проблемы с регистрацией водителя", "data"=> (object)array()));
			}
			// }
			//echo $this->view->render('root.html',array('profile'=>$this->profile[0],"news_mass"=>$this->news_mass,"user_list"=>$this->user_mas));

		}
		public function get_body_type(){
			parent::checkToken("any");
			$this->model_driver=new Driver();
			$list_body=$this->model_driver->getBodyType();
			$new_list_body=array();
			foreach ($list_body as $key => $value) {
				$new_list_body[]=array("id"=>$value["iBodyTypeId"],"name"=>$value["iBodyTypeName"]);
			}
			echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> array("body_types"=>$new_list_body)));
		}
		public function respond_order($id_order){
			parent::checkToken("main");
			$this->model_driver=new Driver();
			$this->model_customer=new Customer();
			$id_device=parent::getIdDevice();
			$id_driver=$this->model_driver->findDriver($id_device);
			if(count($id_driver)==0){
				echo parent::json_encode_cyr(array("code"=> 30,"message"=> "Вы не можете откликнуться на заказ", "data"=> (object)array()));
				exit();
			}
			$check_order=$this->model_customer->checkOrder($id_order[0]);
			//print_r($check_order);
			if($check_order[0]["count"]==0){
				echo parent::json_encode_cyr(array("code"=> 13,"message"=> "Такой заказ не существует", "data"=> (object)array()));
				exit();
			}
			$check=$this->model_driver->checkRespond($id_order[0],$id_driver[0]["iDriverId"]);
			if($check[0]["count"]>0){
				echo parent::json_encode_cyr(array("code"=> 12,"message"=> "Вы уже откликнулись на заказ", "data"=> (object)array()));
				exit();
			}
			$new_id=$this->model_driver->respondDriverOrder($id_order[0],$id_driver[0]["iDriverId"],$_POST);
			if($new_id>0){
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> (object)array()));

			}
			else{
				echo parent::json_encode_cyr(array("code"=> 11,"message"=> "Не возможно откликнуться на этот заказ", "data"=> (object)array()));
			}
			// print_r($new_id);
			// print_r($id_order);
			// print_r($id_driver);
		}
		public function respond_order_site(){
			parent::checkSession();
			$this->model_driver=new Driver();
			$this->model_customer=new Customer();
			$device[0]["iDeviceId"]=$_SESSION["id_user"];
			$id_driver=$this->model_driver->findDriver($device);
			$check_order=$this->model_customer->checkOrder($_POST["id"]);
			//print_r($check_order);
			if($check_order[0]["count"]==0){
				echo parent::json_encode_cyr(array("result"=>false,"message"=> "Такой заказ не существует"));
				exit();
			}
			$check=$this->model_driver->checkRespond($_POST["id"],$id_driver[0]["iDriverId"]);
			if($check[0]["count"]>0){
				echo parent::json_encode_cyr(array("result"=>false,"message"=> "Вы уже откликнулись на заказ"));
				exit();
			}
			$new_id=$this->model_driver->respondDriverOrder($_POST["id"],$id_driver[0]["iDriverId"],$_POST);
			if($new_id>0){
				echo parent::json_encode_cyr(array("result"=>true));

			}
			else{
				echo parent::json_encode_cyr(array("result"=>false,"message"=> "Не возможно откликнуться на этот заказ"));
			}
			// print_r($new_id);
			// print_r($id_order);
			// print_r($id_driver);
		}
		public function update_profile_driver(){
			$this->model_driver=new Driver();
			$this->model_order=new order();
			$this->model_geo=new Geo();
			$finish_city_info=$this->model_order->getCity($_POST["city_id"]);
			$info_region_finish=$this->model_geo->getRegion($finish_city_info["region"]);
			//print_r($info_region_finish);
			//exit();
			if((isset($_POST["intercity_driver"]) && $_POST["intercity_driver"]==true) && (isset($_POST["in_city_driver"]) && $_POST["in_city_driver"]==true)){
				$_POST["driver_specialization"]="both";
			}
			else if((isset($_POST["intercity_driver"]) && $_POST["intercity_driver"]==true) && (!isset($_POST["in_city_driver"]) && $_POST["in_city_driver"]==false)){
				$_POST["driver_specialization"]="intercity";
			}
			else if((isset($_POST["in_city_driver"]) && $_POST["in_city_driver"]==true) && (!isset($_POST["intercity_driver"]) && $_POST["intercity_driver"]==false)){
				$_POST["driver_specialization"]="in_city";
			}
			if($_POST["driver_specialization"]!="in_city" && $_POST["driver_specialization"]!="both"){
				//echo "yes";
				$_POST["loaders"]="";
			}
			$_POST["iRegionVkid"]=$info_region_finish[0]["iRegionVkid"];
			if(isset($_POST["load_type"]) && !empty($_POST["load_type"])){
				$load_type=json_decode($_POST["load_type"],true);
				if(!is_array($load_type) || count($load_type)==0){
					echo parent::json_encode_cyr(array("code"=> 18,"message"=> "Не выбран не один тип загрузки1", "data"=> (object)array()));
					exit();
				}
				//print_r($load_type);
				if(isset($load_type["top"]) && $load_type["top"]=='true'){
					$_POST["load_type_top"]=true;
				}
				if(isset($load_type["side"]) && $load_type["side"]=='true'){
					$_POST["load_type_side"]=true;
				}
				if(isset($load_type["rear"]) && $load_type["rear"]=='true'){
					$_POST["load_type_rear"]=true;
				}
				//print_r($_POST);
			}
			//print_r($_POST);
			// if(!isset($_POST["load_type_top"]) && !isset($_POST["load_type_side"]) && !isset($_POST["load_type_rear"])){
			// 	echo parent::json_encode_cyr(array("code"=> 18,"message"=> "Не выбран не один тип загрузки", "data"=> (object)array()));
			// 	exit();
			// }
			// $error=0;
			// if(isset($_POST["load_type_top"]) && $_POST["load_type_top"]=='true'){$error+=1;}
			// if(isset($_POST["load_type_rear"]) && $_POST["load_type_rear"]=='true'){$error+=1;}
			// if(isset($_POST["load_type_side"]) && $_POST["load_type_side"]=='true'){$error+=1;}
			// if($error==0){
			// 	echo parent::json_encode_cyr(array("code"=> 18,"message"=> "Не выбран не один тип загрузки", "data"=>(object)array()));
			// 	exit();
			// }
			$device[0]["iDeviceId"]=$_SESSION["id_user"];
			$id_driver=$this->model_driver->findDriver($device);
			$new_id=$this->model_driver->update_profile_driver($id_driver[0]["iDriverId"],$_POST);
			if($new_id==1){
				echo parent::json_encode_cyr(array("result"=> true));
			}
			else{
				echo parent::json_encode_cyr(array("result"=> false));
			}
		}
		public function delete_avatar_driver(){
			parent::checkSession();
			$this->model=new Driver();
			$device[0]["iDeviceId"]=$_SESSION["id_user"];
			$id_driver=$this->model->findDriver($device);
			$id=$this->model->update_profile_driver($id_driver[0]["iDriverId"],array("avatar"=>$_POST["avatar"]));
			if($id==1){
				echo parent::json_encode_cyr(array("result"=> true));
			}
			else{
				echo parent::json_encode_cyr(array("result"=> true));
			}
		}
		public function add_avatar(){
			// echo "yes";
			parent::checkToken("main");
			$this->model_driver=new Driver();
			$id_device=parent::getIdDevice();
			$id_driver=$this->model_driver->findDriver($id_device);
			$image=parent::getDateImage();
			$md5_link=md5(microtime());
			$driver=array();
			// print_r($id_driver);
			$id=$id_driver[0]["iDriverId"];
			$link_to_image="/images/driver/".$id."/l_".$md5_link.".jpg";
			$link_to_small_image="/images/driver/".$id."/s_".$md5_link.".jpg";
			if(!is_dir($_SERVER["DOCUMENT_ROOT"]."/images/driver/".$id."/")) mkdir($_SERVER["DOCUMENT_ROOT"]."/images/driver/".$id."/");
			$handle = fopen($_SERVER["DOCUMENT_ROOT"].$link_to_image, "w+");
			$new_cus=explode("Content-Type: image/jpeg",$image);
			$new_cus2=explode("----------------------------",$new_cus[1]);
			 // print_r($image);
			fwrite($handle,substr($new_cus2[0], 4));
			fclose($handle);
			$driver["avatar"]=$md5_link.".jpg";
			$driver["avatar_hash"]=$md5_link;
			if(parent::img_resize($_SERVER["DOCUMENT_ROOT"].$link_to_image, $_SERVER["DOCUMENT_ROOT"].$link_to_small_image, 100, 0)!=1){
		    			//echo parent::json_encode_cyr(array("result"=> false));
		    			//exit();
			}
			if(!empty($profile[0]["sAvatar"])){
				unlink($_SERVER["DOCUMENT_ROOT"]."/images/driver/".$id_driver[0]["iDriverId"]."/s_".$profile[0]["sAvatar"]);
				unlink($_SERVER["DOCUMENT_ROOT"]."/images/driver/".$id_driver[0]["iDriverId"]."/l_".$profile[0]["sAvatar"]);
			}
			$new_id=$this->model_driver->update_profile_driver($id_driver[0]["iDriverId"],$driver);
			if($new_id>0){
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> (object)array()));
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 23,"message"=> "Не удалось изменить профиль", "data"=> (object)array()));

			}
		}
		public function add_avatar_driver(){
			parent::checkSession();
			$this->model=new Driver();
			$device[0]["iDeviceId"]=$_SESSION["id_user"];
			$id_driver=$this->model->findDriver($device);
			$allowed = array('gif', 'jpg','jpeg','png');
			if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){
				$extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);
				if(!in_array(strtolower($extension), $allowed)){
					echo '{"status":"error"}';
					exit;
				}
			    //print_r($_POST);
			    //print_r($id_driver);
				$dir=$_SERVER["DOCUMENT_ROOT"]."/images/driver/".$id_driver[0]["iDriverId"]."/";
				$name_file=md5(iconv( 'utf-8', 'windows-1251', $_FILES['upl']['name'] )).".".$extension;
				$base_name=md5(microtime());
				$new_name_file="l_".$base_name.".".$extension;
				$dir_new="/images/driver/".$id_driver[0]["iDriverId"]."/".iconv('windows-1251', 'utf-8', $new_name_file);
				if(!is_dir($dir)) mkdir($dir);
				if(parent::img_resize($_FILES['upl']['tmp_name'], $_SERVER["DOCUMENT_ROOT"].$dir_new, 300, 0)!=1){
					echo parent::json_encode_cyr(array("result"=> false));
					exit();
				}
				$link_to_image="/images/driver/".$id_customer[0]["iDriverId"]."/s_".$base_name.".".$extension;
					//$link_to_small_image="/images/customer/".$id_customer[0]["iCustomerId"]."/s_".$md5_link.".png";
				if(parent::img_resize($_SERVER["DOCUMENT_ROOT"].$dir_new, $_SERVER["DOCUMENT_ROOT"].$link_to_image, 100, 0)!=1){
					echo parent::json_encode_cyr(array("result"=> false));
					exit();
				}
				$avatar_hash=md5($dir_new);
				$id_files=$this->model->update_profile_driver($id_driver[0]["iDriverId"],array("avatar"=>$base_name.".".$extension,"avatar_hash"=>$avatar_hash));
		    		//echo $id_driver[0]["iDriverId"];
				echo parent::json_encode_cyr(array("result"=>"true","name_file"=>"/get_image/?image=".$avatar_hash));
			}
		}
		public function get_list_response_drivers(){
			parent::checkSession();
			$this->model_driver=new Driver();
			$this->model_customer=new Customer();
			$this->model_auth=new Auth_user();
			$info_order=$this->model_customer->getOrder(array("id"=>$_POST["id"]),"site");
			//print_r($info_order);
			$list_driver=$this->model_driver->getRespondDriver($_POST["id"],$info_order[0]["dLastGetDriver"],"");
			//print_r($list_driver);
			$new_list_d=array();
			if(count($list_driver)>0){
				foreach ($list_driver as $key => $value) {
					$new_list_d_el=array();
					$new_list_d_el["id"]=$value["iDriverId"];
					if($value["sDriverSpecialization"]=="in_city"){
						//echo "yes";
						$new_list_d_el["intercity_driver"]=false;
						$new_list_d_el["in_city_driver"]=true;
					}
					else if($value["sDriverSpecialization"]=="intercity"){
						$new_list_d_el["intercity_driver"]=true;
						$new_list_d_el["in_city_driver"]=false;
					}
					else if($value["sDriverSpecialization"]=="both"){
						$new_list_d_el["intercity_driver"]=true;
						$new_list_d_el["in_city_driver"]=true;
					}	
					$new_list_d_el["avatar"]="";
					$new_list_d_el["name"]=$value["sDriverName"];
					$new_list_d_el["car_name"]=$value["sCarName"];
					$new_list_d_el["rating"]=$value["iRating"];
					$new_list_d_el["price"]=$value["iPrice"];
					$new_list_d_el["rate"]=$value["iRate"];
					$new_list_d_el["loaders"]=$value["iLoaders"];
					$new_list_d_el["currency"]=$value["sCurrency"];
					$device=$this->model_auth->getInfoDevice($value["iDeviceId"]);
					$new_list_d_el["phone"]=$device[0]["sSmsNumber"];
					$new_list_d[]=$new_list_d_el;
				}
			}
			$list_driver2=$this->model_driver->getNewRespondDriver($_POST["id"],$info_order[0]["dLastGetDriver"],"");
			$new_list_d2=array();
			if(count($list_driver2)>0){
				foreach ($list_driver2 as $key => $value2) {
					$new_list_d_el2=array();
					$new_list_d_el2["id"]=$value2["iDriverId"];
					if($value["sDriverSpecialization"]=="in_city"){
						//echo "yes";
						$new_list_d_el2["intercity_driver"]=false;
						$new_list_d_el2["in_city_driver"]=true;
					}
					else if($value2["sDriverSpecialization"]=="intercity"){
						$new_list_d_el2["intercity_driver"]=true;
						$new_list_d_el2["in_city_driver"]=false;
					}
					else if($value2["sDriverSpecialization"]=="both"){
						$new_list_d_el2["intercity_driver"]=true;
						$new_list_d_el2["in_city_driver"]=true;
					}	
					$link="";
					if(!empty($value2["sHashImage"])){
						$link="/get_image/?image=".$value2["sHashImage"];
					}
					$new_list_d_el2["avatar"]=$link;
					$new_list_d_el2["name"]=$value2["sDriverName"];
					$new_list_d_el2["car_name"]=$value2["sCarName"];
					$new_list_d_el2["rating"]=$value2["iRating"];
					$new_list_d_el2["price"]=$value2["iPrice"];
					$new_list_d_el2["rate"]=$value2["iRate"];
					$new_list_d_el2["loaders"]=$value2["iLoaders"];
					$new_list_d_el2["currency"]=$value2["sCurrency"];
					$device=$this->model_auth->getInfoDevice($value["iDeviceId"]);
					$new_list_d_el["phone"]=$device[0]["sSmsNumber"];
					$new_list_d2[]=$new_list_d_el2;
				}
			}
			$this->model_customer->update_order($_POST["id"],array("dTimeLast"=>""));
			//print_r($new_list_d);
			echo parent::json_encode_cyr(array("result"=>true,"list_drivers"=>$new_list_d,"new_list_drivers"=>$new_list_d2));
			//print_r($list_driver);
		}
		public function get_more_drivers(){
			$this->model_driver=new Driver();
			if(isset($_POST["json_str"]) && !empty($_POST["json_str"])){
				$_POST=json_decode($_POST["json_str"],true);
			}
			$list_driver=$this->model_driver->getDriver($_POST,"site");
			$new_array=array();
			foreach ($list_driver as $key => $value) {
				$new_array[]=$this->model_driver->arraySortDriver($value);
			}
			echo parent::json_encode_cyr(array("result"=>true,"list_driver"=>$new_array));
		}
		public function add_subscribe(){
			parent::checkSession();
			$this->model_driver=new Driver();
			$this->model_sub=new Subscription_driver();
			$device[0]["iDeviceId"]=$_SESSION["id_user"];
			$id_driver=$this->model_driver->findDriver($device);
			$count_subscribe=$this->model_sub->getSubscriptionDriver($id_driver[0]["iDriverId"],"count");
			if($count_subscribe[0]["count"]>=3){
				echo parent::json_encode_cyr(array("result"=>false,"error"=>1));
				exit();
			}
			if(!empty($_POST["start_date"])){
				$_POST["start_date"]=date('Y-m-d H:i:s', strtotime($_POST["start_date"]));
			}
			else{
				$_POST["start_date"]='';
			}
			if(!empty($_POST["finish_date"])){
				$_POST["finish_date"]=date('Y-m-d H:i:s', strtotime($_POST["finish_date"]));
			}
			else{
				$_POST["start_date"]='';
			}
			//print_r($id_driver);
			//echo $id_driver[0]["iDriverId"];
			$idd=$this->model_sub->AddSubscription($_POST,$id_driver[0]["iDriverId"]);
			$count_subscribe=$this->model_sub->getSubscriptionDriver($id_driver[0]["iDriverId"],"count");
			if($idd>0){
				$subscribe_one=$this->model_sub->getOneSubscribe($idd);
				echo parent::json_encode_cyr(array("result"=>true,"subscribe"=>$this->model_sub->arraySortSubsribe($subscribe_one[0],"site"),"count_subscribe"=>$count_subscribe[0]["count"]));
			}
			else{
				echo parent::json_encode_cyr(array("result"=>false));

			}
		}
		public function delete_subscribe(){
			parent::checkSession();
			$this->model_sub=new Subscription_driver();
			$this->model_driver=new Driver();
			$device[0]["iDeviceId"]=$_SESSION["id_user"];
			$id_driver=$this->model_driver->findDriver($device);
			$idd=$this->model_sub->deleteSubscribe($_POST["id"],$id_driver[0]["iDriverId"]);
			if($idd>0){
				$count_subscribe=$this->model_sub->getSubscriptionDriver($id_driver[0]["iDriverId"],"count");
				echo parent::json_encode_cyr(array("result"=>true,"count_subscribe"=>$count_subscribe[0]["count"]));
			}
			else{
				echo parent::json_encode_cyr(array("result"=>false));

			}
		}
		public function edit_subscribe(){
			parent::checkSession();
			$this->model_sub=new Subscription_driver();
			$this->model_driver=new Driver();
			if($_POST["start_date"]=="null"){
				$_POST["start_date"]=date('Y-m-d H:i:s', date());
			}
			else{
				$_POST["start_date"]=date('Y-m-d H:i:s', strtotime($_POST["start_date"]));
			}
			if($_POST["finish_date"]=="null"){
				$_POST["finish_date"]="0000-00-00 00:00:00";
			}
			else{
				$_POST["finish_date"]=date('Y-m-d H:i:s', strtotime($_POST["finish_date"]));
			}
			$device[0]["iDeviceId"]=$_SESSION["id_user"];
			$id_driver=$this->model_driver->findDriver($device);
			$idd=$this->model_sub->update_subscription($id_driver[0]["iDriverId"],$_POST);
			if($idd>0){
				echo parent::json_encode_cyr(array("result"=>true));
			}
			else{
				echo parent::json_encode_cyr(array("result"=>false));

			}
		}
		public function offer_driver(){
			parent::checkSession();
			$this->model_driver=new Driver();
			$this->model_customer=new Customer();
			$check_order=$this->model_customer->checkOrder($_POST["order_id"]);
			//print_r($check_order);
			if($check_order[0]["count"]==0){
				echo parent::json_encode_cyr(array("result"=>false,"message"=> "Такой заказ не существует"));
				exit();
			}
			$check=$this->model_driver->checkRespond($_POST["order_id"],$_POST["driver_id"]);
			if($check[0]["count"]==0){
				echo parent::json_encode_cyr(array("result"=>false,"message"=> "Водитель не оставлял отклик на заказ"));
				exit();
			}
			$new_id=$this->model_driver->changeDriver($_POST["order_id"],$_POST["driver_id"]);
			if($new_id>0){
				echo parent::json_encode_cyr(array("result"=>true));

			}
			else{
				echo parent::json_encode_cyr(array("result"=>false,"message"=> "Не возможно откликнуться на этот заказ"));
			}
			// print_r($new_id);
			// print_r($id_order);
			// print_r($id_driver);
		}
		public function get_delete_subscriptions($id_sub){
			//parent::checkToken("main");
			$this->model_driver=new Driver();
			$this->model_main=new Main();
			$this->model_subc=new Subscription_driver();
			$id_device=parent::getIdDevice();
			$id_driver=$this->model_driver->findDriver($id_device);
			if($_SERVER['REQUEST_METHOD']=="DELETE"){
				// echo $id_order[0];
				// print_r($id_driver);
				$idd=$this->model_subc->deleteSubscribe($id_sub[0],$id_driver[0]["iDriverId"]);
				//echo $idd;
				if($idd>0){
					echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> (object)array()));
				}
				else{
					echo parent::json_encode_cyr(array("code"=> 21,"message"=> "Не удалось удалить подписку", "data"=> (object)array()));
				}
			}
			else if($_SERVER['REQUEST_METHOD']=="PATCH"){
				$date_r=parent::getFormData();
				//print_r($date_r);
				if(isset($date_r["start_date"]) && !empty($date_r["start_date"])){
					$date_r["start_date"]=substr($date_r["start_date"],0,10)." ".substr($date_r["start_date"],11,8);
				}
				if(isset($date_r["finish_date"]) && !empty($date_r["finish_date"])){
					$date_r["finish_date"]=substr($date_r["finish_date"],0,10)." ".substr($date_r["finish_date"],11,8);
				}
				//print_r($date_r);
				$date_r["id"]=$id_sub[0];

				$idd=$this->model_subc->update_subscription($id_driver[0]["iDriverId"],$date_r);
				if($idd>0){
					echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> (object)array()));
				}
				else{
					echo parent::json_encode_cyr(array("code"=> 22,"message"=> "Не удалось изменить подписку", "data"=> (object)array()));
				}
			}
			else if($_SERVER['REQUEST_METHOD']=="GET"){
				//echo $id_sub[0];
				$info_sub=$this->model_subc->getOneSubscribe($id_sub[0]);
				//print_r($info_sub);
				if(count($info_sub)==1){
					$new_info_sub=$this->model_subc->arraySortSubsribe($info_sub[0],"app");
				}
				else{
					echo parent::json_encode_cyr(array("code"=> 23,"message"=> "Не удалось получить подписку", "data"=> (object)array()));
					exit();
				}
				if(is_array($new_info_sub)){
					echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "subscribe"=> $new_info_sub));
				}
				else{
					echo parent::json_encode_cyr(array("code"=> 23,"message"=> "Не удалось получить подписку", "data"=> (object)array()));exit();
				}
			}
		}
		public function subscriptions(){
			parent::checkToken("main");
			$this->model_driver=new Driver();
			$this->model_main=new Main();
			$this->model_subc=new Subscription_driver();
			$id_device=parent::getIdDevice();
			$id_driver=$this->model_driver->findDriver($id_device);
			// if(isset($_GET["debug"])){
			// 	print_r($_POST);
			// 	echo "yes";
			// }
			if(count($id_driver)==0){
				echo parent::json_encode_cyr(array("code"=> 26,"message"=> "Водитель не найден", "data"=> (object)array()));exit();
			}
			if($_SERVER['REQUEST_METHOD']=="POST"){
				$count_sub=$this->model_subc->getSubscriptionDriver($id_driver[0]["iDriverId"],"count");
				//print_r($count_sub);
				if($count_sub[0]["count"]>2){
					echo parent::json_encode_cyr(array("code"=> 20,"message"=> "Превышено количество допустимых подписок", "data"=> (object)array()));
					exit();
				}
				//$_POST["subscription"]=json_decode($_POST["subscription"],true);
				if($_POST["active"]=="1"){
					$_POST["active"]="true";
				}
				if($_POST["notifications"]=="1"){
					$_POST["notifications"]="true";
				}
				if(!empty($_POST["start_date"])){
					$_POST["start_date"]=substr($_POST["start_date"],0,10)." ".substr($_POST["start_date"],11,8);
				}
				else{
					unset($_POST["start_date"]);
				}
				if(!empty($_POST["finish_date"])){
					$_POST["finish_date"]=substr($_POST["finish_date"],0,10)." ".substr($_POST["finish_date"],11,8);
				}
				//print_r($_POST);
				$idd=$this->model_subc->AddSubscription($_POST,$id_driver[0]["iDriverId"]);
				if($idd>0){
					$info_sub=$this->model_subc->getOneSubscribe($idd);
					if(count($info_sub)==1){
						$new_info_sub=$this->model_subc->arraySortSubsribe($info_sub[0],"app");
					}
					echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> $new_info_sub));
				}
				else{
					echo parent::json_encode_cyr(array("code"=> 19,"message"=> "Не удалось создать подписку", "data"=> (object)array()));

				}
			}
			else if($_SERVER['REQUEST_METHOD']=="GET"){
				$list_subscribe=$this->model_subc->getSubscriptionDriver($id_driver[0]["iDriverId"],"list");
				$new_array_subsc=array();
				foreach ($list_subscribe as $key => $value) {
					$new_array_subsc[]=$this->model_subc->arraySortSubsribe($value,"app");
				}
					//print_r($new_array_subsc);
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("subscriptions"=>$new_array_subsc)));
			}
		}
		public function get_profile(){
			parent::checkToken("main");
			$this->model_auth=new Auth_User();
			$this->model_geo=new Geo();
			$this->model_order=new Order();
			$this->model_driver=new Driver();
			$id_device=parent::getIdDevice();
			$info_device=$this->model_auth->getInfoDevice($id_device[0]["iDeviceId"]);
			$id_driver=$this->model_driver->findDriver($id_device);
			//print_r($_SERVER['REQUEST_METHOD']);
			if($_SERVER['REQUEST_METHOD']=="GET"){
				//print_r($id_driver);
				$profile=$this->model_driver->getProfileDriver($id_driver);
				//print_r($profile);
				$new_profile=array();
				$new_profile["name"]=$profile[0]["sDriverName"];
				$new_phone=str_replace(array(')','('," ","-"),"",$info_device[0]["sSmsNumber"]);
				if($new_phone[0]=="+" && $new_phone[1]=="7"){
					$code_country="+7";
					$new_phone=substr($new_phone, 2);
				}
				else if($new_phone[0]=="8" || $new_phone[0]=="7"){
					$code_country="+7";
					$new_phone=substr($new_phone, 1);
				}
				$new_profile["phone"]=$new_phone;
				// $link="";
				// if(!empty($profile[0]["sHashImage"])){
				// 	$link="/get_image/?image=".$profile[0]["sHashImage"];
				// }
				//$new_profile["avatar"]=$link;
				$new_profile["phone_confirmed"]=$info_device[0]["iStatus"]==1 ? "true" : "false";;
				$new_profile["car_name"]=$profile[0]["sCarName"];
				$new_profile["car_type"]=$profile[0]["iCarType"];
				$new_profile["body_type_id"]=$profile[0]["iBodyType"];
				if($new_profile["body_type_id"]>0){
					$new_profile["body_type_name"]=$profile[0]["iBodyTypeName"];
				}
				$new_profile["capacity"]=$profile[0]["iCapacity"];
				$new_profile["volume"]=$profile[0]["iVolume"];
				$load_type=array();
				if(!empty($profile[0]["sLoadTypeTop"])){
					$load_type["top"]="true";
				}
				if(!empty($profile[0]["sLoadTypeRear"])){
					$load_type["rear"]="true";
				}
				if(!empty($profile[0]["sLoadTypeSide"])){
					$load_type["side"]="true";
				}
				$new_profile["load_type"]=$load_type;
				// $new_profile["load_type_top"]=$profile[0]["sLoadTypeTop"]=="" ? false : true;
				// $new_profile["load_type_rear"]=$profile[0]["sLoadTypeRear"]=="" ? false : true;
				// $new_profile["load_type_side"]=$profile[0]["sLoadTypeSide"]=="" ? false : true;
				//$new_profile["driver_specialization"]=$profile[0]["sDriverSpecialization"];
				if($profile[0]["sDriverSpecialization"]=="in_city"){
					//echo "yes";
					$new_profile["intercity_driver"]=false;
					$new_profile["in_city_driver"]=true;
				}
				else if($profile[0]["sDriverSpecialization"]=="intercity"){
					$new_profile["intercity_driver"]=true;
					$new_profile["in_city_driver"]=false;
				}
				else if($profile[0]["sDriverSpecialization"]=="both"){
					$new_profile["intercity_driver"]=true;
					$new_profile["in_city_driver"]=true;
				}	
				$new_profile["loaders"]=$profile[0]["iLoaders"];
				$new_profile["rate_intercity"]=$profile[0]["iRate"];
				$new_profile["rate_in_city"]=$profile[0]["iRateInCity"];
				if($profile[0]["iCityId"]!=0){
					$start_city_info=$this->model_order->getCity($profile[0]["iCityId"]);
					$info_region=$this->model_geo->getRegion($start_city_info["region"]);
					$new_profile["city"]=array(
						"id"=>$start_city_info["id"],
						"name"=>$start_city_info["title"],
						"parent_name"=>$start_city_info["region"],
						"parent_id"=>$info_region[0]["iRegionVkid"],
						"region"=>true,
						"country_name"=>"Россия",
						"country_id"=>1);
				}
				else{
					$new_profile["city"]=0;
				}
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("profile"=>$new_profile)));
			}
			else if($_SERVER['REQUEST_METHOD']=="PATCH"){
				$date_r=parent::getFormData();
				// $content_file=$driver["avatar"];
 			// 	//print_r($id_driver[0]["iDriverId"]);
				// $profile=$this->model_driver->getProfileDriver($id_driver);
 			// 	//print_r($profile);
				// if (isset($content_file) && strlen($content_file)>0) {
				// 	$md5_link=md5(microtime());
				// 	$link_to_image="/images/driver/".$id."/l_".$md5_link.".png";
				// 	$link_to_small_image="/images/driver/".$id."/s_".$md5_link.".png";
				// 	if(!is_dir($_SERVER["DOCUMENT_ROOT"]."/images/driver/".$id."/")) mkdir($_SERVER["DOCUMENT_ROOT"]."/images/driver/".$id."/");
				// 	$handle = fopen($_SERVER["DOCUMENT_ROOT"].$link_to_image, "w+");
				// 	fwrite($handle, $content_file);
				// 	fclose($handle);
				// 	$_POST["avatar"]=$md5_link.".png";
				// 	if(parent::img_resize($_SERVER["DOCUMENT_ROOT"].$link_to_image, $_SERVER["DOCUMENT_ROOT"].$link_to_small_image, 100, 0)!=1){
		  //   			//echo parent::json_encode_cyr(array("result"=> false));
		  //   			//exit();
				// 	}
				// 	unlink($_SERVER["DOCUMENT_ROOT"]."/images/driver/".$id_driver[0]["iDriverId"]."/s_".$profile[0]["sAvatar"]);
				// 	unlink($_SERVER["DOCUMENT_ROOT"]."/images/driver/".$id_driver[0]["iDriverId"]."/l_".$profile[0]["sAvatar"]);
				// }
				$device_info=$this->model_auth->getInfoDevice($profile[0]["iDeviceId"]);
				// echo "<pre>";
				// 	print_r($date_r);
				// echo "</pre>";
				if(isset($date_r["phone"]) && !empty($date_r["phone"])){
					$new_phone=str_replace(array(')','('," ","-"),"",$date_r["phone"]);
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
					//echo $phone;
					if($phone!=$device_info[0]["sSmsNumber"]){
						//print_r($customer);
						$this->model_auth->updatePhoneAndStatus($profile[0]["iDeviceId"],$phone);
					}
				}
				$finish_city_info=$this->model_order->getCity($date_r["city_id"]);
				$info_region_finish=$this->model_geo->getRegion($finish_city_info["region"]);
			//print_r($info_region_finish);
			//exit();
				if($date_r["in_city_driver"]!="true" && $date_r["intercity_driver"]!="both"){
				//echo "yes";
					$date_r["loaders"]="";
				}
				$date_r["iRegionVkid"]=$info_region_finish[0]["iRegionVkid"];

				if(isset($date_r["load_type"]) && !empty($date_r["load_type"])){
					$load_type=json_decode($date_r["load_type"],true);
					if((!is_array($load_type) || count($load_type)==0) && ($profile[0]["sLoadTypeTop"]!='true' && $profile[0]["sLoadTypeRear"]!='true' && $profile[0]["sLoadTypeSide"]!='true')){
						echo parent::json_encode_cyr(array("code"=> 18,"message"=> "Не выбран не один тип загрузки1", "data"=> (object)array()));
						exit();
					}

				//print_r($load_type);
					if(isset($load_type["top"])){
						$date_r["load_type_top"]=$load_type["top"]=='true' ? 'true' : 1;
						$profile[0]["sLoadTypeTop"]=$load_type["top"]==true ? true : 1;
					}
					if(isset($load_type["side"])){
						$date_r["load_type_side"]=$load_type["side"]=='true' ? 'true' : 1;
						$profile[0]["sLoadTypeSide"]=$load_type["side"]==true ? true : 1;
					}
					if(isset($load_type["rear"])){
						$date_r["load_type_rear"]=$load_type["rear"]=='true' ? 'true' : 1;
						$profile[0]["sLoadTypeRear"]=$load_type["rear"]==true ? true : 1;
					}
				//print_r($_POST);
			// 				if(isset($_GET["test"])){
			// 	print_r($date_r);
			// }
				}
				if($profile[0]["sLoadTypeTop"]!='true' && $profile[0]["sLoadTypeRear"]!='true' && $profile[0]["sLoadTypeSide"]!='true'){
					echo parent::json_encode_cyr(array("code"=> 18,"message"=> "Не выбран не один тип загрузки", "data"=> (object)array()));
					exit();
				}

				$error=0;
				if(isset($date_r["load_type_top"]) && $date_r["load_type_top"]!='true' && $date_r["load_type_top"]!=1){$error+=1;}
				if(isset($date_r["load_type_rear"]) && $date_r["load_type_rear"]!='true' && $date_r["load_type_rear"]!=1){$error+=1;}
				if(isset($date_r["load_type_side"]) && $date_r["load_type_side"]!='true' && $date_r["load_type_side"]!=1){$error+=1;}
			//echo $error;
				if($error!=0){
					echo parent::json_encode_cyr(array("code"=> 18,"message"=> "Не выбран не один тип загрузки", "data"=>(object)array()));
					exit();
				}

				$new_id=$this->model_driver->update_profile_driver($id_driver[0]["iDriverId"],$date_r);
				if($new_id>0){
					echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> (object)array()));
				}
				else{
					echo parent::json_encode_cyr(array("code"=> 23,"message"=> "Не удалось изменить профиль", "data"=> (object)array()));

				}
			}
		}
		public function get_profile_driver(){
			$this->model=new Driver();
			$id_driver_new[0]["iDriverId"]=$_POST["id_driver"];
			$profile=$this->model->getProfileDriver($id_driver_new);
			$new_profile["name"]=$profile[0]["sDriverName"];
			$link="";
			if(!empty($profile[0]["sHashImage"])){
				$link="/get_image/?image=".$profile[0]["sHashImage"];
			}
			$new_profile["avatar"]=$link;
			echo parent::json_encode_cyr(array("result"=>true,"profile"=>$new_profile,"id_driver"=>$_POST["id_driver"]));
		}
		public function delete_avatar(){
			parent::checkToken("main");
			$this->model_driver=new Driver();
			$id_device=parent::getIdDevice();
			$id_driver=$this->model_driver->findDriver($id_device);
			$profile=$this->model_driver->getProfileDriver($id_driver);
			//print_r($id_device);
			$id=$this->model_driver->update_profile_driver($id_driver[0]["iDriverId"],array("avatar"=>"","avatar_hash"=>""));
			if($id==1){
				if(!empty($profile[0]["sAvatar"])){
					unlink($_SERVER["DOCUMENT_ROOT"]."/images/driver/".$id_driver[0]["iDriverId"]."/s_".$profile[0]["sAvatar"]);
					unlink($_SERVER["DOCUMENT_ROOT"]."/images/driver/".$id_driver[0]["iDriverId"]."/l_".$profile[0]["sAvatar"]);
				}
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> (object)array()));
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 23,"message"=> "Не удалось изменить профиль", "data"=> (object)array()));
			}
		}
		public function rating_order(){
			parent::checkSession();
			$this->model=new Customer();
			$info_order=$this->model->getOrder(array("id"=>$_POST["id_order"]),"site");
			if($info_order[0]["iCustomerID"]==0){
				echo parent::json_encode_cyr(array("result"=>false));
			}
			//print_r($info_order);
			if(parent::checkRating($_POST["rating"],"customer",$info_order[0]["iCustomerID"])){
				echo parent::json_encode_cyr(array("result"=>true));
			}
			else{
				echo parent::json_encode_cyr(array("result"=>false));
			}
		}
		private function checkHash($hash){
			$this->model_auth=new Auth_user();
			if(!$this->model_auth->checkImageHash($hash)){
				$hash=substr(md5(microtime() . rand(0, 9999)), 0, 25);
				$this->checkHash($hash);
			}
			return $hash;
		}
		public function rateOrder($id_order){
			parent::checkToken("main");
			$this->model_driver=new Driver();
			$this->model_customer=new Customer();
			$rating=json_decode($_POST["rating"],true);
			if(!is_int($rating["rating"])){
				echo parent::json_encode_cyr(array("code"=> 17,"message"=> "Не возможно оценить заказ", "data"=> (object)array()));exit();
			}
			$id_device=parent::getIdDevice();
			$id_driver=$this->model_driver->findDriver($id_device);
			$info_order=$this->model_customer->getOrder(array("id"=>$id_order[0]),"site");
			if($info_order[0]["iDriverId"]!=$id_driver[0]["iDriverId"]){
				echo parent::json_encode_cyr(array("code"=> 17,"message"=> "Не возможно оценить заказ", "data"=> (object)array()));exit();
			}
			if(parent::checkRating($rating["rating"],"driver",$info_order[0]["iDriverId"])){
				$this->model_driver->rateOrder($id_order[0],$rating["rating"]);
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> (object)array()));
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 17,"message"=> "Не возможно оценить заказ", "data"=> (object)array()));exit();
			}

		}
		public function refuseOrderDriver($id_order){
			parent::checkToken("main");
			//header("Content-type: text/plain");
			//echo "yes";
			$this->model_driver=new Driver();
			$this->model_customer=new Customer();
			$id_device=parent::getIdDevice();
			$id_driver=$this->model_driver->findDriver($id_device);
			$result=$this->model_driver->checkRespond($id_order[0],$id_driver[0]["iDriverId"]);
			if($result[0]["count"]==0){
				echo parent::json_encode_cyr(array("code"=> 31,"message"=> "Водитель не оставлял отклик на этот заказ", "data"=> (object)array()));exit();
				exit();
			}
			if(count($id_driver)==0){
				echo parent::json_encode_cyr(array("code"=> 31,"message"=> "Водитель не оставлял отклик на этот заказ", "data"=> (object)array()));exit();
			}
			$info_order=$this->model_customer->getOrder(array("id"=>$id_order[0]),"site");
			if($result_delete=$this->model_driver->deleteCheckDriver($id_order[0],$id_driver[0]["iDriverId"])==0){
				echo parent::json_encode_cyr(array("code"=> 31,"message"=> "Водитель не оставлял отклик на этот заказ", "data"=> (object)array()));exit();
			}
			//print_r($info_order); echo $id_driver[0]["iDriverId"];
			if($info_order[0]["iDriverId"]==$id_driver[0]["iDriverId"]){
				if($this->model_customer->update_order($id_order[0],array("driver_id"=>0))==0){
					echo parent::json_encode_cyr(array("code"=> 31,"message"=> "Водитель не оставлял отклик на этот заказ", "data"=> (object)array()));exit();
				}
			}
			echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> (object)array()));
			//print_r($info_order);
			// print_r($result);
		}
		public function checkRefuse(){
			parent::checkSession();
			$this->model_driver=new Driver();
			$this->model_customer=new Customer();
			$id_device[0]["iDeviceId"]=$_SESSION["id_user"];
			$id_driver=$this->model_driver->findDriver($id_device);
			//print_r($id_driver);
			$result=$this->model_driver->checkRespond($_POST["id"],$id_driver[0]["iDriverId"]);
			if($result[0]["count"]==0){
				echo parent::json_encode_cyr(array("result1"=>false));exit();
				exit();
			}
			if(count($id_driver)==0){
				echo parent::json_encode_cyr(array("result"=>false));exit();
			}
			$info_order=$this->model_customer->getOrder(array("id"=>$_POST["id"]),"site");
			if($result_delete=$this->model_driver->deleteCheckDriver($_POST["id"],$id_driver[0]["iDriverId"])==0){
				echo parent::json_encode_cyr(array("result2"=>false));exit();
			}
			//print_r($info_order); echo $id_driver[0]["iDriverId"];
			if($info_order[0]["iDriverId"]==$id_driver[0]["iDriverId"]){
				if($this->model_customer->update_order($_POST["id"],array("driver_id"=>0))==0){
					echo parent::json_encode_cyr(array("result3"=>false));exit();
				}
			}
			echo parent::json_encode_cyr(array("result"=>true));
			//print_r($info_order);
			// print_r($result);
		}
		public function getOrderDriver($id_order){
			parent::checkToken("main");
			$this->model_geo=new Geo();
			$this->model_customer=new Customer();
			$this->model_driver=new Driver();
			$this->model_order=new order();
			$id_device=parent::getIdDevice();
			$id_driver=$this->model_driver->findDriver($id_device);
			if(count($id_driver)==0){
				echo parent::json_encode_cyr(array("code"=> 8,"message"=> "Проблемы с получением информации по заказу", "data"=> (object)array()));exit();
			}
			if(!empty($id_order[0]) && is_numeric($id_order[0])){
				$info_order=$this->model_customer->getOrder(array("id"=>$id_order[0]),"site");
				if(count($info_order)==0){
					echo parent::json_encode_cyr(array("code"=> 8,"message"=> "Проблемы с получением информации по заказу", "data"=> (object)array()));exit();
				}
				$price=$this->model_driver->getInfoResponse($info_order[0]["iOrderid"],$id_driver[0]["iDriverId"]);
					//print_r($price);
					//print_r($info_order);
				$new_array=array();
				$new_array["id"]=$info_order[0]["iOrderid"];
				$new_array["cargo_name"]=$info_order[0]["sCargoName"];
 					//$new_array["cargo_weight"]=$info_order[0]["iCargWeight"];
				if($info_order[0]["sWeightUnit"]=="ton"){
					$new_array["cargo_weight"]=$info_order[0]["iCargWeight"]/1000;
				}
				else{
					$new_array["cargo_weight"]=$info_order[0]["iCargWeight"];
				}
				$new_array["weight_unit"]=$info_order[0]["sWeightUnit"];
				$new_array["price"]=$info_order[0]["iPrice"];
				$new_array["currency"]=$info_order[0]["sCurrencyTypePrice"];
				$new_array["payment_type"]=$info_order[0]["sPaymentMethod"];
				$new_array["comment"]=$info_order[0]["tComment"];
				$new_array["rating"]=$info_order[0]["iRatingDriver"];
				$new_array["status"]=$info_order[0]["iStatus"];
				$new_array["offered_price"]=$price[0]["iPrice"];
				$dt = new DateTime($info_order[0]["dStartDate"]);
				$new_array["start_date"]=$dt->format(DATE_ISO8601);
				$dt = new DateTime($info_order[0]["dFinishDate"]);
				$new_array["finish_date"]=$dt->format(DATE_ISO8601);;
				$start_city_info=$this->model_order->getCity($info_order[0]["iStartCityId"]);
				if(isset($start_city_info["region"])){
					$info_region=$this->model_geo->getRegion($start_city_info["region"]);
					$info_region[0]["typeRegion"]=true;
				}
				else{
					$start_city_info["region"]="";
					$info_region[0]["iRegionVkid"]="";
					$info_region[0]["typeRegion"]=false;
				}
 					//print_r($info_order[0]["iFinishCityId"]);
				$new_array["start_city"]=array("id"=>$start_city_info["id"],"name"=>$start_city_info["title"],"parent_name"=>$start_city_info["region"],"parent_id"=>$info_region[0]["iRegionVkid"],"region"=>$info_region[0]["typeRegion"],"country_name"=>"Россия","country_id"=>1);
				if($info_order[0]["iFinishCityId"]>0){
					$finish_city_info=$this->model_order->getCity($info_order[0]["iFinishCityId"]);
 					//print_r($finish_city_info["region"]);
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
 					//print_r($finish_city_info);
					$new_array["finish_city"]=array("id"=>$finish_city_info["id"],"name"=>$finish_city_info["title"],"parent_name"=>$finish_city_info["region"],"parent_id"=>$info_region_finish[0]["iRegionVkid"],"region"=>$info_region_finish[0]["typeRegion"],"country_name"=>"Россия","country_id"=>1);
				}
				else{
					$new_array["finish_city"]=array();
				}
				$customer_info=$this->model_customer->getProfile($info_order[0]["iCustomerID"]);
				$link="";
				if(!empty($customer_info[0]["sHashImage"])){
					$link="/get_image_phone/?image=".$customer_info[0]["sHashImage"];
				}
				$new_array["customer"]=array(
					"id"=>$customer_info[0]["iCustomerId"],
					"name"=>$customer_info[0]["iCustomerName"],
					"organization"=>$customer_info[0]["iCustomerOrg"],
					"avatar"=>$link,
					"rating"=>$customer_info[0]["iRating"]
				);
				$this->model_customer->updateCountViews($info_order[0]["iOrderid"]);
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> $new_array));
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 8,"message"=> "Проблемы с получением информации по заказу", "data"=> (object)array()));
			}
		}
		public function get_orders_driver(){
			parent::checkToken("main");
			$id_device=parent::getIdDevice();
			$this->model_order=new Order();
			$this->model_geo=new Geo();
			$this->model_driver=new Driver();
			$id_driver=$this->model_driver->findDriver($id_device);
			if($_GET["order_type"]=="current"){
				$list=$this->model_driver->getOtklik($id_driver[0]["iDriverId"],$_GET);
			}
			else if($_GET["order_type"]=="in_work"){
				$list=$this->model_driver->getOrdersDriver($id_driver[0]["iDriverId"],"current",$_GET);
			}
			else if($_GET["order_type"]=="done"){
				$list=$this->model_driver->getOrdersDriver($id_driver[0]["iDriverId"],"old",$_GET);
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 36,"message"=> "Не удалось получить список заказов водителя", "data"=> (object)array()));
				exit();
			}
			$info_order_new=array();

			foreach ($list as $key => $value) {
				$new_array=array();
				$new_array["id"]=$value["iOrderid"];
				$new_array["cargo_name"]=$value["sCargoName"];
				$new_array["cargo_weight"]=$value["iCargWeight"];
				$new_array["weight_unit"]=$value["sWeightUnit"];
				$new_array["price"]=$value["iPrice"];
				$new_array["currency"]=$value["sCurrencyTypePrice"];
				$new_array["payment_type"]=$value["sPaymentMethod"];
				$new_array["comment"]=$value["tComment"];
				$new_array["status"]=$value["iStatus"];
				$new_array["rating"]=$value["iRatingDriver"];
				if(isset($value["iPrice"])){
					$new_array["offered_price"]=$value["iPrice"];
				}
				if($value["dStartDate"][0]=="0"){
					$new_array["start_date"]="";
				}
				else{
					$dt = new DateTime($value["dStartDate"]);
					$new_array["start_date"]=$dt->format(DATE_ISO8601);
				}
				if($value["dFinishDate"][0]=="0"){
					$new_array["finish_date"]="";
				}
				else{
					$dt = new DateTime($value["dFinishDate"]);
					$new_array["finish_date"]=$dt->format(DATE_ISO8601);
				}
				if(isset($value["iStartCityId"]) && $value["iStartCityId"]!=0){
 						//echo "yes"; echo $value["iStartCityId"];
 						//echo $value["iStartCityId"];
					$start_city_info=$this->model_order->getCity($value["iStartCityId"]);
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
				if(isset($value["iFinishCityId"]) && $value["iFinishCityId"]!=0){
 						//echo $value["iFinishCityId"];
					$finish_city_info=$this->model_order->getCity($value["iFinishCityId"]);
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
				$info_order_new[]=$new_array;
			}
			if(is_array($info_order_new)){
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("orders"=> $info_order_new)));
			}
		}
		public function get_left_profile(){
			parent::checkToken("main");
			$this->model_auth=new Auth_User();
			$this->model_geo=new Geo();
			$this->model_order=new Order();
			$this->model_driver=new Driver();
			$id_device=parent::getIdDevice();
			$info_device=$this->model_auth->getInfoDevice($id_device[0]["iDeviceId"]);
			$id_driver=$this->model_driver->findDriver($id_device);
			//print_r($_SERVER['REQUEST_METHOD']);
				//print_r($id_driver);
			$profile=$this->model_driver->getProfileDriver($id_driver);
			//print_r($profile);
			$new_profile=array();
			$new_profile["name"]=$profile[0]["sDriverName"];
			$link="";
			if(!empty($profile[0]["sHashImage"])){
				$link="/get_image_phone/?image=".$profile[0]["sHashImage"];
			}
			$new_profile["avatar"]=$link;
			$new_profile["rating"]=$profile[0]["iRating"];
			if(empty($profile[0]["sNotification"])){
				$new_profile["notifications"]="false";
			}
			else{
				$new_profile["notifications"]=$profile[0]["sNotification"];
			}
			echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("profile"=>$new_profile)));
		}
		public function getCalls(){
			parent::checkToken("main");
			$this->model_driver=new Driver();
			$list_calls=array();
			$id_device=parent::getIdDevice();
			$id_driver=$this->model_driver->findDriver($id_device);
			if(count($id_driver)!=0){
				$user_type="driver";
				$list_calls=$this->model_driver->getCallsDriver($id_driver[0]["iDriverId"],$_GET);
			}
			else{
				//echo "YES";
				$this->model_customer=new Customer();
				$id_customer=$this->model_customer->findCustomer($id_device);
				//echo $id_customer[0]["iCustomerId"];
				$user_type="customer";
				if(count($id_customer)!=0){
					$list_calls=$this->model_customer->getCallsCustomer($id_customer[0]["iCustomerId"],$_GET);
				}
			}
			$new_list=array();
			foreach($list_calls as $key=>$call){
				$new_list_element=array();
				$new_list_element["id"] = $call["id"];
				$new_list_element["name"]=$call["name"];
				$link="";
				if(!empty($call["hash"])){
					$link="/get_image_phone/?image=".$call["hash"];
				}
				$new_list_element["avatar"]=$link;
				$new_list_element["user_type"]=$user_type;
				$dt = new DateTime($call["date_call"]);
				$new_list_element["date"]=$dt->format(DATE_ISO8601);
				$new_list_element["type"]=$call["cus"]==0 ? "in" : "out";
				$new_list[]=$new_list_element;

			}
			echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("calls"=>$new_list)));
		}	
		public function getProfileDriver($id){
			parent::checkToken("main");
			$this->model_geo=new Geo();
			$this->model_order=new Order();
			$this->model_driver=new Driver();
			$this->model_customer=new Customer();
			$id_device=parent::getIdDevice();
			//print_r($id_device);
			$id_customer=$this->model_customer->findCustomer($id_device);
			if(count($id_customer)==0){
				echo parent::json_encode_cyr(array("code"=> 37,"message"=> "Водитель не могут просматривать профили других водителей", "data"=> (object)array()));
				exit();
			}
			$id_driver[0]["iDriverId"]=$id[0];
			//print_r($id_driver);
			$profile=$this->model_driver->getProfileDriver($id_driver);
			if(count($profile)==0){
				echo parent::json_encode_cyr(array("code"=> 26,"message"=> "Водитель не найден", "data"=> (object)array()));
				exit();
			}
				//print_r($profile);
			$new_profile=array();
			$new_profile["id"]=$id[0];
			if($profile[0]["sDriverSpecialization"]=="in_city"){
					//echo "yes";
				$new_profile["intercity_driver"]=false;
				$new_profile["in_city_driver"]=true;
			}
			else if($profile[0]["sDriverSpecialization"]=="intercity"){
				$new_profile["intercity_driver"]=true;
				$new_profile["in_city_driver"]=false;
			}
			else if($profile[0]["sDriverSpecialization"]=="both"){
				$new_profile["intercity_driver"]=true;
				$new_profile["in_city_driver"]=true;
			}	
			$new_profile["name"]=$profile[0]["sDriverName"];
				// $link="";
				// if(!empty($profile[0]["sHashImage"])){
				// 	$link="/get_image/?image=".$profile[0]["sHashImage"];
				// }
				//$new_profile["avatar"]=$link;
			$link="";
			if(!empty($profile[0]["sHashImage"])){
				$link="/get_image_phone/?image=".$profile[0]["sHashImage"];
			}
			$new_profile["avatar"]=$link;
			$new_profile["car_name"]=$profile[0]["sCarName"];
			$new_profile["car_type"]=$profile[0]["iCarType"];
			if($profile[0]["iBodyType"]>0){
				$info_body_types=$this->model_driver->getInfoBodyType($profile[0]["iBodyType"]);
					//print_r($info_body_types);
				$new_profile["body_type"]=array("id"=>$info_body_types[0]["iBodyTypeId"],"title"=>$info_body_types[0]["iBodyTypeName"]);
			}
			else{
				$new_profile["body_type"]=null;
			}
				//$new_profile["body_type_id"]=$profile[0]["iBodyType"];
			$new_profile["capacity"]=$profile[0]["iCapacity"];
			$new_profile["volume"]=$profile[0]["iVolume"];
			$new_profile["load_type"]=array(
				"top"=>$profile[0]["sLoadTypeTop"]=="" ? false : true,
				"rear"=>$profile[0]["sLoadTypeRear"]=="" ? false : true,
				"side"=>$profile[0]["sLoadTypeSide"]=="" ? false : true,
			);
			$new_profile["loaders"]=$profile[0]["iLoaders"];
			$new_profile["rating"]=$profile[0]["iRating"];
			$new_profile["rate_intercity"]=$profile[0]["iRate"];
			$new_profile["rate_in_city"]=$profile[0]["iRateInCity"];
			if(isset($profile[0]["iCityId"]) && $profile[0]["iCityId"]!=0){
 						//echo "yes"; echo $value["iStartCityId"];
 						//echo $value["iStartCityId"];
				$start_city_info=$this->model_order->getCity($profile[0]["iCityId"]);
				if(isset($start_city_info["region"])){
					$info_region=$this->model_geo->getRegion($start_city_info["region"]);
					$info_region[0]["typeRegion"]=true;
				}
				else{
					$start_city_info["region"]="";
					$info_region[0]["iRegionVkid"]="";
					$info_region[0]["typeRegion"]=false;
				}
				$new_profile["city"]=array("id"=>$start_city_info["id"],"name"=>$start_city_info["title"],"parent_name"=>$start_city_info["region"],"parent_id"=>$info_region[0]["iRegionVkid"],"region"=>$info_region[0]["typeRegion"],"country_name"=>"Россия","country_id"=>1);
			}
				// if($profile[0]["iCityId"]!=0){
				// 	$start_city_info=$this->model_order->getCity($profile[0]["iCityId"]);
				// 		$info_region=$this->model_geo->getRegion($start_city_info["region"]);
	 		// 			$new_profile["city"]=array(
	 		// 				"id"=>$start_city_info["id"],
	 		// 				"name"=>$start_city_info["title"],
	 		// 				"parent_name"=>$start_city_info["region"],
	 		// 				"parent_id"=>$info_region[0]["iRegionVkid"],
	 		// 				"region"=>true,
	 		// 				"country_name"=>"Россия",
	 		// 				"country_id"=>1);
 			// 		}
			else{
				$new_profile["city"]=array();
			}
			echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("profile"=>$new_profile)));
		}
	}