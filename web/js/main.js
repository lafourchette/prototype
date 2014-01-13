$(document).ready(function()
{
    $('#launch-prototype').on('click', function(event)
    {
        $('#create-prototype').submit();
    });

    $('#refresh-log').click(function() {
        location.reload();
    });
});