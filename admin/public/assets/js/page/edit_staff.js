(function() {

	var edit_staff_url = "index.php/Staff/get_staff"; // 获取代理公司的信息

	var a_id = $.ynf.parse_url(window.location.href).params.id ;  
     window.edit_staff  ;
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
       	 var e = edit_staff ; 

       	 window.vm_edit_staff = avalon.define("edit_staff",function(vm){
       	 	vm.a_id =  e.a_id ;        	 	
       	 	vm.a_userName =  e.a_userName ; 
			vm.a_email =  e.a_email ;        	 			
       	 	vm.a_type =  e.a_type ; 
       	 	vm.a_status = e.a_status ;   
       	 });

        avalon.scan();

       }

		avalon.ajax({
			url:  edit_staff_url,
			type: "POST",
			dataType: "json",
			data: {a_id: a_id || 12},
			success: function(json) {
				if (json.status == "success") {
					edit_staff = json.data;
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