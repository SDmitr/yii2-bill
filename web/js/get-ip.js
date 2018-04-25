$(document).ready(function () {
//    $("#inet-switch").parent().parent().prop("hidden", true);
//    $("#inet-interface").parent().parent().prop("hidden", true);
//    $("#inet-onu_mac").parent().parent().prop("hidden", true);
    $("#network-name").change(function () {
        var network = $("#network-name option:selected").val();
        var data = {'id': network};
        if (network === '') {
            $("#inet-ip").val('');
            $("#inet-aton").val('');
        } else {
            $.ajax({
                url: "/network/getip",
                type: 'GET',
                dataType: 'json',
                data: data,
                success: function (data) {
                    $("#inet-ip").prop("value", data["ip"]);
                    $("#inet-aton").prop("value", data["aton"]);
                    if (data["pon"]){
                        $("#inet-switch").val('');
                        $("#inet-interface").val('');
                        $("#inet-switch").parent().parent().prop("hidden", true);
                        $("#inet-interface").parent().parent().prop("hidden", true);
                        $("#inet-onu_mac").parent().parent().prop("hidden", false);
                    } else {
                        $("#inet-switch").parent().parent().prop("hidden", false);
                        $("#inet-interface").parent().parent().prop("hidden", false);
                        $("#inet-onu_mac").parent().parent().prop("hidden", true);
                        $("#inet-onu_mac").val('');
                    }
                }
            });
        }
    });
});