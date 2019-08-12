$(document).on('click', '#reset-filter', function () {
    var filter = $(this).closest('#grid-filters').find("input[type=text], textarea, select");
    filter.val('');
    filter.first().trigger('change');
});
