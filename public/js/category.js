$(document).ready(function()
{
    if ($('#category-table').length) {
        $('#category-table').dataTable({
            "sPaginationType": "full_numbers"
        });
    }
});