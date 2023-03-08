; (function ($) {
    $(document).ready(function () {
        $('#toggle1').minitoggle();

        var current_value = $('#toggle1_hidden').val();
        if (current_value == 1) {
            $("#toggle1 .minitoggle").addClass('active');
            $('#toggle1 .toggle-handle').attr('style', 'transform: translate3d(36px, 0px, 0px);');

        }

        $('#toggle1').on("toggle", function (e) {
            if (e.isActive)
                $("#toggle1_hidden").val(1);
            else
                $("#toggle1_hidden").val(0);
        });



    });

})(jQuery);

