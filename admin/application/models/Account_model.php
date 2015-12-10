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

       //获取本订单的付款状态和付款金额
        function get_payment($o_id){
        	$this->db->select('o_sn,o_paymentStatus,o_realSale');
        	$this->db->where('o_id', $o_id);
        	$query = $this->db->get("pd_order");       	

        	return $query->result_array();
        }

        //更新订单的付款状态
        function update_payment($o_id,$data){        	
          
        	$this->db->where('o_id', $o_id);
			if($this->db->update(self::TBL_O, $data)){
				return  1;
			}
				else{
				return  0;	
				}  	
        } 

		 
		  
	}

?>