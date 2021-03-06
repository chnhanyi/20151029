 (function() {


    jQuery(function($) {
        var grid_selector = "#ynf_list",
            pager_selector = "#ynf_list_pager",
            ynf_table_nav = "#ynf_table_nav",
            ynf_modal_list_pager_selector ="#ynf_modal_list_pager",
            $ynf_list = $(grid_selector),
            $ynf_list_pager = $(pager_selector),
            $ynf_table_nav = $(ynf_table_nav),
            _ynf_c = YNF_CONFIG;
           table = _ynf_c.table;

         var defaults_grid_args = {
            viewrecords: true,
            rowNum: 20,
            rowList: [10, 20, 30, 50],
            pager: ynf_modal_list_pager_selector,
            altRows: true,
            // toppager: true,
            bottompager:true,
            multiselect: true,
            multiboxonly: true,
            gridComplete: function(json) {},
            loadError: function(json) {},
            loadBeforeSend: function(json) {},
            beforeProcessing: function(json) { //渲染数据前
            },
            onHeaderClick: function(json) {},
            loadComplete: function(json) {
                var table = this;
                setTimeout(function() {
                    updateActionIcons(table);
                    updatePagerIcons(table);
                    enableTooltips(table);
                }, 0);
            }
         }

        $ynf_list.jqGrid({
            url: table.url,
            editurl: table.editurl, //nothing is saved
            jsonReader: {
                root: "data.data",
                page: "data.currentPage",
                total: "data.totalPages",
                records: "data.totalRecords",
                repeatitems: false
            },
            datatype: "json",
            sortorder: "desc",
            height: 500,
            caption: "Order List",
            autowidth: true,
            colNames: ["ID", "Invoice No","Dept notice","User Name","My Reference", "Tour Name","Tour Date","Total Pax", "Total Rooms","Nett", "Order Status" ,"Operator", "Payment Status","Operation"],

            colModel: [
			{
                name: 'id',
                index: 'id',
                width: 30,
                editable: false,
                search:false,
                // formatter: user_name_formatter,
            }, {
                name: 'order_sn',
                index: 'order_sn',
                width: 60,
                formatter:order_sn_formatter
			}, { 
                name: 'd_notice',
                index: 'd_notice',
                width: 100,
                editable: false,
                search:false,
                // formatter: user_name_formatter,
			}, { 
                name: 'user',
                index: 'user',
                width: 60,
                editable: false,                
                // formatter: user_name_formatter,
            }, {
                name: 'agent_reference',
                index: 'agent_reference',
                width: 80,
                align: "center",
            }, {
                name: 'tour_name',
                index: 'tour_name',
                width: 150,
                search:false,
                formatter: tour_name_formatter,
            
            }, {
                name: 'tour_date',
                index: 'tour_date',
                width: 80
            }, {
                name: 'total_guest',
                index: 'total_guest',
                width: 100,
                sortable: false,
                editable: false,
                search:false,
                formatter: total_people_formatter
            }, {
                name: 'total_room',
                index: 'total_room',
                width: 80,
                search:false,                
                formatter: total_room_formatter
			}, {                
                name: 'order_amount',
                index: 'order_amount',
                width: 60,
                sortable: false,
                editable: false,
                search:false,
                // formatter:money_paid_formatter
            },{
                name: 'order_status',
                index: 'order_status',
                width: 60,
                sortable: true,
                formatter: order_status_formatter
			},{
                name: 'op_name',
                index: 'op_name',
                width: 60
            }, {
                name: 'payment_status',
                index: 'payment_status',
                width: 60,
                sortable: true,
                formatter: payment_status_formatter
			},{
                name: 'oper',
                index: '',
                width: 120,
                fixed: true,
                sortable: false,
                resize: false,
                search:false,
                formatter: oper_formatter
            }],

            viewrecords: true,
            rowNum: 20,
            rowList: [10, 20, 30, 50],
            pager: pager_selector,
            altRows: true,
            toppager: true,

            multiselect: true,
            multiboxonly: true,
            gridComplete: function() {
            	//已分配 数量 和 已清分数量不等时，标粉 
            	 // afterCompleteFunction();
            	},
            loadError: function(json) {},
            loadBeforeSend: function(json) {},
            beforeProcessing: function(json) { //渲染数据前
            },
            onHeaderClick: function(json) {},

            loadComplete: function(json) {
                var table = this;
                setTimeout(function() {

                    updateActionIcons(table);
                    updatePagerIcons(table);
                    enableTooltips(table);
                }, 0);
            }


        });

        function oper_formatter(cellvalue, options, rowdata) {
            var oper_html_arr = [];
            // 订单状态，订单状态，1待付款，2待发货，3待收货，4待评价，5交易完成；-1已取消，-2已过期
            var style = 'style="font-size:16px;text-decoration:none; display:inline-block; margin-left:5px; cursor:pointer " ';

            var oper_view = ['<a ',
                ' target="_blank"  href="/index.php/Order_show/show_order_detail?o_sn=', rowdata.id, '"',
                style,
                ' class="c-blue" title="查看订单">View Details</a><br />'
            ].join("");
             var mark;
            
            


            oper_html_arr.push(oper_view);


            return oper_html_arr.join("");

        }

        function order_sn_formatter(cellvalue, options, rowdata) {
            var html = [
            '<span class="black">',cellvalue,'</span>'
            ].join("");
             
             return html;
        }


        function tour_name_formatter(cellvalue, options, rowdata) {
               var html = [
            '<span class="black">',rowdata.tour_cName,'</span><br />',
            '<span class="orange">',rowdata.tour_eName,'</span><br />'
            ].join("");
             
             return html;
        }

         function total_people_formatter(cellvalue, options, rowdata) {
               var html = [
            '<span class="black">Total ',rowdata.total_guests,'</span><br />',
            '<span class="orange">Adult ',rowdata.adult_num,'</span><br />',
            '<span class="black">Tnfant ',rowdata.infant_num,'</span><br />',
            '<span class="orange">Child(No Bed) ',rowdata.child_num1,'</span><br />',
            '<span class="blue">Child(With Bed) ',rowdata.child_num2,'</span><br />'
            ].join("");
             
             return html;
          }

          function total_room_formatter(cellvalue, options, rowdata) {
               var html = [
            '<span class="black">Total ',rowdata.total_room,'</span><br />',
            '<span class="orange">Twin ',rowdata.twin_num,'</span><br />',
            '<span class="black">Single ',rowdata.single_num,'</span><br />',
            '<span class="orange">Double ',rowdata.double_num,'</span><br />',
            '<span class="blue">Triple ',rowdata.triple_num,'</span><br />'
            ].join("");
             
             return html;
          }


        function order_status_formatter(cellvalue, options, rowdata) {
            // 订单状态， 已提交，等待op确认、 已经确认
            var result = '';
            switch (cellvalue) {
                case '1':
                    result = '<span class="red">Pending</span>';
                    break;
                case '2':
                    result = '<span class="orange">Processing</span>';
                    break;
                case '3':
                    result = '<span class="green">Confirmed</span>';
                    break;
                case '4':
                    result = '<span class="red">Terminated</span>';
                    break;
            }

            return result;
        }

         function payment_status_formatter(cellvalue, options, rowdata) {
            // 订单状态， 准时付款、延迟付款、超时未付款
            var result = '';
            switch (cellvalue) {
                case '1':
                    result = '<span class="red">Unpaying</span>';
                    break;
                case '2':
                    result = '<span class="green">On time</span>';
                    break;
                case '3':
                    result = '<span class="orange">Delay</span>';
                    break;
				case '4':
                    result = '<span class="grey">Overdue</span>';
                    break;
            }

            return result;
        }

        

        function time_createuser_formatter(cellvalue, options, rowdata) {
            if (cellvalue != "0")
                return '<span class="ynf-f-s c-green ">' + rowdata.createtime + '</span> <br/> <span class="c-red">' + rowdata.createuser + '</span>';
            else
                return '无数据';
        }

        function time_lastupdateuser_formatter(cellvalue, options, rowdata) {
            if (cellvalue != "0")
                return '<span class="ynf-f-s c-green " >' + rowdata.lastupdatetime + '</span> <br/> <span class="c-blue" >' + rowdata.lastupdateuser + '</span>';
            else
                return '无数据';
        }
        
        function pay_time_formatter(cellvalue, options, rowdata){
        	if(rowdata.pay_time != null){
        		return rowdata.pay_time;
        	}else{
        		return "无数据";
        	}
        }
        
        //对discount_amount进行判空处理 zhangyu 2015-01-05
        function discount_amount_formatter(cellvalue, options, rowdata){
        	if(rowdata.discount != null){
        		return rowdata.discount;
        	}else{
        		return 0;
        	}
        }
        
        //对money_paid进行判空处理 zhangyu 2015-01-05
        function money_paid_formatter(cellvalue, options, rowdata){
        	if(rowdata.money_paid != null){
        		return rowdata.money_paid;
        	}else{
        		return 0;
        	}
        }

        (function() {


            // 配置表导航过滤搜索  
            $.ynf.init_single_filter({
                empty_datepicker: true
            });

            // 配置表导航时间范围搜索  
            $.ynf.init_time_filter();

            // 下拉框 条件选择搜索
            $.ynf.init_single_search({
                special: {
                    user_name: "cn",
                    order_sn: "cn",
                    consignee:"cn"
                }
            });

        })();


        $.ynf.init_close_order({
            $el: $ynf_list,
            body_cont: "<span class='c-red'> 你确定取消订单吗？</span>",
            success: function(json) {
                if (json.code && json.code == 200) {
                    $ynf_list.trigger('reloadGrid');
                }
            }

        });
        

        $.ynf.init_quick_oper_list({
            $el: $ynf_list,
            target:".mark",
            success: function(json) {
              if (json && json.code == 200) {
                $ynf_list.trigger("reloadGrid");
              } else {
                $.ynf.small_modal({
                  body_cont: json.msg,
                  show: true
                });
              }
            }
          });


        //navButtons
        jQuery(grid_selector).jqGrid('navGrid', pager_selector, { //navbar options
            edit: false,
            add: false,
            del: false,
            refresh: false,
            search: true,
            searchicon: 'icon-search orange', 
            view: false,
        }, {
            recreateForm: true,
            beforeShowForm: function(e) {
                var form = $(e[0]);
                form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
            }
        });
    });

    // unlike navButtons icons, action icons in rows seem to be
    // hard-coded
    // you can change them like this in here if you want
    function updateActionIcons(table) {

        var replacement = {
            'ui-icon-pencil': 'icon-pencil blue',
            'ui-icon-trash': 'icon-trash red',
            'ui-icon-disk': 'icon-ok green',
            'ui-icon-cancel': 'icon-remove red'
        };
        $(table).find('.ui-pg-div span.ui-icon').each(function() {
            var icon = $(this);
            var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
            if ($class in replacement) icon.attr('class', 'ui-icon ' + replacement[$class]);
        })

    }

    // replace icons with FontAwesome icons like above
    function updatePagerIcons(table) {
        var replacement = {
            'ui-icon-seek-first': 'icon-double-angle-left bigger-140',
            'ui-icon-seek-prev': 'icon-angle-left bigger-140',
            'ui-icon-seek-next': 'icon-angle-right bigger-140',
            'ui-icon-seek-end': 'icon-double-angle-right bigger-140'
        };
        $('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function() {
            var icon = $(this);
            var $class = $.trim(icon.attr('class').replace('ui-icon', ''));

            if ($class in replacement) icon.attr('class', 'ui-icon ' + replacement[$class]);
        })
    }

    function enableTooltips(table) {
        $('.navtable .ui-pg-button').tooltip({
            container: 'body'
        });
        $(table).find('.ui-pg-div').tooltip({
            container: 'body'
        });
    }
    
    function afterCompleteFunction(){
    	var grid_selector = "#ynf_list",
        $ynf_list = $(grid_selector);
    	var ids = $ynf_list.jqGrid("getDataIDs");
    	var rowDatas = $ynf_list.jqGrid("getRowData");
    	for(var i=0;i < rowDatas.length;i++){
	    	var rowData = rowDatas[i];
	    	var oper = rowData.oper;	 
	    	if(oper.indexOf("取消标记")>=0){
	    		$("#"+ids[i]).css("background-color","#c7d3a9");//--------(1)
	    	}
    	}
    	return true;
    }



})();