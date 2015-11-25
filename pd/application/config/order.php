<?php
$config['order_add_field'] = array(
			'info' => array(
					'router_id' 		=> 'r_id',
					'cur_date' 			=> 'o_bookingTime',
					'agent_reference' 	=> 'o_agentReference',
					),
			'num' =>array(
					'adult_num'			=>	'o_adultNumber',//成人数量
					'adult_price'		=>  'o_adultPrice',//成人价格
					'infant_num'		=>	'o_infantNumber',	//婴儿数量
					'infant_price'      =>	'o_infantPrice', //婴儿价格
					'child_1_num'		=>	'o_childNumber1',//2到四岁儿童
					'child_1_price'		=> 	'o_child1Price',//成年人1
					'child_2_num'		=>	'o_childNumber2',//2到4岁占床
					'child_2_price' 	=> 	'o_child2Price',//成年人2

					
					'total_people'		=>	'o_totalPeople', //总人数
					),
			'price'=>array(		
					'difference'		=>	'o_difference',//房屋差价
					'discount'			=>	'o_discount', //折扣率
					'fees_amount'		=> 	'o_saleTotal', //房屋总价格
					'brokerage'			=> 	'o_brokerage', //佣金
					'real_fees_amount' 	=>	'o_orderAmount',//减去佣金之后的价格
					),
			"home" =>array(
						'room_request'		=>	'room_request'
					),			
		);
$config['order_add_fd'] = array(
					'guest_list' => array(
										"g_firstname"=>"g_firstname",
										"g_lastname"=>"g_lastname",
										"g_gender"=>"g_gender",
										"g_naiton"=>"g_gender",
										"g_birth"=>"g_birth",
										"g_passport"=>"g_passport",
										"g_passexp"=>"g_passexp",
										"g_guestType"=>"g_guestType",
									),
					"flightInfo" => array(
								"arrive" =>array(
									"g_arriveDate",
									"a_flightno",
									"a_airport",
									"a_time",
									"arrivedName"
									),
								"leave"=>array(
									"departure_date",
									"d_flightno",
									"d_time",
									"d_airport",
									"arrivedName"
									),
							),	
						"contact" =>array(
								"contactor",
								"mobile",
								"email"
							),
						"additional_service" => array(
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
						)						
				); 		
			
?>