var routers = window.routers || {
	"1": "6日库克雪山豪华全包游",
	"2": "6日南岛纵横全超值游览全包团",
	"3": "2日北岛罗吐鲁阿满满",
	"4": "6日库克雪山深度游",
	"5": "7日西岸有氧健行美食全包团",
	"6": "7日南岛绿野仙踪超值游",
	"7": "9日南北岛纵横全览超值游",
	"8": "7日西岸冰川品位游"
};
var _ = {};
var _date;

(function() {


	// var date_url = "/index.php/Order/a_route_detail" ; // 获取日期以及机票信息
	// var pre_order_url =  "/index.php/Order/add_order" ;  //预定 锁定半成品订单
	// var reset_order_url =  "/index.php/Order/reset_order" ;//释放 半成品订单
	// var submit_order_url =  "/index.php/Order/addallorder" ;//提交 成品订单

	var date_url = "./assets/json/date.json"; // 获取日期以及机票信息
	var pre_order_url = "./assets/json/pre_order.json"; //预定 锁定半成品订单
	var reset_order_url = "./assets/json/reset_order.json"; //释放 半成品订单
	var submit_order_url = "./assets/json/submitOrder.json"; //提交 成品订单


	// var completed = false ; 
	var completed_one = false; // true 表示第一阶段校验通过
	var completed_two = false; //true 表示第二阶段校验通过
	var completed_three = false; // true 表示第三阶段校验通过 准备提价完整订单

	// var _flightInfo = [];

	var language = {
		"1": "国语",
		'2': "粤语",
		'3': "英语 "
	}

	window.info = {};

	_.modal = function(params) {

		var $t_modal = $("#t_modal");
		if (!params || params.show === false) {
			$t_modal.fadeOut(300);
			return;
		}

		var congig_modal = function($d, par) {
			$d.find(".modal-title").text(par.title);
			$d.find(".modal-body").html(par.cont);
		}

		var create_modal = function() {
			var modal = [' <div id="t_modal" class="t-modal">',
				'<div class="modal-cont">',
				'<div class="modal-title"></div>',
				'<div class="modal-body"></div>',
				'<div class="modal-footer">',
				'<span class="t-btn t-btn-primary sure-btn">确定</span>',
				'</div>',
				'</div>',
				'</div>'
			].join("");

			$(document.body).append(modal);
			$t_modal = $("#t_modal");

			$t_modal.on("click", ".sure-btn", function() {
				// alert(1);
				$t_modal.fadeOut(300);
				if (params.close_fn && $.isFunction(params.close_fn)) {
					params.close_fn();
				}
			});

		}

		if ($t_modal.length == 0) {
			create_modal();
		}

		congig_modal($t_modal, params);
		$t_modal.fadeIn(300);

	};

	 



	info.id = "";
	info.router_id = "";
	info.cur_date = "";
	// info.language = "1";

	// info.childPrice = 0;
	info.infant_num = 0 ; 

	info.adult_price = 1260;//成人价格
	info.infant_price = 400 ; //婴儿不占床 价格
	info.child_1_price = 120 ; 
	info.child_2_price = 220 ; 
	info.child_3_price = 320 ; 
	info.discount = 0.22 ; //佣金折扣
	info.room_difference_price = 300 ;
	info.early_double_room_price = 120 ;//双人间 价格
	info.early_breakfast_price = 175 ;//早餐价格
	info.early_triple_room_price = 175 ;//单人间价格
	info.later_double_room_price = 120 ;//双人间 价格
	info.later_triple_room_price = 175 ;//单人间价格
	info.later_breakfast_price = 20 ;//早餐价格
	info.later_fare = 100 ;//早餐价格


	var $date = $("#r_time");

	info.adult_num = 0;
	info.child_num = 0;
	info.difference = 0;

	info.double_room_num = 0 ;
	info.triple_room_num = 0 ; 
	

	// 联系人
	info.contact = {
		contactor:"",
		mobile:"",
		email:""
	};



	info.guest = [];
	info.remark = {
		triple_room_num : 0 ,
		double_room_num : 0 ,
		single_room_num : 0 ,
		twin_room_num : 0 ,
		msg:""
	}; //留言
	var order_info = {};

	// info.arrive_flight = [{
	// 	g_arriveDate: "",
	// 	a_flightno: "",
	// 	a_time: "",
	// 	a_airport: "1",
	// 	arrivedName: "",
	// 	no_focus: true,
	// }];
	// info.leave_flight = [{
	// 	departure_date: "",
	// 	d_flightno: "",
	// 	d_time: "",
	// 	d_airport: "1",
	// 	arrivedName: "",
	// 	no_focus: true,
	// }];

	

	// var vm_tour_router, //线路 
	// 	vm_tour_date, //日期
	// 	// vm_tour_language, //语言
	// 	vm_tour_people, //人数
	// 	vm_tour_fees, //费用
	// 	vm_tour_guest, //游客
	// 	vm_tour_flight, //航班
	// 	vm_tour_house, // 拼房
	// 	vm_tour_order, //订单总计价格
	// 	vm_tour_remark, //留言
	// 	vm_tour_contact ,//联系人信息 
	// vm_agent_reference ,//提前抵达 预定房间数
	// 	vm_additional_service ;//提前抵达 预定房间数


	function clear() {

		console.log("重置数据---");

		info.cur_date = "";
		info.adult_num = 0;
		info.child_num = 0;
		info.infant_num = 0;
		info.difference = 0;
		info.remain = "";
		info.o_sn = "";

		// vm_tour_language.selected = "1";
		vm_tour_date.date = "";
		vm_tour_people.adult_num = 0;
		vm_tour_people.child_num = 0;
		vm_tour_people.infant_num = 0;


		vm_tour_house.agree = "true";
		vm_tour_house.active = false;

		vm_tour_fees.adult_num = 0;
		vm_tour_fees.child_num = 0;
		vm_tour_fees.infant_num = 0 ;
		vm_tour_fees.difference = 0;

		vm_tour_guest.list = [];

		vm_tour_order.order_fees = (0).toFixed(2);


		vm_tour_flight.list = [{
			g_arriveDate: "",
			a_flightno: "",
			a_airport: "1",
			a_time: "",
			arrivedName: "",
			no_focus: true,
		}];
		vm_tour_flight.leave_list = [{
			departure_date: "",
			d_flightno: "",
			d_time: "",
			d_airport: "1",
			arrivedName: "",
			no_focus: true,
		}];

		info.remark = {};

		vm_tour_guest.is_hide = true;
		vm_tour_flight.is_hide = true;
		vm_tour_remark.is_hide = true;
		vm_tour_contact.is_hide =true ; 
		vm_additional_service.is_hide =true ; 

	}


	vm_tour_router = avalon.define("tour_router", function(vm) {
		vm.options = routers;
		vm.selected = "1";

		function get_date() {
			avalon.ajax({
				url: date_url,
				type: "get",
				dataType: "json",
				data: {
					id: vm.selected
				},
				success: function(json) {
					if (json.status == "success") {
						_date = json.data;
						console.log("日期数据获取成功");
					} else {
						_.modal({
							"title": "错误提示",
							cont: json.data
						});
					}

				},
				error: function() {
					alert(1);

				}
			});
		}
		vm.selectchange = function() {

			get_date();
			clear(); //数据重置

			Dates.close();
			info.router_id = vm.selected;
		}

		// get_date();

	});

	// vm_tour_router.selected = "1";

	// var vm_tour_language = avalon.define("tour_language", function(vm) {
	// 	vm.options = language;
	// 	vm.selected = "1";
	// 	vm.selectchange = function() {
	// 		console.log("vm_tour_language change");
	// 		info.language = vm.selected;
	// 	}
	// });

	vm_tour_date = avalon.define("tour_date", function(vm) {
		vm.date = "";
	});

	vm_agent_reference = avalon.define("agent_reference", function(vm) {
		vm.agent_reference = "qq";
	});

	

	vm_tour_people = avalon.define("tour_people", function(vm) {

		vm.adult_num =0;
		vm.infant_num =  0 ;
		vm.child_num = 0 ;


		vm.child_1_num = 0;
		vm.child_2_num = 0;
		vm.child_3_num = 0;

		vm.infant_price = info.infant_price ; 

        vm.discount = 1 ; 
		vm.adult_price = info.child_1_price;

		vm.child_1_price = info.child_1_price;
		vm.child_2_price = info.child_2_price;
		vm.child_3_price = info.child_3_price;

		vm.total = 0;

		 function count_difference(){
		 	if(vm.adult_num%2 == 0 ){ //偶数 禁用
               vm_tour_house.is_disabled =true ;
                vm_tour_fees.difference = vm_tour_house.difference =  0 ; 
		 	}else{
               vm_tour_house.is_disabled = false ;
               vm_tour_fees.difference = vm_tour_house.difference =  info.room_difference_price ; 
		 	}

		 }

		vm.reduce_adult = function() {
			if (vm.adult_num <= 0) return;
			vm.adult_num--;

			info.adult_num = vm.adult_num;
			vm_tour_fees.adult_num = vm.adult_num;

			vm.total = vm.adult_num + vm.child_1_num + vm.child_2_num + vm.child_3_num + vm.infant_num;
            count_difference();
		};
		vm.add_adult = function() {
			if (info.remain == undefined || info.remain == "" || info.remain == 0) {
				$date.trigger('focus');
				return;
			}
			if (vm.total >= info.remain) return;
			vm.adult_num++;
			info.adult_num = vm.adult_num;
			vm_tour_fees.adult_num = vm.adult_num;

			vm.total = vm.adult_num + vm.child_1_num + vm.child_2_num + vm.child_3_num + vm.infant_num;
            count_difference();
			 
			console.log("增加游客 成人");

		};

		vm.reduce_child = function() { //婴儿 和儿童

			var _this =avalon(this);
			var child_type = _this.attr("data-type") ; 
			if (vm[child_type] <= 0) return;
            vm[child_type]-- ; 

			info[child_type] = vm[child_type];
			vm_tour_fees[child_type] = vm[child_type];

			vm.child_num = vm.child_1_num + vm.child_2_num + vm.child_3_num 
			vm.total =  vm.adult_num +vm.child_num + vm.infant_num;

		};
		vm.add_child = function() {
			if (info.remain == undefined || info.remain == "" || info.remain == 0) {
				$date.trigger('focus');
				return;
			}
			if (vm.total >= info.remain) return;

			var _this =avalon(this);
			var child_type = _this.attr("data-type") ; 
            console.log(child_type, vm[child_type] );
			vm[child_type]++;

			console.log("pople add_child", child_type , vm[child_type]);

			info[child_type] = vm[child_type];
			vm_tour_fees[child_type] = vm[child_type];

			console.log("pople add_child", child_type , vm_tour_fees[child_type]);

			vm.child_num = vm.child_1_num + vm.child_2_num + vm.child_3_num 
			vm.total =  vm.adult_num +vm.child_num + vm.infant_num;
			 
		};

		vm.$watch("total", function(a, b) {

			// console.log(vm.total);

			if (a > parseInt(info.remain)) {
				_.modal({
					title: "输入错误",
					cont: "购买票数大于最大剩余票数" + info.remain + "票"
				})
				return false;
			}

		});
		vm.$watch("adult_num", function(a, b) {

			if (info.remain == undefined || info.remain == "" || info.remain == 0) {
				$date.trigger('focus');
				return;
			}
			vm.adult_num = a;
			vm_tour_fees.adult_num = vm.adult_num;
			vm.total = vm.adult_num + vm.child_num;

			vm.adult_num % 2 == 0 ? vm_tour_house.agree = "true" : vm_tour_house.agree = "false";

		});
		vm.$watch("child_num", function(a, b) {

			if (info.remain == undefined || info.remain == "" || info.remain == 0) {
				$date.trigger('focus');
				return;
			}
			vm.child_num = a;
			vm_tour_fees.child_num = vm.child_num;
			vm.total = vm.adult_num + vm.child_num;

		});

	});

		vm_room_request = avalon.define("room_request", function(vm) {
				vm.double_room_num = 0 ;
				vm.triple_room_num = 0 ; 
				vm.single_room_num = 0 ;
				vm.twin_room_num = 0 ;
				vm.agent_refernce = 0 ;

				vm.add_room_num = function() {
					var _this =avalon(this);
					var room_type = _this.attr("data-type") ; 
		            console.log(room_type, vm[room_type] );
					vm[room_type]++;
					info.remark[room_type] = vm[room_type];
				};

				vm.reduce_room_num = function() {
					var _this =avalon(this);
					var room_type = _this.attr("data-type") ; 
		            console.log(room_type, vm[room_type] );
		            if( vm[room_type] <= 0 ) return ;
					vm[room_type]--;
					info.remark[room_type] = vm[room_type];
				};
 
			});



	var create_guest_list = function() {


		for (var i = 0; i < vm_tour_people.adult_num; i++) {
			vm_tour_guest.list.push({
				g_firstname: "",
				g_lastname: "",
				g_gender: "1",
				g_naiton: "",
				g_birth: "",
				g_passport: "",
				g_passexp: "",
				g_visaexp: "",
				g_guestType: "1"
			});

		}

		for (var i = 0; i < vm_tour_people.infant_num; i++) {
			vm_tour_guest.list.push({
				g_firstname: "",
				g_lastname: "",
				g_gender: "1",
				g_naiton: "",
				g_birth: "",
				g_passport: "",
				g_passexp: "",
				g_visaexp: "",
				g_guestType: "2"
			});
		}

	  for (var i = 0; i < vm_tour_people.child_num; i++) {
			vm_tour_guest.list.push({
				g_firstname: "",
				g_lastname: "",
				g_gender: "1",
				g_naiton: "",
				g_birth: "",
				g_passport: "",
				g_passexp: "",
				g_visaexp: "",
				g_guestType: "3"
			});
			// $("select[name=g_guestType]:last").val(2);

		}

	}

	var lock_order = function() { //锁定

		$(".t-can_disabled").attr("disabled", "disabled");
		$("#reset_order").attr("disabled", false);
		$("#reserveBtn").attr("disabled", true);
	}

	var show_second_section = function() { //展示第二阶段的数据模块

		vm_tour_guest.is_hide = false;
		vm_additional_service.is_hide = false;
		vm_tour_contact.is_hide = false;
		vm_tour_flight.is_hide = false;
		vm_tour_remark.is_hide = false;

	}

	var un_lock_order = function() { //解除锁定

		$(".step-1 .t-can_disabled").attr("disabled", false);
		$("#reserveBtn").attr("disabled", false);
		$("#reset_order").attr("disabled", true);


	}

	function validate_one(data) { //第一步校对

		if (!data.router_id) {
			_.modal({
				"title": "错误提示",
				cont: "请选择线路"
			});
			return false;
		}

		if (!data.cur_date) {
			_.modal({
				"title": "错误提示",
				cont: "请选择出行日期"
			});
			return false;
		}
		if (data.adult_num + data.child_num == 0) {
			_.modal({
				"title": "错误提示",
				cont: "出行人员数量不少于1"
			});
			return false;
		}

		// if (data.adult_num % 2 != 0 && info.difference == 0) {
		// 	_.modal({
		// 		"title": "错误提示",
		// 		cont: "成年人个数为奇数，请选择 不同意拼房 "
		// 	});
		// 	return false;
		// }

		completed_one = true;
		return true;
	}

	function validate_two(data) { //第二步校对
		var data = vm_tour_guest.$model.list;
		var len;

		data = JSON.stringify(data);
		data = JSON.parse(data);
		len = data.length;
		console.log("第二部校验", data);
		var names = [];
		var leave_names = [];
		var a = 0;

		for (var i = 0; i < len; i++) {

			if (data[i].g_firstname == "") {
				_.modal({
					"title": "错误提示",
					cont: "游客信息：第" + (i + 1) + "行输入 英文姓 输入不能为空"
				});
				return false;
			}

			if (!/^[A-Za-z]+$/.test(data[i].g_firstname)) {
				_.modal({
					"title": "错误提示",
					cont: "游客信息：第" + (i + 1) + "行输入 英文姓 只能为英文"
				});
				return false;
			}

			if (!/^[A-Za-z]+$/.test(data[i].g_lastname)) {
				_.modal({
					"title": "错误提示",
					cont: "游客信息：第" + (i + 1) + "行输入 英文名 只能为英文"
				});
				return false;
			}

			if (data[i].g_lastname == "") {
				_.modal({
					"title": "错误提示",
					cont: "游客信息：第" + (i + 1) + "行输入英文名输入不能为空"
				});
				return false;
			}

			if (data[i].g_naiton != "" && !/^[A-Za-z]+$/.test(data[i].g_naiton)) {
				_.modal({
					"title": "错误提示",
					cont: "游客信息：第" + (i + 1) + "行 国籍 只能为英文或者为空"
				});
				return false;
			}

			if (data[i].g_birth != "" && !/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/.test(data[i].g_birth)) {
				_.modal({
					"title": "错误提示",
					cont: "游客信息：第" + (i + 1) + "行 出生年月日 为“13/10/1978”格式"
				});
				return false;
			}

			if (data[i].g_passexp != "" && !/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/.test(data[i].g_passexp)) {
				_.modal({
					"title": "错误提示",
					cont: "游客信息：第" + (i + 1) + "行 护照有效期  为“13/10/2015”格式"
				});
				return false;
			}

			if (data[i].g_visaexp != "" && !/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/.test(data[i].g_visaexp)) {
				_.modal({
					"title": "错误提示",
					cont: "游客信息：第" + (i + 1) + "行 签证有效期 为“13/10/2015”格式"
				});
				return false;
			}

			if (data[i].g_passport != "" && !/^[A-Za-z0-9]+$/.test(data[i].g_passport)) {
				_.modal({
					"title": "错误提示",
					cont: "游客信息：第" + (i + 1) + "行 护照号码 只能为字母+数字"
				});
				return false;
			}
			console.log(i, data[i].g_firstname + data[i].g_lastname);
			names.push({
				name: data[i].g_firstname + data[i].g_lastname,
				// not_focus:true ,
				checked: false,
				index: (i + 1)
			});
			leave_names.push({
				name: data[i].g_firstname + data[i].g_lastname,
				// not_focus:true ,
				checked: false,
				index: (i + 1)
			});

		}
		order_info.guest_list = data;

		vm_tour_flight.peoples = names;
		vm_tour_flight.leave_peoples = leave_names;

		completed_two = true;
		return true;
	}

	function validate_three(data) { //第三步校对

		var data = vm_tour_flight.$model;
		var len;

		data = JSON.stringify(data);
		data = JSON.parse(data);
		len = data.length;
		console.log("第三部校验", data);
	    email_re =  /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

		var leave_names = [];
		var arrive_names = [];

		var arrive_names_str = ""
		var leave_names_str = ""

		var arrive_not_deal = false;
		var leave_not_deal = false;

		for (var i = 0; i < data.list.length; i++) {

			// if (data.list[i].g_arriveDate == "" || data.list[i].a_flightno == "" || data.list[i].a_time == "") {
			// 	// arrive_not_deal_index.push(i);
			// 	arrive_not_deal = true;
			// 	break;
			// }

			data.list[i].arrivedName = $.trim(data.list[i].arrivedName);


			
			if (data.list[i].g_arriveDate == "") {
				_.modal({
					"title": "错误提示",
					cont: "抵达-航班信息：第" + (i + 1) + "行输入 抵达航班日期 不能为空"
				});
				return false;
			}

			if ( $.trim( data.list[i].a_flightno ) == "" ) {
				_.modal({
					"title": "错误提示",
					cont: "抵达-航班信息：第" + (i + 1) + "行输入 航班号 不能为空"
				});
				return false;
			}


			if (data.list[i].a_flightno != "" && !/^[A-Za-z0-9]+$/.test(data.list[i].a_flightno)) {
				_.modal({
					"title": "错误提示",
					cont: "抵达-航班信息：第" + (i + 1) + "行输入 航班号 只能为英文数字"
				});
				return false;
			}
			

			if (data.list[i].a_time == "" && !/^[0-9]{2}:[0-9]{2}$/.test(data.list[i].a_time)) {
				_.modal({
					"title": "错误提示",
					cont: "抵达-航班信息：第" + (i + 1) + "行输入 抵达时间格式 为“12:40” "
				});
				return false;
			}
			if (data.list[i].arrivedName == "") {
				_.modal({
					"title": "错误提示",
					cont: "抵达-航班信息：第" + (i + 1) + "行输入 到达人员 输入不能为空"
				});
				return false;
			} else {
				console.log(data.list[i].arrivedName);

				arrive_names = arrive_names.concat(data.list[i].arrivedName.split(" "));
			}

		}
		for (var i = 0; i < data.leave_list.length; i++) {
			// arrivedName: ""d_airport: ""d_flightno: ""d_time: ""departure_date: "
			// if (data.leave_list[i].departure_date == "" || data.leave_list[i].d_flightno == "" || data.list[i].d_time== "") {
			// 	// leave_not_deal_index.push(i);
			// 	leave_not_deal = true;
			// 	break;
			// }
			data.leave_list[i].arrivedName = $.trim(data.leave_list[i].arrivedName);
			if (data.list[i].departure_date == "") {
				_.modal({
					"title": "错误提示",
					cont: "离开-航班信息：第" + (i + 1) + "行输入 离开航班日期 不能为空"
				});
				return false;
			}
			if ( $.trim(data.leave_list[i].d_flightno ) == ""   ) {
				_.modal({
					"title": "错误提示",
					cont: "离开-航班信息：第" + (i + 1) + "行输入 航班号 输入不能为空"
				});
				return false;
			}

			if (data.leave_list[i].d_flightno != "" && !/^[A-Za-z0-9]+$/.test(data.leave_list[i].d_flightno)) {
				_.modal({
					"title": "错误提示",
					cont: "离开-航班信息：第" + (i + 1) + "行输入 航班号 只能为英文数字"
				});
				return false;
			}

			if (data.list[i].d_time == "" && !/^[0-9]{2}:[0-9]{2}$/.test(data.list[i].d_time)) {
				_.modal({
					"title": "错误提示",
					cont: "离开-航班信息：第" + (i + 1) + "行输入 离开时间格式 为“12:40” "
				});
				return false;
			}
			if (data.leave_list[i].arrivedName == "") {
				_.modal({
					"title": "错误提示",
					cont: "离开-航班信息：第" + (i + 1) + "行输入 出发人员 输入不能为空"
				});
				return false;
			} else {
				leave_names = leave_names.concat(data.leave_list[i].arrivedName.split(" "));
			}


		}



		arrive_names_str = arrive_names.join("");
		leave_names_str = leave_names.join("");
		var arr = vm_tour_guest.$model.list;


		if( arrive_not_deal == false && leave_not_deal == false ){

			   //最大抵达日期 小于 最小离开日期 
				var max_g_arriveDate = 0;
				var min_departure_date = 22000101;

				$.each( vm_tour_flight.$model.list ,function(i,v){
		         max_g_arriveDate =   Math.max(  parseInt(v.g_arriveDate.split("/").reverse().join("")) ,max_g_arriveDate   );  
				});

				$.each( vm_tour_flight.$model.leave_list ,function(i,v){
		          min_departure_date =   Math.min(  parseInt(v.departure_date.split("/").reverse().join("")) , min_departure_date  );  
				});

				if( min_departure_date <  max_g_arriveDate  ){

					var datenum_to_str  = function(num){
						var str = (num+"");
						return [str.substring(0,4) , str.substring(4,6), str.substring(6,8) ].reverse().join("/");
					}
					_.modal({
					"title": "错误提示",
					cont: "航班信息:离开日期 " + datenum_to_str(min_departure_date) + "小于" + "抵达日期 " + datenum_to_str(max_g_arriveDate)
				});

					return false ; 
				}




		}
		




		for (var i = 0; i < arr.length; i++) {
			var cur_name = (i + 1) + "." + arr[i].g_firstname + arr[i].g_lastname;
			if (arrive_names_str.indexOf(cur_name) == -1 && arrive_not_deal == false) {
				_.modal({
					"title": "错误提示",
					cont: "抵达-航班信息 : 抵达人员 " + cur_name + "没有被选中"
				});
				return false;
			}
		}

		if (arrive_names.length != arr.length && arrive_not_deal == false) {
			_.modal({
				"title": "错误提示",
				cont: "抵达人员数和游客信息表中人员数不一致"
			});
			return false;
		}


		for (var i = 0; i < arr.length; i++) {
			var cur_name = (i + 1) + "." + arr[i].g_firstname + arr[i].g_lastname;
			if (leave_names_str.indexOf(cur_name) == -1 && leave_not_deal == false) {
				_.modal({
					"title": "错误提示",
					cont: "离开-航班信息 : 离开人员 " + cur_name + "没有被选中"
				});
				return false;
			}
		}

		if (leave_names.length != arr.length && leave_not_deal == false) {
			_.modal({
				"title": "错误提示",
				cont: "离开人员数和游客信息表中人员数不一致"
			});
			return false;
		}

		// $.each(vm_tour_guest.$model.list ,function(k,v){
		// 	var name = (k+1) + "." + v.g_firstname + v.g_lastname ;
		// 	if(leave_names_str.indexOf(name) == -1)

		// });

      // 联系人信息 校验
      var _k , _contact  =vm_tour_contact.$model.contact ;
      for( _k in _contact ){

	       if(_contact[_k] == ""){
	       	 _.modal({
					"title": "错误提示",
					cont: "联系人信息请填写完整"
				});
				return false;
			}
			break ;
        }
			
        if( !/^[A-Za-z0-9]+$/.test(_contact.mobile) ){
            
    	  	 _.modal({
				"title": "错误提示",
					cont: "联系人信息 mobile 信息请填写正确"
				});
				return false;
        }
         if( !email_re.test( _contact.email) ){
     		 _.modal({
			"title": "错误提示",
				cont: "联系人信息 Email 信息请填写正确"
			});
			return false;
        }
        $.extend(order_info , get_base_data() );
		order_info.flightInfo = {};
		order_info.flightInfo.arrive = JSON.parse(JSON.stringify(data.list));
		order_info.flightInfo.leave = JSON.parse(JSON.stringify(data.leave_list));

		$.each(order_info.flightInfo.arrive, function(i, v) {

			if (v.arrivedName != "") {
				var name_indexs = [];
				v.arrivedName = $.trim(v.arrivedName);
				$.each(v.arrivedName.split(" "), function(key, val) {
					var index_1 = parseInt(val.substring(0, 1));
					if (index_1 != NaN) {
						name_indexs.push(index_1 - 1);
					}
				});
				v.arrivedName = name_indexs.join(",");
			}
			delete v.no_focus;
		});

		$.each(order_info.flightInfo.leave, function(i, v) {
			if (v.arrivedName != "") {
				var name_indexs = [];

				v.arrivedName = $.trim(v.arrivedName);
				$.each(v.arrivedName.split(" "), function(key, val) {
					var index_1 = parseInt(val.substring(0, 1));
					if (index_1 != NaN) {
						name_indexs.push(index_1 - 1);
					}
				});
				v.arrivedName = name_indexs.join(",");
			}
			delete v.no_focus;
		});
		// 联系人信息
		order_info.contact =   JSON.parse( JSON.stringify(vm_tour_contact.$model.contact ) );
		// delete order_info.contact.is_hide ;

       // 提前抵达房间信息
		order_info.additional_service =   JSON.parse( JSON.stringify(vm_additional_service.$model ) );
		delete order_info.additional_service.is_hide ; 
         // 
		order_info.remark =   JSON.parse( JSON.stringify( vm_tour_remark.$model.remark ));
		// delete order_info.remark.is_hide ; 

		completed_three = true;
		console.log( JSON.stringify( order_info ) );
		return true;
	}

	function get_base_data (){


		var data = {};

		data.router_id = info.router_id; //线路id
		data.cur_date = info.cur_date; //订票日期
		// data.language = info.language; //语言
		var _people = vm_tour_people.$model ; 
		var _fees = vm_tour_fees.$model ; 
		var _house = vm_tour_house.$model ; 
		var _agent_reference = vm_agent_reference.$model ; 
		var _room_request  = vm_room_request.$model ;

		$.extend(data , {

			agent_reference :_agent_reference.agent_reference,
			is_share : _house.is_share ,
			adult_fees: _fees.adult_fees ,
			adult_num:_fees.adult_num ,
			adult_price: _fees.adult_price ,
			infant_fees: _fees.infant_fees ,
			infant_num: _fees.infant_num ,
			infant_price: _fees.infant_price ,
			child_1_num: _fees.child_1_num ,
			child_1_price: _fees.child_1_price ,
			child_2_num: _fees.child_2_num ,
			child_2_price: _fees.child_2_price ,
			child_3_num: _fees.child_3_num ,
			child_3_price: _fees.child_3_price ,
			total_people: _people.total ,
			difference: _fees.difference ,
			discount: _fees.discount ,
			fees_amount: parseFloat( _fees.fees_amount ),
			brokerage: parseFloat( _fees.brokerage ),
			real_fees_amount: parseFloat( _fees.real_fees_amount ),
			room_request : JSON.parse(JSON.stringify(_room_request ))

		});

		return data;

	}



	$("#reserveBtn").on("click", function() {

		var data = get_base_data();

 
		if (!validate_one(data)) return false;

		$(".step-1 .t-can_disabled").attr("disabled", "disabled");
		$(this).attr("disabled", "disabled");


		// order_info = $.extend(order_info, data);

		avalon.ajax({
			url: pre_order_url,
			type: "POST",
			dataType: "json",
			data: data,
			success: function(json) {

				if (json.status == "success") {
					console.log("订单数据 初步提交");
					// info.id = json.data.id ;
					order_info.o_sn = json.data.o_sn;
					lock_order();
					create_guest_list();
					show_second_section();
				} else {
				  _.modal({
						title: "发生错误",
						cont: "请求发生错误"
					});

					$(".t-can_disabled").attr("disabled", false);
					$("#reserveBtn").attr("disabled", false);
				}
			},
			error: function() {
				$(".t-can_disabled").attr("disabled", false);
				$("#reserveBtn").attr("disabled", false);
				_.modal({
					title: "发生错误",
					cont: "请求发生错误"
				});
			}
		});

	});


	$("#reset_order").on("click", function() {

		var data = {};
		avalon.ajax({
			url: reset_order_url,
			type: "POST",
			dataType: "json",
			data: data,
			success: function(json) {
				if (json.status == "success") {
					console.log("订单数据 重置");
					info.id = "";
					un_lock_order();
					clear();
				}
			}
		});

	});


	vm_tour_house = avalon.define("tour_house", function(vm) {
		vm.agree = "true";
		vm.active = false;
		vm.is_disabled =false;
		vm.is_share = 1 ;  // 1分享 0 不分享
		vm.difference = 0 ; 
		vm.$watch("agree", function() {
			// vm.agree == "true" ?  vm.active =false : vm.active =true ; 
			// vm.agree == "true" ?  info.difference = 0 : info.difference = 200  ; 
			// vm.agree == "true" ?  vm_tour_fees.difference = 0 : vm_tour_fees.difference = 200  ; 
			if (vm.agree == "true") {

				vm.active = false;
				vm.is_share = 1 ;
				info.difference = 0;
				vm_tour_fees.difference = 0;

			} else {
				vm.active = true;
				vm.is_share = 0 ;

				// info.difference = 200;
				// vm_tour_fees.difference = 200;

			}
		});
	});

	vm_tour_fees = avalon.define("tour_fees", function(vm) {

		vm.adult_num = info.adult_num;

		vm.child_1_num = 0;
		vm.child_2_num = 0;
		vm.child_3_num = 0;

		vm.infant_num = 0;
		vm.difference = 0;

		vm.adult_price = 0;

		vm.child_1_price = info.child_1_price;
		vm.child_2_price = info.child_2_price;
		vm.child_3_price = info.child_3_price;

		vm.infant_price = 400;

		vm.early_double_room_num = 0 ; 
		vm.early_triple_room_num =0 ;
		vm.early_breakfast_num = 0 ;

		vm.later_double_room_num = 0 ; 
		vm.later_triple_room_num =0 ;
		vm.later_breakfast_num = 0 ;
		vm.later_fare_num = 0 ;
 
		vm.discount = 1; //佣金折扣


        vm.adult_fees  =  0 ; 
		vm.child_fees  = 0 ; //儿童费用
		vm.infant_fees  = 0 ; //儿童费用

		vm.early_double_room_fees = 0 ; 
		vm.early_triple_room_fees = 0 ; 
		vm.early_breakfast_fees = 0 ; 

		vm.later_double_room_fees = 0 ; 
		vm.later_triple_room_fees = 0 ; 
		vm.later_breakfast_fees = 0 ; 
		vm.later_fare_fees = 0 ; 

		vm.early_fees  = 0 ;//提前抵达费用
		vm.later_fees  = 0 ;//推迟离开费用
	    vm.fees_amount = (0).toFixed(2); //向客人收取
	    vm.fees_amount = (0).toFixed(2);
		vm.brokerage = (0).toFixed(2);
		vm.real_fees_amount = (0).toFixed(2);

		vm.early_double_room_price = 0 ;//双人间 价格
		vm.early_breakfast_price = 0;//早餐价格
		vm.early_triple_room_price = 0 ;//单人间价格
		vm.later_double_room_price = 0 ;//双人间 价格
		vm.later_triple_room_price =0 ;//单人间价格
		vm.later_breakfast_price = 0;//早餐价格
		vm.later_fare_price =0 ;//交通费用

		function count() { 

		    vm.adult_fees = vm.adult_num * vm.adult_price; 

		    vm.child_1_fees = vm.child_1_num * vm.child_1_price; 
		    vm.child_2_fees = vm.child_2_num * vm.child_2_price; 
		    vm.child_3_fees = vm.child_3_num * vm.child_3_price; 

		    vm.infant_fees = vm.infant_num * vm.infant_price; 

		    vm.early_double_room_fees  = vm.early_double_room_price * vm.early_double_room_num ;
		    vm.early_triple_room_fees  = vm.early_triple_room_price * vm.early_triple_room_num ;
		    vm.early_breakfast_fees =  vm.early_breakfast_price * vm.early_breakfast_num  ; 
		    vm.early_fees = vm.early_double_room_fees + vm.early_triple_room_fees + vm.early_breakfast_fees ;

		    vm.later_double_room_fees  = vm.later_double_room_price * vm.later_double_room_num ;
		    vm.later_triple_room_fees  = vm.later_triple_room_price * vm.later_triple_room_num ;
		    vm.later_breakfast_fees =  vm.later_breakfast_price * vm.later_breakfast_num  ; 
		    vm.later_fare_fees =  vm.later_fare_price * vm.later_fare_num  ; 
		    vm.later_fees = vm.later_double_room_fees + vm.later_triple_room_fees + vm.later_breakfast_fees +vm.later_fare_fees ;

		    vm.fees_amount = ( vm.adult_fees + vm.child_1_fees + vm.child_2_fees + vm.child_3_fees + vm.infant_fees + vm.difference +   vm.early_fees + vm.later_fees ).toFixed(2);

			vm.brokerage = (vm.adult_fees * vm.discount).toFixed(2);
			vm.real_fees_amount = (vm.fees_amount - parseFloat(vm.brokerage)).toFixed(2);

			vm_tour_order.order_fees = vm.real_fees_amount;

		};

		// vm.$watch("$all", function(a, b) {
		// 	console.log("count");
		// 	count();
		// });


		vm.$watch("child_1_num", function(a, b) {
			count();
		});
		vm.$watch("child_2_num", function(a, b) {
			count();
		});
		vm.$watch("child_3_num", function(a, b) {
			count();
		});

		vm.$watch("adult_num", function(a, b) {
			count();
		});

		vm.$watch("infant_num", function(a, b) {
			count();
		});
		
		vm.$watch("difference", function(a, b) {
			count();
		});
	 
		vm.$watch("early_triple_room_num", function(a, b) {
			count();
		});
		vm.$watch("early_double_room_num", function(a, b) {
			count();
		});
		vm.$watch("early_breakfast_num", function(a, b) {
			count();
		});

		vm.$watch("later_double_room_num", function(a, b) {
			count();
		});

		vm.$watch("later_triple_room_num", function(a, b) {
			count();
		});
		 
		vm.$watch("later_breakfast_num", function(a, b) {
			count();
		});
		vm.$watch("later_fare_num", function(a, b) {
			count();
		});



	});



	vm_tour_guest = avalon.define("tour_guest", function(vm) {
		vm.list =  [{
				g_firstname: "",
				g_lastname: "",
				g_gender: "1",
				g_naiton: "",
				g_birth: "",
				g_passport: "",
				g_passexp: "",
				g_visaexp: "",
				g_guestType: "1"
			}];
		vm.is_hide = true;
		$guestInfoTable = $(".guestInfoTable");

		$guestInfoTable.on("change", "select[name=g_gender]", function() {
			var index = parseInt(avalon(this).attr("index"));
			var g_gender = $(this).val();
			vm.list[index].g_gender = g_gender;
			console.log(index, g_gender);
		});


		$guestInfoTable.on("change", "select[name=g_guestType]", function() {
			var index = parseInt(avalon(this).attr("index"));
			var g_g_guestType = $(this).val();
			vm.list[index].g_guestType = g_guestType;
			console.log(index, g_guestType);
		});

		vm.fn_g_firstname = function(ev) {
			var _this = this;
			var index = parseInt(avalon(this).attr("index"));
			var val = _this.value;
			var re = /[^a-z]{1}/ig;
			if (val != "") {
				val.replace(re, "");
				// console.log("新的val",val);
				val = val.substring(0, 1).toUpperCase() + val.substring(1);
				vm.list[index].g_firstname = val;

			}
		}

		vm.fn_g_lastname = function(ev) {
			var _this = this;
			var index = parseInt(avalon(this).attr("index"));
			var val = _this.value;
			var re = /[^a-z]{1}/ig;
			if (val != "") {
				val.replace(re, "");
				val = val.substring(0, 1).toUpperCase() + val.substring(1);

				vm.list[index].g_lastname = val;

			}
		}

		vm.fn_g_naiton = function(ev) {
			var _this = this;
			var index = parseInt(avalon(this).attr("index"));
			vm.list[index].g_naiton = _this.value;
			// console.log(_this.value);
		}
		vm.fn_g_birth = function(ev) {
			var _this = this;
			var index = parseInt(avalon(this).attr("index"));
			vm.list[index].g_birth = _this.value;

		}
		vm.fn_g_passport = function(ev) {
			var _this = this;
			var index = parseInt(avalon(this).attr("index"));
			vm.list[index].g_passport = _this.value;
		}
		vm.fn_g_passexp = function(ev) {
			var _this = this;
			var index = parseInt(avalon(this).attr("index"));
			vm.list[index].g_passexp = _this.value;
		}
		vm.fn_g_visaexp = function(ev) {
			var _this = this;
			var index = parseInt(avalon(this).attr("index"));
			vm.list[index].g_visaexp = _this.value;
		}
		vm.fn_g_guestType = function() {
			var _this = this;
			var index = parseInt(avalon(this).attr("index"));
			vm.list[index].g_guestType = _this.value;

		}
	});

 //  vm_tour_house = avalon.define("tour_house", function(vm) {
	// 	vm.is_hide = true;
	// 	vm.house_num = 0 ;
	// 	vm.house_price =200 ;
	// 	vm.total_difference =0 ; 

	// 	vm.add_house =function(){
 //          vm.house_num ++ ;
	// 	}

	// 	vm.reduce_house =function(){
	// 	  if(vm.house_num == 0 ) return ;
 //          vm.house_num ++ ;
	// 	}

	// 	vm.$watch("house_num",function(a,b){
 //          vm.total_difference =  vm.house_num * vm.house_price  ;
	// 	});
		 
	// });

  // 提前抵达和延迟抵达服务

	vm_additional_service = avalon.define("additional_service", function(vm) {
		vm.is_hide = true;

		vm.double_room_num = 0 ;
		vm.triple_room_num = 0 ;

		vm.early_double_room_num = 0 ;
		vm.early_triple_room_num = 0 ;
		vm.early_breakfast_num = 0 ; 
		vm.early_double_room_price = 120 ;
		vm.early_triple_room_price = 175 ;
		vm.early_breakfast_price = 10 ; 

		vm.later_double_room_num = 0 ;
		vm.later_triple_room_num = 0 ;
		vm.later_breakfast_num = 0 ; 
		vm.later_double_room_price = 130 ;
		vm.later_triple_room_price = 195 ;
		vm.later_breakfast_price = 20 ; 
		vm.later_fare_num = 0 ;
		vm.later_fare_price = 40 ; 



		vm.reduce_num = function() { //房间
			var _this = avalon(this);
			var room_type = _this.attr("data-type") ; 
			if (vm[room_type] <= 0) return;
            vm[room_type]-- ; 

			info[room_type] = vm[room_type];
			vm_tour_fees[room_type] = vm[room_type];

		};
		vm.add_num = function() {

			var _this =avalon(this);
			var room_type = _this.attr("data-type") ; 
			vm[room_type]++;

			info[room_type] = vm[room_type];
			vm_tour_fees[room_type] = vm[room_type];
		};

	});


	vm_tour_flight = avalon.define("tour_flight", function(vm) {
		vm.is_hide = true;
		vm.list = [{
			g_arriveDate: "",
			a_flightno: "",
			a_time: "",
			a_airport: "1",
			arrivedName: "",
			no_focus: true,
		}];

		vm.leave_list = [{
			departure_date: "",
			d_flightno: "",
			d_time: "",
			d_airport: "1",
			arrivedName: "",
			no_focus: true,
		}];

		$flightInfo = $(".flightInfo");

		$flightInfo.on("change", "select[name=a_airport]", function() {
			var index = parseInt(avalon(this).attr("index"));
			var a_airport = $(this).val();
			vm.list[index].a_airport = a_airport;
			console.log(index, a_airport);
		});

		$flightInfo.on("change", "select[name=d_airport]", function() {
			var index = parseInt(avalon(this).attr("index"));
			var d_airport = $(this).val();
			vm.leave_list[index].d_airport = d_airport;
			console.log(index, d_airport);
		});

		vm.peoples = [];
		vm.leave_peoples = [];

		vm.remove = function() {
			var _this = avalon(this);
			var index = parseInt(_this.attr("index"));
			var type = _this.attr("data-type");

			vm[type].removeAt(index);
		}

		vm.removeIt = function(perish) {
			perish();
			// vm.left = getLeft();
		};
		vm.focus_index = 0;


		vm.focus = function() {

			if (!validate_two()) { //第二部校验
				console.log("第二部校验 失败");
				return false;
			}

			var _this = avalon(this);
			var index = parseInt(_this.attr("index"));
			vm.focus_index = index;

			var type = _this.attr("data-type");
			avalon.each(vm.list, function(i, v) {
				v.no_focus = true;
			});
			avalon.each(vm.leave_list, function(i, v) {
				v.no_focus = true;
			});
			vm[type][index].no_focus = false;

		}
		vm.focus_departure_date = function() {
			if (!$(this).data("is_date_pick")) {
				$(this).date_input();
				$(this).data("is_date_pick", true);
			}
		}
		vm.focus_arriveDate = function() {

			console.log("focus_arriveDate ArriveDate");

			if (!$(this).data("is_date_pick")) {
				$(this).date_input();
				$(this).data("is_date_pick", true);
			}

		}

		function render_flightInfo(dom, data) {
			var $flight_list_wrap = $(dom).parent();
			$flight_list_wrap.find(".flight_list").removeClass("hide");

		}

		vm.flightno_focus = function() {

		}

		vm.blur_arriveDate = function() {
			var _this = avalon(this);
			var index = parseInt(_this.attr("index"));
			setTimeout(function() {
				vm.list[index].g_arriveDate = _this.val();
			}, 400);
		}

		vm.fn_a_time = function() {
			var _this = this;
			var index = parseInt(avalon(this).attr("index"));
			vm.list[index].a_time = _this.value;
		}
		vm.fn_a_flightno = function() {
			var _this = this;
			var index = parseInt(avalon(this).attr("index"));
			vm.list[index].a_flightno = _this.value;
		}

		vm.blur_departure_date = function() {
			var _this = avalon(this);
			var index = parseInt(_this.attr("index"));
			setTimeout(function() {
				vm.leave_list[index].departure_date = _this.val();
			}, 400);
		}
		vm.fn_d_time = function() {
			var _this = this;
			var index = parseInt(avalon(this).attr("index"));
			vm.leave_list[index].d_time = _this.value;
		}
		vm.fn_d_flightno = function() {
			var _this = this;
			var index = parseInt(avalon(this).attr("index"));
			vm.leave_list[index].d_flightno = _this.value;
		}



		vm.close = function() {
			var _this = avalon(this);
			var index = parseInt(_this.attr("index"));
			var type = _this.attr("data-type");
			vm[type][index].no_focus = true;
			console.log("close");
		}

		vm.add_list_item = function() {

			vm.list.push({
				g_arriveDate: "",
				a_flightno: "",
				a_time: "",
				a_airport: "1",
				arrivedName: "",
				no_focus: true,
			});

			vm.peoples = vm.peopels

		}
		vm.add_leave_list_item = function() {
			vm.leave_list.push({
				departure_date: "",
				d_flightno: "",
				d_time: "",
				d_airport: "1",
				arrivedName: "",
				no_focus: true,
			});
		}


		vm.checkIt = function(people) {

			var _this = this;
			vm.left = getLeft();
			var index = $(this).parent().parent().parent().attr("index");
			var name = (people.index + "." + people.name + " ");
			index = parseInt(index);

			if (this.checked) {
				// $input.val($input.val() +" "+people.name );
				if (vm.list[index].arrivedName.indexOf((people.index + "." + people.name + " ")) == -1) {
					vm.list[index].arrivedName += (people.index + "." + people.name + " ");
				}
				console.log(index, vm.list[index].arrivedName, (people.index + "." + people.name + " "));
				avalon.each(vm.list, function(i, v) {
					if (i != index) {
						vm.list[i].arrivedName = vm.list[i].arrivedName.replace((people.index + "." + people.name + " "), "");
					}
				});
			} else {

				avalon.each(vm.list, function(i, v) {


					vm.list[i].arrivedName = vm.list[i].arrivedName.replace((people.index + "." + people.name + " "), "");

				});
			}

			people.checked = this.checked;
			// people.td_index = index ; 
			vm.left = getLeft();

		}

		vm.leave_checkIt = function(people) {

			var _this = this;
			vm.leave_left = getleave_Left();
			var index = $(this).parent().parent().parent().attr("index");
			var name = (people.index + "." + people.name + " ");
			index = parseInt(index);

			if (this.checked) {
				// $input.val($input.val() +" "+people.name );
				if (vm.leave_list[index].arrivedName.indexOf((people.index + "." + people.name + " ")) == -1) {
					vm.leave_list[index].arrivedName += (people.index + "." + people.name + " ");
				}
				avalon.each(vm.leave_list, function(i, v) {
					if (i != index) {
						vm.leave_list[i].arrivedName = vm.leave_list[i].arrivedName.replace((people.index + "." + people.name + " "), "");
					}
				});
			} else {
				avalon.each(vm.leave_list, function(i, v) {
					vm.leave_list[i].arrivedName = vm.leave_list[i].arrivedName.replace((people.index + "." + people.name + " "), "");
				});
			}

			people.checked = this.checked;
			vm.leave_left = getleave_Left();
		}

		vm.checkall = false;
		vm.leave_checkall = false;

		vm.left = getLeft();
		vm.leave_left = getleave_Left();


		function getLeft() {
			return vm.peoples.filter(function(people) {
				return !people.checked;
			}).length;
		}

		function getleave_Left() {
			return vm.leave_peoples.filter(function(people) {
				return !people.checked;
			}).length;
		}

		vm.$watch("checkall", function(checked, old_checked) {
			var len = vm.peoples.length;

			vm.peoples.forEach(function(people) {
				people.checked = checked;
				console.log("checked all")

				if (checked) {
					len--
				}
			});
			// var index = avalon(this).attr("index");
			index = vm.focus_index;
			var all_name = "";
			avalon.each(vm.peoples, function(i, people) {
				all_name += (people.index + "." + people.name + " ");
			});



			if (checked == true) {
				avalon.each(vm.list, function(i, v) {
					v.arrivedName = "";
				});
				vm.list[index].arrivedName = all_name;
			} else {
				avalon.each(vm.list, function(i, v) {
					v.arrivedName = "";
				});
			}



			vm.left = len;
		});

		vm.$watch("leave_checkall", function(checked, old_checked) {
			var len = vm.leave_peoples.length;
			vm.leave_peoples.forEach(function(people) {
				people.checked = checked;
				if (checked) {
					len--
				}
			});

			// var index = avalon(this).attr("index");
			index = vm.focus_index;
			var all_name = "";
			avalon.each(vm.leave_peoples, function(i, people) {
				all_name += (people.index + "." + people.name + " ");
			});

			if (checked == true) {
				avalon.each(vm.leave_list, function(i, v) {
					v.arrivedName = "";
				});
				vm.leave_list[index].arrivedName = all_name;
			} else {
				avalon.each(vm.leave_list, function(i, v) {
					v.arrivedName = "";
				});
			}


			vm.leave_left = len;
		});



	});


	vm_tour_contact = avalon.define("tour_contact", function(vm) {
		vm.is_hide =true ;
		vm.contact = info.contact;
		// vm.contactor = info.contact.contactor; 
		// vm.mobile = info.contact.mobile; 
		// vm.email = info.contact.email; 

		// vm.$watch("all",function(){
		// 	 info.contact.contactor = vm.contactor ; 
		//      info.contact.mobile =	vm.mobile ; 
		// 	 info.contact.email = 	vm.mobile ; 
		// });

    });

	vm_tour_remark = avalon.define("tour_remark", function(vm) {
		vm.remark = "";
		vm.is_hide = true;
		vm.$watch("remark", function(_new, _old) {
			info.remark["msg"] = vm.remark;
		});
		 
	});


	vm_tour_order = avalon.define("tour_order", function(vm) {
		vm.order_fees = 0;
	});

	// var bind =false ;

	$date.on("focus", function() {

		laydate({
			elem: '#laydate',
			format: "YYYY-MM-DD"
		});

	});

	function two_length(s) {
		s = s || "0";
		s.length == 1 ? s = "0" + s : s = s;
		return s;

	}

	$(document.body).on("click", ".td-abled", function() {
		var _this = $(this);
		info.remain = _this.find(".date-remain").attr("remain");
		info.price = _this.find(".date-price").attr("price");
		info.childPrice = _this.find(".date-price").attr("childPrice");
		info.cur_date = two_length(_this.attr("d")) + "/" + two_length(_this.attr("m")) + "/" + _this.attr("y");

		$("#r_time").val(info.cur_date);
		Dates.close();

		vm_tour_people.adult_num = 0;
		vm_tour_people.infant_num = 0 ;
		vm_tour_people.child_1_num = 0;
		vm_tour_people.child_2_num = 0;
		vm_tour_people.child_3_num = 0;
		vm_tour_people.total = 0;

		var cur_data  =_date[info.cur_date] ; 

		vm_tour_people.adult_price = cur_data.adult_price;//成人价格
		vm_tour_people.infant_price =cur_data.infant_price ; //婴儿不占床 价格
		vm_tour_people.child_1_price = cur_data.child_1_price ; 
		vm_tour_people.child_2_price = cur_data.child_2_price ; 
		vm_tour_people.child_3_price = cur_data.child_3_price ; 
		vm_tour_people.discount   = cur_data.discount ; //佣金折扣

		vm_tour_house.room_difference_price = cur_data.room_difference_price;

		vm_tour_fees.adult_price = cur_data.adult_price;//成人价格
		vm_tour_fees.infant_price = cur_data.infant_price ; //婴儿不占床 价格
		vm_tour_fees.child_1_price = cur_data.child_1_price ; 
		vm_tour_fees.child_2_price = cur_data.child_2_price; 
		vm_tour_fees.child_3_price = cur_data.child_3_price; 
		vm_tour_fees.discount   = cur_data.discount ; //佣金折扣

		vm_tour_fees.early_double_room_price = cur_data.early_double_room_price ;//双人间 价格
		vm_tour_fees.early_breakfast_price = cur_data.early_breakfast_price ;//早餐价格
		vm_tour_fees.early_triple_room_price = cur_data.early_triple_room_price ;//单人间价格
		vm_tour_fees.later_double_room_price = cur_data.later_double_room_price ;//双人间 价格
		vm_tour_fees.later_triple_room_price = cur_data.later_triple_room_price ;//单人间价格
		vm_tour_fees.later_breakfast_price = cur_data.later_breakfast_price ;//早餐价格
		vm_tour_fees.later_fare_price = cur_data.later_fare_price ;//交通费用价格
		

		vm_additional_service.early_double_room_price = cur_data.early_double_room_price ;//双人间 价格
		vm_additional_service.early_breakfast_price = cur_data.early_breakfast_price ;//早餐价格
		vm_additional_service.early_triple_room_price = cur_data.early_triple_room_price ;//单人间价格
		vm_additional_service.later_double_room_price = cur_data.later_double_room_price ;//双人间 价格
		vm_additional_service.later_triple_room_price = cur_data.later_triple_room_price ;//单人间价格
		vm_additional_service.later_breakfast_price = cur_data.later_breakfast_price ;//早餐价格
		vm_additional_service.later_fare_price = cur_data.later_fare_price ;//交通费用价格

		// 佣金折扣

		cur_data.discount ? vm_tour_fees.discount = parseFloat(cur_data.discount) : vm_tour_fees.discount = 1;
		vm_tour_people.discount = vm_tour_fees.discount;


        
	});

	$(document.body).on("click", ".td-disabled", function() {
		Dates.close();
	});

	// $date.on("blur", function() {
	// 	setTimeout(function() {
	// 		Dates.close();
	// 	}, 300);
	// });

	$(".submitOrder").on("click", function() {

		console.log("#submitOrder")

		if (!completed_one) {
			_.modal({
				"title": "提示",
				"cont": "请先点击立即预定"
			});
			return false;
		}
		if (!completed_two && !validate_two()) {
			_.modal({
				"title": "提示",
				"cont": "请先完整输入游客信息"
			});
			return false;
		}

		if (!validate_three()) {
			return false;
		}
		var _this = $(this) ;

		_this.attr("disabled", "disabled");

		avalon.ajax({
			url: submit_order_url,
			type: "POST",
			dataType: "json",
			data: order_info,
			success: function(json) {
				if (json.status == "success") {
					_.modal({
						"title": "提交成功",
						cont: "订单数据提交成功",
						close_fn:function(){
							window.location.href="./order_list.html"
						}
					});
		            _this.removeAttr("disabled");

					// get_date();
					// clear();

				} else {
					_.modal({
						"title": "错误提示",
						cont: json.data
					});
		            _this.removeAttr("disabled");

				}
			},
			error: function() {

			}
		});

	});

})();