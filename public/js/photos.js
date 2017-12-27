$(document).ready(function()
{
    if ($('.photo-preview-toggle').length) {
        $('.photo-preview-toggle').click(function(e)
        {
            e.preventDefault();
            $('#photo-modal-preview').attr('src', '');
            $('#photo-modal-preview').attr('src', $(this).attr('href'));
            $('#photo-modal').modal('show');
        });
    }

    if ($('#photo-from-date').length) {
        $('#photo-from-date').datepicker({
            dateFormat: 'MM dd, yy'
        });
    }

    if ($('#photo-to-date').length) {
        $('#photo-to-date').datepicker({
            dateFormat: 'MM dd, yy'
        });
    }

    if ($('.photos-deactivate').length) {
        $(document.body).on('click', '.photos-deactivate', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');

            if (confirm('Are you sure you want to set the status of photo ID ' + id + ' to inactive?')) {
                $.post(
                    '/cms/photos/deactivate',
                    {
                        'id': id
                    }).done(function (d) {
                        window.location.replace("/cms/photos/index/");
                    });
            } else {
                return false;
            }
        });
    }
});