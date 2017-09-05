$(document).ready(function () {
    init_notice();
    init_order_by();
    init_auto_load_data();
    init_count_down();
    init_slide_top();
});

function init_slide_top(){
	
	//获取菜单距离顶部的高度
	var nav_top_height = $(".slider-nav").offset().top;
	nav_top_height = parseInt(nav_top_height)-50;
	slideNavGoTop(nav_top_height);
}
//向上按钮显示隐藏
function slideNavGoTop(min_height){
//获取页面的最小高度，无传入值则默认为600像素
    min_height ? min_height = min_height : min_height = 50;
    //为窗口的scroll事件绑定处理函数
    $(window).scroll(function(){
        //获取窗口的滚动条的垂直位置
        var s = $(window).scrollTop();
        //当窗口的滚动条的垂直位置大于页面的最小高度时，让返回顶部元素渐现，否则渐隐
        if( s > min_height){
        	$(".slider-nav").addClass("slider-nav-top");
        }else{
        	$(".slider-nav").removeClass("slider-nav-top");
        };
    });
};

function init_adv_slider(){
	$('#marquee').bxSlider({
        mode:'vertical', //默认的是水平
        displaySlideQty:1,//显示li的个数
        moveSlideQty: 1,//移动li的个数  
        captions: true,//自动控制
        auto: true,
        controls: false//隐藏左右按钮
  });
}

function init_count_down()
{
	var timespan = parseInt($(".w-countdown-nums:first").attr("nowtime")+"000") - new Date().getTime(); 
	$(".w-countdown-nums").each(function(i,o){
		var endtime = parseInt($(o).attr("endtime")+"000");
		$(o).count_down({endtime:endtime,timespan:timespan,interval:10,format:"%H:%M:%S:%MS",callback:function(){
			$(o).html("计算中");
		}});

	});
}




function setCookie(name, value, iDay) {

    /* iDay 表示过期时间
     
     cookie中 = 号表示添加，不是赋值 */

    var oDate = new Date();

    oDate.setDate(oDate.getDate() + iDay);

    document.cookie = name + '=' + value + ';expires=' + oDate;

}

function getCookie(name) {

    /* 获取浏览器所有cookie将其拆分成数组 */

    var arr = document.cookie.split('; ');



    for (var i = 0; i < arr.length; i++) {

        /* 将cookie名称和值拆分进行判断 */

        var arr2 = arr[i].split('=');

        if (arr2[0] == name) {

            return arr2[1];

        }

    }

    return '';

}

//公告滚动
function init_notice()
{
    $(".notice-box").everyTime(3000, function () {
        roll_news();
    });
    $(".notice-box").hover(function () {
        $(".notice-box").stopTime();
    }, function () {
        $(".notice-box").everyTime(3000, function () {
            roll_news();
        });
    });

}
function roll_news()
{
    $(".notice-box ul").find("li:first").animate({marginTop: "-" + $(".notice_board ul").find("li:first").height() + "px"}, 300, function () {
        var li = $(this);
        $(".notice-box ul").append('<li class="n-item">' + $(li).html() + '</li>');
        $(li).remove();
    });
}

/**
 * 排序初始化
 */

function init_order_by() {
    $(".slider-nav .nav-item a").bind("click", function () {
        if ($(this).is('.last')) {
            if ($(this).is('.f-down')) {
                $(this).removeClass('f-down').addClass('f-up');
                location.href = $(this).find('.i-up').attr('data_url');
            } else if ($(this).is('.f-up')) {
                $(this).removeClass('f-up').addClass('f-down');
                location.href = $(this).find('.i-down').attr('data_url');
            } else {
                $(this).removeClass('f-up').addClass('f-down');
                location.href = $(this).find('.i-down').attr('data_url');
            }
        }
    });
}




var cur_num = 0;
/*加入购车事件*/
function add_cart(obj){
    var btn_item = $(obj);

        var buy_num = parseInt($(obj).attr('buy_num'));
        //请求服务端加入购物车表
        var query = new Object();
        query.act = "add_cart";
        query.buy_num = buy_num;
        query.data_id = parseInt($(obj).attr('data_id'));
        $.ajax({
            url: AJAX_URL,
            data: query,
            type: "POST",
            dataType: "json",
            success: function (obj) {
                if (obj.status == -1) {
                	$.showErr(obj.info,function(){
                		if (obj.jump)
                        {
                            location.href = obj.jump;
                        }
                    });
                    return false;
                }
                if (obj.status == 1) {

                    //增加购物车里面商品数量
                    if (obj.cart_item_num > 0) {
                        //填充购物车数值
                        $(".nav_cart_num").html(obj.cart_item_num);
                        $(".nav_cart_num").fadeIn(1000);
                        if(obj.cart_item_num>cur_num){
                            $(".nav_cart_num").addClass("nav_cart_num_zoom");
                            
                            setTimeout(function(){
                                $(".nav_cart_num").removeClass("nav_cart_num_zoom");
                            }, 200 );
                        }
                        
                        cur_num = obj.cart_item_num;
                        return false;
                    } else {
                        $(".nav_cart_num").hide();
                    }
                    return false

                } else {

                    $.showErr(obj.info);
                    return false
                }
            }
        });
}

var page=2;
var stop=true;
var page_total = 0;
function init_auto_load_data(){
    $(window).scroll(function(){ 
        if(page_total>0 && page>page_total){
            stop=false;
            $(".page-load").html("没有更多夺宝活动了~");
        }else{
            $(".page-load").html("努力加载中...~");
        }
        var totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop())+100; 
        if($(document).height() <= totalheight){ 
            if(stop==true){ 
                stop=false;
                var query = new Object();
                query.page = page;
                query.act="load_index_list_data";
                $.ajax({
                        url: AJAX_URL,
                        data: query,
                        type: "POST",
                        dataType: "json",
                        success: function (obj) {
                            $(".tuan-ul").append(obj.html);    
                            stop=true;
                            page++;
                            page_total = obj.page.page_total;
                        }
                });
            } 
        }
    });
}
