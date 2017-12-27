$(document).ready(function()
{
    $(".dropdown-menu li a").click(function(){
        $(this).parents(".dropdown").find('.btn').html($(this).text() + ' <span class="caret"></span>');
        $(this).parents(".dropdown").find('.btn').val($(this).data('value'));
    });

    $("#add-cuisines").click(function(){

        $("#cuisines").val($("input[name=cuisine]:checked").map(
            function () {return this.value;}).get().join(", "));
    });
});
