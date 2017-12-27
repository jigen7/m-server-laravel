$(document).ready(function()
{
    if ($('#review-table').length) {
        $('#review-table').dataTable({
            "sPaginationType": "full_numbers"
        });
    }
});