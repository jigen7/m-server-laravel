$(document).ready(function()
{
    if ($('#menu-table').length) {
        $('#menu-table').dataTable({
            "sPaginationType": "full_numbers"
        });
    }

    if ($('.delete-menu').length) {
        $(document.body).on('click', '.delete-menu', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var restaurant_id = $(this).attr('data-restaurant-id');

            if (confirm('Are you sure you want to delete Menu with ID: ' + id + ' ?')) {
                $.post(
                    '/cms/menu/delete',
                    {
                        'id': id
                    }).done(function (d) {
                        window.location.replace("/cms/menu/view/" + restaurant_id);
                    });
            } else {
                return false;
            }
        });
    }
});
