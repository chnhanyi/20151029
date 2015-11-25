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
                root: "data",
                page: "currentPage",
                total: "totalPages",
                records: "totalRecords",
                repeatitems: false
            },
            datatype: "json",
            sortorder: "desc",
            height: 500,            
            autowidth: true,
            colNames: ["ID", "Tour Code","Tour Date","Promotion","Capacity","Vacancy","Coach RESV","Current Pax","Room RESV","Current Rooms","Operator","Operation"],

            colModel: [
			{
                name: 't_id',
                index: 't_id',
                width: 20,
                editable: false,
                // formatter: user_name_formatter,
			}, { 
                name: 't_tourCode',
                index: 't_tourCode',
                width: 120,
                editable: false,
                // formatter: user_name_formatter,
            }, { 
                name: 't_date',
                index: 't_date',
                width: 60,
                editable: false,
                // formatter: user_name_formatter,
            }, {
                name: 't_promo',
                index: 't_promo',
                width: 50,
				formatter: t_promo_formatter,
            }, {
                name: 't_capacity',
                index: 't_capacity',
                width: 50,
                align: "center",
            }, {
                name: 't_vacancy',
                index: 't_vacancy',
                width:50,
				align: "center",
				formatter: vacancy_formatter,
            }, {
                name: 't_bus',
                index: 't_bus',
                width: 60
			}, {
                name: 't_currentpax',
                index: 't_currentpax',
                width: 100,
				formatter: t_currentpax_formatter,
            }, {
                name: 't_room',
                index: 't_room',
                width: 80,

			}, {
                name: 't_currentroom',
                index: 't_currentroom',
                width: 80,
				formatter: t_currentroom_formatter,
			}, {
                name: 'a_userName',
                index: 'a_userName',
                width:50,               
			},{
                name: 'oper',
                index: '',
                width: 120,
                fixed: true,
                formatter: oper_formatter,
            }],

            viewrecords: true,
            rowNum: 200,
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
            // 导出航班信息表，导出酒店信息表
			var style = 'style="font-size:16px;text-decoration:none; display:inline-block; margin-left:5px; cursor:pointer " ';  		
		                

            var oper_modify = ['<a ',
                ' target="_self"  href="index.php/Group/edit_group?id=', rowdata.t_id, '"',
                style,
                ' class="c-orange" title="modify">Modify</a><br /><br />'

            ].join("");		
                        
			var oper_flight = ['<a ',
                ' target="_blank"  href="index.php/Table/tour_guide?id=', rowdata.t_id, '"',
                style,
                ' class="c-blue" title="To Tour Guide">Tour Guide List</a><br /><br />'
            ].join("");
            
            //var mark;                      

            var oper_hotel = ['<a ',
                ' target="_blank"  href="index.php/Table/hotel?id=', rowdata.t_id, '"',
                style,
                ' class="c-orange" title="To Hotel">Room List</a><br /><br />'

            ].join("");
			
            oper_html_arr.push(oper_modify); 
			oper_html_arr.push(oper_flight);  
            oper_html_arr.push(oper_hotel);
          
            return oper_html_arr.join("");

        }
		
		function vacancy_formatter(cellvalue, options, rowdata) {
               var html = [            
            '<span class="blue">',rowdata.t_vacancy,'</span><br />'
            ].join("");
             
             return html;
          }


         function t_currentpax_formatter(cellvalue, options, rowdata) {
               var html = [
            '<span class="red">Total ',rowdata.totalNum,'</span><br />',
            '<span class="black">Adult ',rowdata.adultNumber,'</span><br />',
            '<span class="orange">Tnfant ',rowdata.infantNumber,'</span><br />',
            '<span class="blue">Child(With Bed)',rowdata.childNumber1,'</span><br />',
            '<span class="blue">Child(No Bed)',rowdata.childNumber2,'</span><br />'
            ].join("");
             
             return html;
          }
		  
		 function t_currentroom_formatter(cellvalue, options, rowdata) {
               var html = [
			'<span class="red">Total ',rowdata.total_rooms,'</span><br />',
            '<span class="black">Twin ',rowdata.twin_num,'</span><br />',
            '<span class="orange">Double ',rowdata.double_num,'</span><br />',
            '<span class="blue">Triple ',rowdata.triple_num,'</span><br />',
            '<span class="orange">Single ',rowdata.single_num,'</span><br />'
            ].join("");
             
             return html;
          }


        function t_promo_formatter(cellvalue, options, rowdata) {
            // 旅游团状态， 1.正常团，2.促销团
            var result = '';
            switch (cellvalue) {
                case '1':
                    result = '<span class="black">Regular</span>';
                    break;
                case '2':
                    result = '<span class="orange">Special</span>';
                    break;
            }
            return result;
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