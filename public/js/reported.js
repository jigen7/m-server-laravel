$(document).ready(function()
{
    if ($('#reported-table').length) {
        $('#reported-table').dataTable({
            "sPaginationType": "full_numbers"
        });
    }
});