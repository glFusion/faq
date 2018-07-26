/**
* glFusion CMS
*
* FAQ - Frequently Asked Questions Plugin
*
* FAQ JavaScript
*
* @license GNU General Public License version 2 or later
*     http://www.opensource.org/licenses/gpl-license.php
*
*  Copyright (C) 2017-2018 by the following authors:
*   Mark R. Evans   mark AT glfusion DOT org
*
*/
$(document).ready(function() {
    $("#helpful_yes").click(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/faq/vote.php",
            data: {
                id: $("#faqid").val(),
                type: "yes",
            },
            success: function(result) {
                $.UIkit.notify("<i class='uk-icon-check'></i>&nbsp;" + 'Thank you for your feedback', {timeout: 1000,pos:'top-center'});
                $("#helpful_yes").removeAttr('onclick');
                $("#helpful_yes").attr("disabled", "disabled");
                $("#helpful_no").attr("disabled", "disabled");
            },
            error: function(result) {
                alert('error');
            }
        });
    });
    $("#helpful_no").click(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "/faq/vote.php",
            data: {
                id: $("#faqid").val(),
                type: "no",
            },
            success: function(result) {
                $.UIkit.notify("<i class='uk-icon-check'></i>&nbsp;" + 'Thank you for your feedback', {timeout: 1000,pos:'top-center'});
                $("#helpful_yes").removeAttr('onclick');
                $("#helpful_yes").attr("disabled", "disabled");
                $("#helpful_no").attr("disabled", "disabled");
                $("#faq-feedback").text('Thank you for your feedback');
            },
            error: function(result) {
                alert('error');
            }
        });
    });
});