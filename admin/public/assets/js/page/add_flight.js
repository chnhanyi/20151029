 var _ = {};

 (function() {


 
   var date_url = "/index.php/Order/a_route_detail"; // 获取日期以及余位信息
    var submit_order_url = "/index.php/Order/add_order"; //提交 成品订单
 



     window.info = {}; // 初始化的部分数据（以前 做的设定 不需要太重视这个数据）
     var st = $.jStorage;


     _.modal = function(params) { //modal 框 做提示用户 

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



     info.guest = [];


         vm.checkIt = function(people) {

             var _this = this;
             var index = $(this).parent().parent().parent().attr("index");
             var name = (people.index + "." + people.name + " ");
             index = parseInt(index);

             if (this.checked) {
                 if (vm.list[index].guests.indexOf((people.index + "." + people.name + " ")) == -1) {
                     vm.list[index].guests += (people.index + "." + people.name + " ");
                 }
                 console.log(index, vm.list[index].guests, (people.index + "." + people.name + " "));
                 avalon.each(vm.list, function(i, v) {
                     if (i != index) {
                         vm.list[i].guests = vm.list[i].guests.replace((people.index + "." + people.name + " "), "");
                     }
                 });
             }

             people.checked = this.checked;

         }
         vm.close = function() {
             var _this = avalon(this);
             var index = parseInt(_this.attr("index"));
             var type = _this.attr("data-type");
             vm[type][index].no_focus = true;
         }





     });





     var create_guest_list = function() {
         vm_tour_guest.list = [];


         for (var i = 0; i < vm_tour_people.adult_num; i++) {
             vm_tour_guest.list.push({
                 g_firstname: "",
                 g_lastname: "",
                 g_gender: "1",
                 g_naiton: "",
                 g_guestType: "1"
             });

         }

         for (var i = 0; i < vm_tour_people.infant_num; i++) {
             vm_tour_guest.list.push({
                 g_firstname: "",
                 g_lastname: "",
                 g_gender: "1",
                 g_naiton: "",
                 g_guestType: "2"
             });
         }

         for (var i = 0; i < vm_tour_people.child_1_num; i++) {
             vm_tour_guest.list.push({
                 g_firstname: "",
                 g_lastname: "",
                 g_gender: "1",
                 g_naiton: "",
                 g_guestType: "3"
             });

         }

         for (var i = 0; i < vm_tour_people.child_2_num; i++) {
             vm_tour_guest.list.push({
                 g_firstname: "",
                 g_lastname: "",
                 g_gender: "1",
                 g_naiton: "",
                 g_guestType: "4"
             });  
         }

     }

    

     var create_flight_list = function() { // create_flight_list
         vm_tour_flight.list = [{
             g_arriveDate: "",
             a_flightno: "",
             a_time: "",
             a_route: "",
             arrivedName: "",
             no_focus: true,
         }];
         vm_tour_flight.peoples = [];
         vm_tour_flight.peoples_index = [];
         vm_tour_flight.selectd_index = [];
     }


    

     validate_flight = function(data) { //第三步校对

             order_info.flightInfo = {};
             order_info.flightInfo.is_not_need = vm_tour_flight.$model.is_not_need;


         if (vm_tour_flight.$model.is_not_need == false) { //需要航班信息

             var $arrivedates = $("input[name=g_arriveDate]") ; 
             $.each($arrivedates , function(i,v){ //重新获取准确的 日期
                vm_tour_flight.$model.list[i].g_arriveDate = $(this).val() ;
             });



             var data = vm_tour_flight.$model;
             var len;

             data = JSON.stringify(data);
             data = JSON.parse(data);
             len = data.length;
             console.log("第三部校验", data);
             email_re = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

             // var leave_names = [];
             var arrive_names = [];

             // var arrive_names_str = ""

             var arrive_not_deal = false;
             var leave_not_deal = false;

             for (var i = 0; i < data.list.length; i++) {

                 data.list[i].arrivedName = $.trim(data.list[i].arrivedName);



                 if (data.list[i].g_arriveDate == "") {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Please select Flight Date, Row #" + (i + 1) + "!"
                     });
                     return false;
                 }

                 if ($.trim(data.list[i].a_flightno) == "") {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Please input Flight No. , Row " + (i + 1) + "!"
                     });
                     return false;
                 }


                 if (data.list[i].a_flightno != "" && !/^[A-Za-z0-9]+$/.test(data.list[i].a_flightno)) {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Flight No. Format incorrect, only English letters and Arabic numerals allowed, Row #" + (i + 1) + "!"
                     });
                     return false;
                 }


                 if (!/^[0-9]{1,2}[0-9]{1,2}-[0-9]{1,2}[0-9]{1,2}$/.test(data.list[i].a_time)) {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Please input Flight Time, Row #" + (i + 1) + " Format “0950-2112” ! "
                     });
                     return false;
                 }

                 if (!/^[a-z]{3}-[a-z]{3}$/i.test(data.list[i].a_route)) {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Please input Flight Route, Row " + (i + 1) + " Format “MEL-AKL” !"
                     });
                     return false;
                 }

                 if (data.list[i].arrivedName == "") {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Please select Passengers, Row #" + (i + 1) + "!"
                     });
                     return false;
                 } else {
                     console.log(data.list[i].arrivedName);

                     arrive_names = arrive_names.concat(data.list[i].arrivedName.split(" "));
                 }

             }

             data.selectd_index.sort(function(a, b) {
                 return a > b ? 1 : -1
             }); //从小到大排序

             var peoples = vm_tour_guest.$model.list;
             for (var i = 0; i < data.peoples_index.length; i++) {
                 if (data.peoples_index[i] != data.selectd_index[i]) {
                     var people_index = data.peoples_index[i];
                     var people_name = peoples[people_index - 1].g_firstname + "/" + peoples[people_index - 1].g_lastname;
                     var confirm_result = confirm(people_name + " do not have a flight Information, are you sure?");
                     if (!confirm_result) return false;
                 }
             }






             order_info.flightInfo.arrive = JSON.parse(JSON.stringify(data.list));
             // order_info.flightInfo.leave = JSON.parse(JSON.stringify(data.leave_list));

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

         };

        // $.extend(order_info, get_base_data());
         return true;
     }

     function get_base_data() {


         var data = {};

         data.router_id = parseInt(info.router_id); //线路id
         data.cur_date = info.cur_date; //订票日期
         data.tourCode = info.tourCode; //旅游团代码
         var _people = vm_tour_people.$model;
         var _fees = vm_tour_fees.$model;
         var _house = vm_tour_house.$model;
         // var _agent_reference = vm_agent_reference.$model;
         var _room_request = vm_room_request.$model;
         $.extend(data, {

             // agent_reference: _agent_reference.agent_reference,
             is_share: _house.is_share,
             adult_fees: Math.round(_fees.adult_fees * 100),
             adult_num: _fees.adult_num,
             adult_price: Math.round(_fees.adult_price * 100),
             infant_fees: Math.round(_fees.infant_fees * 100),
             infant_num: _fees.infant_num,
             infant_price: Math.round(_fees.infant_price * 100),
             child_1_num: _fees.child_1_num,
             child_1_price: Math.round(_fees.child_1_price * 100),
             child_2_num: _fees.child_2_num,
             child_2_price: Math.round(_fees.child_2_price * 100),

             total_people: _people.total,
             difference: Math.round(_fees.difference * 100),
             discount: _fees.discount.toFixed(2),
             fees_amount: Math.round(_fees.fees_amount * 100),
             brokerage: Math.round(_fees.brokerage * 100),
             real_fees_amount: Math.round(_fees.real_fees_amount * 100),
             room_request: JSON.parse(JSON.stringify(_room_request))

         });

         data.room_request.single_room_difference_price = Math.round(data.room_request.single_room_difference_price * 100);

         return data;

     }


     $("#reserveBtn").on("click", function() {

         var data = get_base_data();


         if (!validate_one(data)) return false;

         $(".step-1 .t-can_disabled").attr("disabled", "disabled");
         $(this).attr("disabled", "disabled");


         console.log("订单数据 初步提交");
         lock_order();

         $page_one.fadeOut(300);
         $page_two.fadeIn(300);
         create_guest_list();
         create_room_people_list();
         create_flight_list();
         $.extend(order_info, get_base_data());


     });

     vm_tour_house = avalon.define("tour_house", function(vm) {
         vm.agree = "false";

         vm.is_disabled = false;
         vm.is_share = 0; // 1拼房 0 不拼房
         vm.difference = 0;
         vm.$watch("difference", function() {
             console.log(123);
             info.difference = vm.difference;
             vm_tour_fees.difference = vm.difference;
         });

         vm.$watch("agree", function() {
             if (vm.agree == "true") {
                 vm.active = false;
                 vm.is_share = 1;     
             } else {
                 vm.active = true;
                 vm.is_share = 0;

             }
         });


     });

     vm_tour_fees = avalon.define("tour_fees", function(vm) {

         vm.adult_num = info.adult_num;

         vm.child_1_num = 0;
         vm.child_2_num = 0;


         vm.single_room_num = 0;
         vm.single_room_difference_price = 0;

         vm.infant_num = 0;
         vm.difference = 0;

         vm.adult_price = 0;

         vm.child_1_price = info.child_1_price;
         vm.child_2_price = info.child_2_price;


         vm.infant_price = 400;

         vm.early_double_room_num = 0;
         vm.early_triple_room_num = 0;
         vm.early_breakfast_num = 0;

         vm.later_double_room_num = 0;
         vm.later_triple_room_num = 0;
         vm.later_breakfast_num = 0;
         vm.later_fare_num = 0;

         vm.discount = 0; //佣金折扣
         vm.currency =_price.currency; //货币种类


         vm.adult_fees = 0;
         vm.child_fees = 0; //儿童费用
         vm.infant_fees = 0; //儿童费用

         vm.early_double_room_fees = 0;
         vm.early_triple_room_fees = 0;
         vm.early_breakfast_fees = 0;

         vm.later_double_room_fees = 0;
         vm.later_triple_room_fees = 0;
         vm.later_breakfast_fees = 0;
         vm.later_fare_fees = 0;

         vm.early_fees = 0; //提前抵达费用
         vm.later_fees = 0; //推迟离开费用
         vm.fees_amount = (0).toFixed(2); //向客人收取
         vm.fees_amount = (0).toFixed(2);
         vm.brokerage = (0).toFixed(2);
         vm.real_fees_amount = (0).toFixed(2);

         vm.early_double_room_price = 0; //双人间 价格
         vm.early_breakfast_price = 0; //早餐价格
         vm.early_triple_room_price = 0; //单人间价格
         vm.later_double_room_price = 0; //双人间 价格
         vm.later_triple_room_price = 0; //单人间价格
         vm.later_breakfast_price = 0; //早餐价格
         vm.later_fare_price = 0; //交通费用

         function count() {

             vm.adult_fees = vm.adult_num * vm.adult_price;

             vm.child_1_fees = vm.child_1_num * vm.child_1_price;
             vm.child_2_fees = vm.child_2_num * vm.child_2_price;


             vm.infant_fees = vm.infant_num * vm.infant_price;
            
             vm.fees_amount = (vm.adult_fees + vm.child_1_fees + vm.child_2_fees + vm.infant_fees + vm.difference ).toFixed(2);


             vm.brokerage = ((vm.adult_fees + vm.child_1_fees + vm.child_2_fees) * vm.discount).toFixed(2);
             vm.real_fees_amount = (vm.fees_amount - parseFloat(vm.brokerage)).toFixed(2);

             vm_tour_order.order_fees = vm.real_fees_amount;

         };


         vm.$watch("child_1_num", function(a, b) {
             count();
         });
         vm.$watch("child_2_num", function(a, b) {
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
         vm.list = [];
         vm.is_hide = false;
         $guestInfoTable = $(".guestInfoTable");

         $guestInfoTable.on("change", "select[name=g_gender]", function() {
             var index = parseInt(avalon(this).attr("index"));
             var g_gender = $(this).val();
             vm.list[index].g_gender = g_gender;
             console.log(index, g_gender);
         });



         vm.fn_g_firstname = function(ev) {
             var _this = this;
             var index = parseInt(avalon(this).attr("index"));
             var val = _this.value;
             var re = /[^a-z]{1}/ig;
             if (val != "") {
                 val.replace(re, "");
                 // console.log("新的val",val);
                 val = val.toUpperCase();
                // val = val.substring(0, 1).toUpperCase() + val.substring(1);

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
                  val = val.toUpperCase();
                //val = val.substring(0, 1).toUpperCase() + val.substring(1);


                 vm.list[index].g_lastname = val;

             }
         }

         vm.fn_g_nation = function(ev) {
             var _this = this;
             var index = parseInt(avalon(this).attr("index"));
             vm.list[index].g_naiton = _this.value;
             // console.log(_this.value);
         }


         vm.fn_g_guestType = function() {
             var _this = this;
             var index = parseInt(avalon(this).attr("index"));
             vm.list[index].g_guestType = _this.value;
         }
     });



     /* 航班 */

     vm_tour_flight = avalon.define("tour_flight", function(vm) {
         vm.is_hide = false;
         vm.is_not_need = true;

         vm.is_need = function() {
             var _this = avalon(this);
             vm.is_not_need = !vm.is_not_need;
         }
         vm.list = [{
             g_arriveDate: "",
             a_flightno: "",
             a_time: "",
             a_route: "",
             arrivedName: "",
             no_focus: true,
             checkall: false 
         }];


         $flightInfo = $(".flightInfo");


         vm.peoples = [];
         vm.peoples_index = [];
         vm.selectd_index = [];
         // vm.leave_peoples = [];

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

             if (!validate_guests()) { //第二步校验
                 console.log("第二步校验 失败");
                 return false;
             }

             // vm.checkall = false ;

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
                 console.log("blur_arriveDate" ,_this , _this.val());
             }, 1000);
         }

           
 

         vm.fn_a_time = function() {
             var _this = this;
             var index = parseInt(avalon(this).attr("index"));
             vm.list[index].a_time = _this.value;
         }
         vm.fn_a_flightno = function() {
             var _this = this;
             this.value = this.value.toLocaleUpperCase();
             var index = parseInt(avalon(this).attr("index"));
             vm.list[index].a_flightno = _this.value;
         }
         vm.fn_a_route = function() {
             var _this = this;
             this.value = this.value.toLocaleUpperCase();
             var index = parseInt(avalon(this).attr("index"));
             vm.list[index].a_route = _this.value;
         }

         vm.blur_departure_date = function() {
             var _this = avalon(this);
             var index = parseInt(_this.attr("index"));
             setTimeout(function() {
                 vm.leave_list[index].departure_date = _this.val();
             }, 900);
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

          vm.fn_checkall =function(){
              var _this = this;
             var index = parseInt(avalon(this).attr("index"));
             var old_checked = vm.list[index].checkall ; 
             vm.list[index].checkall = !vm.list[index].checkall ;
             var checked =  vm.list[index].checkall;

          var len = vm.peoples.length;

             vm.peoples.forEach(function(people) {
                 people.checked = checked;
                 console.log("checked all")

                 if (checked) {
                     len--
                 }
             });

             var all_name = "";
             avalon.each(vm.peoples, function(i, people) {
                 all_name += (people.index + "." + people.name + " ");
             });

             vm.selectd_index = vm.peoples_index;

             if (checked == true) {
                 // avalon.each(vm.list, function(i, v) {
                 //     v.arrivedName = "";
                 // });
                 vm.list[index].arrivedName = all_name;
             } else {
                vm.list[index].arrivedName = "";
             }
            
         }



         vm.close = function() {
             var _this = avalon(this);
             var index = parseInt(_this.attr("index"));
             var type = _this.attr("data-type");
             vm[type][index].no_focus = true;
             console.log("close");
         }

         vm.add_list_item = function() {
            console.log(  vm.$model.list);

              var $arrivedates = $("input[name=g_arriveDate]") ; 
             $.each($arrivedates , function(i,v){ //重新获取准确的 日期
               vm.$model.list[i].g_arriveDate = $(this).val() ;
             });


             vm.list.push({
                a_flightno: "",
                a_route: "",
                a_time: "",
                arrivedName: "",
                g_arriveDate: "",
                no_focus: true,
                checkall:false ,
             });


         }

         function put_in_selected_index(val) {
             var l = vm.selectd_index.length;
             for (var i = 0; i < l; i++) {
                 if (vm.selectd_index[i] == val) return;
             }
             vm.selectd_index.push(val);

         }



         vm.checkIt = function(people) {

             var _this = this;
             var index = $(this).parent().parent().parent().attr("index");
             var name = (people.index + "." + people.name + " ");
             index = parseInt(index);

             if (this.checked) {
                 if (vm.list[index].arrivedName.indexOf((people.index + "." + people.name + " ")) == -1) {
                     vm.list[index].arrivedName += (people.index + "." + people.name + " ");
                 }

                 put_in_selected_index(people.index);
             }
             people.checked = this.checked;

         }

     });


     function two_length(s) {
         s = s || "0";
         s.length == 1 ? s = "0" + s : s = s;
         return s;
     }



     $(document.body).on("click", ".td-disabled", function() {
         Dates.close();
     });

     $(document.body).on("click", ".go-step1", function() {
         $page_one.fadeIn(300);
         $page_two.fadeOut(300);
         un_lock_order();
     });



     $("#order_submit").on("click", function() {

         // $("#page_two").hide();
         // $("#page_three").show();
         // alert(1);
         var _this = $(this);

         _this.attr("disabled", "disabled");

         get_remark_data();

         console.log("最终数据", JSON.stringify(order_info));

         avalon.ajax({
             url: submit_order_url,
             type: "POST",
             dataType: "json",
             data: order_info,
             success: function(json) {
                 if (json.status == "success") {

                     $("#page_three").hide();
                     $("#page_success").show();

 
                     _this.removeAttr("disabled");

                     // get_date();
                     // clear();

                 } else {
                     _.modal({
                         "title": "Error Tips",
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
