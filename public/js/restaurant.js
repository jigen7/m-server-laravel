$(document).ready(function()
{
    if ($('#restaurant-table').length) {
        $('#restaurant-table').dataTable({
            "sPaginationType": "full_numbers"
        });
    }

    if ($('#restaurant-budget').length) {
        $('#restaurant-budget').spinner();
    }

    if ($('.restaurant-delete').length) {
        $(document.body).on('click', '.restaurant-delete', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');

            if (confirm('Are you sure you want to delete restaurant ID ' + id + ' ?')) {
                $.post(
                    '/cms/restaurant/delete',
                    {
                        'id': id
                    }).done(function (d) {
                        window.location.replace("/cms/restaurant/index/");
                    });
            } else {
                return false;
            }
        });
    }
});