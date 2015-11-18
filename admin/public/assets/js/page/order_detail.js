(function() {

	var order_detail_url = "/index.php/Order/get_order_detail"; // 获取日期以及机票信息

	var o_sn = $.ynf.parse_url(window.location.href).params.id ;  
     window.order_detail  ;
     var _ = {} ; 
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

       function  init_vm(){
       	 var o = order_detail ; 

       	 window.vm_tour_order = avalon.define("tour_order",function(vm){
       	 	vm.o_sn =  o.o_sn ; 
       	 	vm.router_cName =  o.router_cName ; 
       	 	vm.router_eName =  o.router_eName ; 
       	 	vm.cur_date =  o.cur_date ; 
       	 	vm.tour_code =  o.tour_code ; 
       	 	vm.is_share =  o.is_share ; 
       	 	vm.adult_fees =  o.adult_fees ; 
       	 	vm.adult_num =  o.adult_num; 
       	 	vm.adult_price =  o.adult_price ; 

       	 	vm.infant_num =  o.infant_num ; 
       	 	vm.infant_price =  o.infant_price ; 
       	 	vm.child_1_num =  o.child_1_num ;
       	 	vm.child_1_price =  o.child_1_price ;
       	 	vm.child_2_num =  o.child_2_num ;
       	 	vm.child_2_price =  o.child_2_price ;
       	 	vm.total_people =  o.total_people ;
       	 	vm.difference =  o.difference ;
       	 	vm.fees_amount =  o.fees_amount ;
       	 	vm.discount =  o.discount ;
       	 	vm.brokerage =  o.brokerage ;
       	 	vm.opNote =  o.opNote ;
       	 	vm.real_fees_amount =  o.real_fees_amount ;

       	 	avalon.each(o.guest_list,function(k , v ){
       	 		 v.order  = k+1 ;
       	 	});


       	 	vm.room_request = o.room_request ;
       	 	
            vm.people_text = "";
            vm.room_text = "";
            vm.share_text = "";

            // 拼房信息 start

            if (vm.is_share == '1') {
                vm.share_text = "Yes";
            }

            if (vm.is_share == '0') {
                vm.share_text = "No";
            }


            // 拼房信息 end

            // 人数信息 start

            if (vm.adult_num > 0) {
                vm.people_text += "Adult × " + vm.adult_num + ", ";
            }

            if (vm.infant_num > 0) {
                vm.people_text += "Infant × " + vm.infant_num + ", ";
            }

            if (vm.child_1_num + vm.child_2_num + vm.child_3_num > 0) {
                vm.people_text += "Child ×" + (vm.child_1_num + vm.child_2_num + vm.child_3_num);

            }
            // 人数信息 end


            // 房间信息 start 

            if (vm.room_request.double_room_num > 0) {
                vm.room_text += "Double × " + vm.room_request.double_room_num + ", ";
            }
            if (vm.room_request.triple_room_num > 0) {
                vm.room_text += "Triple × " + vm.room_request.triple_room_num + ", ";
            }
            if (vm.room_request.single_room_num > 0) {
                vm.room_text += "Single × " + vm.room_request.single_room_num + ", ";
            }
            if (vm.room_request.twin_room_num > 0) {
                vm.room_text += "Twin × " + vm.room_request.twin_room_num + ", ";
            }

            avalon.each(o.guest_list, function(k, v) {
                v.order = k + 1;
                if (v.g_gender == '1') {
                    v.g_gender_text = "Male";
                }
                if (v.g_gender == '2') {
                    v.g_gender_text = "Female";
                }
                if (v.g_guestType == '1') {
                    v.g_guestType_text = "Adult";
                }

                if (v.g_guestType == '2') {
                    v.g_guestType_text = "Infant";
                }
                if (v.g_guestType == '3') {
                    v.g_guestType_text = "Child(No Bed)";
                }
                if (v.g_guestType == '4') {
                    v.g_guestType_text = "Child(With Bed)";
                }
            });


            o.room_people = o.room_people || [];
            $.each(o.room_people, function(k, v) {
                if (v.room_type == '1') {
                    v.room_type_text = "Single Room";
                }

                if (v.room_type == '2') {
                    v.room_type_text = "Double Room";
                }
                if (v.room_type == '3') {
                    v.room_type_text = "Triple Room";
                }
                if (v.room_type == '4') {
                    v.room_type_text = "Twin Room";
                }

            });


            vm.guest_list = o.guest_list;
            vm.room_people = o.room_people;
            vm.flightInfo = o.flightInfo; 

            vm.contact = o.contact;

            vm.remark = o.remark;
            vm.agent_reference =  o.agent_reference;

            vm.opNote = o.opNote;
            vm.opCode = o.opCode

       	 });

        avalon.scan();

       }

		avalon.ajax({
			url:  order_detail_url,
			type: "POST",
			dataType: "json",
			data: {o_sn: o_sn || 12},
			success: function(json) {
				if (json.status == "success") {
					order_detail = json.data ;
					  init_vm();
				} else {
					_.modal({
						"title": "错误提示",
						cont: json.data
					});
				}
			},
			error: function() {

			}
		});



	

	 

})();