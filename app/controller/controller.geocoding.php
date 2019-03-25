<?php
	Class Geocoding extends base{
		public function __construct(){
			parent::__construct();
			//parent::checkToken();
			$this->model=new Geo();
		}
		public function countries(){
			$list_counties=$this->model->getListCountries();
		    echo parent::json_encode_cyr(array("code"=> 0,"message"=> "", "data"=>array("countries"=>$list_counties)));
		}
		public function regions(){
			//echo "yes";
			//$list_counties=$this->model->updateRegions();
			$countryId=1;
			  $lang = 0; // russian
			    $headerOptions = array(
			        'http' => array(
			            'method' => "GET",
			            'header' => "Accept-language: en\r\n" . // Вероятно этот параметр ни на что не влияет
			            "Cookie: remixlang=$lang\r\n"
			        )
			    );
			    $methodUrl = 'https://api.vk.com/method/database.getRegions?v=5.5&need_all=1&access_token=52d55de852d55de852d55de8b552b78099552d552d55de8082f4e9cd61e4ca74f1b598e&offset=0&count=1000&country_id=' . $countryId;
			    $streamContext = stream_context_create($headerOptions);
			    $json = file_get_contents($methodUrl, false, $streamContext);
			    $arr = json_decode($json, true);
			    foreach($arr['response']['items'] as $item){
			    	$this->model->addRegion($item);
			    	//print_r($item);
			    }
			    // echo 'Total regions count: ' . $arr['response']['count'] . ' loaded: ' . count($arr['response']['items']);
			    // print_r($arr['response']['items']); 
		}
		public function search(){
			$start = microtime(true); 
		    $lang = 0;
		    $headerOptions = array(
		        'http' => array(
		            'method' => "GET",
		            'header' => "Accept-language: en\r\n" .
		            "Cookie: remixlang=$lang\r\n"
		        )
		    );
		    if(isset($_GET["limit"])){
		    	$limit=$_GET["limit"];
		    }
		    else{
		    	$limit=1000;
		    }
		   // echo 'https://api.vk.com/method/database.getCities?v=5.5&access_token=52d55de852d55de852d55de8b552b78099552d552d55de8082f4e9cd61e4ca74f1b598e&q='.urlencode($_GET["query"]).'&country_id=' . $_GET["country_id"] . '&offset='.$_GET["offset"].'&need_all=1&count='.$limit;
		    $methodUrl = 'https://api.vk.com/method/database.getCities?v=5.5&access_token=52d55de852d55de852d55de8b552b78099552d552d55de8082f4e9cd61e4ca74f1b598e&q='.urlencode($_GET["query"]).'&country_id=' . $_GET["country_id"] . '&offset='.$_GET["offset"].'&need_all=1&count='.$limit;
		    $streamContext = stream_context_create($headerOptions);
		    $json = file_get_contents($methodUrl, false, $streamContext);
		    $arr = json_decode($json, true);
		    $list_city=array();
		    $region_list=array();
		    foreach ($arr['response']['items'] as $key => $count) {
				$current_city=array();
				$current_city["id"]=$count["id"];
				$current_city["name"]=$count["title"]; 
				$info_county=$this->model->getCountryById($_GET["country_id"]);	
				if(isset($count["region"])){
					//$region_name=explode(" ",$count["region"]);
					if (!in_array($region_name[0], $region_list)) {
					    $info_region=$this->model->getRegion($count["region"]);
					    // $region_list[$info_region[0]["id"]]=$region_name[0];
					    // echo "<pre>";
				    	// 	print_r($info_region);
				    	// echo "</pre>";
					}
					$current_city["parent_name"]=trim($count["region"]);
					$current_city["parent_id"]=$info_region[0]["iRegionVkid"];
					$current_city["region"]="true";
					$current_city["country_name"]=$info_county[0]["iCountyName"];
					$current_city["country_id"]=$info_county[0]["iCountryId"];
				}
				else{
					$current_city["region"]="false";
					$current_city["country_name"]=$info_county[0]["iCountyName"];
					$current_city["country_id"]=$info_county[0]["iCountryId"];
				}
				 $list_city[]=$current_city;
		    }
		    //echo 'Время выполнения скрипта: '.(microtime(true) - $start).' сек.';
		    // echo "<pre>";
		    // 	print_r($list_city);
		    // echo "</pre>";
		    $result=array("code"=> 0,"message"=> "","total"=>count($list_city), "data"=>array("geo_objects"=>$list_city));
		    if(isset($_GET["site"])){
		    	$result["count_s"]=mb_strlen($_GET["query"]);
		    }
		    echo parent::json_encode_cyr($result);
		    // echo 'Total cities count: ' . $arr['response']['count'] . ' loaded: ' . count($arr['response']['items']);
		    // print_r($arr['response']['items']); 
		}
		public function getInfoRegion($countryId,$name_region){
			    $lang = 0; // russian
			    $headerOptions = array(
			        'http' => array(
			            'method' => "GET",
			            'header' => "Accept-language: en\r\n" . // Вероятно этот параметр ни на что не влияет
			            "Cookie: remixlang=$lang\r\n"
			        )
			    );
			    //echo $name_region;
			    $methodUrl = 'http://api.vk.com/method/database.getRegions?v=5.5&q='.$name_region.'&need_all=1&count=1000&offset=0&country_id=' . $countryId;
			    $streamContext = stream_context_create($headerOptions);
			    $json = file_get_contents($methodUrl, false, $streamContext);
			    $arr = json_decode($json, true);
			    return $arr['response']['items'];
					}
	}