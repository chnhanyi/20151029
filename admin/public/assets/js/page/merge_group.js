(function() {
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





    window.south_date = {}, window.north_date = {}, window.south_north_date = {};

    $south_date = $("#south_date");
    $north_date = $("#north_date");
    $south_north_date = $("#south_north_date");
    var south_date_url = "./assets/json/south_date.json"; // 获取日期以及机票信息
    var north_date_url = "./assets/json/north_date.json"; // 获取日期以及机票信息
    var south_north_date_url = "./assets/json/south_north_date.json"; // 获取日期以及机票信息
    var addGroup_url = "./assets/json/south_north_date.json"; // 获取日期以及机票信息

    var $cur_date = $south_date;

    vm_tour_south = avalon.define("tour_south" ,function(vm){
        vm.router_id ="";
        vm.date ="";
        vm.change = function(){
            vm.router_id = this.value ; 
            if( vm.router_id == "") return ;
            get_date(south_date_url,  vm.router_id , "south");

        }

    });

    vm_tour_north = avalon.define("tour_north" ,function(vm){
        vm.router_id ="";
        vm.date ="";

        vm.change = function(){
            vm.router_id = this.value ; 
            if( vm.router_id == "") return ;
            get_date(north_date_url,  vm.router_id , "north");
        }
    });

     vm_tour_south_north = avalon.define("tour_south_north" ,function(vm){
        vm.router_id ="";
        vm.date ="";
        vm.tourCode = "";

        vm.change = function(){
            vm.router_id = this.value ; 
            if( vm.router_id == "") return ;
            get_date(south_north_date_url,  vm.router_id , "south_north");
        }
    });


    $south_date.on("focus", function() {

        $cur_date = $south_date;
        console.log(south_date);
        laydate({
            elem: '#south_laydate',
            format: "YYYY-MM-DD",
            date: south_date
        });

    });

    $north_date.on("focus", function() {

        $cur_date = $north_date;
        console.log(north_date);
        laydate({
            elem: '#north_laydate',
            format: "YYYY-MM-DD",
            date: north_date
        });

    });

     $south_north_date.on("focus", function() {
        $cur_date = $south_north_date;
        console.log(south_north_date);
        laydate({
            elem: '#north_laydate',
            format: "YYYY-MM-DD",
            date: south_north_date
        });

    });

    function two_length(s) {
        s = s || "0";
        s.length == 1 ? s = "0" + s : s = s;
        return s;
    }

    // 获取线路的日期信息  
    // url 
    // router_id 线路id
    // type "north" 或者 "south "

    function get_date(url, router_id, type) {
        avalon.ajax({
            url: url,
            type: "get",
            dataType: "json",
            async: false,
            data: {
                id: router_id,
                _v : (new Date()).getTime()
            },
            success: function(json) {
                if (json.status == "success") {
                    // console.log( type);
                    if (type == "north") {
                        window.north_date = json.data;

                    }else if(type == "south") {
                        window.south_date = json.data;

                    }else {
                        window.south_north_date = json.data;
                    }
                } else {
                    _.modal({
                        "title": "Error Tpis",
                        cont: json.data
                    });
                }

            },
            error: function() {

            }
        });
    }

    get_date(south_date_url,  1, "north");
    get_date(north_date_url , 2, "south");
    get_date(south_north_date_url , 3, "south_north");

    $(document.body).on("click", ".td-abled", function() {
        var _this = $(this);
        // info.remain = _this.find(".date-remain").attr("remain");
        // info.price = _this.find(".date-price").attr("price");
        // // info.childPrice = _this.find(".date-price").attr("childPrice");
        cur_date = two_length(_this.attr("d")) + "/" + two_length(_this.attr("m")) + "/" + _this.attr("y");

        $cur_date.val(cur_date);
        console.log($cur_date[0].id );

        var id = $cur_date[0].id  ;

        if( id == "south_date"){
            vm_tour_south.date = cur_date ;
        }else if(id == "north_date" ){
            vm_tour_north.date = cur_date ;

        }else{
            vm_tour_south_north.date = cur_date ;
        }

        Dates.close();
    });


    $(document.body).on("click", ".td-disabled", function() {
        Dates.close();
    });

    function validate_info(info){

        info = info || {};

        if( info.tour_north.router_id == ""){
              _.modal({
                        "title": "Error Tips",
                        cont: json.data
                    });
                return false ;

        }
        return true  ;
    }

    // 提交数据 start

    $addGroup = $("#addGroup");

    $addGroup.click(function (argument) {

        var info = {};

        info.tour_north = JSON.parse(JSON.stringify(vm_tour_north.$model) );
        info.tour_south = JSON.parse(JSON.stringify(vm_tour_south.$model) );
        info.tour_south_north = JSON.parse(JSON.stringify(vm_tour_south_north.$model) );

        if(!validate_info(info) ){
            return false ;
        }
         avalon.ajax({
            url: addGroup_url,
            type: "get",
            dataType: "json",
            async: false,
            data:  info,
            success: function(json) {
                if (json.status == "success") {

                    alert("提交成功")
                   
                } else {
                    _.modal({
                        "title": "Error Tips",
                        cont: json.data
                    });
                }

            },
            error: function() {

            }
        });
    });

    // 提交数据 end






})();
