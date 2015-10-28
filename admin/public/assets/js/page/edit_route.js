(function() {

	var edit_route_url = "index.php/Route/get_route"; // 获取线路的信息

	var r_id = $.ynf.parse_url(window.location.href).params.id ;  
     window.edit_route  ;
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
       	 var e = edit_route; 

       	 window.vm_edit_route = avalon.define("edit_route",function(vm){
       	 	vm.r_id =  e.r_id ;        	 	
       	 	vm.cName =  e.r_cName ; 
			vm.eName =  e.r_eName ; 
			vm.type =  e.r_type ;  
			vm.code =  e.r_code ;  
			vm.frequency =  e.r_frequency ; 
			vm.city =  e.r_city ;       	 			
       	 	vm.ltd =  e.l_id ; 
       	 	vm.pdf_au = e.r_Pdf_au ; 
			vm.pdf_nz = e.r_Pdf_nz ;
			vm.pdf_sa = e.r_Pdf_sa ;
			vm.auAdultPrice = e.r_auAdultPrice; 
			vm.auChildPrice1 = e.r_auChildPrice1; 
			vm.auChildPrice2 = e.r_auChildPrice2; 
			vm.auChildPrice3 = e.r_auChildPrice3; 
			vm.auInfantPrice = e.r_auInfantPrice; 
			vm.auSinglePrice = e.r_auSinglePrice;  
			
			vm.nzAdultPrice = e.r_nzAdultPrice; 
			vm.nzChildPrice1 = e.r_nzChildPrice1; 
			vm.nzChildPrice2 = e.r_nzChildPrice2; 
			vm.nzChildPrice3 = e.r_nzChildPrice3; 
			vm.nzInfantPrice = e.r_nzInfantPrice; 
			vm.nzSinglePrice = e.r_nzSinglePrice; 
			
			vm.saAdultPrice = e.r_saAdultPrice; 
			vm.saChildPrice1 = e.r_saChildPrice1; 
			vm.saChildPrice2 = e.r_saChildPrice2; 
			vm.saChildPrice3 = e.r_saChildPrice3; 
			vm.saInfantPrice = e.r_saInfantPrice; 
			vm.saSinglePrice = e.r_saSinglePrice; 

       	 });

        avalon.scan();

       }

		avalon.ajax({
			url:  edit_route_url,
			type: "POST",
			dataType: "json",
			data: {r_id: r_id || 12},
			success: function(json) {
				if (json.status == "success") {
					edit_route = json.data;
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