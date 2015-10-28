 (function(window, $) {

    $interOrders = $(".inter-order");

    $(document.body).on("click",".inter-order",function(ev){
        ev.preventDefault();
        var $this =$(this)
        var st =  $.jStorage ;
        st.set("id", $this.attr("data-id") );
        st.set("routerName", $this.attr("data-routerName") );
        st.set("routerEnName", $this.attr("data-routerEnName") );
        window.location.href = $(this).attr("href");

    });



})(window , jQuery);