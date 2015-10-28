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
			sortable:true,
            sortorder: "desc",
			sortname: "a_id",
            height: 500,            
            autowidth: true,
            colNames: ["ID", "Staff Name","E-mail","Staff Type", "Status", "Operation"],

            colModel: [
			{
                name: 'a_id',
                index: 'a_id',
                width: 30,
                editable: false,
                // formatter: user_name_formatter,
			}, { 
                name: 'a_userName',
                index: 'a_userName',
                width: 130,
                editable: false,
                //formatter: company_area_formatter,
            }, { 
                name: 'a_email',
                index: 'a_email',
                width: 130,
                editable: false,
                // formatter: user_name_formatter,
            }, {
                name: 'a_type',
                index: 'a_type',
                width: 100,
				formatter: staff_type_formatter,          
            
            }, {
                name: 'a_status',
                index: 'a_status',
                width: 80,
				formatter: status_formatter,            
			},{
                name: 'oper',
                index: '',
                width: 120,
                fixed: true,
                sortable: false,
                resize: false,
                formatter: oper_formatter,
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

            var style = 'style="font-size:16px;text-decoration:none; display:inline-block; margin-left:5px; cursor:pointer " ';

            var oper_view = ['<a ',
                ' target="_self"  href="index.php/Staff/edit_staff?id=' , rowdata.a_id, '"',
                style,
                ' class="c-blue" title="Modify">Modify</a><br />'
            ].join("");
             var mark;


            oper_html_arr.push(oper_view);


            return oper_html_arr.join("");

        }




        function staff_type_formatter(cellvalue, options, rowdata) {           
            var result = '';
            switch (cellvalue) {
                case '1':
                    result = 'Manager';
                    break;
                case '2':
                    result = 'Operator';
                    break;
                case '3':
                    result = 'Accountant';
                    break;
            }

            return result;
        }

         function status_formatter(cellvalue, options, rowdata) {
            var result = '';
            switch (cellvalue) {
                case '0':
                    result = 'Frozen';
                    break;
                case '1':
                    result = 'Active';
                    break;
            }

            return result;
        }		



        

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