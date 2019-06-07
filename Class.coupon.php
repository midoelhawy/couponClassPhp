<?php
if(!isset($_SESSION)){
	session_start();
}
if(!isset($calledConnect)){
include(__DIR__ ."/../../../includes/functions/connect.php");//connection File ; RETURN PDO $conn ;
}
include(__DIR__ ."/../../../includes/functions/login_functions.php");//Check login Function

if(!class_exists('Coupon')){
	class Coupon{
		private $__coupon_code;
		private $__data_coupon;
		public 	$__price_descounted;
		public  $_session_coupon_name;
		public  $_coupon_id;
		public  $errors;
		public function __construct($session_coupon_name){
			global $conn;
			$this->errors = array();
			$this->_session_coupon_name = $session_coupon_name;
			$this->check_coupo();
		}

		public function check_coupo(){
			global $conn;
			if(isset($_SESSION[$this->_session_coupon_name])){
				$login_status = chickissetlogin();//Checklogin function ; return array [INT id,BOOL success]
				$userid = isset($login_status['id']) ? $login_status['id']:0;
				$gettokin = $conn->prepare("SELECT * FROM coupons WHERE code = ? && ( user = ? || user = 0) LIMIT 1");
				$gettokin->execute(array($_SESSION[$this->_session_coupon_name],$userid));
				$row 	  = $gettokin->rowCount();
				if($row > 0){
					$fetchdata = $gettokin->fetch();
					$this->__data_coupon = $fetchdata;
					$this->__coupon_code = $fetchdata['code'];
					$this->_coupon_id    = $fetchdata['id'];
					return true;
				}else{
					$this->__coupon_code = null;
				}
			}else{
				$this->__coupon_code = null;
			}
		}

		public function is_coupon(){
			if($this->__coupon_code != null){
				return true;
			}
			return false;
		}

		public function type_coupon(){
			if($this->__data_coupon['is_percenurl'] == 1){
				return 1;//is persentual
			}else{
				return 0;//is Value
			}
		}

		public function codetion_is_true($order_value){
			if($this->__data_coupon['min_value'] != 0){
				if($this->__data_coupon['min_value'] > $order_value){
					return false;
				}
			}else{
				if($this->__data_coupon['max_value'] != 0){
					if($this->__data_coupon['max_value'] < $order_value){
						return false;
					}
				}
			}
			$datanow = strtotime(@date("Y-m-d H:i:s"));
			$datastart = strtotime($this->__data_coupon['data_start']);
			$dataend = strtotime($this->__data_coupon['data_end']);
			if($dataend < $datanow){
				$this->errors[] = "il coupon non è più valido!";
				return false;
			}

			return true;
		}


		public function descount_value($order_value){
			$checkcondation = $this->codetion_is_true($order_value);
			if($checkcondation != false){
				if($this->type_coupon() == 0){
						$descountval =  $this->__data_coupon['descount_value'];
						$this->__price_descounted = ($order_value - $this->__data_coupon['descount_value']);
				}else{
					if($this->__data_coupon['descount_value'] <= 100 && $this->__data_coupon['descount_value'] > 0){
						 $descountval = ($order_value*$this->__data_coupon['descount_value'])/100;
						 $this->__price_descounted = ($order_value - $descountval);
					}
				}
				return array("descount"=>$descountval,"price_descounted"=>$this->__price_descounted);;
				return array("descount"=>$descountval,"price_descounted"=>$totoalprice);$this->__price_descounted;
			}
			return array("descount"=>00.00,"price_descounted"=>$order_value);
		}

		public function Set_coupon($coupon){
			$_SESSION[$this->_session_coupon_name] = $coupon;
			if($this->check_coupo()){
				return "is_true";
			}else{
				if($this->is_coupon() != false){
					if(count($this->errors) > 0){
						return $this->errors[0];
					}

				}
			}
			return "il coupon non è stato trovato!";
		}




	}//end class
}

?>
