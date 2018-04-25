$(document).on("click", "a[href*='inet/arp']", function(e){
    $("html,body").css("overflow-x","hidden");
    $("html,body").css("overflow-x","auto");
    $("#arp-response .modal-body #ip").html('');
    $("#arp-response .modal-body #status").html('');
    $("#arp-response .modal-body #icon").removeClass();
    $("#arp-response .modal-body #icon").addClass();
    $.ajax({
        url: this,                             
        dataType : "json",                     
        success: function (data) {         
            $("#arp-response .modal-body #ip").html(data.ip);
            $("#arp-response .modal-body #status").html(data.result);
            $("#arp-response .modal-body #icon").addClass(data.icon);
        }
    });
});