(function() {

    var room_detail_url = "/index.php/Group/get_room_list"; // 获取订单信息和房间信息   

    var t_id = $.ynf.parse_url(window.location.href).params.id;
    window.room_detail;
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
        var r = room_detail;

        window.vm_room_list = avalon.define("room_list", function(vm) {
			vm.tour_code = r.tour_code; 
            vm.adult_num = r.adult_num; 
            vm.infant_num = r.infant_num;
            vm.child_1_num = r.child_1_num;
            vm.child_2_num = r.child_2_num;
            vm.total_people = r.total_people;

            vm.single = r.single;
            vm.doubleroom = r.doubleroom;
            vm.twin = r.twin;
            vm.triple = r.triple;

            vm.people_text = "";
            vm.room_text = "";

            // 人数信息 start

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


            // 人数信息 end


            // 房间信息 start 

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

            // 房间信息 start end


            r.room_people = r.room_people || [];
            $.each(r.room_people, function(k, v) {
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

            vm.room_people = r.room_people;

        });

        avalon.scan();


    }



    $.ajax({
        url: room_detail_url,
        type: "POST",
        dataType: "json",
        data: {
            t_id: t_id
        },
        success: function(json) {

            console.log(json);
            if (json.status == "success") {
                room_detail= json.data;

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