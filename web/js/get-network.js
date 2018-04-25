$(document).ready(function () {
//    $("#inet-switch").parent().parent().prop("hidden", true);
//    $("#inet-interface").parent().parent().prop("hidden", true);
//    $("#inet-onu_mac").parent().parent().prop("hidden", true);
    $("#network-mask-text").prop("value", $("#network-mask option:selected").val());
    $("#network-mask,#network-subnet").change(function () {
        $("#network-mask-text").prop("value", $("#network-mask option:selected").val())
        var subnet = $("#network-subnet").val();
        var mask = $("#network-mask option:selected").val();
        var data = {'subnet' : subnet, 'mask' : mask};
        if (mask === '' || subnet === '') {
            $("#network-first_ip").val('');
            $("#network-last_ip").val('');
            $("#network-gateway").val('');
        } else {
            $.ajax({
                url: "/network/getnetwork",
                type: 'GET',
                dataType: 'json',
                data: data,
                success: function (data) {
                    $("#network-subnet").prop("value", data["subnet"]);
                    $("#network-mask-text").prop("value", data["mask"]);
                    $("#network-first_ip").prop("value", data["first_ip"]);
                    $("#network-last_ip").prop("value", data["last_ip"]);
                    $("#network-gateway").prop("value", data["gateway"]);

                }
            });
        }
    });
});