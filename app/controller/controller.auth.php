<?php
	Class Auth extends base{
		public function __construct(){
			$this->model=new Auth_User();
			parent::__construct();
		}
		public function main(){
			echo $this->view->render('teacher.html',array('profile'=>$this->profile[0],"user_list"=>$this->user_mas));
		}
		public function get_guest_token(){
			//print_r($_POST);
			$param=parent::getDate();
			$hash=md5($param["device_id"].$param["device_id"]);
			$device=$this->model->check_device_id($param["device_id"]);
			if(count($device)==1){
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("token"=> $hash)));
				exit();
			}
			//print_r($param);
			//$new_massiv=json_decode($_POST["user_type"],true);
			//print_r($new_massiv);
			$id=$this->model->get_info_token($param["device_id"],$hash);
			if($id!=1){
				echo parent::json_encode_cyr(array("code"=> 3,"message"=> "Проблемы с получением токена", "data"=> (object)array()));
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("token"=> $hash)));
			}
		}
		public function registration(){
			$hash=md5(date("Y-m-d H:i:s").date("Y-m-d H:i:s"));
			//$code=$this->generatePassword(5);
			$code=11111;
			//print_r($_POST);
			if((!isset($_POST["phone"]) || empty($_POST["phone"])) || (!isset($_POST["user_type"]) || empty($_POST["user_type"]))){
				echo parent::json_encode_cyr(array("result"=> false));
				exit();
			}
			$new_phone=str_replace(array(')','('," ","-"),"",$_POST["phone"]);
			if($new_phone[0]=="+" && $new_phone[1]=="7"){
				$code_country="7";
				$new_phone=substr($new_phone, 2);
			}
			else if($new_phone[0]=="8"){
				$code_country="7";
				$new_phone=substr($new_phone, 1);
			}
			else if($new_phone[0]=="9"){
				$code_country="7";
			}
			// echo $code_country;
			// echo $new_phone;
			if(!$this->model->checkPhone($code_country.$new_phone)){
				echo parent::json_encode_cyr(array("result"=> false,"error"=>1));
				exit();
			}
			$new_id=$this->model->addRegistrationSite($hash,$code_country.$new_phone,$_POST["user_type"],$code);	
			if($new_id>0){
				//$result_sms=file_get_contents("http://smsc.ru/sys/send.php?login=akininroman&psw=digital105&phones=".$_POST["phone"]."&mes=Ваш код ".$code." &charset=utf-8");
				//echo $result_sms;]
				//$result_sms="OK";
				$result_sms="OK";
				if(substr($result_sms, 0, 2)=="OK"){
					echo parent::json_encode_cyr(array("result"=> true));
					exit();	
				}
				else{
					echo parent::json_encode_cyr(array("result"=> false));
					exit();	
				}
			}
			else{
				echo parent::json_encode_cyr(array("result"=> false));
				exit();	
			}
		}
		public function send_code(){
			//$headers["X-Auth-Token"]="fa1d3eb08a879de9a4cd9995a1aa91e1";
			//print_r($headers);
			$_POST=parent::getDate();
			parent::checkToken("guest");
			//parent::getIdDevice();
			$count=$this->model->check_device(parent::getIdDevice());
			//print_r($count);
			if($count[0]["count"]==0){
				echo parent::json_encode_cyr(array("code"=> 23,"message"=> "Нет зарегистрированных пользователей на этом устройстве", "data"=> (object)array()));
				exit();
			}
			$now=date("Y-m-d H:i:s");
			$info_token=$this->model->get_token_guest($this->header_token);
			if($info_token[0]["dDateSend"]>$now){
				echo parent::json_encode_cyr(array("code"=> 4,"message"=> "С момента последнего вопроса не прошло 2 минуты", "data"=> (object)array()));
				exit();
			}
			//print_r($info_token);
			//$code=$this->generatePassword(5);
			$code=11111;
			//$new_massiv=json_decode($_POST["phone"],true);
			$phone=$_POST["country_code"].$_POST["phone"];
			//print_r($this->header_token);
			$idd=$this->model->update_code_and_phone($this->header_token,$phone,$code);
			//$result_sms=file_get_contents("http://smsc.ru/sys/send.php?login=akininroman&psw=digital105&phones=".$phone."&mes=Ваш код ".$code." &charset=utf-8");
			//echo $this->header_token;
			$result_sms="OK";
			if(substr($result_sms, 0, 2)=="OK"){
				if($idd>1){
					echo parent::json_encode_cyr(array("code"=> 1,"message"=> "Проблемы с токеном", "data"=> (object)array()));
				}
				else if($idd==1){
					echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> (object)array()));
				}
				else{
					echo parent::json_encode_cyr(array("code"=> 2,"message"=> "Ошибка с добавлением кода", "data"=> (object)array()));	
				}
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 3,"message"=> "Проблемы с отправкой смс кода", "data"=> (object)array()));	
			}
		}
		public function check_code(){
			$this->model_driver=new Driver();
			$this->model_customer=new Customer();
			$_POST=parent::getDate();
			parent::checkToken("guest");
			//$new_massiv=json_decode($_POST["code"],true);
			// echo $_POST["code"];
			// echo $this->header_token;
			$idd=$this->model->check_code($_POST["code"],$this->header_token);
			if($idd==1){
				$new_token=md5(date("h").$this->header_token);
				$id_device=parent::getIdDevice();
				$this->model->getTypeUser($id_device[0]["iDeviceId"]);
				$this->model->update_main_token(md5(date("h").$this->header_token),$this->header_token,$_POST["code"]);
				$this->model->update_status($this->header_token);
				$type="";
				$id_customer_new=$this->model_customer->findCustomer($id_device);
				$id_driver=$this->model_driver->findDriver($id_device);
				if(count($id_customer_new)>0){
					$type="customer";
				}
				else if(count($id_driver)>0){
					$type="driver";
				}
				$this->model->update_type_user($id_device[0]["iDeviceId"],$type);
				echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=> array("token"=>$new_token,"user_type"=>$type)));
			}
			else{
				echo parent::json_encode_cyr(array("code"=> 4,"message"=> "Код не совпадает", "data"=> (object)array()));	
			}
		}
		public function check_code_site(){
			if((!isset($_POST["phone"]) || empty($_POST["phone"])) || (!isset($_POST["code"]) || empty($_POST["code"]))){
				echo parent::json_encode_cyr(array("result"=> false));
				exit();
			}
			$new_phone=str_replace(array(')','('," ","-"),"",$_POST["phone"]);
			if($new_phone[0]=="+" && $new_phone[1]=="7"){
				$code_country="7";
				$new_phone=substr($new_phone, 2);
			}
			else if($new_phone[0]=="8"){
				$code_country="7";
				$new_phone=substr($new_phone, 1);
			}
			else if($new_phone[0]=="9"){
				$code_country="7";
			}
			$idd=$this->model->check_code_site($code_country.$new_phone,$_POST["code"]);
			if($idd==1){
				$this->model->update_status_site($_POST["phone"]);
				if(parent::AutorizeUser("phone",$_POST["phone"])=="false"){
					echo parent::json_encode_cyr(array("result"=> false));
					exit();
				}
				$device[0]["iDeviceId"]=$_SESSION["id_user"];
				//print_r($_SESSION);
				if($_SESSION["type_user"]=="customer"){
					//echo "yes";
					$this->model_customer=new Customer();
					$_POST=array("name"=>"", "organization","phone"=>"","country_id"=>1,"city_id"=>"","iDeviceId"=>$device);
					$id=$this->model_customer->AddRegistrationCustomer($_POST,$device);
					if($id==0){
						echo parent::json_encode_cyr(array("result"=> false));
						exit();
					}
				}
				else if($_SESSION["type_user"]=="driver"){
					$this->model_driver=new Driver();
					$_POST=array("name"=>"", "organization","phone"=>"","country_id"=>1,"city_id"=>"","car_name"=>"","car_type"=>"","body_type_id"=>"","capacity"=>"","volume"=>"","driver_specialization"=>"","iRegionVkid"=>"");
					$id=$this->model_driver->AddRegistrationDriver($_POST,$device);
					if($id==0){
						echo parent::json_encode_cyr(array("result"=> false));
						exit();
					}
				}
				//exit();
				//print_r($_SESSION);
				echo parent::json_encode_cyr(array("result"=> true,"link"=>$_SESSION["type_user"]));
			}
			else{
				echo parent::json_encode_cyr(array("result"=> false));	
			}
		}
		public function check_code_enter(){

			if((!isset($_POST["phone"]) || empty($_POST["phone"])) || (!isset($_POST["code"]) || empty($_POST["code"]))){
				echo parent::json_encode_cyr(array("result"=> false));
				exit();
			}
			$new_phone=str_replace(array(')','('," ","-"),"",$_POST["phone"]);
			if($new_phone[0]=="+" && $new_phone[1]=="7"){
				$code_country="7";
				$new_phone=substr($new_phone, 2);
			}
			else if($new_phone[0]=="8"){
				$code_country="7";
				$new_phone=substr($new_phone, 1);
			}
			else if($new_phone[0]=="9"){
				$code_country="7";
			}
			//echo $code_country.$new_phone;
			$idd=$this->model->check_code_site($code_country.$new_phone,$_POST["code"]);
			if($idd==1){

				$this->model->update_status_site_phone($code_country.$new_phone);
				if(parent::AutorizeUser("phone",$code_country.$new_phone)=="false"){
					echo parent::json_encode_cyr(array("result"=> false));
					exit();
				}
			echo parent::json_encode_cyr(array("result"=> true,"link"=>$_SESSION["type_user"]));
			}
			else{
				echo parent::json_encode_cyr(array("result"=> false));	
			}
		}
		public function check_email(){
			if((!isset($_POST["email"]) || empty($_POST["email"])) || (!isset($_POST["code"]) || empty($_POST["code"]))){
				echo parent::json_encode_cyr(array("result"=> false));
				exit();
			}
			$idd=$this->model->check_code_email($_POST["phone"],$_POST["code"]);
			if($idd==1){
				echo parent::json_encode_cyr(array("result"=> true));
			}
			else{
				echo parent::json_encode_cyr(array("result"=> false));	
			}
		}
		public function authorization_site(){
			if(!isset($_POST["phone"]) || empty($_POST["phone"])){
				echo parent::json_encode_cyr(array("result"=> false));
				exit();
			}
			$new_phone=str_replace(array(')','('," ","-"),"",$_POST["phone"]);
			if($new_phone[0]=="+" && $new_phone[1]=="7"){
				$code_country="7";
				$new_phone=substr($new_phone, 2);
			}
			else if($new_phone[0]=="8"){
				$code_country="7";
				$new_phone=substr($new_phone, 1);
			}
			else if($new_phone[0]=="9"){
				$code_country="7";
			}
			// echo $code_country;
			// echo $new_phone;
			//echo $this->model->checkPhone($code_country.$new_phone);
			//echo $code_country.$new_phone;
			if($this->model->checkPhone($code_country.$new_phone)){
				echo parent::json_encode_cyr(array("result"=> false,"error"=>1));
				exit();
			}
			// if(parent::AutorizeUser("phone",$_POST["phone"])=="false"){
			// 		echo parent::json_encode_cyr(array("result"=> false));
			// 		exit();
			// 	}
				//print_r($_SESSION);
				echo parent::json_encode_cyr(array("result"=> true));

		}
		public function generatePassword($length = 5){
			$chars = '0123456789';
			$numChars = strlen($chars);
			$string = '';
			for ($i = 0; $i < $length; $i++) {
			$string .= substr($chars, rand(1, $numChars) - 1, 1);
			}
			return $string;
		}
		public function send_code_email(){
			parent::checkSession();
			if(!isset($_POST["item"]) || empty($_POST["item"])){
				echo parent::json_encode_cyr(array("result"=> false));
				exit();
			}
			$info_device=$this->model->getInfoDevice($_SESSION["id_user"]);
			$code=$this->generatePassword(5);
			if($this->model->update_email($_POST["item"],$_SESSION["id_user"],$code)==1){
				if(parent::sendMessage($_POST["item"],array("code"=>$code,"id_user"=>$_SESSION["id_user"]), "send_reg")){
					echo parent::json_encode_cyr(array("result"=> true));
				}
				else{
					echo parent::json_encode_cyr(array("result"=> false));
				}
			}
			else{
				echo parent::json_encode_cyr(array("result"=> false));
			}
		}
		public function send_code_phone(){
			parent::checkSession();
			$info_device=$this->model->getInfoDevice($_SESSION["id_user"]);
			$code=$this->generatePassword(5);
			$new_phone=str_replace(array(')','('," ","-"),"",$_POST["item"]);
			if($new_phone[0]=="+" && $new_phone[1]=="7"){
				$code_country="7";
				$new_phone=substr($new_phone, 2);
			}
			else if($new_phone[0]=="8"){
				$code_country="7";
				$new_phone=substr($new_phone, 1);
			}
			else if($new_phone[0]=="9"){
				$code_country="7";
			}
			// echo $code_country.$new_phone;
			if($info_device[0]["iStatus"]==1 && $info_device[0]["sSmsNumber"]==$new_phone){
				echo parent::json_encode_cyr(array("result"=> false,"error"=>2));
				exit();
			}
			if($info_device[0]["sSmsNumber"]==$new_phone){
				$idd=$this->model->updateCodePhone($_SESSION["id_user"],$code_country.$new_phone,$code);
				if($idd==1){
					echo parent::json_encode_cyr(array("result"=> true));
				}
				else{
					echo parent::json_encode_cyr(array("result"=> false));
				}
			}
			else{
				if(!$this->model->checkPhone($code_country.$new_phone)){
					echo parent::json_encode_cyr(array("result"=> false,"error"=>1));
					exit();
				}
				$idd=$this->model->updateCodePhone($_SESSION["id_user"],$code_country.$new_phone,$code);
				if($idd==1){
					echo parent::json_encode_cyr(array("result"=> true));
				}
				else{
					echo parent::json_encode_cyr(array("result"=> false));
				}
			}
		}
		public function check_code_email_profile(){
			parent::checkSession();
			if((!isset($_POST["item"]) || empty($_POST["item"])) || (!isset($_POST["code"]) || empty($_POST["code"]))){
				echo parent::json_encode_cyr(array("result"=> false));
				exit();
			}
			$idd=$this->model->check_code_site_email($_POST["item"],$_POST["code"]);
			echo $idd;
			if($idd==1){
				if($id_status_email=$this->model->update_email_status_site($_POST["item"])==1){
					echo parent::json_encode_cyr(array("result"=> true));
				}
				else{
					echo parent::json_encode_cyr(array("result"=> false));
				}
			}
			else{
				echo parent::json_encode_cyr(array("result"=> false));	
			}
		}
		public function check_code_phone_profile(){
			parent::checkSession();
			if((!isset($_POST["item"]) || empty($_POST["item"])) || (!isset($_POST["code"]) || empty($_POST["code"]))){
				echo parent::json_encode_cyr(array("result"=> false));
				exit();
			}
			$new_phone=str_replace(array(')','('," ","-"),"",$_POST["item"]);
			if($new_phone[0]=="+" && $new_phone[1]=="7"){
				$code_country="7";
				$new_phone=substr($new_phone, 2);
			}
			else if($new_phone[0]=="8"){
				$code_country="7";
				$new_phone=substr($new_phone, 1);
			}
			else if($new_phone[0]=="9"){
				$code_country="7";
			}
			$idd=$this->model->check_code_site($code_country.$new_phone,$_POST["code"]);
			if($idd==1){
			echo parent::json_encode_cyr(array("result"=> true));
			}
			else{
				echo parent::json_encode_cyr(array("result"=> false));	
			}
		}
	}