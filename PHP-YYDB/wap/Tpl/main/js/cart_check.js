$(document).ready(function() {
    init_arrow_switch();
    count_buy_total();
    $(".pay-ways").bind("click", function() {
        var radio_item = $(this).parent().find("input");
        var span_item = $(this).parent().find("span");
        $(".payment-item").removeAttr("checked");
        $(".payment-name").removeClass("checked");
        $(span_item).addClass("checked");
        $(radio_item).attr("checked", "checked");

        count_buy_total();
    });
    count_buy_total();

    $("input[name='ecvsn'],input[name='ecvpassword']").bind("blur", function() {
        count_buy_total();
    });
    $("*[name='ecvsn']").bind("change", function() {
        count_buy_total();
    });
    var is_lock = 0;
    $("#pay-form").bind("submit", function() {

        if (is_lock) {
            return false;
        }
        var query = $(this).serialize();
        var action = $(this).attr("action");
       /* alert(query+','+action);*/

        /*return false;*/
        if (!ajaxing) {
            is_lock = 1;
            $(".btn_order .sub").addClass("is_lock");
            $.ajax({
                url: action,
                data: query,
                type: "POST",
                dataType: "json",
                success: function(obj) {
                    if (obj.status == 1) {
                        if (obj.is_app) {
                            $(".reload-btn").attr("href", obj.reload_url + '&is_app=1');
                            $(".success-btn").attr("href", obj.success_url + '&is_app=1');
                            tolayer();
                            var json = '{"url":"' + obj.jump + '&is_app=1","open_url_type":"1"}';
                            App.open_type(json);
                        } else {
                            location.href = obj.jump;
                        }
                    } else if (obj.status == 2) {
                        //console.log(obj['sdk_code']);
                        try {
                            var str = pay_sdk_json(obj['sdk_code']);
                            //console.log(str);
                            //$.showErr(str);
                            App.pay_sdk(str);
                        } catch (ex) {
                            $.showErr(ex);
                        }

                    } else {
                        if (obj.info) {
                            is_lock = 0;
                            $(".btn_order .sub").removeClass("is_lock");
                            $.showErr(obj.info, function() {
                                if (obj.jump) {
                                    location.href = obj.jump;
                                }
                            });
                        } else {
                            if (obj.jump) {

                                location.href = obj.jump;
                            }
                        }

                    }
                }
            });
        }

        return false;
    });
    $(".reload-btn").click(function(event) {
        var re_url = $(".reload-btn").attr("href");
        var json = '{"url":"' + re_url + '","open_url_type":"1"}';
        App.open_type(json);
        event.preventDefault();
    });
});

function count_buy_total() {
    ajaxing = true;
    var query = new Object();

    //全额支付
    if ($("input[name='all_account_money']").attr("checked")) {
        query.all_account_money = 1;
    } else {
        query.all_account_money = 0;
    }

    //通宝支付
    if ($("input[name='all_account_points']").attr("checked")) {
        query.all_account_points = 1;
    } else {
        query.all_account_points = 0;
    }
    var paypassword = $("input[name='paypassword']").val();
    if (!paypassword) {
        paypassword = '';
    }
    query.paypassword = paypassword;

    //代金券
    var ecvsn = $("*[name='ecvsn']").val();
    if (!ecvsn) {
        ecvsn = '';
    }
    var ecvpassword = $("input[name='ecvpassword']").val();
    if (!ecvpassword) {
        ecvpassword = '';
    }
    query.ecvsn = ecvsn;
    query.ecvpassword = ecvpassword;

    //支付方式
    var payment = $("input[name='payment']:checked").val();

    if (!payment) {
        payment = 0;
    }
    query.payment = payment;
    /*query.paymentid = payment;*/
    query.bank_id = $("input[name='payment']:checked").attr("rel");
    query.id = order_id;
    if (!isNaN(order_id) && order_id > 0)
        query.act = "count_order_total";
    else
        query.act = "count_buy_total";
    /*alert(query.act +','+query.payment+','+query.id+','+AJAX_URL+','+query);*/
    $.ajax({
        url: AJAX_URL,
        data: query,
        type: "POST",
        dataType: "json",
        success: function(data) {
            /*alert(data);*/
            if (data) {
                $("#cart_total").html(data.html);

                if (data.pay_price == 0) {

                    $("input[name='payment']").attr("checked", false);
                }
            }
            ajaxing = false;
        },
        error: function(ajaxobj) {
            //			if(ajaxobj.responseText!='')
            //			alert(LANG['REFRESH_TOO_FAST']);
        }
    });
}

function init_arrow_switch() {
    /*$(".goodsum-info").hide();*/
    $(".order-info-btn").bind("click", function() {
        $(this).hide();
        if ($(this).hasClass("up-btn")) {
            $(".down-btn").slideToggle(100);
            $(".goodsum-info").slideUp("slow");
            $(".blank-bar").show();
        } else {
            $(".up-btn").slideToggle(100);
            $(".goodsum-info").slideDown("slow");
            $(".blank-bar").hide();
        }
    });
}


/*
 * 弹出遮罩层
 */
function tolayer() {
    $(".am-layer").addClass("am-modal-active");
    if ($(".layerbg").length > 0) {
        $(".layerbg").addClass("layerbg-active");
    } else {
        $("body").append('<div class="layerbg"></div>');
        $(".layerbg").addClass("layerbg-active");
    }
    $(".layerbg-active,.cencel-btn").click(function() {
        close_layer();
    });
}
/*关闭遮罩*/
function close_layer() {
    $(".am-layer").removeClass("am-modal-active");
    setTimeout(function() {
        $(".layerbg-active").removeClass("layerbg-active");
        $(".layerbg").remove();
    }, 300);
}