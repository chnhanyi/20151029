(function() {

    var tourguide_detail_url = "/index.php/Group/get_tourguide_list"; // 获取订单信息和房间信息   

    var t_id = $.ynf.parse_url(window.location.href).params.id;
    window.tourguide_detail;
    var _ = {};
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

    function init_vm() {
        var t = tourguide_detail;

        window.vm_tourguide_list = avalon.define("tourguide_list", function(vm) {
			vm.date= t.date; 
            vm.cName = t.cName; 
            vm.eName = t.eName; 
            vm.tour_code = t.tour_code; 
            vm.adult_num = t.adult_num; 
            vm.infant_num = t.infant_num;
            vm.child_1_num = t.child_1_num;
            vm.child_2_num = t.child_2_num;
            vm.total_people = t.total_people;

            vm.single = t.single;
            vm.doubleroom = t.doubleroom;
            vm.twin = t.twin;
            vm.triple = t.triple;          
            

            vm.people_text = "";
            vm.room_text = "";

             // 本团的人数信息 start

            if (vm.adult_num > 0) {
                vm.people_text += "Adult × " + vm.adult_num + ", ";
            }

            if (vm.infant_num > 0) {
                vm.people_text += "Infant × " + vm.infant_num + ", ";
            }

            if (vm.child_1_num  > 0) {
                vm.people_text += "Child(no bed) ×" + vm.child_1_num + ", ";
            }

            if (vm.child_2_num  > 0) {
                vm.people_text += "Child(with bed) ×" + vm.child_2_num;
            }


            // 本团的人数信息 end


            // 本团的房间信息 start 

            if (vm.doubleroom > 0) {
                vm.room_text += "Double × " + vm.doubleroom + ", ";
            }
            if (vm.triple > 0) {
                vm.room_text += "Triple × " + vm.triple + ", ";
            }
            if (vm.single > 0) {
                vm.room_text += "Single × " + vm.single + ", ";
            }
            if (vm.twin > 0) {
                vm.room_text += "Twin × " + vm.twin;
            }

            // 本团的房间信息 end


            //遍历本团的所有订单
            t.order = t.order || [];
            $.each(t.order, function(k, v) {

                            
                           vm.doubleroom = v.roomInfo.doubleroom;
                           vm.twin = v.roomInfo.twin;
                           vm.triple = v.roomInfo.triple;
                           vm.single = v.roomInfo.single; 

                    vm.room_order_text = "";

                    if (vm.doubleroom> 0) {
                        vm.room_order_text += "Double × " + vm.doubleroom + ", ";
                    }
                    if (vm.triple > 0) {
                        vm.room_order_text += "Triple × " + vm.triple + ", ";
                    }
                    if (vm.single > 0) {
                        vm.room_order_text += "Single × " + vm.single + ", ";
                    }
                    if (vm.twin > 0) {
                        vm.room_order_text += "Twin × " + vm.twin;
                    }
                                        
                                        avalon.each(v.guest_list, function(k, e) {                                            
                                            if (e.g_gender == '1') {
                                                e.g_gender_text = "Male";
                                            }
                                            if (e.g_gender == '2') {
                                                e.g_gender_text = "Female";
                                            }
                                            if (e.g_guestType == '1') {
                                                e.g_guestType_text = "Adult";
                                            }

                                            if (e.g_guestType == '2') {
                                                e.g_guestType_text = "Infant";
                                            }
                                            if (e.g_guestType == '3') {
                                                e.g_guestType_text = "Child(No Bed)";
                                            }
                                            if (e.g_guestType == '4') {
                                                e.g_guestType_text = "Child(With Bed)";
                                            }
                                        });
                    
                    vm.contact= v.contact;
                    vm.guest_list= v.guest_list;
                    vm.flightInfo = v.flightInfo;

                     vm.order_list=t.order;
            });
               


         });



        avalon.scan();


    }



    $.ajax({
        url: tourguide_detail_url,
        type: "POST",
        dataType: "json",
        data: {
            t_id: t_id
        },
        success: function(json) {

            console.log(json);
            if (json.status == "success") {
                tourguide_detail= json.data;

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