
$('#sms').on('click', function(){
    var keys = $('#grid').yiiGridView('getSelectedRows');
    if (keys.length == 0) {
        alert('Не выбраны пользователи для рассылки');
    } else {
        $.post(
            'sms', {'id' : keys}
        )
        window.location.href = 'sms';
    }
})
$(document).ready(function(){
    addEventForCheckbox();
})
$(document).on('pjax:start', function(){
    $('#sms').hide();
})
$(document).on('pjax:end', function(){
    addEventForCheckbox();
})

function addEventForCheckbox(){
    $('input:checkbox').on('change', function(){
        var keys = $('#grid').yiiGridView('getSelectedRows');
        if (keys.length == 0) {
            $('#sms').hide();
        } else {
            $('#sms').show();
        }
    })
}



