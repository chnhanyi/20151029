(function() {

	var modify_payment_url = "index.php/Account/get_payment"; // 获取代理公司的信息

	var o_id = $.ynf.parse_url(window.location.href).params.id ;  
     window.modify_payment  ;
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
       	 var m = modify_payment ; 

       	 window.vm_modify_payment = avalon.define("modify_payment",function(vm){
       	 	vm.o_id =  m.o_id ;
       	 	vm.invoice =  m.invoice ;        	 	
       	 	vm.amount =  m.amount ; 
			vm.payment = m.payment ;
       	 });

        avalon.scan();

       }

		avalon.ajax({
			url:  modify_payment_url,
			type: "POST",
			dataType: "json",
			data: {o_id: o_id},
			success: function(json) {
				if (json.status == "success") {
					modify_payment = json.data;
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