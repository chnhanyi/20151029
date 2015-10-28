<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Myinput{
		private $rule = array();
		private $error = array();
		/**@ 设置验证规则
		 * @ $var 变量名
		 * @ $rule array(rule  规则  message提示信息)
		 * 
		 */
		public function setRule($var,$rule){
		    if(isset($this->rule[$var])){
		    	array_push($this->rule[$var],$rule);
		    }else{
		    	$this->rule[$var][0] = $rule;
		    }
		}
		private function isbool($v){
			if(is_bool($v)){
				return true;
			}
			return false;
		}
		private function isInt($v){
			if(is_numeric($v)){
				return true;
			}
			return false;
		}
		/** @单个验证函数
		 *  @  $var 变量名
		 *  @ $value 需要验证的值
		 */
		 public function getAllVadation(){
		 	$flag = true;
		 	foreach($this->rule as$k => $v)
			{
				foreach ($v as $va){
					
		 		if(count($va) == 3){
		 			if(!$this->$va['rule']($va['value']))
					{
						$flag = false;
						$this->error[$k][] = $va['message'];
					}
		 		}else{
		 			if(!$this->$va['rule']($va['value'],$va['param']))
					{
						$flag = false;
						$this->error[$k][] =$va['message'];
					}
		 		}
				}		 		
			}
			return $flag;	
		 }
		 //验证
		 public function getVadation($var,$value){
		 	if(isset($this->rule[$var])){
		 		if($this->Vadation($this->rule[$var], $vaule)){
		 			return true;
		 		}else{
		 			return false; //验证失败了
		 		}
		 	}else{
		 		  $this->error = "验证规则不存在";
		 		   return false; //验证规则不存在
		 	}
		 }
		 //验证类
		private function  Vadation($rule,$vaule){
		 	$flag= true;
		 	foreach ($rule as $v){
		 		if(count($v) == 2){
		 			if(!$this->$v['rule']($value))
					{
						$flag = false;
						$this->error=$v['message'];
						break;
					}
		 		}else{
		 			if(!$this->$v['rule']($value,$v['param']))
					{
						$flag = false;
						$this->error=$v['message'];
						break;
					}
		 		}	 		
		 	}
			return $flag;		
		 }
		//清空验证规则
		public function clearRule()
		{
			$this->rule = array();
		}
		//单个正则验证
		public function getRegVadation($rule,$value)
		{
			if(preg_match($rule['rule'] ,$value)){
				return true;
			}else{
				return false;
			}
		}
		//验证单个数据验证
		public function getDataVadation($data,$method){
			if($this->$method($data)){
				return true;
			}
			return false;
		}
		//in验证
		private function inRange($value,$var){
			$flag = false;
			foreach ($var as $v){
				if($v == $value){$flag = true;}
			}
			return $flag;
		}
		//数字验证
		private function isNumber($value){
			if(preg_match('/^\d{1,}$/', $value)){
					return true;
			}else{
				return false;
			}
		}

		//验证旅游团代码
		private function isTourCode($value){
			if(preg_match('/^[a-zA-Z0-9\+]+$/', $value)){
					return true;
			}else{
				return false;
			}
			}


		//日期验证
		private function isDate($value,$formate="/^(\d{4})-(\d{2})-(\d{2})$/i"){
			if(preg_match($formate ,$value)){
				return true;
			}else{
				return false;
			}
		}
		//范围验证
		private function Range($value,$range)
		{
			$max = $range['max'];
			$min = $range['min'];
			if($value >=$min && $value <=$max){
				return true;
			}else{
				return false;
			}
		}
		//浮点数验证
		private function isFloat($value){
			$reg = '/^\d*\.\d{2}$/i';
			if(preg_match($reg, $value)){
				return true;
			}else{
				return false;
			}
		}
		private function eq($value,$d){
			if($value == $d + 0){
				return true;
			}else{
				return false;
			}
		}
		private function isarray($value){
			if(is_array($value)){
				return true;
			}
			return false;
		}
		private function isarraypor($value,$d){
			if($this->isarray($value)){
				$k = array_keys($value);
				 $kd = array_diff($k, $d);
				 $dk = array_diff($d, $k);
				if(empty($kd) && empty($dk)){
					return true;
				}
				return false;
			}
			return false;
		}
		private function isPhone($value){
			$reg = "/^[0-9+\-\s]*$/";
			if(preg_match($reg, $value)){
				return true;
			}
			return false;
		}
		private function isemail($v){
			if(empty($v)){
				return true;
			}
		$p = "/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/";
		if(preg_match($p, $v)){
			return true;
		}
		return false;
	}
		private function isContent($value){
			$reg = "/^[0-9a-zA-Z+\-\s.]*$/";
			if(preg_match($reg,$value)){
				return true;
			}
			return false;
		}
		//geterror
		public function geterror(){
			return $this->error;
		}
		public function isnotempty($value){
			return true;
		}
		public function isword($value){
			$reg = "/^[a-zA-Z\s\d]*$/";
			if(preg_match($reg, $value)){
				return true;
			}else{
				return false;
			}
		}
	}
?>