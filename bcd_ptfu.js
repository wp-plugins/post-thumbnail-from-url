// WP plugin bcd ptfu js script
var bptfu = {};
bptfu.spinner = '<i class="fa fa-cog fa-spin"></i>';
jQuery(document).ready(function($) {
    // get the admin url
    var bcd_ptfu_lang = {
        lang_001: "",
        lang_002: "",
        lang_003: ""
    };
    jQuery.ajax({
        type: "GET",
        url: ajaxurl,
        data: {
            action: 'bcd_post_thumbnail_from_url_ajax_get_language'
        },
        beforeSend: function() {
            // console.log('before::bcd_post_thumbnail_from_url_ajax_get_language');
        },
        success: function(data){
            // console.log(data);
            if(data.lang_001 && data.lang_002 && data.lang_003) {
                bcd_ptfu_lang.lang_001 = data.lang_001;
                bcd_ptfu_lang.lang_002 = data.lang_002;
                bcd_ptfu_lang.lang_003 = data.lang_003;

                // console.log("we are in!");
                $("<div />")
                    .attr("id", "bptfu_container")
                    .addClass("bptfu-container")
                    .appendTo("#postimagediv");
                /* title */
                $("<div />")
                    .attr("id", "bptfu_title_container")
                    .addClass("bptfu-title-container")
                    .html('<span>' + bcd_ptfu_lang.lang_001 + '</span>')
                    .appendTo("#bptfu_container");
                /* input */
                $("<div />")
                    .attr("id", "bptfu_input_container")
                    .appendTo("#bptfu_container");
                $("<input />")
                    .attr("id", "bptfu_input")
                    .addClass("bptfu-input")
                    .css({})
                    .appendTo("#bptfu_input_container");
                /* submit */
                $("<div />")
                    .attr("id", "bptfu_submit_container")
                    .appendTo("#bptfu_container");
                /* the answer */
                $("<a />")
                    .attr("id", "bptfu_submit")
                    .addClass("button button-large button-primary bptfu-submit")
                    .html("get image from url")
                    .appendTo("#bptfu_submit_container");
                $("<div />")
                    .attr("id", "bptfu_answer_container")
                    .addClass("bptfu-answer-container")
                    .appendTo("#bptfu_container");

                $("a#bptfu_submit").bind("click", function(event) {
                    event.preventDefault();
                    var url = $("#bptfu_input").val();
                    jQuery.ajax({
                        type: "GET",
                        url: ajaxurl,
                        data: {
                            action: 'bcd_post_thumbnail_from_url_ajax_register_url',
                            url: url
                        },
                        beforeSend: function() {
                            $("#bptfu_answer_container").html(bptfu.spinner);
                            // console.log('before::bcd_post_thumbnail_from_url_ajax_register_url');
                        },
                        success: function(data){
                            // console.log(data);
                            if(data.message == 1) {
                                $("#bptfu_answer_container").html('<span class="bptfu-success">' + bcd_ptfu_lang.lang_002 + '</span>');
                                $("#bptfu_input").val("");
                            } else {
                                $("#bptfu_answer_container").html('<span class="bptfu-error">' + bcd_ptfu_lang.lang_003 + '</span>');
                            }
                            setTimeout(function() {
                                $("#bptfu_answer_container").html("");
                            }, 3000);
                        },
                        error:function (xhr, ajaxOptions, thrownError){
                            // code goes here;
                            console.log(xhr + ajaxOptions + thrownError + 'failed on button');
                        }
                    });
                });
            }
        },
        error:function (xhr, ajaxOptions, thrownError){
            // code goes here;
            console.log(xhr + ajaxOptions + thrownError + 'failed on button');
        }
    });
});