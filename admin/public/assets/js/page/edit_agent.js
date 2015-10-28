(function() {

	var edit_agent_url = "index.php/Agent/get_agent"; // 获取agent员工的信息

	var s_id = $.ynf.parse_url(window.location.href).params.id ;  
     window.edit_agent  ;
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
       	 var e = edit_agent ; 

       	 window.vm_edit_agent = avalon.define("edit_agent",function(vm){
       	 	vm.s_id =  e.s_id ; 
       	 	vm.a_id =  e.a_id ; 
       	 	vm.s_name =  e.s_name ; 			
       	 	vm.s_email =  e.s_email ;        	 			
       	 	vm.a_status =  e.a_status ;  
       	 });

        avalon.scan();

       }

		avalon.ajax({
			url:  edit_agent_url,
			type: "POST",
			dataType: "json",
			data: {s_id: s_id || 12},
			success: function(json) {
				if (json.status == "success") {
					edit_agent = json.data;
					  init_vm();				
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

	

	 

})();