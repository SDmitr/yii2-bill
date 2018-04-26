
$('#sms').on('click', function(){
    var keys = $('#w1').yiiGridView('getSelectedRows');
    if (keys.length == 0) {
        alert('Не выбраны пользователи для рассылки');
    } else {
        $.post(
            'sms', {'id' : keys}
        )
        window.location.href = 'sms';
    }
})



