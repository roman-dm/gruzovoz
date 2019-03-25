<?php
	class base extends DB_Connect{
		public $view;
		public $model;
		public $token;
		public $header_token;
		public $current_page;
		public $type_token;
		public function __construct(){
			session_start();
			parent::__construct($dbo);
			$headers = getallheaders();
			Twig_Autoloader::register();
			$loader = new Twig_Loader_Filesystem('app/views/');
			$this->view = new Twig_Environment($loader, array(
					    'auto_reload' => true,
					    //'cache'       => 'compilation_cache',
			));
			$this->current_page=explode("/",$_SERVER["REQUEST_URI"]);
			$this->model=new Auth_user();
			if(isset($headers["X-Auth-Token"]) || !empty($headers["X-Auth-Token"])){
				$token=$this->model->get_token($headers["X-Auth-Token"],"any");
				if(!empty($token["sToken"])){
					$type_token="main";
				}
				elseif(!empty($token["sGuestToken"])){
					$type_token="guest";
				}
			}
			// if(!$this->getSession()){
			// 	if($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]!=$_SERVER["SERVER_NAME"]."/"){
			// 		$_SESSION["mess"]="<span style='color:red'>Ошибка авторизации, пожалуйста введи логин и пароль</span>";
			// 		header("Location:http://".$_SERVER["SERVER_NAME"]);
			// 	}
			// }
			// $this->access=new access();
		}
		public function checkToken($type){
			//include_once $_SERVER["DOCUMENT_ROOT"]."/app/model/class.user.inc.php";
			$this->model=new Auth_user();
			$headers = getallheaders();
			//print_r($headers);
			if(isset($headers["x-auth-token"])){
				$headers["X-Auth-Token"]=$headers["x-auth-token"];
			}
			if(!isset($headers["X-Auth-Token"]) || empty($headers["X-Auth-Token"])){
				echo $this->json_encode_cyr(array("code"=> 1,"message"=> "Проблемы с токеном", "data"=> ""));
				exit();
			}
			else{
				$this->token=$this->model->get_token($headers["X-Auth-Token"],$type);
				if(count($this->token)!=1){
					echo $this->json_encode_cyr(array("code"=> 1,"message"=> "Проблемы с токеном", "data"=> ""));
					exit();
				}
			}
			$this->header_token=$headers["X-Auth-Token"];
		}
		public function getIdDevice(){
			$headers = getallheaders();
			$this->model=new Auth_user();
			if(isset($headers["x-auth-token"])){
				$headers["X-Auth-Token"]=$headers["x-auth-token"];
			}
			return $this->model->getIdToken($headers["X-Auth-Token"]);
		}
		public function checkSession(){
			if(!isset($_SESSION["type_user"])){
				//echo "ошибка доступа";
				echo $this->view->render('denied.html');
				exit();
			}
		}
		public function logout(){
			unset($_SESSION);
			session_destroy();
			//$_SESSION["mess"]="<span style='color:green'>Сеанс закончен!</span>";
			header("Location:http://".$_SERVER["SERVER_NAME"]);

		}
		public function json_encode_cyr($str) {
			$arr_replace_utf = array('\u0410', '\u0430','\u0411','\u0431','\u0412','\u0432',
			'\u0413','\u0433','\u0414','\u0434','\u0415','\u0435','\u0401','\u0451','\u0416',
			'\u0436','\u0417','\u0437','\u0418','\u0438','\u0419','\u0439','\u041a','\u043a',
			'\u041b','\u043b','\u041c','\u043c','\u041d','\u043d','\u041e','\u043e','\u041f',
			'\u043f','\u0420','\u0440','\u0421','\u0441','\u0422','\u0442','\u0423','\u0443',
			'\u0424','\u0444','\u0425','\u0445','\u0426','\u0446','\u0427','\u0447','\u0428',
			'\u0448','\u0429','\u0449','\u042a','\u044a','\u042b','\u044b','\u042c','\u044c',
			'\u042d','\u044d','\u042e','\u044e','\u042f','\u044f','\/','\u2116','\u2019','&#243;','&#233;','&#225;','&#237;','&#252;','&#250;','&#241;','&#161;','&#228;','&#246;','\u00ab','\u00bb','\u2013','&#220;');
			$arr_replace_cyr = array('А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е',
			'Ё', 'ё', 'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м','Н','н','О','о',
			'П','п','Р','р','С','с','Т','т','У','у','Ф','ф','Х','х','Ц','ц','Ч','ч','Ш','ш',
			'Щ','щ','Ъ','ъ','Ы','ы','Ь','ь','Э','э','Ю','ю','Я','я','/','№','’','ó','é','á','í','ü','ú','ñ','¡','ä','ö','«','»','–','Ü');
			$str1 = json_encode($str);
			$str2 = str_replace($arr_replace_utf,$arr_replace_cyr,$str1);
			$str2 = preg_replace('{"(\d+(\.\d+)?)"}', '$1', $str2);
			$this->addLogAutorization($str2);
			return $str2;
		}
		public function getDateImage() {
			return file_get_contents('php://input');
		}
		public function getDate() {
			return json_decode(file_get_contents('php://input'),true);
		}
		public function getFormData() {		 
		    // PUT, PATCH или DELETE
		    $data = array();
		    $exploded = explode('&', file_get_contents('php://input'));
			if(isset($_GET["debug"])){
				print_r($exploded);
			}
		    foreach($exploded as $pair) {
		        $item = explode('=', $pair);
		        if (count($item) == 2) {
		            $data[urldecode($item[0])] = urldecode($item[1]);
		        }
		    }
		    return $data;
		}
		public function AutorizeUser($type,$param){
			$str="";	
			if($type=="phone"){
				if($param[0]=="+"){
					$param=substr($param, 1);
				}
				$str="sSmsNumber=:sSmsNumber";
			}
			$query = $this->db->prepare("SELECT * FROM Device WHERE $str");
			if($type=="phone"){
				$query->bindParam('sSmsNumber', $param);
			}
			$query->execute();
			$info_user=$query->fetchAll(PDO::FETCH_ASSOC);
			if($query->rowCount()==1){
				$_SESSION["type_user"]=$info_user[0]["TypeUser"];
				$_SESSION["id_user"]=$info_user[0]["iDeviceId"];
			}
			else{
				return false;
			}
		}
	    static function sendMessage($email_val,$array, $type){
	       if($type=="send_reg"){
	       	 	$mes = '<body bgcolor="#ffffff" link="#f89e1b" vlink="#f89e1b" alink="#f89e1b"><table cellpadding="0" cellspacing="0" border="0" width="100%" align="center" style="font-family: arial; font-size: 12px; color: #FEFEFE;table-layout: fixed;"><tbody><tr><td bgcolor="#ffffff"><table cellpadding="0" cellspacing="0" border="0" width="800" align="center"><tbody><tr><td><table width="100%" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td><div align="center"><font style="font-family: arial, Helvetica, sans-serif; font-size:20px; color:#232728"><p>Здравствуйте,</p><p>Для подтверждения Вашей почты, пожалуйста,<br/>введите код '.$array["code"].' или пройдите по ссылке указанной ниже и<br/>наслаждайтесь нашим сервисом.<br/><br/><a href="http://gruzovoz.alexkam.ru/activation_email/?email='.$email_val.'&code='.$array["code"].'">Подтвердить почту</a></p><div style="background-color: gray;color: gray;height: 2px;"></div><p style="color:gray;font-size: 15px;">Данное письмо сформированно автоматически, пожалуйста, не отвечайте на него!<br><br>С уважением,<br>Проект "Грузовоз"<br>Телефон: +7(123)456-78-90<br>Email: <a href="mailto:123@123.ru">123@123.ru</a></p></font></div></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></body>';
			}
	        $sub='Подтверждение электронной почты';
	        $email='info@gruzovoz1.ru';
	        if(mail ($email_val,$sub,$mes,"Content-type:text/html; charset = utf-8\r\nFrom:$email")){
	          return true;
   			}
   			else{
   				return false;
   			}
	    }
	   	private function addLogAutorization($str){
		$name = "log/".date("H")."-".date("d-m-y").".txt";
		//если файла нету... тогда
			$fp = fopen($name, "a+"); // ("r" - считывать "w" - создавать "a" - добовлять к тексту), мы создаем файл
			$string=date("d-m H:i:s")." ".$str." | \r\n";
			$string.="________________________________________________________________________________________________________________________________\r\n";
			fwrite($fp, $string);
			fclose ($fp);
		//fopen("/log/".$name.".txt", "a+"); 
	}
	public function img_resize($src, $dest, $width, $height, $rgb = 0xFFFFFF, $quality = 100)
{  
    if (!file_exists($src))
        return false;
 
    $size = getimagesize($src);
      
    if ($size === false)
        return false;
 
    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
    $icfunc = 'imagecreatefrom'.$format;
     
    if (!function_exists($icfunc))
        return false;
 
    $x_ratio = $width  / $size[0];
    $y_ratio = $height / $size[1];
     
    if ($height == 0)
    { 
        $y_ratio = $x_ratio;
        $height  = $y_ratio * $size[1];
    }
    elseif ($width == 0)
    { 
        $x_ratio = $y_ratio;
        $width   = $x_ratio * $size[0];
    }
     
    $ratio       = min($x_ratio, $y_ratio);
    $use_x_ratio = ($x_ratio == $ratio);
     
    $new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
    $new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
    $new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width)   / 2);
    $new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);
      
    // если не нужно увеличивать маленькую картинку до указанного размера
    if ($size[0]<$new_width && $size[1]<$new_height)
    {
        $width = $new_width = $size[0];
        $height = $new_height = $size[1];
    }
 
    $isrc  = $icfunc($src);
    $idest = imagecreatetruecolor($width, $height);
      
    imagefill($idest, 0, 0, $rgb);
    imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);
 
    $i = strrpos($dest,'.');
    if (!$i) return '';
    $l = strlen($dest) - $i;
    $ext = substr($dest,$i+1,$l);
     
    switch ($ext)
    {
        case 'jpeg':
        case 'jpg':
        case 'JPG':
        imagejpeg($idest,$dest,$quality);
        break;
        case 'gif':
        imagegif($idest,$dest);
        break;
        case 'png':
        imagepng($idest,$dest);
        break;
    }
 
    imagedestroy($isrc);
    imagedestroy($idest);
 
    return true;  
}
	function checkRating($rating,$type,$id_user){
		if($type=="customer"){
			$this->model=new Customer();
		}
		elseif($type="driver"){
			$this->model=new Driver();
		}
		switch ($rating) {
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
				echo $this->json_encode_cyr(array("code"=> 16,"message"=> "Не корректная оценка рейтинга", "data"=>(object)array()));
				exit();
				break;
		}
		$rating=$this->model->getRating($id_user);
		if(count($rating)==0){
			$this->model->addRating($id_user);
		}
		$result=$this->model->updateRate($name_col,$id_user);
		$rating_list=$this->model->getRating($id_user);
		$count_rating=$rating_list[0]["iRatingOne"]+$rating_list[0]["iRatingTwo"]+$rating_list[0]["iRatingThree"]+$rating_list[0]["iRatingFour"]+$rating_list[0]["iRatingFive"];
		//echo $count_rating."<br>";
		if($count_rating==0){
			$count_rating=1;
		}
		$new_rating=round(($rating_list[0]["iRatingOne"]*1+$rating_list[0]["iRatingTwo"]*2+$rating_list[0]["iRatingThree"]*3+$rating_list[0]["iRatingFour"]*4+$rating_list[0]["iRatingFive"]*5)/$count_rating,1);
		if($type=="customer"){
			$result=$this->model->update_profile_customer(array("rating"=>$new_rating),$id_user);
		}
		elseif($type="driver"){
			$result=$this->model->update_profile_driver($id_user,array("rating"=>$new_rating));
		}
		if($result==1){
			return true;
		}
		else{
			return false;
		}

		}
	}
