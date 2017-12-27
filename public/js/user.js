$(document).ready(function()
{
    if ($('#user-table').length) {
        $('#user-table').dataTable({
            "sPaginationType": "full_numbers"
        });
    }
});