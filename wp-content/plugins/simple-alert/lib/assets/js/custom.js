function get_post_type(type) {

    jQuery('.notification-top-bar').addClass('show');
    jQuery.ajax({
        type: "post",
        url: simple_alert_ajax_object.ajax_url,
        data: { 'action': 'simple_alert_get_post_data', 'type': type },
        success: function (msg) {
            jQuery('#tab' + type).append(msg);
            jQuery('.notification-top-bar .msg').text('Updated');
            jQuery('.notification-top-bar .loading').hide();
            setTimeout(function () {
                jQuery('.notification-top-bar').removeClass('show');
            }, 400);

        }
    });
}
function remove_post_type(type) {

    jQuery('.notification-top-bar').addClass('show');
    jQuery.ajax({
        type: "post",
        url: simple_alert_ajax_object.ajax_url,
        data: { 'action': 'simple_alert_remove_post_data', 'type': type },
        success: function (msg) {
            jQuery('#tab' + type).append(msg);
            jQuery('.notification-top-bar .msg').text('Updated');
            jQuery('.notification-top-bar .loading').hide();
            setTimeout(function () {
                jQuery('.notification-top-bar').removeClass('show');
            }, 400);

        }
    });
}
function update_post_type(ID, value) {
    jQuery('.notification-top-bar').addClass('show');
    jQuery.ajax({
        type: "post",
        url: simple_alert_ajax_object.ajax_url,
        data: { 'action': 'simple_alert_update_post_data', 'post_id': ID, 'value': value },
        success: function (msg) {
            jQuery('.notification-top-bar .msg').text('Updated');
            jQuery('.notification-top-bar .loading').hide();
            setTimeout(function () {

                jQuery('.notification-top-bar').removeClass('show');
            }, 400);
        }
    });
}

function seve_chages(ID, value) {
    jQuery('.notification-top-bar').addClass('show');
    jQuery.ajax({
        type: "post",
        url: simple_alert_ajax_object.ajax_url,
        data: { 'action': 'simple_alert_save', 'alert_message': jQuery('.simple-alert-message').val() },
        success: function (msg) {
            jQuery('.notification-top-bar .msg').text('Updated');
            jQuery('.notification-top-bar .loading').hide();
            setTimeout(function () {

                jQuery('.notification-top-bar').removeClass('show');
            }, 400);
        }
    });
}

jQuery(document).ready(function ($) {


    $('.save-changes').click(function () {
        seve_chages();
    });
    $('#tab-content').on("click", ".update-posttype", function () {
        var pchbox = 0;
        if ($(this).is(':checked')) {
            pchbox = 1;
        }
        update_post_type($(this).val(), pchbox);

    });
    $('.custom-control-input').click(function () {
        postTypeLabe = $(this).siblings('.custom-control-label').text();

        postType = $(this).val();
        if ($(this).is(':checked')) {
            get_post_type(postType);

            $('#tab-list').append($('<li class="nav-item m-0"><a class="nav-link" id="tabli' + postType + '" data-toggle="pill" href="#tab' + postType + '" role="tabpanel">' + postTypeLabe + '</a></li>'));
            $('#tab-content').append($('<div  class="tab-pane fade" role="tabpanel" aria-labelledby="tab' + postType + '" id="tab' + postType + '"></div>'));
            $('.nav-item a[href="#tab' + postType + '"]').tab('show');
        } else {
            remove_post_type(postType);

            $('#tabli' + postType).remove();
            $('#tab' + postType).remove();
            $('#tab-list li:first').addClass('active');
            $('#tab-content div:first').addClass('active show');
        }
    });


});