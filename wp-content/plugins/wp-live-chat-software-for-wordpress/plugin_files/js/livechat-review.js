(function ($) {
    $(document).ready(function () {
        var dismissButton = $("#lc-review-notice button");
        dismissButton.hide();
        $("#lc-review-dismiss").click(function (e) {
            e.preventDefault();
            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: 'lc_review_dismiss'
                }
            });
            dismissButton.click();
        });
        $("#lc-review-postpone").click(function (e) {
            e.preventDefault();
            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: 'lc_review_postpone'
                }
            });
            dismissButton.click();
        });
        $("#lc-review-now").click(function () {
            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: 'lc_review_dismiss'
                }
            });
            dismissButton.click();
        });
    })
})(jQuery);