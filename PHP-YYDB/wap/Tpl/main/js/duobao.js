$(function(){
  init_duobao_cart();
  init_count_down();


  if(cart_conf_json.min_buy!=10){
	  $('.tenyen').hide();
  }
  init_adv_list();


  if($("#duobao_sn_list").find("dd").length>20)
  {
	  $("#duobao_sn_list").attr("status","close");
	  $("#duobao_sn_list").css({"height":"140","overflow-y":"hidden"});
	  $("#func").bind("click",function(){
		  if($("#duobao_sn_list").attr("status")=="close")
		  {
			  $("#duobao_sn_list").css({"height":"auto","overflow-y":"hidden"});
			  $("#func").html("收起<i class='iconfont'>&#xe6c4;</i>");
			  $("#duobao_sn_list").attr("status","open");
		  }
		  else
		  {
			  $("#duobao_sn_list").css({"height":"140","overflow-y":"hidden"});
			  $("#func").html("展开<i class='iconfont'>&#xe6c3;</i>");
			  $("#duobao_sn_list").attr("status","close");
		  }
	  });

  }
  else
  {
	  $("#func").hide();
  }

});

function init_adv_list(){
	TouchSlide({
		slideCell:"#banner_box",
		titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
		mainCell:".bd ul",
		effect:"leftLoop",
		autoPage:true,//自动分页
		autoPlay:true, //自动播放
		delayTime:750
	});
}
function init_count_down()
{
	var timespan = parseInt($(".w-countdown-nums:first").attr("nowtime")+"000") - new Date().getTime();
	$(".w-countdown-nums").each(function(i,o){
		var endtime = parseInt($(o).attr("endtime")+"000");
		$(o).count_down({endtime:endtime,timespan:timespan,interval:10,format:"%H:%M:%S:%MS",callback:function(){
			$(o).html("开奖中");
			$(o).everyTime(5000,function(){
				var duobao_item_id = $(o).attr("duobao_item_id");
				$.ajax({
					url: AJAX_URL,
		            data: {"act":"duobao_status","duobao_item_id":duobao_item_id},
		            type: "POST",
		            dataType: "json",
		            success: function (obj) {
		            	if(obj.status==1)
		            	{
		            		location.reload();
		            	}
		            }
				});
			});
		}});

	});
}



/*
* 弹出遮罩层
*/
function tolayer(){
  $(".am-layer").addClass("am-modal-active");
  if($(".layerbg").length>0){
    $(".layerbg").addClass("layerbg-active");
  }else{
    $("body").append('<div class="layerbg"></div>');
    $(".layerbg").addClass("layerbg-active");
  }
  $(".layerbg-active,.cencel-btn").click(function(){
    close_layer();
  });
}
/*关闭遮罩*/
function close_layer(){
  $(".am-layer").removeClass("am-modal-active");

  $(".layerbg-active").removeClass("layerbg-active");
  $(".layerbg").remove();
}

/*
*初始化购物车
*/
function init_duobao_cart(){
  $("input[name='buy_num']").val(cart_conf_json.min_buy);
  if(cart_conf_json.residue_count){//没有库存了
      $(".joinin").show();
      $(".gotonew").hide();
  }else{

      $(".joinin").hide();
      $(".gotonew").show();
  }

  //增加购买数量
  $(".plus-btn").bind("click",function(){
    if(cart_conf_json.residue_count==0)
        return false;
    var cur_buy_num = parseInt($("input[name='buy_num']").val());
    var count_num = 1;

    if((cart_conf_json.residue_count-cur_buy_num)>0){
        count_num = cur_buy_num+parseInt(cart_conf_json.min_buy);
    }else{
        count_num = cart_conf_json.residue_count;
    }
    $("input[name='buy_num']").val(count_num);
  });
  //减少购买数量
  $(".reduce-btn").bind("click",function(){
    var cur_buy_num = parseInt($("input[name='buy_num']").val());
    var count_num = 1;
    if(cur_buy_num>parseInt(cart_conf_json.min_buy)){
       count_num = cur_buy_num-parseInt(cart_conf_json.min_buy);
    }

    $("input[name='buy_num']").val(count_num);
  });

  //修改购买数量
  $("input[name='buy_num']").blur(function(event){
	  var cur_num = $(this).val();
	  var min_buy = parseInt(cart_conf_json.min_buy);
	  var residue_count = parseInt(cart_conf_json.residue_count);
	  if(cur_num==''){
		  $(this).val(min_buy);
	  }else{
		  if(cur_num>residue_count){
			  $(this).val(residue_count);
		  }else{
			  var multiple = parseInt(parseInt($(this).val())/parseInt(cart_conf_json.min_buy));
			  if(multiple>0){
				  $(this).val(parseInt(cart_conf_json.min_buy)*multiple);
			  }
		  }

	  }

  });


  /*弹出加入购物车的方式*/
  $(".joinin-btn").bind("click",function(){
    var data_type = $(this).attr("data-type");
    add_cart_view(data_type);
  });

  /*购物车显示的数值*/
  load_cart_data();

  /*加入购车事件*/
  init_add_cart_event();

}



/*加入购车事件*/
function init_add_cart_event(){

  $(".add_cart_item").bind("click",function(){
    var add_cart_type = $(this).attr("data-type");
    var buy_num = parseInt($("input[name='buy_num']").val());
    //请求服务端加入购物车表

    var query = new Object();
    query.act="add_cart";
    query.buy_num = buy_num;
    query.data_id = $("input[name='data_id']").val();

    $.ajax({
      url:AJAX_URL,
      data:query,
      type:"POST",
      dataType:"json",
      success:function(obj){
        if(obj.status==-1){
            $.showErr(obj.info,function(){
            	if(obj.jump)
                {
                        location.href = obj.jump;
                }
            });

            return false;
        }
        if (obj.status==1) {

          //增加购物车里面商品数量
          if(obj.cart_item_num>0){
            $(".goods-in-list").html(obj.cart_item_num);
            $(".goods-in-list").show();
          }else{
            $(".goods-in-list").hide();
          }

          //不同的加入购物车方式的流程
          if(add_cart_type==1){//跳转至购物车
              window.location = to_cart_url;
          }else if (add_cart_type==2) {//当前页面缩小
            close_layer();
          }


        }else{
          $.showErr(obj.info,function(){
        	  close_layer();
          });

        }
      }
    });
    //ajax end
  });

}
/*弹出不同按钮的 购物车按钮列表*/
function add_cart_view(type){
  $(".sub-btn-list .f-btn-box").hide();
  if(type==1){//直接购买
    $(".sub-btn-list .sub-btn-"+type).show();
  }else{//加入购物车
    $(".sub-btn-list .sub-btn-"+type).show();
  }
  tolayer();
}

/*查询购物车内容*/
function load_cart_data(){
  if(cart_data_json){
    //增加购物车里面商品数量
    if(cart_data_json.cart_item_num>0){
      $(".goods-in-list").html(cart_data_json.cart_item_num);
      $(".goods-in-list").show();
    }else{
      $(".goods-in-list").hide();
    }
  }
}
