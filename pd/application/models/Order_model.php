<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class order_model extends CI_Model {
	function __construct() {
		parent::__construct();
	}

	/** 获取线路列表方法
	 *  @return array 线路信息
	 */
	public function get_route_detail() {
		$this->load->model("User_model");
		$userConf = $this->User_model->get_user_conf();
		//使用的币种
		$userArea = $userConf['area'];
		$sql[1] = sprintf(
				"select 
					r_id as routeId,
					r_cName as routeChineseName,
					r_eName as routeEnglishName,
					r_frequency as routeFrequency,
					r_city as city,
					r_Pdf_au as routePdfAu,
					r_Pdf_nz as routePdfnz,
					r_Pdf_sa as routePdfsa,
					r_auAdultPrice as AdultPrice,
					r_auChildPrice1 as ChildPrice1,
					r_auChildPrice2 as ChildPrice2,
					r_auChildPrice3 as ChildPrice3,
					r_auInfantPrice as InfantPrice,
					r_auSinglePrice as SinglePrice
				from 
					pd_route
				order by
				    r_type ASC, 
				    r_id ASC
				"
			);
		$sql[2] = sprintf(
				"select 
					r_id as routeId,
					r_cName as routeChineseName,
					r_eName as routeEnglishName,
					r_frequency as routeFrequency,
					r_city as city,
					r_Pdf_au as routePdfAu,
					r_Pdf_nz as routePdfnz,
					r_Pdf_sa as routePdfsa,
					r_nzAdultPrice as AdultPrice,
					r_nzChildPrice1 as ChildPrice1,
					r_nzChildPrice2 as ChildPrice2,
					r_nzChildPrice3 as ChildPrice3,
					r_nzInfantPrice as InfantPrice,
					r_nzSinglePrice as SinglePrice
				from 
					pd_route
				order by
				    r_type ASC, 
				    r_id ASC
				"
			);	
		$sql[3] = sprintf(
				"select 
					r_id as routeId,
					r_cName as routeChineseName,
					r_eName as routeEnglishName,
					r_frequency as routeFrequency,
					r_city as city,
					r_Pdf_au as routePdfAu,
					r_Pdf_nz as routePdfnz,
					r_Pdf_sa as routePdfsa,
					r_saAdultPrice as AdultPrice,
					r_saChildPrice1 as ChildPrice1,
					r_saChildPrice2 as ChildPrice2,
					r_saChildPrice3 as ChildPrice3,
					r_saInfantPrice as InfantPrice,
					r_saSinglePrice as SinglePrice
				from 
					pd_route
				order by
				    r_type ASC, 
				    r_id ASC
				");	
		
		$result = $this->db->query($sql[$userArea]);
		return $result -> result_array();
	}
	/** 获取旅游团的价格
	 * @param int $r_id 线路图的id
	 * @return array  旅游价格信息
	 */
	public function get_price($r_id) {
		$this->load->model("User_model");
		$user_conf = $this->User_model->get_user_conf();
		$currency = $this->config->item('currency');
		$currency_field = $currency[$user_conf['area']];
		
		$field = array();
		while(list($k,$v) = each($currency_field) ){
			array_push($field, "`b`.`".$k."` as ".$v );
		}
		$res = $this->db->query(sprintf("select %s from pd_route as b where r_id = %s",join($field,","),$r_id));
		$res = $res->row_array();
		if(!empty($res)){
			return $res;
		}
		return false;
	}
	 /** 获取旅游团的类型
	 * @param int $r_id 线路图的id
	 * @return array  旅游线路的id
	 */
	public function get_group_type($r_id) {
		$query = $this->db->query(sprintf("SELECT r_type FROM pd_route where r_id=%s LIMIT 1",$r_id));
        $row = $query->row();
		
		if(!empty($row)){
			return $row->r_type;
		}
		return false;
	}
	/** 根据线路信息，获取旅游团信息
	 *  @param string $r_id  线路信息
	 *  @return array 线路的日期信息
	 */
	public function get_route_date($r_id){
		$this->load->model("user_model");
		$nowDate = date("Ymd");

		$sqlt = sprintf("select t_type from pd_tourGroup where r_id = %s",$r_id);
		$type_res  = $this->db->query($sqlt);
		$row = $type_res->row();
		$type= $row->t_type;
			if($type == 1){
					$sql = sprintf("
					select t_date,t_tourCode,t_capacity - t_currentpax as remain_guest
					from pd_tourGroup 
					where r_id = %s and t_date >= %s
				",$r_id,$nowDate);
			}elseif($type == 2){
					$sql = sprintf("
					SELECT t_date,t_tourCode,min(remain_guest) AS remain_guest FROM
						(
						SELECT pd2.t_date, pd2.t_tourCode, (
						pd1.t_capacity - pd1.t_currentpax
						) AS remain_guest
						FROM pd_tourGroup pd1
						JOIN pd_tourGroup pd2 ON pd2.t_Nid = pd1.t_id
						AND pd2.r_id = %s and pd2.t_date >= %s
						UNION 
						SELECT  pd2.t_date, pd2.t_tourCode, (
						pd1.t_capacity - pd1.t_currentpax
						) AS remain_guest
						FROM pd_tourGroup pd1
						JOIN pd_tourGroup pd2 ON pd2.t_Sid = pd1.t_id
						AND pd2.r_id = %s and pd2.t_date >= %s
						) as combine
						group by t_date
				",$r_id,$nowDate,$r_id,$nowDate);
			}
		$result  = $this->db->query($sql);
		$data = $result->result_array();
		$price = $this->get_price($r_id);
		$user_conf = $this->User_model->get_user_conf();
		$group_type=$this->get_group_type($r_id);
			if($group_type==1){
				$discount = $user_conf['northRate'];
			}else{
				$discount = $user_conf['commissionRotate'];
			}		
		$prices = array();
		foreach($data as $k => $v){
			$p['adult_price'] = $price['AdultPrice'];
			$p['infant_price'] = $price['InfantPrice'];
			$p['child_1_price'] = $price['ChildPrice1'];
			$p['child_2_price'] = $price['ChildPrice2'];			
			$p['room_difference_price'] = $price['SinglePrice'];
			$p['discount'] = $discount;
			$p['remain_guest'] = $v['remain_guest'];
			$p['tourCode'] = $v['t_tourCode'];
			$prices[date_to_lxs($v['t_date'])] = $p;	 
		}
		return $prices;
	}
	/** 查询库存
	 * @param string $r_id  线路信息
	 * @param string $date   查询日期的库存信息
	 * @return array  返回库存信息
	 */
	public function get_route_store($r_id,$date){
		$sqlt = sprintf("select t_type from pd_tourGroup where r_id = %s",$r_id);
		$type_res  = $this->db->query($sqlt);
		$row = $type_res->row();
		$type= $row->t_type;


			if($type == 1){
					$sql = sprintf("select 
						t_capacity - t_currentpax as curreent 
						from  
							pd_tourGroup
						where 
							r_id = %s and t_date = '%s'
						",$r_id,$date);	
				}elseif($type == 2){
					$sql = sprintf("
					SELECT min(remain_guest) AS curreent  FROM
						(
						SELECT (
						pd1.t_capacity - pd1.t_currentpax
						) AS remain_guest
						FROM pd_tourGroup pd1
						JOIN pd_tourGroup pd2 ON pd2.t_Nid = pd1.t_id
						AND pd2.r_id = %s and pd2.t_date = '%s'
						UNION 
						SELECT  (
						pd1.t_capacity - pd1.t_currentpax
						) AS remain_guest
						FROM pd_tourGroup pd1
						JOIN pd_tourGroup pd2 ON pd2.t_Sid = pd1.t_id
						AND pd2.r_id = %s and pd2.t_date = '%s'
						) as combine						
				",$r_id,$date,$r_id,$date);
			}

		$result = $this->db->query($sql);
		$data = $result->row_array();

		return $data['curreent'];				
	} 
	/** 插入库存信息
	 *  @param array $data 插入提交
	 * 	@param array $person 插入游客信息和航班信息
	 */
	public function insert_order($data,$person,$room) {
		$this -> db -> trans_begin();
		$r_id = $data['r_id'];
		$r_time = $data['o_bookingTime'];
		$total = $data['o_infantNumber'] +$data['o_childNumber1'] + $data['o_childNumber2']  + $data['o_adultNumber'];

		$sqlt = sprintf("select t_type from pd_tourGroup where r_id = %s and t_date = '%s'",$r_id,$r_time);
		$type_res  = $this->db->query($sqlt);
		$row = $type_res->row();
		$type= $row->t_type;

			if($type == 1){
						$db = $this -> db -> query("select * from pd_tourGroup where r_id =" . $r_id . " and t_date='" . $r_time . "' for update");						
						$dbs = $db -> result();
						if ($dbs[0] -> t_capacity - $dbs[0] -> t_currentpax > $total) {
							$where = array("r_id" => $r_id, "t_date" => $r_time);
							$this -> db -> where($where);
							$dd['t_currentpax'] = $dbs[0] -> t_currentpax + $total;
							$this -> db -> update("pd_tourGroup", $dd);
						}
				}elseif($type == 2){
					    $sql = sprintf("
							SELECT min(remain_guest) AS current  FROM
								(
								SELECT (
								pd1.t_capacity - pd1.t_currentpax
								) AS remain_guest
								FROM pd_tourGroup pd1
								JOIN pd_tourGroup pd2 ON pd2.t_Nid = pd1.t_id
								AND pd2.r_id = %s and pd2.t_date >= %s
								UNION 
								SELECT  (
								pd1.t_capacity - pd1.t_currentpax
								) AS remain_guest
								FROM pd_tourGroup pd1
								JOIN pd_tourGroup pd2 ON pd2.t_Sid = pd1.t_id
								AND pd2.r_id = %s and pd2.t_date >= %s
								) as combine						
							",$r_id,$r_time,$r_id,$r_time);
						    	$remain_res  = $this->db->query($sql);
								$row1 = $remain_res->row();
								$remain= $row1->current;													
						if ($remain > $total) {
							//减去北岛团的库存
							$db1 = $this -> db -> query("select pd1.t_id, pd1.t_currentpax from pd_tourGroup pd1 JOIN pd_tourGroup pd2 ON pd2.t_Nid = pd1.t_id
							AND pd2.r_id =" . $r_id . " AND pd2.t_date='" . $r_time . "' for update");						
							$dbs1 = $db1 -> result();							
							$this -> db -> where('t_id',$dbs1[0] -> t_id);
							$dd1['t_currentpax'] = $dbs1[0] -> t_currentpax + $total;
							$this -> db -> update("pd_tourGroup", $dd1);

							//减去南岛团的库存
							$db2 = $this -> db -> query("select pd1.t_id, pd1.t_currentpax from pd_tourGroup pd1 JOIN pd_tourGroup pd2 ON pd2.t_Sid = pd1.t_id
							AND pd2.r_id =" . $r_id . " AND pd2.t_date='" . $r_time . "' for update");						
							$dbs2 = $db2 -> result();							
							$this -> db -> where('t_id',$dbs2[0] -> t_id);
							$dd2['t_currentpax'] = $dbs2[0] -> t_currentpax + $total;
							$this -> db -> update("pd_tourGroup", $dd2);

                            //减去本团的库存
							$db = $this -> db -> query("select * from pd_tourGroup where r_id =" . $r_id . " and t_date='" . $r_time . "' for update");						
							$dbs = $db -> result();
							$where = array("r_id" => $r_id, "t_date" => $r_time);
							$this -> db -> where($where);
							$dd['t_currentpax'] = $dbs[0] -> t_currentpax + $total;
							$this -> db -> update("pd_tourGroup", $dd);
						}
			}
						$this -> db -> insert("pd_order", $data);
						$id = $this->db->insert_id();


		//返回插入的id；
		//插入游客信息
		foreach($person[0] as $v){
			$v['o_id'] = $id;
			$this->db->insert("pd_guest",$v);
		}
		//插入航班信息
		foreach($person[1] as $v){
			$v['o_id'] =$id;
			$this->db->insert("pd_flight",$v);
		}
		//插入分房情况
		foreach($room as $v){
			$v['o_id'] = $id;
			$this->db->insert("pd_room",$v);
		}
		//插入订单处理状态
	
		if ($this -> db -> trans_status() === FALSE) {
			$this -> db -> trans_rollback();
			return false;
		} else {
			$id = $this -> db -> insert_id();
			$this -> db -> trans_commit();
			return $id;
		}

	}
	// 获取路线id
	public function get_roulte_id() {
		$this -> db -> select("r_id");
		$this -> db -> from("pd_route");
		$result = $this -> db -> get();
		$ids = $result -> result_array();
		$id = array();
		foreach ($ids as $v) {
			array_push($id, $v['r_id']);
		}
		return $id;
	}
	//获取id的数据
	public function get_roulte_date($id) {
		$d = date("Y-m-d");
		$this -> db -> select("r_time");
		$this -> db -> from("pd_routeinfo");
		$where = array("r_id=" => $id,"r_time>="=>$d);
		$this -> db -> where($where);
		$result = $this -> db -> get();

		$dates = $result -> result_array();
		$date = array();
		if (empty($dates)) {
			return $date;
		}
		foreach ($dates as $v) {
			array_push($date, $v['r_time']);
		}
		return $date;
	}

	//获取总的订单
	public function get_order_count() {
		$this -> load -> model("User_model");
		$uin = $this -> User_model -> get_uin();

		$this -> db -> select("*");
		$where = array("user_id=" => $uin);
		$this -> db -> where($where);
		$this -> db -> order_by("inserttime desc,o_orderTime desc");
		$this -> db -> from("pd_order");
		$count = $this -> db -> get();
		return $count -> num_rows();
	}

	//显示每页信息
	public function get_list($p, $length) {
		$this -> load -> model("User_model");
		$uin = $this -> User_model -> get_uin();
		$this -> db -> select("o_id as id,o_sn as sn,r_name as name,o_orderTime as otime,o_orderStatus as ostatus,o_adultNumber as anumber,o_childNumber as cnumber ,inserttime as ortime,o_goodsAmount as totalprice");
		$where = array("user_id=" => $uin, "o_status=" => 0);
		$this -> db -> where($where);
		$this -> db -> from("pd_order");
		$this -> db -> join("pd_route", "pd_order.r_id = pd_route.r_id", "left");
		$this -> db -> order_by("ortime desc,otime desc");
		$this -> db -> limit($length, $p);
		$result = $this -> db -> get();
		return $result -> result_array();
	}
	//获取订单数量
	public function get_order_detail($id){
		$id = $this->db->where("o_id=$id");
		$result = $this->db->get("pd_order");
		return $result->row_array();
	}
	//获取总数量
	public function cancel_order($id){
		$this->load->model("User_model");
		$user_id = $this->User_model->get_uin();
		$this->db->trans_begin();
		$where=array("user_id="=>$user_id,"o_status="=>0,"o_id="=>$id);
		$this->db->where($where);
		$this->db->select("o_totalPeople,r_id");
		$res = $this->db->get("pd_order");
		$order = $res->row_array();
		
		$this->db->where($where);
		$this->db->update("pd_order","o_status",-1);
		
		$this->db->query(
			"update pd_routeinfo set r_currentGuest = r_currentGuest - ".$order['o_totalPeople'] . " where ri_id=".$order['r_id']
		);
		
		if ($this->db->trans_status() === FALSE)
		{
		    $this->db->trans_rollback();
		    return false;
		}
		else
		{
		    $this->db->trans_commit();
			return true;
		}
	}
}
?>