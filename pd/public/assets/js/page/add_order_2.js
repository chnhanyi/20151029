 var _ = {};
 var _date;
 var $page_one = $("#page_one");
 var $page_two = $("#page_two");

 (function() {


 
    var date_url = "/index.php/Order/a_route_detail"; // 获取日期以及余位信息
    var submit_order_url = "/index.php/Order/add_order"; //提交 成品订单
 
     // var completed = false ; 
     var completed_one = false; // true 表示第一阶段校验通过
     var completed_two = false; //true 表示第二阶段校验通过
     var completed_three = false; // true 表示第三阶段校验通过 准备提价完整订单


     window.info = {}; // 初始化的部分数据（以前 做的设定 不需要太重视这个数据）
     var st = $.jStorage;

     var cur_price = {};

     if (!window._price) {
         _.modal({
             title: "Error Tips",
             cont: "发生错误"
         })
         return false;
     }
     $.each(window._price, function(k, v) {
         if ($.isNumeric(v) && k != "discount") {
             cur_price[k] = (parseFloat(v / 100)).toFixed(1);
         }else{
             cur_price[k] = v ;
         }

     });




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





     info.id = "";
     info.router_id = "";
     info.cur_date = "";
     info.tourCode = "";

     var $date = $("#r_time"); //日期选择的dom 

     // 联系人
     info.contact = {
         contactor: "",
         mobile: "",
         email: ""
     };



     info.guest = [];

     var order_info = {};

     // var vm_tour_router, //线路 
     //  vm_tour_date, //日期
     //  // vm_tour_language, //语言
     //  vm_tour_people, //人数
     //  vm_tour_fees, //费用
     //  vm_tour_guest, //游客
     //  vm_room_request ;//房间要求
     //  vm_tour_flight, //航班
     //  vm_tour_house, // 拼房
     //  vm_tour_order, //订单总计价格
     //  vm_tour_remark, //留言
     //  vm_tour_contact ,//联系人信息 



     function clear(step) {
         step = step || 0;

         console.log("重置数据---");

         if (step != 1) {
             info.cur_date = "";
             info.tourCode = "";
             vm_tour_date.date = "";
         }

         vm_tour_people.adult_num = 0;
         vm_tour_people.child_num = 0;
         vm_tour_people.infant_num = 0;


         vm_tour_house.agree = "true";
         vm_tour_house.active = false;

         vm_tour_fees.adult_num = 0;
         vm_tour_fees.child_num = 0;
         vm_tour_fees.infant_num = 0;
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

     }


     vm_tour_router = avalon.define("tour_router", function(vm) {

         vm.routerId = st.get("id");
         vm.routerName = st.get("routerName");
         vm.routerEnName = st.get("routerEnName")

         function get_date() {
             avalon.ajax({
                 url: date_url,
                 type: "get",
                 dataType: "json",
                 async: false,
                 data: {
                     id: vm.routerId
                 },
                 success: function(json) {
                     if (json.status == "success") {
                         _date = json.data;
                         console.log("日期数据获取成功");
                     } else {
                         _.modal({
                             "title": "Error Tips",
                             cont: json.data
                         });
                     }

                 },
                 error: function() {
                     alert("请求错误");

                 }
             });
         }
         get_date();
         info.router_id = vm.routerId;

     });



     vm_tour_date = avalon.define("tour_date", function(vm) {
         vm.date = "";
     });

     vm_tour_people = avalon.define("tour_people", function(vm) {

         vm.adult_num = 0;
         vm.infant_num = 0;
         vm.child_num = 0;


         vm.child_1_num = 0;
         vm.child_2_num = 0;


         vm.infant_price = info.infant_price;

         vm.discount = 0;
         vm.adult_price = info.adult_price;

         vm.child_1_price = info.child_1_price;
         vm.child_2_price = info.child_2_price;


         vm.total = 0;

         vm.reduce_adult = function() {
             if (vm.adult_num <= 0) return;
             vm.adult_num--;

             info.adult_num = vm.adult_num;
             vm_tour_fees.adult_num = vm.adult_num;

             vm.total = vm.adult_num + vm.child_1_num + vm.child_2_num + vm.infant_num;
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

             vm.total = vm.adult_num + vm.child_1_num + vm.child_2_num + vm.infant_num;

             console.log("增加游客 成人");

         };

         vm.reduce_child = function() { //婴儿 和儿童

             var _this = avalon(this);
             var child_type = _this.attr("data-type");
             if (vm[child_type] <= 0) return;
             vm[child_type]--;

             info[child_type] = vm[child_type];
             vm_tour_fees[child_type] = vm[child_type];

             vm.child_num = vm.child_1_num + vm.child_2_num
             vm.total = vm.adult_num + vm.child_num + vm.infant_num;

         };
         vm.add_child = function() {
             if (info.remain == undefined || info.remain == "" || info.remain == 0) {
                 $date.trigger('focus');
                 return;
             }
             if (vm.total >= info.remain) return;

             var _this = avalon(this);
             var child_type = _this.attr("data-type");
             console.log(child_type, vm[child_type]);
             vm[child_type]++;

             console.log("pople add_child", child_type, vm[child_type]);

             info[child_type] = vm[child_type];
             vm_tour_fees[child_type] = vm[child_type];

             console.log("pople add_child", child_type, vm_tour_fees[child_type]);

             vm.child_num = vm.child_1_num + vm.child_2_num
             vm.total = vm.adult_num + vm.child_num + vm.infant_num;

         };

         vm.$watch("total", function(a, b) {


             if (a > parseInt(info.remain)) {
                 _.modal({
                     title: "输入错误",
                     cont: "购买数量大于最大剩余数量" + info.remain + "位"
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
         vm.double_room_num = 0;
         vm.triple_room_num = 0;
         vm.single_room_num = 0;
         vm.twin_room_num = 0;
         vm.single_room_difference_price = 10;

         vm.add_room_num = function() {
             var _this = avalon(this);
             var room_type = _this.attr("data-type");
             console.log(room_type, vm[room_type]);
             vm[room_type]++;

             if (room_type == "single_room_num") {
                 console.log(1);
                 vm_tour_house.difference = vm.single_room_difference_price * vm.single_room_num;
                 vm_tour_fees.single_room_num = vm.single_room_num;
             }
         };

         vm.reduce_room_num = function() {
             var _this = avalon(this);
             var room_type = _this.attr("data-type");
             console.log(room_type, vm[room_type]);
             if (vm[room_type] <= 0) return;
             vm[room_type]--;
             if (room_type == "single_room_num") {
                 vm_tour_house.difference = vm.single_room_difference_price * vm.single_room_num;
                 vm_tour_fees.single_room_num = vm.single_room_num;

             }
         };

     });

     vm_tour_room_people = avalon.define("tour_room_people", function(vm) {
         vm.list = [];
         vm.peoples = [];
         vm.focus = function() {

             if (!validate_guests()) { //第二步校验
                 console.log("第二步校验 失败");
                 return false;
             }

             var _this = avalon(this);
             var index = parseInt(_this.attr("index"));
             vm.focus_index = index;

             var type = _this.attr("data-type");
             avalon.each(vm.list, function(i, v) {
                 v.no_focus = true;
             });
             vm[type][index].no_focus = false;

         }


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


     var create_room_people_list = function() {
         vm_tour_room_people.list = [];
         var i;
         for (i = 0; i < vm_room_request.single_room_num; i++) {
             vm_tour_room_people.list.push({
                 room_type: "1",
                 guests: "",
                 no_focus: true

             });

         }

         for (i = 0; i < vm_room_request.double_room_num; i++) {
             vm_tour_room_people.list.push({
                 room_type: "2",
                 guests: "",
                 no_focus: true

             });
         }

         for (i = 0; i < vm_room_request.triple_room_num; i++) {
             vm_tour_room_people.list.push({
                 room_type: "3",
                 guests: "",
                 no_focus: true

             });
         }
         for (i = 0; i < vm_room_request.twin_room_num; i++) {
             vm_tour_room_people.list.push({
                 room_type: "4",
                 guests: "",
                 no_focus: true


             });
         }

     };

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



     var lock_order = function() { //锁定

         $page_one.find(".t-can_disabled").attr("disabled", "disabled");
         $("#reset_order").attr("disabled", false);
         $("#reserveBtn").attr("disabled", true);
     }


     var un_lock_order = function() { //解除锁定

         $page_one.find(".t-can_disabled").attr("disabled", false);
         $("#reserveBtn").attr("disabled", false);
         $("#reset_order").attr("disabled", true);

     }

     validate_one = function(data) { //第一步校对
         console.log("validate_one", data);

         if (!data.router_id) {
             _.modal({
                 "title": "Error Tips",
                 cont: "Please Choose a Tour Route"
             });
             return false;
         }

         if (!data.cur_date) {
             _.modal({
                 "title": "Error Tips",
                 cont: "Please Choose a Tour Date"
             });
             return false;
         }
         if (data.adult_num == 0) {
             _.modal({
                 "title": "Error Tips",
                 cont: "You must enter at least 1 adult"
             });
             return false;
         }

         if (data.adult_num == 1 && data.child_1_num == 1) {
             _.modal({
                 "title": "Error Tips",
                 cont: "1 adult and 1 child travel together, child must with bed"
             });
             return false;

         }
         var ro = data.room_request;


         var room_peoples_num = ro.double_room_num * 2 +
             ro.single_room_num * 1 +
             ro.triple_room_num * 3 +
             ro.twin_room_num * 2;
         var own_room_peoples_num = data.total_people - data.child_1_num - data.infant_num;

         if (room_peoples_num > own_room_peoples_num) {
             _.modal({
                 "title": "Error Tips",
                 cont: "Room capacity is greater than number of customers!"
             });
             return false;
         }

         if (room_peoples_num < own_room_peoples_num) {
             _.modal({
                 "title": "Error Tips",
                 cont: "Rooms are not enough for customers!"
             });
             return false;
         }


         completed_one = true;
         return true;
     }

     validate_guests = function(data) { //第二步 校对客人
         var data = vm_tour_guest.$model.list;
         var len;

         data = JSON.stringify(data);
         data = JSON.parse(data);
         len = data.length;
         console.log("第二步校验", data);
         var names = [];
         var leave_names = [];
         var no_infant_names = [];
         var a = 0;
         vm_tour_flight.peoples_index = [];


         for (var i = 0; i < len; i++) {

             if (data[i].g_firstname == "") {
                 _.modal({
                     "title": "Error Tips",
                     cont: "Please input First Name for Customer  #" + (i + 1) + "!"                     
                 });
                 return false;
             }

             if (!/^[A-Za-z]+$/.test(data[i].g_firstname)) {
                 _.modal({
                     "title": "Error Tips",
                     cont: "First Name Format incorrect,no space, only English letters allowed #" + (i + 1) + "!"
                 });
                 return false;
             }
             
             if (data[i].g_lastname == "") {
                 _.modal({
                     "title": "Error Tips",
                     cont: "Please input Last Name for Customer  #" + (i + 1) + "!"   
                 });
                 return false;
             }

             if (!/^[A-Za-z]+$/.test(data[i].g_lastname)) {
                 _.modal({
                     "title": "Error Tips",
                     cont: "Last Name Format incorrect,no space, only English letters and space allowed #" + (i + 1) + "!"
                 });
                 return false;
             }



             if (data[i].g_naiton == "") {
                 _.modal({
                     "title": "Error Tips",
                     cont: "Please select Citizen/PR for Customer #" + (i + 1) + "!"
                 });
                 return false;
             }


             console.log(i, data[i].g_firstname+"/"+ data[i].g_lastname);

             if (data[i].g_guestType != "2") {
                 no_infant_names.push({
                     name: data[i].g_firstname+"/"+ data[i].g_lastname,
                     // not_focus:true ,
                     checked: false,
                     index: (i + 1)
                 });
                 vm_tour_flight.peoples_index.push(i + 1);

             }
             names.push({
                 name: data[i].g_firstname+"/"+ data[i].g_lastname,
                 // not_focus:true ,
                 checked: false,
                 index: (i + 1)
             });


         }
         $.each(data,function(i,v){
            delete v.index ; 
         });

         order_info.guest_list = data;
         vm_tour_flight.peoples = no_infant_names;
         vm_tour_room_people.peoples = no_infant_names;
         return true;
     }

     validate_room_people = function() {

        var no_infant_peoples = [];

         var peoples = vm_tour_guest.$model.list;
         var total_peoples =  vm_tour_guest.$model.list; 

         $.each( peoples ,function( i,v) {
            if(v.g_guestType != "2"){
                 v.index = i ;
                 no_infant_peoples.push(v);
            }
         });

         peoples =  no_infant_peoples;


         var peoples_l = no_infant_peoples.length;
         var room_peoples = vm_tour_room_people.$model.list;
         var res; //type 的 计算结果

         function type(arr) {
             var obj = {
                 "1": 0,
                 "2": 0,
                 "3": 0,
                 "4": 0,
                 "total": 0
             };
             obj.total = arr.length;

             for (var k = 0; k < obj.total; k++) {
                 obj[total_peoples[arr[k] - 1].g_guestType]++;
             }

             return obj;
         }

         var i = 0,
             l = room_peoples.length;
         var indexs = [],
             rp;
         var total_arr = [];

         for (i; i < l; i++) {
             rp = room_peoples[i];

             if (rp.guests == "") {

                 _.modal({
                     "title": "Error Tips",
                     cont: "Please select a customer for room #" + (i + 1) + "!"
                 });
                 return false;
             }

             indexs = $.trim(rp.guests).replace(/[a-z\s\/]/ig, "").split(".")
             indexs.pop();

             // 将字符串转成数字
             $.each(function(index, val) {
                 indexs[index] = parseInt(val);
             });

             total_arr = total_arr.concat(indexs);


             if (rp.room_type == "1") {
                 if (indexs.length > 1) {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Sorry! 1 Single room maximum capacity is 1, Room ID #" + (i + 1) + "!"
                     });
                     return false;
                 }

                 if (peoples[indexs[0] - 1] && peoples[indexs[0] - 1].g_guestType != "1") {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Sorry! Child/Infant is not allowed into a single room, Room ID #" + (i + 1) + "!"
                     });
                     return false;
                 }

             }

             /*
              一间Double/Twin Room 只能住2个adult或1个adult+一个小孩(Child, with bed)或2个adult + 1个(Child, no bed);
           */


             if (rp.room_type == "2" || rp.room_type == "4") {

                 res = type(indexs);
                 if (res.total > 3) {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Sorry! 1 Double/Twin room maximum capacity is 3, Room ID #" + (i + 1) + "!"
                     });

                     return false;

                 }

                 if (res.total == 3) {

                     if (res["1"] == 3) {
                         _.modal({
                             "title": "Error Tips",
                             cont: "Sorry! Only 2 adult is allowed into a Double/Twin room, Room ID #" + (i + 1) + "!"
                         });
                         return false;
                     }

                     if (res["1"] == 2 && res["4"] == 1) {
                         _.modal({
                             "title": "Error Tips",
                             cont: "Sorry! Child (no bed) needs to stay with 2 adults, Room ID #" + (i + 1) + "!"
                         });
                         return false;
                     }

                     if (res["1"] == 1 && res["4"] == 2) {
                         _.modal({
                             "title": "Error Tips",
                             cont: "Sorry! only one Child (need bed) needs to stay with 1 adults, Room ID #" + (i + 1) + "!"
                         });
                         return false;
                     }

                     if (res["1"] == 0) {
                         _.modal({
                             "title": "Error Tips",
                             cont: "Sorry! Please select at least 1 adult, Room ID #" + (i + 1) + "!"
                         });
                         return false;
                     }

                 }

                 if (res.total == 2) {

                     if (res["1"] == 1 && res["3"] == 1) {
                         _.modal({
                             "title": "Error Tips",
                             cont: "Sorry! Child (no bed) needs to stay with 2 adults, Room ID #" + (i + 1) + "!"
                         });
                         return false;
                     }

                     if (res["1"] == 0) {
                         _.modal({
                             "title": "Error Tips",
                             cont: "Sorry! Please select at least 1 adult, Room ID #" + (i + 1) + "!"
                         });
                         return false;
                     }

                 }


                 if (res.total == 1) {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Please select a minimum of 2 customers for a Double/Twin room, Room ID  #" + (i + 1) + "!"
                     });
                     return false;
                 }




             }

             if (rp.room_type == "3") {

                 res = type(indexs);

                 if (res.total != 3) {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Please select a minimum of 2 customers for a Double/Twin room, Room ID  #" + (i + 1) + "!"
                     });
                     return false;
                 }
                 if (res[3] != 0) {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Sorry! Child (no bed) is not allowed into a Triple room, Room ID  #" + (i + 1) + "!"
                     });
                     return false;
                 }

                 if (res.total == 3) {

                     if (res[4] >= 3) {
                         _.modal({
                             "title": "Error Tips",
                             cont: "Sorry! Only 2 child is allowed into a Triple room, Room ID #" + (i + 1) + "!"
                         });
                         return false;
                     }

                 }
             }


         }



         total_arr = total_arr.sort(function(a, b) {
             return parseInt(a) >  parseInt(b)  ? 1 : -1
         }); //从小到大排序

         for (var n = 0; n < peoples_l; n++) {


             if (total_arr[n] != no_infant_peoples[n].index + 1) {
                 _.modal({
                     "title": "Error Tips",
                     cont: "Please allocate room for " + (peoples[n].g_firstname +"/"+ peoples[n].g_lastname) + "!"
                 });
                 return false;
             }
         }


         order_info.room_people = JSON.parse(JSON.stringify(vm_tour_room_people.$model));

         return true;

     }

     validate_contact = function(data) {

         // 联系人信息 校验
         var _k, _contact = vm_tour_contact.$model.contact;
         for (_k in _contact) {

             if (_contact[_k] == "") {

                 if (_k == "contactor") {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Please input Contacts Name"
                     });
                 }

                 if (_k == "mobile") {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Please input Phone Number"
                     });
                 }


                 return false;
             }
             break;
         }

         if (!/^[0-9\s\+\-\(\)]{8,15}$/.test(_contact.mobile)) {

             _.modal({
                 "title": "Error Tips",
                 cont: "Phone number should only arabic numbers,+,-,space and between 8-15"
             });
             return false;
         }

           // 联系人信息
         order_info.contact = JSON.parse(JSON.stringify(vm_tour_contact.$model.contact));
         // completed_two = true;
         console.log(JSON.stringify(order_info));
         return true;

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

     function get_remark_data() {         
         // 留言
         order_info.remark = JSON.parse(JSON.stringify(vm_tour_remark.$model));
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
                 val = val.replace(re, "");
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
                  val = val.replace(re, "");
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


     vm_tour_contact = avalon.define("tour_contact", function(vm) {
         vm.is_hide = false;
         vm.contact = {
             contactor: "",
             mobile: "",
             email: ""
         };

         vm.input = function() {
             this.value = this.value.replace(/\d/g, "");
             this.value = this.value.toLocaleUpperCase();

              vm.contact.contactor=this.value ;
         }

         vm.input_mobile = function() {
             this.value = this.value.replace(/[a-z\s]/ig, "");
              vm.contact.mobile=this.value ;

         }
          vm.input_email= function() {
              vm.contact.email=this.value ;
         }

     });

     vm_tour_remark = avalon.define("tour_remark", function(vm) {
         vm.remark = "";         
         vm.agent_reference = "";
         vm.$watch("remark", function(_new, _old) {
             // info.remark["msg"] = vm.remark;
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

     vm_tour_people.adult_price = cur_price.adult_price; //成人价格
     vm_tour_people.infant_price = cur_price.infant_price; //婴儿不占床 价格
     vm_tour_people.child_1_price = cur_price.child_1_price;
     vm_tour_people.child_2_price = cur_price.child_2_price;

     vm_tour_people.discount = cur_price.discount; //佣金折扣

     

     vm_room_request.single_room_difference_price = cur_price.single_room_difference_price;
     vm_tour_fees.single_room_difference_price = cur_price.single_room_difference_price;

     vm_tour_fees.adult_price = cur_price.adult_price; //成人价格
     vm_tour_fees.infant_price = cur_price.infant_price; //婴儿不占床 价格
     vm_tour_fees.child_1_price = cur_price.child_1_price;
     vm_tour_fees.child_2_price = cur_price.child_2_price;

     vm_tour_fees.discount = cur_price.discount; //佣金折扣
     vm_tour_fees.currency = cur_price.currency; //货币种类



     $(document.body).on("click", ".td-abled", function() {
         var _this = $(this);
         info.remain = _this.find(".date-remain").attr("remain");
         info.tourCode = _this.find(".tour-code").attr("tourCode");         
         info.cur_date = two_length(_this.attr("d")) + "/" + two_length(_this.attr("m")) + "/" + _this.attr("y");

         $("#r_time").val(info.cur_date);
         Dates.close();

         vm_tour_people.adult_num = 0;
         vm_tour_people.infant_num = 0;
         vm_tour_people.child_1_num = 0;
         vm_tour_people.child_2_num = 0;

         vm_tour_people.total = 0;

         var cur_data = _date[info.cur_date];


         // 佣金折扣

         cur_data.discount ? vm_tour_fees.discount = parseFloat(cur_data.discount) : vm_tour_fees.discount = 0;
         vm_tour_people.discount = vm_tour_fees.discount;

     });



     $(document.body).on("click", ".td-disabled", function() {
         Dates.close();
     });

     $(document.body).on("click", ".go-step1", function() {
         $page_one.fadeIn(300);
         $page_two.fadeOut(300);
         un_lock_order();
     });

     $("#inter_step3").on("click", function() {

         if (!validate_contact()) {
             return false;
         }

         if (!validate_guests()) {
             return false;
         }

         if (!validate_room_people()) {
             return false;
         }

         if (!validate_flight()) {
             return false;
         }

         $("#page_two").hide();
         $("#page_three").show();

     });


     $(".three-to-two").on("click", function() {

         $("#page_three").hide();
         $("#page_two").show();

     })

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

                     // _.modal({
                     //     "title": "提交成功",
                     //     cont: "订单数据提交成功",
                     //     close_fn: function() {
                     //         window.location.href = "./order_list.html"
                     //     }
                     // });
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
