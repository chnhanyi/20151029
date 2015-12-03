<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	class Account_model extends CI_Model{
        const TBL_O = "order"; 
        const TBL_T = "tourGroup";             



		//获取所有符合要求的订单信息
		public function get_all_orders($where,$start,$limit){
			$this->db->select('pd_order.o_id, pd_order.o_sn, pd_order.o_agentReference, pd_order.o_totalNum, pd_order.o_adultNumber, pd_order.o_paymentStatus,
								pd_order.o_childNumber1, pd_order.o_childNumber2, pd_order.o_infantNumber, pd_order.o_realSale,pd_order.t_tourCode,
								pd_order.o_opName,pd_order.o_opCode, pd_company.a_name,pd_company.a_tel,pd_company.a_commissionRate,pd_company.a_northRate');
			$this->db->from('pd_order');
			$this->db->join('pd_company','pd_order.a_id=pd_company.a_id');
			$this->db->where('pd_order.o_orderStatus',3);
			$this->db->where($where);
			$this->db->order_by('pd_order.o_id','desc');
			$this->db->limit($limit,$start);
			$query =$this->db->get(); 				
			return $query->result_array();
		}



		#统计订单的总数
		function count_order($where){
			$this->db->where($where);
			$this->db->where('o_orderStatus',3);
			$this->db->from(self::TBL_O);
			return $this->db->count_all_results();
		}

        //获得选定的旅游团的所有订单信息(for 酒店)
		function get_group_total($tourCode){
			 $sql=sprintf("SELECT SUM( o_adultNumber ) AS adultNumber,
			 SUM( o_infantNumber ) AS infantNumber, 
			 SUM( o_childNumber1 ) AS childNumber1, 
			 SUM( o_childNumber2 ) AS childNumber2,
			 SUM( o_totalNum ) AS totalNumber, 
			 SUM( o_triple ) AS triple, 
			 SUM( o_double ) AS doubleroom, 
			 SUM( o_twin ) AS twin, 
			 SUM( o_single ) AS single
			FROM pd_order where o_orderStatus <> 4 AND t_tourCode like '%%%s%%'",$tourCode);

			$query = $this->db->query($sql);			 
			return $query->result_array();
		}


		//获得选定的旅游团的所有订单编号
		function get_all_order_id($tourCode){
			$sql=sprintf("SELECT o_id FROM pd_order where o_orderStatus <> 4 AND t_tourCode like '%%%s%%'",$tourCode);
			$query = $this->db->query($sql);			 
			return $query->result_array();
		}

				//获得选定的旅游团的所有订单编号
		function get_order_detail($o_id){
			$sql=("SELECT *	FROM pd_order where o_id=".$o_id);
			$query = $this->db->query($sql);			 
			return $query->result_array();
		}

		//获得下本订单的agent所在公司的ID
		function get_company_id($o_id){
			$sql=("SELECT *	FROM pd_order where o_id=".$o_id);
			$query = $this->db->query($sql);			 
			return $query->result_array();
		}

        //获得本公司id所在的区域
		function get_company_area($company_id){
			$sql=("SELECT *	FROM pd_company where a_id=".$company_id);
			$query = $this->db->query($sql);			 
			return $query->result_array();
		}
		
				//获取游客信息情况
			function get_order_guest($o_id){
				$this->db->where("o_id",$o_id);
				$this->db->order_by("g_id", "asc"); 				
				$guest = $this->db->get("pd_guest");
				$res = $guest-> result_array();
				return $res;
			}



				//获取游客的航班信息
			function get_order_flight($o_id){
				$this->db->where("o_id",$o_id);
				$this->db->order_by("f_id", "asc"); 				
				$flight = $this->db->get("pd_flight");
				$res = $flight-> result_array();
				return $res;
			}

			//获取分房情况
			function get_room_people($o_id){
				$this->db->where("o_id",$o_id);
				$this->db->order_by("r_type", "asc"); 
				$room = $this->db->get("pd_room");
				$res = $room-> result_array();
				return $res;
			}



		//获得某一订单的单人房间信息
		function get_single_name($o_id){
			$this->db->select('r_guests');
			$this->db->where('o_id', $o_id);
			$this->db->where('r_type',1);
			$query = $this->db->get("pd_room");
			
			return $query->result_array();
		}
         
        //更新订单的状态为正在处理中
        function update_order_status1($o_id,$opname){
        	$status = 2;
        	$data = array(               
               'o_orderStatus' => $status,
               'o_opName' => $opname
            );
        	$this->db->where('o_id', $o_id);
			if($this->db->update(self::TBL_O, $data)){
				return  1;
			}
				else{
				return  0;	
				}
        }

        //获取订单发票的修改次数
        function get_invoice_hit($o_id){
        	$this->db->select('o_invoice_hit');
        	$this->db->where('o_id', $o_id);
        	$query = $this->db->get("pd_order");
        	$row=$query->row();

        	return $row->o_invoice_hit;
        }

        //更新发票的信息和发票点击次数，OPname等
        function update_invoice_info($o_id,$opname,$newhit,$data){
        	$status = 3;
        	$updata = array(
        		'o_invoice_hit' => $newhit,
        		'o_invoice_data' => $data,
                'o_orderStatus' => $status,
                'o_opName' => $opname
            );            
        	$this->db->where('o_id', $o_id);
			if($this->db->update(self::TBL_O, $updata)){
				return  1;
			}
				else{
				return  0;	
				}  	
        } 

        //更新订单,添加OP的审核内容
        function check_order($o_id,$data){        	
        	$this->db->where('o_id', $o_id);
			if($this->db->update(self::TBL_O, $data)){
				return  1;
			}
				else{
				return  0;	
				}
        }


        //更新联系人的信息
        function update_contact($o_id,$data){
        	$this->db->where('o_id', $o_id);
			if($this->db->update(self::TBL_O, $data)){
				return  1;
			}
				else{
				return  0;	
				}
        }

        //取消订单，更新订单的状态为已经取消
        function update_order_status3($o_id,$opname){
        	$status = 4;
        	$data = array(               
               'o_orderStatus' => $status,
               'o_opName' => $opname
            );            
        	$this->db->where('o_id', $o_id);
			if($this->db->update(self::TBL_O, $data)){
				return  1;
			}
				else{
				return  0;	
				}  	
        } 


 		//检查订单是否是取消状态
		function check_order_status($o_id){
			$res = $this->db->query("
							select o_orderStatus from pd_order where o_id = ".$o_id );
				$data = $res->row_array();
				return $data;
		}

		//获取线路详情
			function get_route_info($r_id){
				$res = $this->db->query("
							select r_cName as router_cName,r_eName as router_eName
							from pd_route							
							where r_id = ".$r_id );
				$data = $res->row_array();
				return $data;				
			}

		 
		  
	}

?>