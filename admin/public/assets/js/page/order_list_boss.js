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
            rowNum: 50,
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
                root: "data",
                page: "currentPage",
                total: "totalPages",
                records: "totalRecords",
                repeatitems: false
            },
            datatype: "json",
            sortorder: "desc",
            height: 500,
            caption: "Order List",
            autowidth: true,
            colNames: ["Boook Time","Invoice No","Agent","Tour Code","Total Pax", "Nett", "Status", "Operator"],

            colModel: [
			{
                name: 'bookingTime',
                index: 'bookingTime',
                width: 100,  
            }, {
                name: 'order_sn',
                index: 'order_sn',
                width: 80,
                formatter:order_sn_formatter
            }, {
				name: 'company_name',
                index: 'company_name',
                width: 100,               
            }, {
                name: 'tour_code',
                index: 'tour_code',
                width: 100,                
            }, {
                name: 'total_guests',
                index: 'total_guests',
                width: 60,
                sortable: false,
                editable: false,
                align:'center',                
			}, {                
                name: 'order_amount',
                index: 'order_amount',
                width: 60,
                sortable: false,
                editable: false, 
            },{
                name: 'order_status',
                index: 'order_status',
                width: 70,
                sortable: true,
                formatter: order_status_formatter
			},{
                name: 'operator',
                index: 'operator',
                width: 60,
            }],

            viewrecords: true,
            rowNum: 50,
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
           
            var style = 'style="font-size:16px;text-decoration:none; display:inline-block; margin-left:5px; cursor:pointer " ';

            var oper_check = ['<a ',
                ' target="_self"  href="./index.php/Boss/check_order?id=', rowdata.id, '"',
                style,
                ' class="c-green" title="Order">Details</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'

            ].join("");

            var invoice_print = ['<a ',
                ' target="_blank"  href="./index.php/Boss/invoice_print?id=', rowdata.id, '"',
                style,
                ' class="c-red" title="Invoice">Invoice</a>'

            ].join("");  


            oper_html_arr.push(oper_check); 
            oper_html_arr.push(invoice_print);
  

            return oper_html_arr.join("");            

        }

        function order_sn_formatter(cellvalue, options, rowdata) {
            var html = [
            '<span class="blue">',cellvalue,'</span>'
            ].join("");
             
             return html;
        }


        function tour_name_formatter(cellvalue, options, rowdata) {
               var html = [
            '<span class="orange">',cellvalue,'</span>'
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
            refresh: true,
            search: false,
            refreshicon: 'icon-refresh green',
            view: true,
            viewicon: 'icon-zoom-in grey',
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