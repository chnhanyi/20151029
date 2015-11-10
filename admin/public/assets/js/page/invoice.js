 (function() {

    var confirm_invoice_url="index.php/Order/confirm_invoice";//写入航班信息

    var o_id = $.ynf.parse_url(window.location.href).params.id ;  



    var _ = {} ; 
    var invoice_info={};    

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

  
     var create_invoice_list = function() {
         vm_invoice.list = [{
             i_item: "",
             i_price: ""             
         }];
     }


     /* 发票信息 */

     vm_invoice = avalon.define("invoice", function(vm) {         
         vm.list = [{                       
             i_item: "",
             i_price: ""
         }];

         vm.visible=0;
         vm.date = $('#date').val();                     

         vm.remove = function() {
             var _this = avalon(this);
             var index = parseInt(_this.attr("index"));
             var type = _this.attr("data-type");

             vm[type].removeAt(index);
         }



         function render_flightInfo(dom, data) {
             var $flight_list_wrap = $(dom).parent();
             $flight_list_wrap.find(".flight_list").removeClass("hide");

         }

         vm.fn_i_item = function() {
             var _this = this;
             var index = parseInt(avalon(this).attr("index"));
             vm.list[index].i_item = _this.value;
         }

         vm.fn_i_price = function() {
             var _this = this;
             var index = parseInt(avalon(this).attr("index"));
             vm.list[index].i_price= _this.value;
         }
         
         vm.add_list_item = function() {
            console.log(vm.$model.list); 
                if(vm.$model.list[0].i_item==""){
                      vm.visible=1;
                }else{
                  vm.list.push({
                    i_item: "",
                    i_price: ""  
                 });  
                }               
                 
             }


     });
 //tour_flight函数结束


         $("#confirm_invoice").on("click", function()  {                           
                   var data = vm_invoice.$model;
                   invoice_info.data = JSON.parse(JSON.stringify(data.list)); 
                   invoice_info.date = $('#date').val();  
                   invoice_info.noprepay = $('#noprepay').val(); 
                   invoice_info.prepay = $('#prepay').val();  

                    console.log("最终数据", JSON.stringify(invoice_info));

                     avalon.ajax({
                         url: confirm_invoice_url,
                         type: "POST",
                         dataType: "json",
                         data: invoice_info,
                         success: function(json) {
                             if (json.data.status == "success") {
                                 _.modal({
                                 "title": "Confirm Invoice Success!",
                                 cont: "Confirm Invoice Success!",
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
