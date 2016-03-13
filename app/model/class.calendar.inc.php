<?php

	class Calendar extends DB_Connect{
		private $_useDate;
		private $_m;
		private $_y;
		private $_daysInMonth;
		private $_startDay;
		public function __construct($dbo=NULL, $useDate=NULL){
			parent::__construct($dbo);
			if(isset($useDate)){
				$this->_useDate=$useDate;
			}
			else {
				$this->_useDate=date("Y-m-d H:i:s");
			}
			$ts=strtotime($this->_useDate);
			$this->_m=date("m",$ts);
			$this->_y=date("Y",$ts);
			$this->_daysInMonth=cal_days_in_month(CAL_GREGORIAN, $this->_m, $this->_m);
			$ts=mktime(0,0,0,$this->_m,$this->_y);
			$this->_startDay=date("w",$ts);
		}
	}
?>