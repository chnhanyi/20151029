 (function() {

    var confirm_invoice_url = "index.php/Order/confirm_invoice"; // 获取所有的乘客信息
    var add_invoice_url="index.php/Order/update_invoice";//写入航班信息

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


     /* 发票信息*/
     function init_vm(){
                 var i=invoice_data;   

                 window.vm_invoice = avalon.define("invoice", function(vm) {                     
                     vm.company_name=i.a_name;
                     vm.company_address=i.address;

                     vm.invoice_no=i.o_sn;
                     vm.create_date=i.create_date;
                     vm.tour_date=i.tour_date
                     vm.op_name=i.opname;

                     vm.tour_code=i.tour_code;
                     vm.reference=i.reference;
                     vm.agent_name=i.s_name;
                     vm.c_name=i.cName;
                     vm.e_name=i.eName;

                     vm.invoice_list=i.invoice_list;                     

                     vm.orderAmount=parseFloat(i.orderAmount).toFixed(2);
                     vm.delayAmount=parseFloat(i.delayAmount).toFixed(2);
                     vm.currency=i.currency;

                     vm.list = i.extra_list;
                     if(vm.list!=null){
                          for(var n=0;n<vm.list.length;n++){                                                                                                                      
                             vm.orderAmount=parseFloat(vm.orderAmount)+parseFloat(vm.list[n].total);
                             vm.delayAmount=parseFloat(vm.delayAmount)+parseFloat(vm.list[n].total);
                           }
                             vm.orderAmount=parseFloat(vm.orderAmount).toFixed(2);
                             vm.delayAmount=parseFloat(vm.delayAmount).toFixed(2);
                       }


                     vm.remove = function() {
                             var _this = avalon(this);
                             var index = parseInt(_this.attr("index"));
                             var type = _this.attr("data-type");

                             vm[type].removeAt(index);

                             vm.blur_total();
                     }

                     vm.add_list_item = function() {
                                        if(vm.list==null){
                                            vm.list=[{
                                                     item: "",
                                                     name: "", 
                                                     price: "",
                                                     unit: "",
                                                     total:""
                                                    }];
                                        }else{
                                               vm.list.push({
                                                     item: "",
                                                     name: "", 
                                                     price: "",
                                                     unit: "",
                                                     total:""
                                                 });   

                                        }
                                                    
                        }

                     vm.fn_item = function() {
                         var _this = this;                         
                         var index = parseInt(avalon(this).attr("index"));
                         vm.list[index].item = _this.value;
                     }

                    vm.fn_total = function() {
                         var _this = this;                         
                         var index = parseInt(avalon(this).attr("index"));
                         vm.list[index].total = _this.value;                       
                     }

                     vm.blur_total=function() {
                            vm.orderAmount=parseFloat(i.orderAmount).toFixed(2);
                            vm.delayAmount=parseFloat(i.delayAmount).toFixed(2);                         
                         for(var n=0;n<vm.list.length;n++){
                           if(isNaN(vm.list[n].total)==false&&vm.list[n].total!=""){                                                                                            
                             vm.orderAmount=parseFloat(vm.orderAmount)+parseFloat(vm.list[n].total);
                             vm.delayAmount=parseFloat(vm.delayAmount)+parseFloat(vm.list[n].total);
                             vm.orderAmount=vm.orderAmount.toFixed(2);
                             vm.delayAmount=vm.delayAmount.toFixed(2);                         
                           }
                         } 
                         console.log(vm.list.$model);
                     }


                    });


                    avalon.scan(); 
    }
            //vm_invioce函数结束

        //校对发票信息
             validate_invoice = function(data) {           

             var data = vm_invoice.$model;                       

             data = JSON.stringify(data);
             data = JSON.parse(data);
             
             console.log("校验", data.list);            
            
             for (var m= 0; m< data.list.length; m++) {                 

                 if ($.trim(data.list[m].item) == "") {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Please Input Item, Row #" + (m + 1) + "!"
                     });
                     return false;
                 }

                 if ($.trim(data.list[m].total) == "") {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Please input Amount, Row " + (m + 1) + "!"
                     });
                     return false;
                 }


                 if (data.list[m].total != "" && !/^[-]?\d{1,}(?:\.\d{1,2})?$/.test(data.list[m].total)) {
                     _.modal({
                         "title": "Error Tips",
                         cont: "Amount Format incorrect, only Arabic numerals and . allowed, Row #" + (m+ 1) + "!"
                     });
                     return false;
                 }else {
                     console.log(data.list[m]);
                 } 
                 }
                invoice_info.o_id=o_id;    
                invoice_info.data = JSON.parse(JSON.stringify(data));                 
                
         return true;
     }



        avalon.ajax({
            url:  confirm_invoice_url,
            type: "POST",
            dataType: "json",
            data: {o_id: o_id || 12},
            success: function(json) {
                if (json.status == "success") {
                    invoice_data = json.data;                     
                    init_vm();
                    //console.log(vm_invoice.$model);                                                    
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

         $("#add_invoice").on("click", function()  {            

            if (!validate_invoice()) {
                     return false;
                 }

                    console.log("最终数据", JSON.stringify(invoice_info));

                     avalon.ajax({
                         url: add_invoice_url,
                         type: "POST",
                         dataType: "json",
                         data: invoice_info,
                         success: function(json) {
                             if (json.status == "success") {
                                     _.modal({
                                     "title": "Comfirm Invoice Success!",
                                     cont: "Comfirm Invoice Success!",
                                     close_fn: function() {
                                       window.location.href = "index.php/Order/invoice_print?id="+o_id;                                       
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
