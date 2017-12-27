$(document).ready(function() {
    $('.datefrom').each(function () {
        $(this).datepicker({
            dateFormat: 'yy-mm-dd 00:00:00'
        });
    });

    $('.dateto').each(function () {
        $(this).datepicker({
            dateFormat: 'yy-mm-dd 23:59:59'
        });
    });

    $('.default-popup').popup();
});