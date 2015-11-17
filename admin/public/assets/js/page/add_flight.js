 (function() {

    var get_passengers_url = "index.php/Order/get_passengers"; // 获取所有的乘客信息
    var add_flight_url="index.php/Order/insert_flight";//写入航班信息

    var o_id = $.ynf.parse_url(window.location.href).params.id ;  



    var _ = {} ; 
    var flight_info={};

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



     //  vm_tour_flight, //航班

  
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

             
             var $arrivedates = $("input[name=g_arriveDate]") ; 
             $.each($arrivedates , function(i,v){ //重新获取准确的 日期
                vm_tour_flight.$model.list[i].g_arriveDate = $(this).val() ;
             });

             var data = vm_tour_flight.$model;
             var len;

             data = JSON.stringify(data);
             data = JSON.parse(data);
             len = data.length;
             console.log("校验", data);

             var arrive_names = [];


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
             flight_info.data = JSON.parse(JSON.stringify(data.list)); 
             flight_info.id=o_id;          

         return true;
     }


     /* 航班 */

     vm_tour_flight = avalon.define("tour_flight", function(vm) {      

         vm.list = [{             
             g_arriveDate: "",
             a_flightno: "",
             a_time: "",
             a_route: "",
             arrivedName: "",
             no_focus: true,
             checkall: false 
         }];

         vm.peoples = []; 
         vm.peoples_index = [];
         vm.selectd_index = [];
        

         vm.remove = function() {
             var _this = avalon(this);
             var index = parseInt(_this.attr("index"));
             var type = _this.attr("data-type");

             vm[type].removeAt(index);
         }

         vm.removeIt = function(perish) {
             perish();             
         };
         vm.focus_index = 0;


         vm.focus = function() {
             var _this = avalon(this);
             var index = parseInt(_this.attr("index"));
             vm.focus_index = index;
             var type = _this.attr("data-type");
             avalon.each(vm.list, function(i, v) {
                 v.no_focus = true;
             });
            vm.peoples.forEach(function(people) {
                 people.checked = false;
                 console.log("clear all")
             });            
             vm[type][index].no_focus = false;
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
            console.log(vm.$model.list);

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
 //tour_flight函数结束



        avalon.ajax({
            url:  get_passengers_url,
            type: "POST",
            dataType: "json",
            data: {o_id: o_id || 12},
            success: function(json) {
                if (json.status == "success") {
                    vm_tour_flight.peoples = json.passengers; 
                    console.log(vm_tour_flight.$model.peoples);
                    avalon.scan();                                 
                    } else {
                    _.modal({
                        "title": "Data Transfer failed!",
                        cont: json.data
                    });
                }               
            },
            error: function() {

            }
        });

         $("#add_flight").on("click", function()  {            

            if (!validate_flight()) {
                     return false;
                 }

                    console.log("最终数据", JSON.stringify(flight_info));

                     avalon.ajax({
                         url: add_flight_url,
                         type: "POST",
                         dataType: "json",
                         data: flight_info,
                         success: function(json) {
                             if (json.data.status == "success") {
                                 _.modal({
                                 "title": "Add FlightInfo Success!",
                                 cont: "Add FlightInfo Success!",
                                 close_fn: function() {
                                    window.location.href = "index.php/Order/index"
                                 }
                                 });
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
