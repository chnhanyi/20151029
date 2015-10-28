<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 class Myorder{
 	private	$person;			//所有的人员信息	
	private $air;        		//所有的航班信息
	private $contact;			//额外的联系方式
	private $service ;			//额外的服务费用
	private $totalCount = 0; 	//总的人数
	private $error;
	
	function setAir($air){
		$this->air = $air;
	} 
	function setPerson($person){
		$this->person = $person;
	}
	function setContact($contact){
		$this->contact = $contact;
	}
	function setAdditional_service($service){
		$this->service = $service;
	}
	//验证是数字
	private function isNumber($value){
			if(preg_match('/^\d{1,}$/', $value)){
					return true;
			}else{
				return false;
			}
	}
	//只能是英文
	private function isword($value){
		$reg = "/^[a-zA-Z\s\d]*$/";
		if(preg_match($reg, $value)){
			return true;
		}else{
			return false;
		}
	}
	//验证为空
	private function isempty($value){
		if(empty($value)){
			return true;
		}else{
			return false;
		}
	}
	private function isnotempty($value){
		if(empty($value)){
			return false;
		}
		return true;
	}
	//验证生日
	private function isdate($value){
		if($this->isempty($value)){
			return true;
		}else{
			$reg = "/^\d{2}\/\d{2}\/\d{4}$/";
			if(preg_match($reg, $value)){
				$dd = explode("/", $value);
				if(checkdate($dd[1], $dd[0], $dd[2])){
					return true;
				}
				return false;
			}
			return false;
		}
	}
	//验证国籍
	private function isnation($v){
		if($this->isempty($v)){
			return true;
		}else{
			$reg = "/^.*$/";
			if(preg_match($reg, $v)){
				return true;
			}else{
				return false;
			}
		}
	}
	//验证旅游团代码
	private function isTourCode($v){
		if($this->isempty($v)){
			return true;
		}else{
			$reg = '/^[a-zA-Z0-9]+$/';
			if(preg_match($reg, $v)){
				return true;
			}
			return FALSE;
		}
	}
	//验证航班号
	private function isfightno($v){
		if($this->isempty($v)){
			return true;
		}
		$reg = '/^[a-zA-Z0-9]+$/';
		if(preg_match($reg, $v)){
			return true;
		}
		return false;
	}
	//验证人员list
	private	function namelist($v){
		if($this->isempty($v)){
			return true;
		}
		$reg = '/^\d(\,\d)*$/';
		if(preg_match($reg, $v)){
			return true;
		}
		return false;
	} 
	//验证时间
	private function istime($v){
		if($this->isempty($v)){
			return true;
		}
		$reg = '/^\d{1,2}:\d{1,2}\-\d{1,2}:\d{1,2}$/';
		if(preg_match($reg, $v)){
			return true;
		}
		return FALSE;
	}
	//验证人员信息
	private function isemail($v){
		$p = "/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/";
		if(preg_match($p, $v)){
			return true;
		}
		return false;
	}
	//验证电子邮件
	private function isprice($v){
		$reg = '/^\d+$/';
		if(preg_match($reg, $v)){
			return true;
		}
		return false;
	}
	//验证价格数据
	private function isa($a){
		if(is_array($a)){
			return true;
		}
		return false;
	}
	private function isfightnos($v){
			$reg = '/^[A-Z]*\-[A-Z]*$/';
		if(preg_match($reg, $v)){
			return true;
		}
		return false;
	}
	//验证有哪些必有的键值
	private function vadiarr($a,$k){
		$flag = true;
		foreach($a as $v){
			$keys = array_keys($v);
		    if(!(empty(array_diff($keys,$k)) && empty(array_diff($k,$keys)))){
		        return  false;
		    }
		}
		return $flag;
	}
	//验证键值相等
	private function arrkeys($k,$k2){
		if(!(empty(array_diff($k,$k2)) && empty(array_diff($k2,$k)))){
		        return  false;
		 }
		return true;
	}
	//开始验证人员信息
	public function VadaPerson(){
		$k = array(	"g_firstname",
					"g_lastname",
					"g_gender",
					"g_naiton",					
					"g_guestType"
					);
		if(!$this->isa($this->person)){
			$this->error = "人员信息结构不合法";
			return false;
		}
			//验证结构是否合法
		if(!$this->vadiarr($this->person,$k)){
			$this->error = "人员结构不正确";
			return false;
		}
		//验证人员信息问题
		$rule = array(
					"g_firstname"	=>array("rule" =>"isword",	"message"=>"第一姓名不正确"),
					"g_lastname"	=>array('rule' =>"isword" ,	"message"=>"第二姓名不正确" ),
					"g_gender" 		=>array("rule" =>"isNumber","message"=>"性别格式不合法"),
					"g_naiton"		=>array('rule' =>"isnation","message"=>"国籍不正确"),
					"g_guestType"		=>array("rule" =>"isNumber",	"message"=>"游客信息类型不正常")
					);
		//验证数据
		foreach($this->person as $v){
			foreach($v as $k => $va){
				if (!array_key_exists($k, $rule)){
					continue;
				}
				if(!$this->$rule[$k]['rule']($va)){
					$this->error = $rule[$k]['message'];
					return false;
				}
			}
		}			
		//以上验证都是正确的   就可以开始存入人员信息
		return true;
	}
	//验证航班信息
	public 	function vadaAir(){
		if(!$this->isa($this->air)){
			$this->error = "航班格式信息不正确";
			return false;
		}
		//验证结构
		$keys = array("arrive","is_not_need");
		$k = array_keys($this->air);
		
		if(!$this->arrkeys($keys,$k)){
			$this->error = "航班格式不正确";
		}
		
		$arrive = $this->air['arrive'];
//		$leave = $this->air['leave'];
		$ak = array("g_arriveDate","a_flightno","a_time","arrivedName","a_route");
//		$lk = array("departure_date","d_flightno","d_time","d_airport","arrivedName");
		//验证来来的航班的格式
		if(!$this->vadiarr($arrive, $ak)){
			$this->error = "抵达航班错误";
			return false;
		}
		//验证离开的航班的格式
//		if(!$this->vadiarr($leave, $lk)){
//			$this->error = "离开航班信息错误";
//			return false;
//		}
		//验证航班信息的数据格式
		$akrule = array(
			"g_arriveDate" =>array('rule' =>"isdate" ,"message"=>"抵达航班日期错误"),
			"a_flightno" =>array('rule' =>"isfightno" ,"message"=>"航班号错误" ),
			"a_time" =>array("rule"=>"istime","message"=>"抵达时间格式不正确"),
			"arrivedName"=>array('rule' =>"namelist" ,"message"=>"人员信息不正确" ),
			"a_route" =>array("rule"=>"isfightnos","message"=>"机场数据不正确")
		);
		//验证来的航班信息
		foreach($arrive as $v){
			foreach($v as $k => $v1){
				if(!array_key_exists($k, $akrule)){
					continue;
				}
				if(!$this->$akrule[$k]['rule']($v1)){
					$this->error = $akrule[$k]['message'];
					var_dump($k);
					return false;
				}
			}
		}
		//来的航班信息验证完成
		//验证离开的航班信息
//		$lkrule = array(
//			"departure_date" => array("rule" =>"isdate","message" =>"离开航班日期错误"),
//			"d_flightno" => array("rule"=>"isfightno","message"=>"离开的航班号格式错误"),
//			"d_time"=>array("rule"=>"istime","message"=>"离开的时间格式错误"),
//			"d_airport"=>array("rule"=>"isNumber","message"=>"离开的机场格式错误"),
//			"arrivedName"=>array("rule"=>"namelist","message"=>"离开的人员的名单错误")
//		);
//		//开始验证
//		foreach($leave as $v){
//			foreach($v as $k => $v1){
//				if(!array_key_exists($k, $lkrule)){
//					continue;
//				}
//				if(!$this->$lkrule[$k]['rule']($v1)){
//						$this->error = $lkrule[$k]['message'];
//					return false;
//				}
//			}
//		}
		//验证完成
		return true;
	} 
	//返回验证的错误信息
	public function geterror(){
		return $this->error;
	}
	//验证联系方式
	public function vadaContact(){
		if(!$this->isa($this->contact)){
			$this->error="联系数据结构错误";
			return false;
		}
		$k = array("contactor","mobile","email");
		$keys = array_keys($this->contact);
		if(!$this->arrkeys($k,$keys)){
			$this->error = "联系数据结构错误2";
			return false;	
		}
		$rule = array(
			"contactor"=>array("rule"=>"isword","message"=>"联系人姓名不正确"),
			"mobile"=>array("rule"=>"isNumber","message"=>"联系人电话不正确"),
			"email"=>array("rule"=>"isemail","message"=>"联系人电子邮箱不正确")
			);
		foreach($this->contact as $k1 => $v1){
				if(!array_key_exists($k1, $rule)){
					continue;
				}
				if(!$this->$rule[$k1]['rule']($v1)){
					$this->error = $rule[$k1]['message'];
					return false;
				}
		}
		return true;	
	}
	//验证附加服务
	public function vadaService(){
		$k =  array(
								"early_double_room_num",
								"early_triple_room_num",
								"early_breakfast_num",
								"early_double_room_price",
								"early_triple_room_price",
								"early_breakfast_price",
								"later_double_room_num",
								"later_triple_room_num",
								"later_breakfast_num",
								"later_double_room_price",
								"later_triple_room_price",
								"later_breakfast_price",
								"later_fare_num",
								"later_fare_price"
						);
		if(!$this->isa($this->service)){
			$this->error = "增值服务数据有问题1";
			return false;
		}
		$k2 = array_keys($this->service);
		if(!$this->arrkeys($k, $k2)){
			$this->error = "增值服务数据有问题2";
			return false;
		}
		$rule = array(
			"early_double_room_num"=>array("rule"=>"isnumber","message"=>"提前到的双人间的房间数不正确"),
			"early_triple_room_num"=>array("rule"=>"isnumber","message"=>"提前到的单人间的房间数不正确"),
			"early_breakfast_num"=>array("rule"=>"isnumber","message"=>"提前的草餐数据不正确"),
			"early_double_room_price"=>array("rule"=>"isprice","message"=>"提前到的双人间价格不正确"),
			"early_triple_room_price"=>array("rule"=>"isprice","message"=>"提前到的单人间价格不正确"),
			"early_breakfast_price"=>array("rule"=>"isprice","message"=>"提前到的早餐价格"),
			"later_double_room_num"=>array("rule"=>"isnumber","message"=>"晚点离开的双人间的房间数不正确"),
			"later_triple_room_num"=>array("rule"=>"isnumber","message"=>"晚点离开单间的房间数不正确"),
			"later_breakfast_num"=>array("rule"=>"isnumber","message"=>"晚点离开的早餐数量"),
			"later_double_room_price"=>array("rule"=>"isprice","message"=>"晚离开的双人间的房间数不正确"),
			"later_triple_room_price"=>array("rule"=>"isprice","message"=>"晚离开的单人间的房间数不正确"),
			"later_breakfast_price"=>array("rule"=>"isprice","message"=>"晚离开的早餐价格"),
			"later_fare_num"=>array("rule"=>"isnumber","message"=>"晚离开的交通人数"),
			"later_fare_price"=>array("rule"=>"isprice","message"=>"晚离开的交通价格"),
			);
		foreach($this->service as $k1 => $v1){
				if(!array_key_exists($k1, $rule)){
					continue;
				}
				if(!$this->$rule[$k1]['rule']($v1)){
					$this->error = $rule[$k1]['message'];
					return false;
				}
		}	
		return true;								
	}
	//验证信息
	public function vadationPerson(){
		if($this->VadaPerson() ){
			return true;
		}
		return false;
	}
	//验证机票信息
	public function vadaAirfilght(){
		if( $this->vadaAir()){
			return true;
		}
		return false;
	}
	public function vadationAdd(){
		if($this->vadaContact() && $this->vadaService()){
			return true;
		}
		return false;
	}
 }

?>