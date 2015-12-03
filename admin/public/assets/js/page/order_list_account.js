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
            colNames: ["ID", "Invoice No","Tour Code","Company","Commission","North Rate", "Operator","Pay Amount","Payment Status","Operation"],

            colModel: [
			{
                name: 'id',
                index: 'id',
                width: 25,
                editable: false,
                // formatter: user_name_formatter,
            }, {
                name: 'order_sn',
                index: 'order_sn',
                width: 80,
                formatter:order_sn_formatter
            }, {
                name: 'tour_code',
                index: 'tour_code',
                width: 130,
                formatter:tourcode_formatter   
            }, {
				name: 'company',
                index: 'company',
                width: 200,
                formatter:company_formatter
           }, {
                name: 'commissionRate',
                index: 'commissionRate',
                width: 50,
           }, {
                name: 'northRate',
                index: 'northRate',
                width: 50,                 

            },{
                name: 'operator',
                index: 'operator',
                width: 50,
                sortable: true, 
			}, {                
                name: 'order_realSale',
                index: 'order_realSale',
                width: 60,
                sortable: false,
                editable: false, 

			},{
                name: 'paymentStatus',
                index: 'paymentStatus',
                width: 80,
                formatter: payment_status_formatter
            }, {
                name: 'oper',
                index: '',
                width: 80,
                sortable: false,
                editable: false,
                formatter: oper_formatter
			}],

            viewrecords: true,
            rowNum: 50,
            rowList: [10, 20, 30, 50],
            pager: pager_selector,
            altRows: true,
            toppager: true,

            multiselect: false,
            
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

         function tourcode_formatter(cellvalue, options, rowdata) {
            var html = [
            '<span class="blue">',rowdata.tour_code,'</span><br />',
            '<span class="orange">',rowdata.op_code,'</span><br />'
            ].join("");
             
             return html;
        } 


        function company_formatter(cellvalue, options, rowdata) {
            var html = [            
            '<span class="black">',rowdata.company_name,'</span><br />',
            '<span class="orange">',rowdata.company_tel,'</span><br />'
            ].join("");
             
             return html;
        } 



        function order_sn_formatter(cellvalue, options, rowdata) {
            var html = [
            '<span class="black">',cellvalue,'</span>'
            ].join("");
             
             return html;
        }


        function router_name_formatter(cellvalue, options, rowdata) {
               var html = [
            '<a href="#" class="blue">',cellvalue,'</a>'
            ].join("");
             
             return html;
        }

        function oper_formatter(cellvalue, options, rowdata) {
            var oper_html_arr = [];
           
            var style = 'style="font-size:16px;text-decoration:none; display:inline-block; margin-left:5px; cursor:pointer " ';

            var oper_check = ['<a ',
                ' target="_self"  href="index.php/Account/modify_payment?id=', rowdata.id, '"',
                style,
                ' class="c-green" title="Payment">Modify</a><br /><br />'

            ].join("");
            
            var mark;
            oper_html_arr.push(oper_check);  

            return oper_html_arr.join("");

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
      
        


        (function() {

            //搜索
            $("#find_btn").click(function(){ 
                var title = escape($("#title").val()); 
                var sn = escape($("#sn").val()); 
                $("#list").jqGrid('setGridParam',{ 
                    url:"do.php?action=list", 
                    postData:{'title':title,'sn':sn}, //发送数据 
                    page:1 
                }).trigger("reloadGrid"); //重新载入 
            }); 


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
            search: true,
            searchicon: 'icon-search orange',
            refreshicon: 'icon-refresh green',
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