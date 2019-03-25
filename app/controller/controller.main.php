<?php
	class Main extends base{
		public $mess;
		public $profile_user;
		public function __construct(){
			parent::__construct();
			$this->profile_user["current_page"]=$this->current_page[1];
			//print_r($_SESSION);
			if(isset($_SESSION["type_user"])){
				$this->profile_user["status"]=$_SESSION["type_user"];
			}
		}
		public function get_parsing_car() {
			$this->model_grab=new Grab();
			$list_car=$this->model_grab->getCar();
			foreach($list_car as &$car){
				$car["phone"]=$this->model_grab->getPhone($car["iCarId"]);
			}
			echo "<pre>";
				print_r($list_car);
			echo "</pre>";
		}
		public function get_parsing_cargo() {
			$this->model_grab=new Grab();
			$list_car=$this->model_grab->getCargo();
			echo "<pre>";
				print_r($list_car);
			echo "</pre>";
		}
		public function edit_parsing_car() {
			include  $_SERVER['DOCUMENT_ROOT']."/app/model/Lib/simple_html_dom.php";
			$this->model_grab=new Grab();
			$list_grab=$this->model_grab->getParsingCar();
			$new_list_grab=array();
			foreach ($list_grab as $key => $value) {
				$element_grab=array();
				//print_r($value["sName"]);
				$elemant_gray["name"]=$value["sName"];
				$city=explode(" ",$value["sParsingCity"]);
				$element_grab["city"]=$city[count($city)-1];
				$element_grab["name_company"]=$value["sName"];
				$data=str_get_html($value["sParsingContact"]);
				$phone=array();
				$str="/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/";
				foreach($data->find('table') as $table){
					foreach($table->find('a') as $a){
						$name=$a->parent();
						$new_name=explode(",",$name->plaintext);
						if(!strpos($new_name[count($new_name)-1],"факс")){
							if(preg_match($str,$a->innertext)){
								$new_phone1="";
								$new_phone=str_replace(array(')','('," ","-"),"",$a->innertext);
								if($new_phone[0]=="+" && $new_phone[1]=="7" & $new_phone[2]==9){
									$code_country="+7";
									$new_phone1=substr($new_phone, 2);
								}
								else if($new_phone[0]=="8" & $new_phone[2]==9){
									$code_country="+7";
									$new_phone1=substr($new_phone, 1);
								}
								else if($new_phone[0]=="9"){
									$code_country="+7";
									$new_phone1=$new_phone;
								}
								if($new_phone1!=""){
									$phone[]=array("phone"=>$new_phone,"name"=>str_replace(" Написать", "",$new_name[count($new_name)-1]));
								}
							}
						}
					}
					$element_grab["phone"]=$phone;
				}
				$new_list_grab[]=$element_grab;
			}
			foreach ($new_list_grab as $key => $value) {
				if(count($value["phone"])>0){
					$new_id=$this->model_grab->addCar($value["name_company"],str_replace(")", "",$value["city"]));
					if($new_id>0){
						foreach ($value["phone"] as $key => $phone) {
							$this->model_grab->addPhone(str_replace("</img>", "",$phone["name"]),$phone["phone"],$new_id);
						}
					}
				}
				echo $new_id;
			}
			echo "<pre>";
				print_r($new_list_grab);
			echo "</pre>";
		}
		public function grab() {
			print_r($_GET);
				$this->model_grab=new Grab();
				if(!empty($_GET["phone"])){
					$result=$this->model_grab->addGrab($_GET);
				}
				if($result==1){
					echo true;
				}

// setInterval(function() { 
//   min=4000;
//   max=7000;
//   number=Math.floor(Math.random() * (max - min + 1)) + min;
//   setInterval(function() { 
//     var test=parseInt($("input.ati-form-control").val())+1;
//     $('a:contains("Открыть полную информацию")').eq(0).click();
//   }, number);
//   $(".grid-row").each(function(){
//   //alert($(this).find(".load-cargo-type").text());

//    var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
//       var xhr = new XHR();
//     //(2) запрос на другой домен :)
//       xhr.open('GET','http://gruzovoz.alexkam.ru/add_grab/?name='+$(this).find(".load-cargo-type").text()+"&city="+$(this).find(".loading-col .load-main-city").text()+"&region="+$(this).find(".loading-col .load-region").text()+"&city_end="+$(this).find(".unloading-col .load-main-city").text()+"&region_end="+$(this).find(".unloading-col .load-region").text()+"&name="+$(this).find(".load-firm-name").text()+"&phone="+$(this).find("img").siblings(".telephone-number").text(), true);
//       xhr.onload = function() {
//         //alert( this.responseText );
//       }
//       xhr.onerror = function() {
//         alert( 'Ошибка ' + this.status );
//       }
//       xhr.send();
//      });
//     var test=parseInt($("input.ati-form-control").val())+1;
//     $('a:contains("'+test+'")').click();
//   }, 20000);

//setInterval(function() { 
  //var test=parseInt($("input.ati-form-control").val())+1;
  //$('a:contains("Открыть полную информацию")').eq(0).click();
//}, 5000);

//var XHR = ("onload" in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest;
//var xhr = new XHR();
// (2) запрос на другой домен :)
//xhr.open('GET', 'http://gruzovoz.alexkam.ru/add_grab/', true);

//xhr.onload = function() {
  //alert( this.responseText );
//}

//xhr.onerror = function() {
  //alert( 'Ошибка ' + this.status );
//}

//xhr.send();



//setInterval(function() { 
  //var test=parseInt($("input.ati-form-control").val())+1;
  //$('a:contains("'+test+'")').click();
//}, 5000);
		}
		public function file_force_download() {
	//echo $_GET["image"];
			if(!isset($_GET["image"]) || empty($_GET["image"])){
				echo parent::json_encode_cyr(array("code"=> 25,"message"=> "Проблемы с получением изображения", "data"=> (object)array()));
				exit();
			}
			$this->model_customer=new Customer();
			$this->model_driver=new Driver();
			$image=array();
			$avatar_customer=$this->model_customer->getHashImage($_GET["image"]);
			if(count($avatar_customer)>0){
				//print_r($avatar_customer);
				$image["file"]=$avatar_customer[0]["avatar"];
				$image["id"]=$avatar_customer[0]["id"];	
				$image["type"]="customer";
			}
			$avatar_driver=$this->model_driver->getHashImage($_GET["image"]);
			if(count($avatar_driver)>0){
				$image["file"]=$avatar_driver[0]["avatar"];
				$image["id"]=$avatar_driver[0]["id"];
				$image["type"]="driver";
			}
			//print_r(count($image));
			if(count($image)!=3){
				echo parent::json_encode_cyr(array("code"=> 25,"message"=> "Проблемы с получением изображения", "data"=> (object)array()));
				exit();
			}
			//print_r($image);
			$file=$_SERVER["DOCUMENT_ROOT"]."/images/".$image["type"]."/".$image["id"]."/l_".$image["file"];
			
			//echo $file;
		  if (file_exists($file)) {
		  	//echo "yes";
		    // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
		    // если этого не сделать файл будет читаться в память полностью!
		    if (ob_get_level()) {
		      ob_end_clean();
		    }
		    // заставляем браузер показать окно сохранения файла
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename=avatar.jpg');
		    header('Content-Transfer-Encoding: binary');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file));
		    // читаем файл и отправляем его пользователю
		    if ($fd = fopen($file, 'rb')) {
		      while (!feof($fd)) {
		        print fread($fd, 1024);
		      }
		      fclose($fd);
		    }
		    exit;
		  }
		  else{
		  	//echo "no";
		  	echo parent::json_encode_cyr(array("code"=> 25,"message"=> "Проблемы с получением изображения", "data"=> (object)array()));
		  }
		}
		public function get_image_phone() {
			//echo $_GET["image"];
			if(!isset($_GET["image"]) || empty($_GET["image"])){
				echo parent::json_encode_cyr(array("code"=> 25,"message"=> "Проблемы с получением изображения", "data"=> (object)array()));
				exit();
			}
			$this->model_customer=new Customer();
			$this->model_driver=new Driver();
			$image=array();
			$avatar_customer=$this->model_customer->getHashImage($_GET["image"]);
			if(count($avatar_customer)>0){
				//print_r($avatar_customer);
				$image["file"]=$avatar_customer[0]["avatar"];
				$image["id"]=$avatar_customer[0]["id"];	
				$image["type"]="customer";
			}
			$avatar_driver=$this->model_driver->getHashImage($_GET["image"]);
			if(count($avatar_driver)>0){
				$image["file"]=$avatar_driver[0]["avatar"];
				$image["id"]=$avatar_driver[0]["id"];
				$image["type"]="driver";
			}
			//print_r(count($image));
			if(count($image)!=3){
				echo parent::json_encode_cyr(array("code"=> 25,"message"=> "Проблемы с получением изображения", "data"=> (object)array()));
				exit();
			}
			//print_r($image);
			$file=$_SERVER["DOCUMENT_ROOT"]."/images/".$image["type"]."/".$image["id"]."/s_".$image["file"];
			
			//echo $file;
		  if (file_exists($file)) {
		  	//echo "yes";
		    // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
		    // если этого не сделать файл будет читаться в память полностью!
		    if (ob_get_level()) {
		      ob_end_clean();
		    }
		    // заставляем браузер показать окно сохранения файла
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename=avatar.jpg');
		    header('Content-Transfer-Encoding: binary');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file));
		    // читаем файл и отправляем его пользователю
		    if ($fd = fopen($file, 'rb')) {
		      while (!feof($fd)) {
		        print fread($fd, 1024);
		      }
		      fclose($fd);
		    }
		    exit;
		  }
		  else{
		  	echo parent::json_encode_cyr(array("code"=> 25,"message"=> "Проблемы с получением изображения", "data"=> (object)array()));
		  }
		}
		public function main(){
			$this->model_customer=new Customer();
			$this->model_driver=new Driver();
			$this->model_order=new Order();
			$this->model_geo=new Geo();
			if(isset($_SESSION["type_user"]) && $_SESSION["type_user"]=="customer"){
				$this->profile=array("status"=>$_SESSION["type_user"]);
				$list_driver=$this->model_driver->getDriver(array("limit"=>3),"site");
				$new_array=array();
				foreach ($list_driver as $key => $value) {
					$new_array[]=$this->model_driver->arraySortDriver($value);
				}
				echo $this->view->render('customer_main.html',array("list_driver"=>$new_array,"profile"=>$this->profile_user));
				echo "<pre>";
					print_r($new_array);
				echo "</pre>";
			}
			else if(isset($_SESSION["type_user"]) &&  $_SESSION["type_user"]=="driver"){
				$this->profile=array("status"=>$_SESSION["type_user"]);
				$list_order=$this->model_customer->getOrder(array("limit"=>6,"status_id"=>0),"site");
				$new_array_order=$this->arrayResort($list_order);
				echo $this->view->render('driver_main.html',array("list_order"=>$new_array_order,"profile"=>$this->profile_user));
				echo "<pre>";
					print_r($new_array_order);
				echo "<pre/>";
			}
			else{
				$this->profile=array("status"=>$_SESSION["type_user"]);
				$list_order=$this->model_customer->getOrder(array("limit"=>6,"status_id"=>0),"site");
				$new_array_order=$this->arrayResort($list_order);
				$list_driver=$this->model_driver->getDriver(array("limit"=>3),"site");
				//print_r($list_driver);
				$new_array=array();
				foreach ($list_driver as $key => $value) {
					$new_array[]=$this->model_driver->arraySortDriver($value);
				}
				//echo "<div style='color blue'>test</div>";
				echo $this->view->render('index.html',array("list_order"=>$new_array_order,"list_driver"=>$new_array,"profile"=>$this->profile_user));
				// echo "list_order";
				// echo "<pre>";
				// 	print_r($new_array_order);
				// echo "<pre/>";
				// echo "<pre>";
				// 	print_r($new_array);
				// echo "<pre/>";
				// //print_r($this->profile_user);
			}
		}
		public function driver(){
			$this->model_customer=new Customer();
			$this->profile=array("status"=>$_SESSION["type_user"]);
				$list_order=$this->model_customer->getOrder(array("limit"=>6,"status_id"=>0),"site");
				$new_array_order=$this->arrayResort($list_order);
			echo $this->view->render('driver_main.html',array("list_order"=>$new_array_order,"profile"=>$this->profile_user));
		}
		public function customer(){
			$this->model_driver=new Driver();
			$list_driver=$this->model_driver->getDriver(array("limit"=>3),"site");
				$new_array=array();
				foreach ($list_driver as $key => $value) {
					$new_array[]=$this->model_driver->arraySortDriver($value);
				}
			echo $this->view->render('customer_main.html',array("list_driver"=>$new_array,"profile"=>$this->profile_user));
			print_r($list_driver);
		}
		public function news(){
			echo $this->view->render('news.html',array("profile"=>$this->profile_user));
		}
		public function about(){
			echo $this->view->render('about.html',array("profile"=>$this->profile_user));
		}
		public function contact(){
			echo $this->view->render('contact.html',array("profile"=>$this->profile_user));
		}
		public function how(){
			echo $this->view->render('how.html',array("profile"=>$this->profile_user));
		}
		public function detail_order($id_order){
			parent::checkSession();
			$this->model1=new Customer();
			$this->model_driver=new Driver();
			$this->model_order=new Order();
			$this->model_geo=new Geo();
			//echo $id_order[0];
					$info_order=$this->model1->getOrder(array("id"=>$id_order[0]),"site");
					//print_r($info_order);
					$profile=$this->model1->getProfile($info_order[0]["iCustomerID"]);
					$device[0]["iDeviceId"]=$_SESSION["id_user"];
					$id_customer=$this->model1->findCustomer($device);
					///print_r($id_customer);
					if($_SESSION["type_user"]=="customer" && $id_customer[0]["iCustomerId"]!=$info_order[0]["iCustomerID"]){
						//echo "yes";
						echo $this->view->render('denied.html');
						exit();
					}
				//	print_r($profile);
					$new_array=array();
					$new_array["customer_name"]=$profile[0]["iCustomerName"];
					$new_array["customer_id"]=$profile[0]["iCustomerId"];
					$new_array["id"]=$info_order[0]["iOrderid"];
					$new_array["views"]=$info_order[0]["iViewsCount"];
					$new_array["drivers_count_total"]=0;
					$new_array["drivers_count_new"]=0;
 					$new_array["cargo_name"]=$info_order[0]["sCargoName"];
 					$new_array["id_driver"]=$info_order[0]["iDriverId"];
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
 					$new_array["rating"]=$info_order[0]["iRating"];
 					$new_array["status"]=$info_order[0]["iStatus"];
 					if($info_order[0]["dStartDate"][0]=="0"){
 						$new_array["start_date"]="";
 					}
 					else{
 						$new_array["start_date"]=$info_order[0]["dStartDate"];
 					}
 					if($info_order[0]["dFinishDate"][0]=="0"){
 						$new_array["finish_date"]="";
 					}
 					else{
 						$new_array["finish_date"]=$info_order[0]["dFinishDate"];
 					}
 					// $dt = new DateTime($info_order[0]["dStartDate"]);
 					// $new_array["start_date"]=$dt->format(DATE_ISO8601);
 					// $dt = new DateTime($info_order[0]["dFinishDate"]);
 					// $new_array["finish_date"]=$dt->format(DATE_ISO8601);;
 					$new_array["rating"]=0;
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
					else{
						$new_array["driver"]=array();
					}
 					$new_array["start_city"]=array("id"=>$start_city_info["id"],"name"=>$start_city_info["title"],"parent_name"=>$start_city_info["region"],"parent_id"=>$info_region[0]["iRegionVkid"],"region"=>$info_region_finish[0]["typeRegion"],"country_name"=>"Россия","country_id"=>1);
 					$finish_city_info=$this->model_order->getCity($info_order[0]["iFinishCityId"]);
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
					if($_SESSION["type_user"]=="driver"){
						$this->model1->updateCountViews($info_order[0]["iOrderid"]);
						$this->model_driver=new Driver();
						$device[0]["iDeviceId"]=$_SESSION["id_user"];
						$id_driver=$this->model_driver->findDriver($device);
						//echo $id_order[0].$id_driver[0]["iDriverId"];
						$check=$this->model_driver->checkRespond($id_order[0],$id_driver[0]["iDriverId"]);
						//print_r($check);
						if($check[0]["count"]>0){
							$new_array["checked"]="true";
						}
						else{
							$new_array["checked"]="false";	
						}
						if($info_order[0]["iDriverId"]>0 && $id_driver[0]["iDriverId"]==$info_order[0]["iDriverId"]){
							$new_array["you_driver"]="true";
						}
						else{
							$new_array["you_driver"]="false";
						}
					}
			echo $this->view->render('detail_order.html',array("detail_order"=>$new_array,"profile"=>$this->profile_user));
			echo "detail_order";
			echo "<pre>";
				print_r($new_array);
			echo "<pre/>";
			echo "profile";
			echo "<pre>";
				print_r($this->profile_user);
			echo "<pre/>";
		}
		public function new_order(){
			parent::checkSession();
			// echo "yes";
			// print_r($this->profile_user);
			$this->model_driver=new Driver();
			$list_body=$this->model_driver->getBodyType();
			$new_list_body=array();
			foreach ($list_body as $key => $value) {
				$new_list_body[]=array("id"=>$value["iBodyTypeId"],"name"=>$value["iBodyTypeName"]);
			}
			echo $this->view->render('new_order.html',array("profile"=>$this->profile_user,"body_types"=>$new_list_body));
			echo "body_types";
			echo "<pre>";
				print_r($new_list_body);
			echo "<pre/>";
		}
		public function profile(){
			parent::checkSession();
			$this->model_order=new order();
			$this->model_auth=new Auth_User();
			$this->model_geo=new Geo();
			$info_device=$this->model_auth->getInfoDevice($_SESSION["id_user"]);
			if($_SESSION["type_user"]=="customer"){
				$this->model=new Customer();
				//print_r($_SESSION);
				$device[0]["iDeviceId"]=$_SESSION["id_user"];
				$id_customer=$this->model->findCustomer($device);
				//print_r($device);
				$_GET["customer_id"]=$id_customer[0]["iCustomerId"];
				//выгружаем все заказы
				$info_order=$this->model->getOrder($_GET,"app");
				$info_order_new_all=$this->arrayResort($info_order);
				//выгружаем только актуальные
				$_GET["status_id"]=0;
				$info_order=$this->model->getOrder($_GET,"app");
				$info_order_new_actual=$this->arrayResort($info_order);
				//Выгружаем только закрытые заявки
				$_GET["status_id"]=1;
				$info_order=$this->model->getOrder($_GET,"app");
				$info_order_new_close=$this->arrayResort($info_order);
				//print_r($id_customer);
				$profile=$this->model->getProfile($id_customer[0]["iCustomerId"]);
				if($profile[0]["iCustomerCity"]!=0){
					$start_city_info=$this->model_order->getCity($profile[0]["iCustomerCity"]);
						$info_region=$this->model_geo->getRegion($start_city_info["region"]);
	 					$profile[0]["city"]=array(
	 						"id"=>$start_city_info["id"],
	 						"name"=>trim($start_city_info["title"]),
	 						"parent_name"=>trim($start_city_info["region"]),
	 						"parent_id"=>$info_region[0]["iRegionVkid"],
	 						"region"=>true,
	 						"country_name"=>"Россия",
	 						"country_id"=>1);
 					}
 								$link="";
				if(!empty($profile[0]["sHashImage"])){
					$link="/get_image/?image=".$profile[0]["sHashImage"];
				}
 				$profile[0]["iCustomerAvatar"]=$link;
 				$profile[0]["iCustomerPhone"]=$info_device[0]["sSmsNumber"];
 				$profile[0]["phoneConfinm"]=$info_device[0]["iStatus"];
 				$profile[0]["sEmail"]=$info_device[0]["sEmail"];
 				$profile[0]["emailConfirm"]=$info_device[0]["iStatusEmail"];
				echo $this->view->render('profile_customer.html',array("close_orders"=>$info_order_new_close,"actual_orders"=>$info_order_new_actual,"all_orders"=>$info_order_new_all,"all_profile"=>$profile,"profile"=>$this->profile_user));
				echo "close_orders";
				echo "<pre>"; 
					print_r($info_order_new_close);
				echo "</pre>";
				echo "actual_orders";
				echo "<pre>"; 
					print_r($info_order_new_actual);
				echo "</pre>";
				echo "all_orders";
				echo "<pre>"; 
					print_r($info_order_new_all);
				echo "</pre>";
				echo "all_profile";
				echo "<pre>"; 
					print_r($profile);
				echo "</pre>";
				echo "profile";
				echo "<pre>"; 
					print_r($this->profile_user);
				echo "</pre>";
			}
			else if($_SESSION["type_user"]=="driver"){
				$this->model=new Driver();
				$this->model_customer=new Customer();
				$this->subsc=new Subscription_driver();
				$device[0]["iDeviceId"]=$_SESSION["id_user"];
				$id_driver=$this->model->findDriver($device);
				
				$list_all_order=$this->model->getOrdersDriver($id_driver[0]["iDriverId"],"all",array());
				// echo "<pre>"; print_r($this->arrayResort($list_all_order));
				// echo "</pre>";
				$list_curren_order=$this->model->getOrdersDriver($id_driver[0]["iDriverId"],"current",array());
				// echo "<pre>";print_r($list_all_order);
				// echo "</pre>";
				$list_old_order=$this->model->getOrdersDriver($id_driver[0]["iDriverId"],"old",array());
				$profile=$this->model->getProfileDriver($id_driver);
				//print_r($profile);
				$new_profile=array();
				$new_profile["name"]=$profile[0]["sDriverName"];
				$new_profile["phone"]=$profile[0]["sDriverPhone"];
				$link="";
				if(!empty($profile[0]["sHashImage"])){
					$link="/get_image/?image=".$profile[0]["sHashImage"];
				}
				$new_profile["avatar"]=$link;
				$new_profile["phone_confirmed"]=$info_device[0]["iStatus"];;
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
				$new_profile["phone"]=$info_device[0]["sSmsNumber"];
 				$new_profile["email_confirmed"]=$info_device[0]["iStatusEmail"];
 				$new_profile["email"]=$info_device[0]["sEmail"];
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
 					$list_body=$this->model->getBodyType();
					$new_list_body=array();
					foreach ($list_body as $key => $value) {
						$new_list_body[]=array("id"=>$value["iBodyTypeId"],"name"=>$value["iBodyTypeName"]);
					}
					$list_subscribe=$this->subsc->getSubscriptionDriver($id_driver[0]["iDriverId"],"list");
					foreach ($list_subscribe as $key => $value) {
						$new_array_subsc[]=$this->subsc->arraySortSubsribe($value,"site");
					}
					$new_list_all=$this->arrayResort($list_all_order);
					foreach ($new_list_all as $key => &$value) {
						$customer_info=$this->model_customer->getProfile($list_all_order[$key]["iCustomerID"]);
						$value["customer"]=array(
							"id"=>$customer_info[0]["iCustomerId"],
							"name"=>$customer_info[0]["iCustomerName"],
							"organization"=>$customer_info[0]["iCustomerOrg"],
							"avatar"=>$customer_info[0]["sAvatar"],
							"rating"=>0
							);
					}
					$new_list_current=$this->arrayResort($list_current_order);
					foreach ($new_list_current as $key => &$value) {
						$customer_info=$this->model_customer->getProfile($list_current_order[$key]["iCustomerID"]);
						$value["customer"]=array(
							"id"=>$customer_info[0]["iCustomerId"],
							"name"=>$customer_info[0]["iCustomerName"],
							"organization"=>$customer_info[0]["iCustomerOrg"],
							"avatar"=>$customer_info[0]["sAvatar"],
							"rating"=>0
							);
					}
					$new_list_old=$this->arrayResort($list_old_order);
					foreach ($new_list_current as $key => &$value) {
						$customer_info=$this->model_customer->getProfile($list_old_order[$key]["iCustomerID"]);
						$value["customer"]=array(
							"id"=>$customer_info[0]["iCustomerId"],
							"name"=>$customer_info[0]["iCustomerName"],
							"organization"=>$customer_info[0]["iCustomerOrg"],
							"avatar"=>$customer_info[0]["sAvatar"],
							"rating"=>0
							);
					}
				echo $this->view->render('profile_driver.html',array("all_orders"=>$new_list_all,"current_orders"=>$new_list_current,"old_orders"=>$new_list_old, "subscribes"=>$new_array_subsc,"body_types"=>$new_list_body,"all_profile"=>$new_profile,"profile"=>$this->profile_user));
				echo "all_orders";
				echo "<pre>"; 
					print_r($new_list_all);
				echo "</pre>";
				echo "current_orders";
				echo "<pre>"; 
					print_r($new_list_current);
				echo "</pre>";
				echo "old_orders";
				echo "<pre>"; 
					print_r($new_list_old);
				echo "</pre>";
				echo "subscribes";
				echo "<pre>"; 
					print_r($new_array_subsc);
				echo "</pre>";
				echo "body_types";
				echo "<pre>"; 
					print_r($new_list_body);
				echo "</pre>";
				echo "all_profile";
				echo "<pre>"; 
					print_r($new_profile);
				echo "</pre>";
				echo "profile";
				echo "<pre>"; 
					print_r($this->profile_user);
				echo "</pre>";
	
    // "city": {
    //   "id": "string",
    //   "name": "string",
    //   "parent_name": "string",
    //   "parent_id": "string",
    //   "region": true,
    //   "country_name": "string",
    //   "country_id": 0
    // },
   
	
			}
		}
		public function arrayResort($info_order){
			//print_r($info_order);
			$this->model_order=new Order();
			$this->model_geo=new Geo();
				$info_order_new=array();
				if(count($info_order)>0){
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
 					//$new_array["weight_unit"]=$value["sWeightUnit"];
 					$new_array["price"]=$value["iPrice"];
 					$new_array["currency"]=$value["sCurrencyTypePrice"];
 					$new_array["payment_type"]=$value["sPaymentMethod"];
 					$new_array["comment"]=$value["tComment"];
 					$new_array["rating"]=$value["iRating"];
 					$new_array["status"]=$value["iStatus"];
 					if($value["dStartDate"][0]=="0"){
 						$new_array["start_date"]="";
 					}
 					else{
 						$new_array["start_date"]=date("d-m-Y",strtotime($value["dStartDate"]));
 					}
 					if($value["dFinishDate"][0]=="0"){
 						$new_array["finish_date"]="";
 					}
 					else{
 						$new_array["finish_date"]=date("d-m-Y",strtotime($value["dFinishDate"]));
 					}
 					$new_array["rating"]=$value["iRating"];
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
				return $info_order_new;
				}
				else{
					return array();
				}
		}
	}