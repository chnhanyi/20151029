(function() {

	var edit_group_url = "index.php/Group/get_group"; // 获取旅游团的信息

	var t_id = $.ynf.parse_url(window.location.href).params.id ;  
     window.edit_group  ;

       function  init_vm(){
       	 var e = edit_group; 

       	 window.vm_edit_group = avalon.define("edit_group",function(vm){
       	 	vm.t_id =  e.t_id ;  
			vm.r_id =  e.r_id ;        	 	
       	 	vm.t_date =  e.t_date ; 
			vm.t_tourCode =  e.t_tourCode; 
			vm.t_capacity =  e.t_capcacity ;       	 			
       	 	vm.t_bus =  e.t_bus ; 
       	 	vm.t_room = e.t_room ; 
       	 });

        avalon.scan();

       }

		avalon.ajax({
			url:  edit_group_url,
			type: "POST",
			dataType: "json",
			data: {t_id: t_id || 12},
			success: function(json) {
				if (json.status == "success") {
					edit_group = json.data;
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