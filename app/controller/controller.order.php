<?php
	Class Order extends base{
		public function __construct(){
			parent::__construct();
			$this->model1=new Customer();
		}
		public function registration(){
			$this->model_driver=new Driver();
			parent::checkToken("guest");
			$param=parent::getDate();
			$id_customer=parent::getIdDevice();
			$id_customer_new=$this->model1->findCustomer($id_customer);
			$id_driver=$this->model_driver->findDriver($id_customer);
			if(count($id_customer_new)>0 || count($id_driver)>0){
				echo parent::json_encode_cyr(array("code"=> 24,"message"=> "На данное устройство уже зарегистрирован аккаунт", "data"=> (object)array()));
				exit();
			}
			 // print_r($param);
			 // echo "yes";
			//print_r($_POST);
			//print_r($_FILES);
			//$im=imagecreatefromjpeg($param["avatar"]);
			// $data = base64_decode($param["avatar"]);
			// $postdata = file_get_contents("php://input");
			$content_file=$_POST["avatar"];
			$_POST["avatar"]="";
			//print_r(parent::getIdDevice());
			$id=$this->model1->AddRegistrationCustomer($_POST,parent::getIdDevice());
			$id_customer_new=$this->model1->findCustomer($id_customer);
			if (isset($content_file) && strlen($content_file)>0) {
				$md5_link=md5(microtime());
				$link_to_image="/images/customer/".$id."/l_".$md5_link.".png";
				$link_to_small_image="/images/customer/".$id."/s_".$md5_link.".png";
					if(!is_dir($_SERVER["DOCUMENT_ROOT"]."/images/customer/".$id."/")) mkdir($_SERVER["DOCUMENT_ROOT"]."/images/customer/".$id."/");
			        $handle = fopen($_SERVER["DOCUMENT_ROOT"].$link_to_image, "w+");
			        fwrite($handle, $content_file);
			        fclose($handle);
			        $_POST["avatar"]=$md5_link.".png";
			        if(parent::img_resize($_SERVER["DOCUMENT_ROOT"].$link_to_image, $_SERVER["DOCUMENT_ROOT"].$link_to_small_image, 100, 0)!=1){
		    			//echo parent::json_encode_cyr(array("result"=> false));
		    			//exit();
		    		}
			}
			//else echo "r=error";
			//print_r($_POST);
			//echo $id_customer_new[0]["iCustomerId"];
			$hash_image=substr(md5(microtime() . rand(0, 9999)), 0, 25);
			$new_hash=$this->checkHash($hash_image);
			//echo $hash_image;
			if (isset($content_file) && strlen($content_file)>0) {
				$id_files=$this->model1->update_profile_customer(array("avatar"=>$_POST["avatar"],"avatar_hash"=>$new_hash),$id_customer_new[0]["iCustomerId"]);
			}
			if(count($id_customer)>0){
				$id_customer_device=$this->model1->addCustomer($id,$id_customer[0]["iDeviceId"]);
				if($id_customer_device>0){
					echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> (object)array()));
				}
				else{
					echo parent::json_encode_cyr(array("code"=> 3,"message"=> "Проблемы с регистрацией заказчика", "data"=> (object)array()));
				}
			}
			//echo $this->view->render('root.html',array('profile'=>$this->profile[0],"news_mass"=>$this->news_mass,"user_list"=>$this->user_mas));
		
		}
		public function get_orders(){
			//echo "yes";
			$this->model_customer=new Customer();
			$this->model_main=new Main();
			$list_order=$this->model_customer->getOrder(array("limit"=>30,"status_id"=>0),"site");
			$new_array_order=$this->model_main->arrayResort($list_order);
			echo $this->view->render('get_orders.html',array("orders"=>$new_array_order,"profile"=>$this->profile_user));
			echo "<pre>";
				print_r($new_array_order);
			echo "</pre>";
		}
		public function delete_order(){
			parent::checkSession();
			//this->model_customer=new Customer();
			$device[0]["iDeviceId"]=$_SESSION["id_user"];
			//print_r($id_device);
			$id_customer=$this->model1->findCustomer($device);
			//print_r($id_customer);
			$idd=$this->model1->deleteOrder($_POST["id"],$id_customer[0]["iCustomerId"]);
			//echo $idd;
				if($idd==1){
					echo parent::json_encode_cyr(array("result"=> true));
				}
				else{
					echo parent::json_encode_cyr(array("result"=> false));
				}

		}
		public function update_order(){
			parent::checkSession();
			$this->model_geo=new Geo();
			if(!empty($_POST["start_date"])){
				$date = DateTime::createFromFormat('d.m.Y', $_POST["start_date"]);
 				$_POST["start_date"]=$date->format('Y-m-d H:i:s');
			}
			else{
				$_POST["start_date"]="0000-00-00 00:00:00";
			}
			if(!empty($_POST["finish_date"])){
				$date = DateTime::createFromFormat('d.m.Y', $_POST["finish_date"]);
 				$_POST["finish_date"]=$date->format('Y-m-d H:i:s');
			}
			else{
				$_POST["finish_date"]="0000-00-00 00:00:00";
			}
			//print_r($_POST);
			//$_POST["finish_date"]=date('Y-m-d H:i:s', strtotime($_POST["finish_date"]));
			if($_POST["weight_unit"]=="ton"){
				$_POST["cargo_weight"]=$_POST["cargo_weight"]*1000;
			}
			else{
				$_POST["cargo_weight"]=$_POST["cargo_weight"];
			}
			$new_id=$this->model1->update_order($_POST["id_order"],$_POST);
			if($new_id==1){
				$info_order=$this->model1->getOrder(array("id"=>$_POST["id_order"]),"site");
					//print_r($id_order[0]);
					$new_array=array();
					$new_array["id"]=$info_order[0]["iOrderid"];
					// $new_array["views"]=0;
					// $new_array["drivers_count_total"]=0;
					// $new_array["drivers_count_new"]=0;
 				// 	$new_array["drivers_count_new"]=0;
 					$new_array["cargo_name"]=$info_order[0]["sCargoName"];
 					//$new_array["cargo_weight"]=$info_order[0]["iCargWeight"];
 					//print_r($info_order[0]["iCargWeight"]);
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
 					$new_array["status"]=$info_order[0]["iStatus"];
 					$new_array["rating"]=0;
 					//$dt = new DateTime($info_order[0]["dStartDate"]);
 					if($info_order[0]["dStartDate"][0]=="0"){
 						$new_array["start_date"]="";
 					}else{
 						//$date = DateTime::createFromFormat('Y-m-d H:i:s', $info_order[0]["dStartDate"]);
						$new_array["start_date"]=$info_order[0]["dStartDate"];
 					}
 					//$dt = new DateTime($info_order[0]["dFinishDate"]);
 					if($info_order[0]["dFinishDate"][0]=="0"){
 						$new_array["finish_date"]="";
 					}else{
 						//$date = DateTime::createFromFormat('Y-m-d H:i:s', $info_order[0]["dFinishDate"]);
						$new_array["finish_date"]=$info_order[0]["dFinishDate"];
 					}
 					$new_array["rating"]=0;
 					$start_city_info=$this->getCity($info_order[0]["iStartCityId"]);
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
 					$finish_city_info=$this->getCity($info_order[0]["iFinishCityId"]);
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
				echo parent::json_encode_cyr(array("result"=> true,"info_order"=>$new_array));
			}
			else{
				echo parent::json_encode_cyr(array("result"=> false));
			}

		}
		public function add_order(){
			parent::checkSession();
			$_POST["start_date"]=date('Y-m-d H:i:s', strtotime($_POST["start_date"]));
			$_POST["finish_date"]=date('Y-m-d H:i:s', strtotime($_POST["finish_date"]));
			if($_POST["weight_unit"]=="ton"){
				$_POST["cargo_weight"]=$_POST["cargo_weight"]*1000;
			}
			else{
				$_POST["cargo_weight"]=$_POST["cargo_weight"];
			}
			//print_r($_POST);
			$device[0]["iDeviceId"]=$_SESSION["id_user"];
			$id_customer=$this->model1->findCustomer($device);
			$new_id=$this->model1->addOrder($_POST,$id_customer);
				if(is_numeric($new_id) && $new_id>0){
					if(isset($_POST["body_types"]) && !empty($_POST["body_types"])){
						$body_types_ids=json_decode($_POST["body_types"],true);
					}
					//print_r($body_types_ids);
					if(is_array($body_types_ids)){
						foreach ($body_types_ids as $value) {
							$this->model1->addBodyTypes($value,$new_id);
						}
					}
					echo parent::json_encode_cyr(array("result"=> true));
				}
				else{
					echo parent::json_encode_cyr(array("result"=> false));
				}
		}
		public function orders(){
			parent::checkToken("any");
			$this->model_customer=new Customer();
			$id_device=parent::getIdDevice();
			$id_customer=$this->model_customer->findCustomer($id_device);
			if(count($id_customer)==0){
				echo parent::json_encode_cyr(array("code"=> 37,"message"=> "Заказчик не найден", "data"=> (object)array()));
				exit();
			}
			if($_SERVER['REQUEST_METHOD']=="PUT"){
				$this->model_geo=new Geo();
				//echo $this->model->get_token_device($this->header_token);
				$date_r=parent::getFormData();
				$date_r["start_date"]=date('Y-m-d H:i:s', strtotime(substr($date_r["start_date"],0,10)));
				$date_r["finish_date"]=date('Y-m-d H:i:s', strtotime(substr($date_r["finish_date"],0,10)));
				if($date_r["weight_unit"]=="ton"){
					$date_r["cargo_weight"]=$date_r["cargo_weight"]*1000;
				}
				// /print_r($date_r);
				//echo date("Y-m-d'T'H:m:sZ",strtotime($date["start_date"]));
				//echo date_format($date_r["start_date"], 'Y-m-d H:i:s');
				//echo date("Y",strtotime($date_r["start_date"]));
				// $dt = new DateTime($date_r["start_date"]);
				// Правильный перевод var_dump(date('Y-m-d', strtotime(substr($date_r["start_date"],0,10))));
				//echo $dt->format(DATE_ISO8601);
				// echo date('h:d', strtotime($date_r["start_date"]));
				//print_r();
				//print_r(count($id_customer));
				if(count($id_customer)==0){
					echo parent::json_encode_cyr(array("code"=> 6,"message"=> "Проблема с добавлением нового заказа", "data"=> (object)array()));
					exit();
				}
				$new_id=$this->model1->addOrder($date_r,$id_customer);
				if(is_numeric($new_id) && $new_id>0){
					if(isset($date_r["body_types_ids"]) && !empty($date_r["body_types_ids"])){
						$body_types_ids=json_decode($date_r["body_types_ids"],true);
					}
					//print_r($body_types_ids);
					if(is_array($body_types_ids)){
						foreach ($body_types_ids as $value) {
							$this->model1->addBodyTypes($value,$new_id);
						}
					}
					$info_order=$this->model1->getOrder(array("id"=>$new_id),"site");
					$list_body_types=array();
					$list_body_types=$this->model1->getBodyOrders($info_order[0]["iOrderid"]);
					//echo "yes";
					$new_list_body=array();
					if(count($list_body_types)>0){
						foreach ($list_body_types as $key => $value) {
							$new_list_body[]=array("id"=>$value["iBodyTypeId"],"name"=>$value["iBodyTypeName"]);
						}
					}
					//print_r($info_order);
					$new_array=array();
					$new_array["id"]=$info_order[0]["iOrderid"];
					$new_array["views"]=0;
					$new_array["drivers_count_total"]=0;
					$new_array["drivers_count_new"]=0;
 					$new_array["cargo_name"]=$info_order[0]["sCargoName"];
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
 					$new_array["status"]=$info_order[0]["iStatus"];
 					$new_array["rating"]=0;
 					$new_array["capacity"]=$info_order[0]["iCapacityId"];
 					$new_array["can_call"]=$info_order[0]["iCanCall"];
 					$new_array["can_write"]=$info_order[0]["iCanWrite"];
 					if(count($new_list_body)>0){
 						$new_array["body_types_ids"]=$new_list_body;
 					}
 					if($info_order[0]["dStartDate"][0]=="0"){
 						$new_array["start_date"]="";
 					}
 					else{
 						$dt = new DateTime($info_order[0]["dStartDate"]);
 						$new_array["start_date"]=$dt->format(DATE_ISO8601);
 					}
 					if($info_order[0]["dFinishDate"][0]=="0"){
 						$new_array["finish_date"]="";
 					}
 					else{
 						$dt = new DateTime($info_order[0]["dFinishDate"]);
 						$new_array["finish_date"]=$dt->format(DATE_ISO8601);
 					}
 					$new_array["rating"]=0;
 					$start_city_info=$this->getCity($info_order[0]["iStartCityId"]);
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
 					$finish_city_info=$this->getCity($info_order[0]["iFinishCityId"]);
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
					//$new_array["driver"]=(object)array();
					echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> $new_array));
				}
				else{
					echo parent::json_encode_cyr(array("code"=> 6,"message"=> "Проблема с добавлением нового заказа", "data"=> (object)array()));
				}
			}	
			else if($_SERVER['REQUEST_METHOD']=="GET"){
				$this->model_geo=new Geo();
				$this->model_driver=new Driver();
			
				if($_GET["order_type"]=="active"){
					$_GET["status_id"]=0;
				}
				else if($_GET["order_type"]=="inactive"){
					$_GET["status_id"]=1;
				}
				else{
					echo parent::json_encode_cyr(array("code"=> 27,"message"=> "Проблема c получением списка заказов", "data"=> (object)array()));
					exit();
				}
				$_GET["customer_id"]=$id_customer[0]["iCustomerId"];
				$info_order=$this->model_customer->getOrder($_GET,"app");
				unset($_GET["limit"]);
				unset($_GET["offset"]);
				$count_orders=$this->model_customer->getOrder($_GET,"count");
				$info_order_new["total"]=$count_orders[0]["count"];
				foreach ($info_order as $key => $value) {
				$list_driver=$this->model_driver->getRespondDriver($value["iOrderid"],$value["dLastGetDriver"],"count");
				//print_r($list_driver);
				$list_driver2=$this->model_driver->getNewRespondDriver($value["iOrderid"],$value["dLastGetDriver"],"count");
				//print_r($list_driver2);
				$new_array=array();
					$new_array["id"]=$value["iOrderid"];
					$new_array["views"]=$value["iViewsCount"];
					$new_array["drivers_count_total"]=$list_driver[0]["count"]+$list_driver2[0]["count"];
					$new_array["drivers_count_new"]=$list_driver2[0]["count"];
 					$new_array["cargo_name"]=$value["sCargoName"];
 					if($value["sWeightUnit"]=="ton"){
						$new_array["cargo_weight"]=$value["iCargWeight"]/1000;
					}
					else{
						$new_array["cargo_weight"]=$value["iCargWeight"];
					}
 					//$new_array["cargo_weight"]=$value["iCargWeight"];
 					$new_array["weight_unit"]=$value["sWeightUnit"];
 					$new_array["price"]=$value["iPrice"];
 					$new_array["currency"]=$value["sCurrencyTypePrice"];
 					$new_array["payment_type"]=$value["sPaymentMethod"];
 					$new_array["comment"]=$value["tComment"];
 					$new_array["rating"]=$value["iRatingCustomer"];
 					$new_array["status"]=$value["iStatus"];
 					$new_array["capacity"]=$value["iCapacityId"];
 					$new_array["can_call"]=$value["iCanCall"];
 					$new_array["can_write"]=$value["iCanWrite"];
 					if($info_order[0]["dStartDate"][0]=="0"){
 						$new_array["start_date"]="";
 					}
 					else{
 						$dt = new DateTime($info_order[0]["dStartDate"]);
 						$new_array["start_date"]=$dt->format(DATE_ISO8601);
 					}
 					if($info_order[0]["dFinishDate"][0]=="0"){
 						$new_array["finish_date"]="";
 					}
 					else{
 						$dt = new DateTime($info_order[0]["dFinishDate"]);
 						$new_array["finish_date"]=$dt->format(DATE_ISO8601);
 					}
 					$start_city_info=$this->getCity($value["iStartCityId"]);
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
 					$finish_city_info=$this->getCity($value["iFinishCityId"]);
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
					$info_order_new["orders"][]=$new_array;
				}
				if(isset($_GET["debug"])){
					print_r($info_order_new);
				}
				if($info_order_new["total"]==0){
					$info_order_new["orders"]=array();
				}
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> $info_order_new));
				//print_r($info_order);
			}
			
		}
		public function get_order ($id_order){
			 parent::checkToken("main");
			$this->model_geo=new Geo();
			$this->model_driver=new Driver();
			if($_SERVER['REQUEST_METHOD']=="DELETE"){
				// echo "yes";
				$id_device=parent::getIdDevice();
				$id_customer=$this->model1->findCustomer($id_device);
				$info_order=$this->model1->getOrder(array("id"=>$id_order[0]),"site");
				if($info_order[0]["iCustomerID"]!=$id_customer[0]["iCustomerId"]){
						header('HTTP/1.1 403');
						echo parent::json_encode_cyr(array("code"=> 29,"message"=> "Нет прав на удаление данного заказа", "data"=> (object)array()));
						exit();
					}
				$idd=$this->model1->deleteOrder($id_order[0],$id_customer[0]["iCustomerId"]);
				if($idd==1){
					echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>(object)array()));
				}
				else{
					echo parent::json_encode_cyr(array("code"=> 14,"message"=> "Не возможно удалить заказ", "data"=> (object)array()));
				}
			}
			else if($_SERVER['REQUEST_METHOD']=="GET"){
				//echo "yes";
				if(!empty($id_order[0]) && is_numeric($id_order[0])){
					$info_order=$this->model1->getOrder(array("id"=>$id_order[0]),"site");
					$list_driver=$this->model_driver->getRespondDriver($info_order[0]["iOrderid"],$info_order[0]["dLastGetDriver"],"count");
					$list_driver2=$this->model_driver->getNewRespondDriver($info_order[0]["iOrderid"],$info_order[0]["dLastGetDriver"],"count");
									$list_body_types=array();
				$list_body_types=$this->model1->getBodyOrders($info_order[0]["iOrderid"]);
				//echo "yes";
				$new_list_body=array();
				if(count($list_body_types)>0){
					foreach ($list_body_types as $key => $value) {
						$new_list_body[]=array("id"=>$value["iBodyTypeId"],"name"=>$value["iBodyTypeName"]);
					}
				}
				//print_r($new_list_body);
					//print_r($id_order[0]);
					$id_device=parent::getIdDevice();
					//print_r($id_device);
					$id_customer=$this->model1->findCustomer($id_device);
					// print_r($id_customer);
					// print_r($info_order);
					if($info_order[0]["iCustomerID"]!=$id_customer[0]["iCustomerId"]){
						header('HTTP/1.1 403');
						echo parent::json_encode_cyr(array("code"=> 28,"message"=> "Вы не можете посмотреть этот заказ", "data"=> (object)array()));
						exit();
					}
					$new_array=array();
					$new_array["id"]=$info_order[0]["iOrderid"];
					$new_array["views"]=$info_order[0]["iViewsCount"];
					$new_array["drivers_count_total"]=$list_driver[0]["count"]+$list_driver2[0]["count"];
					$new_array["drivers_count_new"]=$list_driver2[0]["count"];
 					$new_array["cargo_name"]=$info_order[0]["sCargoName"];
 					if($info_order[0]["sWeightUnit"]=="ton"){
						$new_array["cargo_weight"]=$info_order[0]["iCargWeight"]/1000;
					}
					else{
						$new_array["cargo_weight"]=$info_order[0]["iCargWeight"];
					}
 					//$new_array["cargo_weight"]=$info_order[0]["iCargWeight"];
 					$new_array["weight_unit"]=$info_order[0]["sWeightUnit"];
 					$new_array["price"]=$info_order[0]["iPrice"];
 					$new_array["currency"]=$info_order[0]["sCurrencyTypePrice"];
 					$new_array["payment_type"]=$info_order[0]["sPaymentMethod"];
 					$new_array["comment"]=$info_order[0]["tComment"];
 					$new_array["rating"]=$info_order[0]["iRatingDriver"];
 					$new_array["capacity"]=$info_order[0]["iCapacityId"];
 					$new_array["can_call"]=$info_order[0]["iCanCall"];
 					$new_array["status"]=$info_order[0]["iStatus"];
 					$new_array["can_write"]=$info_order[0]["iCanWrite"];
 					if($info_order[0]["iDriverId"]>0){
						$driver_id[0]["iDriverId"]=$info_order[0]["iDriverId"];
						$driver_info=$this->model_driver->getProfileDriver($driver_id);
						$link="";
						if(!empty($driver_info[0]["sHashImage"])){
							$link="/get_image_phone/?image=".$driver_info[0]["sHashImage"];
						}
						$new_driver_info=array(
							"id"=>$info_order[0]["iDriverId"],
							"name"=>$driver_info[0]["sDriverName"],
							"avatar"=>$link,
							"rating"=>$driver_info[0]["iRating"]
							);
						$new_array["driver"]=$new_driver_info;
					}
					else if($info_order[0]["iDriverId"]==0){
						$new_driver_info=array(
							"id"=>0
						);
						$new_array["driver"]=$new_driver_info;
					}
					else{
						//$new_array["driver"]=array();
					}
 					if($info_order[0]["dStartDate"][0]=="0"){
 						$new_array["start_date"]="";
 					}
 					else{
 						$dt = new DateTime($info_order[0]["dStartDate"]);
 						$new_array["start_date"]=$dt->format(DATE_ISO8601);
 					}
 					if($info_order[0]["dFinishDate"][0]=="0"){
 						$new_array["finish_date"]="";
 					}
 					else{
 						$dt = new DateTime($info_order[0]["dFinishDate"]);
 						$new_array["finish_date"]=$dt->format(DATE_ISO8601);
 					}
 					if(count($new_list_body)>0){
 						$new_array["body_types_ids"]=$new_list_body;
 					}
 					// $dt = new DateTime($info_order[0]["dStartDate"]);
 					// $new_array["start_date"]=$dt->format(DATE_ISO8601);
 					// $dt = new DateTime($info_order[0]["dFinishDate"]);
 					// $new_array["finish_date"]=$dt->format(DATE_ISO8601);;
 					//$new_array["rating"]=0;
 					$start_city_info=$this->getCity($info_order[0]["iStartCityId"]);
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
 					$finish_city_info=$this->getCity($info_order[0]["iFinishCityId"]);
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
					if($info_order[0]["iDriverId"]>0){
						$driver_id[0]["iDriverId"]=$info_order[0]["iDriverId"];
						$driver_info=$this->model_driver->getProfileDriver($driver_id);
						$link="";
						if(!empty($driver_info[0]["sHashImage"])){
							$link="/get_image_phone/?image=".$driver_info[0]["sHashImage"];
						}
						$new_driver_info=array(
							"id"=>$info_order[0]["iDriverId"],
							"name"=>$driver_info[0]["sDriverName"],
							"avatar"=>$link,
							"rating"=>$driver_info[0]["iRating"]
							);
						$new_array["driver"]=$new_driver_info;
					}
					else if($info_order[0]["iDriverId"]==0){
						$new_driver_info=array(
							"id"=>0
						);
						$new_array["driver"]=$new_driver_info;
					}
					else{
						//$new_array["driver"]=array();
					}
					$this->model1->update_order($id_order[0],array("dTimeLast"=>""));
					echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> $new_array));
				}
			}
			else if($_SERVER['REQUEST_METHOD']=="PATCH"){
				if(!empty($id_order[0]) && is_numeric($id_order[0])){
					$date_r=parent::getFormData();
					$id_device=parent::getIdDevice();
					//print_r($id_device);
					$id_customer=$this->model1->findCustomer($id_device);
					// print_r($id_customer);
					// print_r($info_order);
					$info_order=$this->model1->getOrder(array("id"=>$id_order[0]),"site");
					if($info_order[0]["iCustomerID"]!=$id_customer[0]["iCustomerId"]){
						header('HTTP/1.1 403');
						echo parent::json_encode_cyr(array("code"=> 34,"message"=> "Вы не можете редактировать этот заказ", "data"=> (object)array()));
						exit();
					}
					$date_r["start_date"]=date('Y-m-d H:i:s', strtotime(substr($date_r["start_date"],0,10)));
					$date_r["finish_date"]=date('Y-m-d H:i:s', strtotime(substr($date_r["finish_date"],0,10)));
					if($date_r["weight_unit"]=="ton"){
						$date_r["cargo_weight"]=$date_r["cargo_weight"]*1000;
					}
					$this->model1->update_order($id_order[0],$date_r);
					if(isset($date_r["body_types_ids"]) && !empty($date_r["body_types_ids"])){
						$body_types_ids=json_decode($date_r["body_types_ids"],true);
					}
					if(is_array($body_types_ids)){
						$this->model1->deleteBodyTypes($id_order[0]);
						foreach ($body_types_ids as $value) {
							$this->model1->addBodyTypes($value,$id_order[0]);
						}
					}
					$info_order=$this->model1->getOrder(array("id"=>$id_order[0]),"site");
					$list_driver=$this->model_driver->getRespondDriver($info_order[0]["iOrderid"],$info_order[0]["dLastGetDriver"],"count");
					$list_driver2=$this->model_driver->getNewRespondDriver($info_order[0]["iOrderid"],$info_order[0]["dLastGetDriver"],"count");
					$list_body_types=$this->model1->getBodyOrders($info_order[0]["iOrderid"]);
					//echo "yes";
					$new_list_body=array();
					if(count($list_body_types)>0){
						foreach ($list_body_types as $key => $value) {
							$new_list_body[]=array("id"=>$value["iBodyTypeId"],"name"=>$value["iBodyTypeName"]);
						}
					}
					//print_r($id_order[0]);
					$new_array=array();
					$new_array["id"]=$info_order[0]["iOrderid"];
					$new_array["views"]=$info_order[0]["iViewsCount"];
					$new_array["drivers_count_total"]=$list_driver[0]["count"]+$list_driver2[0]["count"];
					$new_array["drivers_count_new"]=$list_driver2[0]["count"];
 					$new_array["cargo_name"]=$info_order[0]["sCargoName"];
 					if($info_order[0]["sWeightUnit"]=="ton"){
						$new_array["cargo_weight"]=$info_order[0]["iCargWeight"]/1000;
					}
					else{
						$new_array["cargo_weight"]=$info_order[0]["iCargWeight"];
					}
					if(count($new_list_body)>0){
 						$new_array["body_types_ids"]=$new_list_body;
 					}
 					//$new_array["cargo_weight"]=$info_order[0]["iCargWeight"];
 					$new_array["weight_unit"]=$info_order[0]["sWeightUnit"];
 					$new_array["price"]=$info_order[0]["iPrice"];
 					$new_array["currency"]=$info_order[0]["sCurrencyTypePrice"];
 					$new_array["payment_type"]=$info_order[0]["sPaymentMethod"];
 					$new_array["comment"]=$info_order[0]["tComment"];
 					$new_array["rating"]=$info_order[0]["iRatingCustomer"];
 					$new_array["capacity"]=$info_order[0]["iCapacityId"];
 					$new_array["can_call"]=$info_order[0]["iCanCall"];
 					$new_array["can_write"]=$info_order[0]["iCanWrite"];
 					$new_array["status"]=$info_order[0]["iStatus"];
 					if($info_order[0]["dStartDate"][0]=="0"){
 						$new_array["start_date"]="";
 					}
 					else{
 						$dt = new DateTime($info_order[0]["dStartDate"]);
 						$new_array["start_date"]=$dt->format(DATE_ISO8601);
 					}
 					if($info_order[0]["dFinishDate"][0]=="0"){
 						$new_array["finish_date"]="";
 					}
 					else{
 						$dt = new DateTime($info_order[0]["dFinishDate"]);
 						$new_array["finish_date"]=$dt->format(DATE_ISO8601);
 					}
 					// $dt = new DateTime($info_order[0]["dStartDate"]);
 					// $new_array["start_date"]=$dt->format(DATE_ISO8601);
 					// $dt = new DateTime($info_order[0]["dFinishDate"]);
 					// $new_array["finish_date"]=$dt->format(DATE_ISO8601);;
 					//$new_array["rating"]=0;
 					$start_city_info=$this->getCity($info_order[0]["iStartCityId"]);
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
 					$finish_city_info=$this->getCity($info_order[0]["iFinishCityId"]);
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
					if($info_order[0]["iDriverId"]>0){
						$driver_id[0]["iDriverId"]=$info_order[0]["iDriverId"];
						$driver_info=$this->model_driver->getProfileDriver($driver_id);
						$link="";
						if(!empty($driver_info[0]["sHashImage"])){
							$link="/get_image_phone/?image=".$driver_info[0]["sHashImage"];
						}
						$new_driver_info=array(
							"id"=>$info_order[0]["iDriverId"],
							"name"=>$driver_info[0]["sDriverName"],
							"avatar"=>$link,
							"rating"=>$driver_info[0]["iRating"]
							);
						$new_array["driver"]=$new_driver_info;
					}
					else if($info_order[0]["iDriverId"]==0){
						$new_driver_info=array(
							"id"=>0
						);
						$new_array["driver"]=$new_driver_info;
					}
					else{
						//$new_array["driver"]=array();
					}
					echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> $new_array));
				}
			}
		}
		public function getRespondDriver($id_order){
			 parent::checkToken("main");
			$this->model_driver=new Driver();
			//$list_driver=$this->model_driver->getRespondDriver($info_order[0]["iOrderid"],$info_order[0]["dLastGetDriver"],"count");
			$type="site";
			if(isset($_GET["called"]) && $_GET["called"]==true){
				$type="called";
			}
			$list_driver=$this->model_driver->getNewRespondDriver($id_order[0],"0000-00-00 00:00:00",$type);
			//print_r($list_driver);
			$new_list_d=array();
			foreach ($list_driver as $key => $value) {
				$new_list_d_el=array();
				if($value["iDriverId"]>0){
						$new_list_d_el["id"]=$value["iDriverId"];
				}
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
				$new_list_d_el["rate_in_city"]=$value["iRateInCity"];
				$new_list_d_el["rate_intercity"]=$value["iRate"];
				$new_list_d_el["loaders"]=$value["iLoaders"];
				$new_list_d_el["currency"]=$value["sCurrency"];
				$new_list_d[]=$new_list_d_el;
			}
			echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("drivers"=>$new_list_d)));
			//print_r($list_driver);
		}
		public function closeOrder($id_order){
			parent::checkToken("main");
			$this->model_driver=new Driver();
			$this->model_customer=new Customer();
			$id_device=parent::getIdDevice();
			$id_customer=$this->model_customer->findCustomer($id_device);
			$info_order=$this->model_customer->getOrder(array("id"=>$id_order[0]),"site");
			if($info_order[0]["iCustomerID"]!=$id_customer[0]["iCustomerId"]){
					header('HTTP/1.1 403');
					echo parent::json_encode_cyr(array("code"=> 32,"message"=> "У вас нет прав на этот заказ", "data"=> (object)array()));
					exit();
				}
			//print_r($info_order);
			if($this->model_customer->update_order($id_order[0],array("status_id"=>1))){
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>(object)array()));
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 40,"message"=> "Не закрыть заказ", "data"=>(object)array()));
			}
			//$idd=$this->model_driver->rateOrder($id_order[0],$_POST["rating"]);
		}
		public function changeDriver($id_order){
			parent::checkToken("main");
			$this->model_driver=new Driver();
			$this->model_customer=new Customer();
			$id_device=parent::getIdDevice();
			$id_customer=$this->model_customer->findCustomer($id_device);
			//print_r($id_customer);
			if($id_order[1]!=0){
				$id_driver[0]["iDriverId"]=$id_order[1];
				$id_driver=$this->model_driver->getProfileDriver($id_driver);
				if(count($id_driver)==0){
					echo parent::json_encode_cyr(array("code"=> 15,"message"=> "Не возможно выбрать водителя", "data"=>(object)array()));
					exit();
				}
			}
			$info_order=$this->model_customer->getOrder(array("id"=>$id_order[0]),"site");
			// if(isset($_GET["test"])){
			// 	print_r($id_order[0]);
			// }
			if($info_order[0]["iCustomerID"]!=$id_customer[0]["iCustomerId"]){
					header('HTTP/1.1 403');
					echo parent::json_encode_cyr(array("code"=> 32,"message"=> "У вас нет прав на этот заказ", "data"=> (object)array()));
					exit();
				}
			if($info_order[0]["iDriverId"]>0){
				echo parent::json_encode_cyr(array("code"=> 33,"message"=> "Исполнитель уже выбран на этот заказ", "data"=> (object)array()));
				exit();
			}
			if($id_order[1]!=0){
				$check=$this->model_driver->checkRespond($id_order[0],$id_order[1]);
				//print_r($check);
				if($check[0]["count"]==0){
					echo parent::json_encode_cyr(array("code"=> 31,"message"=> "Водитель не оставлял отклик на этот заказ", "data"=>(object)array()));
					exit();
				}
			}
			$idd=$this->model_driver->changeDriver($id_order[0],$id_order[1]);
			if($idd==1){
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>(object)array()));
				if($id_order[1]==0){
					$this->model_customer->update_order($id_order[0],array("status_id"=>1));
				}
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 15,"message"=> "Не возможно выбрать водителя", "data"=>(object)array()));
			}
			//print_r($id_order);
		}
		public function refuseDriver($id_order){
			parent::checkToken("main");
			$this->model_driver=new Driver();
			$this->model_customer=new Customer();
			$id_device=parent::getIdDevice();
			$id_customer=$this->model_customer->findCustomer($id_device);
			$info_order=$this->model_customer->getOrder(array("id"=>$id_order[0]),"site");
			if($info_order[0]["iCustomerID"]!=$id_customer[0]["iCustomerId"]){
					header('HTTP/1.1 403');
					echo parent::json_encode_cyr(array("code"=> 32,"message"=> "У вас нет прав на этот заказ", "data"=> (object)array()));
					exit();
				}
			$idd=$this->model_driver->refuseDriver($id_order[0]);
			if($idd==1){
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>(object)array()));
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 16,"message"=> "Не возможно удалить водителя", "data"=>(object)array()));
			}
		}
		public function rateOrder($id_order){
			parent::checkToken("main");
			$this->model_driver=new Driver();
			$this->model_customer=new Customer();
			$id_device=parent::getIdDevice();
			$id_customer=$this->model_customer->findCustomer($id_device);
			$info_order=$this->model_customer->getOrder(array("id"=>$id_order[0]),"site");
			$rating=json_decode($_POST["rating"],true);
			if($info_order[0]["iCustomerID"]!=$id_customer[0]["iCustomerId"]){
					header('HTTP/1.1 403');
					echo parent::json_encode_cyr(array("code"=> 32,"message"=> "У вас нет прав на этот заказ", "data"=> (object)array()));
					exit();
				}
			if($info_order[0]["iRatingDriver"]!=0){
				echo parent::json_encode_cyr(array("code"=> 17,"message"=> "Не возможно оценить заказ", "data"=>(object)array()));
				exit();
			}
			$idd=$this->model_driver->rateOrder($id_order[0],$rating["rating"]);
			$name_col="";
			switch ($rating["rating"]) {
				case '1':
					$name_col="iRatingOne";
					break;
				case '2':
					$name_col="iRatingTwo";
					break;
				case '3':
					$name_col="iRatingThree";
					break;
				case '4':
					$name_col="iRatingFour";
					break;
				case '5':
					$name_col="iRatingFive";
					break;
				default:
					echo parent::json_encode_cyr(array("code"=> 16,"message"=> "Не корректная оценка рейтинга", "data"=>(object)array()));
					exit();
					break;
			}
			parent::checkRating($rating["rating"],"driver",$info_order[0]["iDriverId"]);
			if($idd==1){
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>(object)array()));
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 17,"message"=> "Не возможно оценить заказ", "data"=>(object)array()));
			}
		}
		public function Update_profile_customer(){
			parent::checkSession();
			$this->model_customer=new Customer();
			$device[0]["iDeviceId"]=$_SESSION["id_user"];
			$id_customer=$this->model_customer->findCustomer($device);
			$id=$this->model_customer->update_profile_customer($_POST,$id_customer[0]["iCustomerId"]);
			if($id==1){
				echo parent::json_encode_cyr(array("result"=> true));
			}
			else{
				echo parent::json_encode_cyr(array("result"=> false));
			}
		}
		public function add_avatar_customer(){
			parent::checkSession();
			$this->model=new Customer();
			$device[0]["iDeviceId"]=$_SESSION["id_user"];
			$id_customer=$this->model->findCustomer($device);
			$allowed = array('gif', 'jpg','jpeg','png');
			if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){
		    	$extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);
			    if(!in_array(strtolower($extension), $allowed)){
			        echo '{"status":"error"}';
			        exit;
			    }
			    //print_r($_POST);
			    $dir=$_SERVER["DOCUMENT_ROOT"]."/images/customer/".$id_customer[0]["iCustomerId"]."/";
		 		$name_file=md5(iconv( 'utf-8', 'windows-1251', $_FILES['upl']['name'] )).".".$extension;
		 		$base_name=md5(microtime());
		 		$new_name_file="l_".$base_name.".".$extension;
		 		$dir_new="/images/customer/".$id_customer[0]["iCustomerId"]."/".iconv('windows-1251', 'utf-8', $new_name_file);
				if(!is_dir($dir)) mkdir($dir);
				if(parent::img_resize($_FILES['upl']['tmp_name'], $_SERVER["DOCUMENT_ROOT"].$dir_new, 300, 0)!=1){
	    			echo parent::json_encode_cyr(array("result"=> false));
	    			exit();
	    		}
					$link_to_image="/images/customer/".$id_customer[0]["iCustomerId"]."/s_".$base_name.".".$extension;
					//$link_to_small_image="/images/customer/".$id_customer[0]["iCustomerId"]."/s_".$md5_link.".png";
			        if(parent::img_resize($_SERVER["DOCUMENT_ROOT"].$dir_new, $_SERVER["DOCUMENT_ROOT"].$link_to_image, 100, 0)!=1){
		    			echo parent::json_encode_cyr(array("result"=> false));
		    			exit();
		    		}
		    		$avatar_hash=md5($dir_new);
		    		$id_files=$this->model->update_profile_customer(array("avatar"=>$base_name.".".$extension,"avatar_hash"=>$avatar_hash),$id_customer[0]["iCustomerId"]);
		        	echo parent::json_encode_cyr(array("result"=>"true","name_file"=>"/get_image/?image=".$avatar_hash));
			}
		}
		public function get_more_orders(){
			$this->model_customer=new Customer();
				$this->model_geo=new Geo();
				$device[0]["iDeviceId"]=$_SESSION["id_user"];
				//print_r($id_device);
				$id_customer=$this->model_customer->findCustomer($id_device);
				$_GET["customer_id"]=$id_customer[0]["iCustomerId"];
				if(isset($_POST["json_str"]) && !empty($_POST["json_str"])){
					$_POST=json_decode($_POST["json_str"],true);
				}
				if(!empty($_POST["start_date"])){
					$_POST["start_date"]=date('Y-m-d H:i:s', strtotime($_POST["start_date"]));
				}
				if(!empty($_POST["finish_date"])){
					$_POST["finish_date"]=date('Y-m-d H:i:s', strtotime($_POST["finish_date"]));
				}
				//print_r($_POST);
				$info_order=$this->model_customer->getOrder($_POST,"app");
				$info_order_new=array();
				foreach ($info_order as $key => $value) {
				$new_array=array();
					$new_array["id"]=$value["iOrderid"];
					$new_array["views"]=0;
					$new_array["drivers_count_total"]=0;
					$new_array["drivers_count_new"]=0;
 					$new_array["cargo_name"]=$value["sCargoName"];
 					if($value["sWeightUnit"]=="ton"){
						$new_array["cargo_weight"]=$value["iCargWeight"]/1000;
					}
					else{
						$new_array["cargo_weight"]=$value["iCargWeight"];
					}
 					//$new_array["cargo_weight"]=$value["iCargWeight"];
 					$new_array["weight_unit"]=$value["sWeightUnit"];
 					$new_array["price"]=$value["iPrice"];
 					$new_array["currency"]=$value["sCurrencyTypePrice"];
 					$new_array["payment_type"]=$value["sPaymentMethod"];
 					$new_array["comment"]=$value["tComment"];
 					$new_array["rating"]=$value["iRating"];
 					$new_array["rating"]=$value["iStatus"];
 					$dt = new DateTime($value["dStartDate"]);
 					$new_array["start_date"]=$dt->format(DATE_ISO8601);
 					$dt = new DateTime($value["dFinishDate"]);
 					$new_array["finish_date"]=$dt->format(DATE_ISO8601);;
 					$new_array["rating"]=$value["iRating"];
 					$new_array["capacity"]=$value["iCapacityId"];
 					$new_array["can_call"]=$value["iCanCall"];
 					$new_array["can_write"]=$value["iCanWrite"];
 					$start_city_info=$this->getCity($value["iStartCityId"]);
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
 					$finish_city_info=$this->getCity($value["iFinishCityId"]);
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
					$info_order_new[]=$new_array;
				}
				echo parent::json_encode_cyr(array("result"=> true,"list_order"=>$info_order_new));
		}
		private function checkHash($hash){
			$this->model_auth=new Auth_user();
			if(!$this->model_auth->checkImageHash($hash)){
				$hash=substr(md5(microtime() . rand(0, 9999)), 0, 25);
				$this->checkHash($hash);
			}
			return $hash;
		}
		public function getCity($id_city){
			///echo $id_city;
			    $lang = 0; // russian
			    $headerOptions = array(
			        'http' => array(
			            'method' => "GET",
			            'header' => "Accept-language: en\r\n" . // Вероятно этот параметр ни на что не влияет
			            "Cookie: remixlang=$lang\r\n"
			        )
			    );
			    //echo $name_region;
			    $methodUrl = 'https://api.vk.com/method/database.getCitiesById?v=5.5&access_token=52d55de852d55de852d55de8b552b78099552d552d55de8082f4e9cd61e4ca74f1b598e&city_ids='.$id_city;
			    $streamContext = stream_context_create($headerOptions);
			    $json = file_get_contents($methodUrl, false, $streamContext);
			    $arr = json_decode($json, true);
			    $methodUrl = 'https://api.vk.com/method/database.getCities?v=5.5&access_token=52d55de852d55de852d55de8b552b78099552d552d55de8082f4e9cd61e4ca74f1b598e&q='.urlencode($arr['response'][0]["title"]).'&country_id=1';
			    $streamContext = stream_context_create($headerOptions);
			    $json = file_get_contents($methodUrl, false, $streamContext);
			    $arr1 = json_decode($json, true);
			    $city_info=array();
			    if(isset($_GET["debug"])){
			    	header('Content-Type: text/html; charset=utf-8', true);
						print_r($arr1);
					}
					//print_r($arr1);
			    foreach($arr1['response']['items'] as $items){
			    	//print_r($items);
			    	if($items["id"]==$id_city){
			    		$city_info=$items;
			    	}
			    }
			    return $city_info;
		}

	}