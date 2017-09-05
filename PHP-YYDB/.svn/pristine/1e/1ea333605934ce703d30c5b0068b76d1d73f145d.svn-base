$(document).ready(function(){
	// 搜索商品
	$("input[name='deal_key_btn']").bind("click",function(){
		var deal_key = $.trim($("input[name='deal_key']").val());
		if(deal_key == ''){
			alert('请输入商品名称关键字');
			return false;
		}
		
		// 查询商品，返回option列表
		var url = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=get_deal_option";
		
		$.post(url, { "deal_key": deal_key },function(data){
			if(data.status == 1){
				$("select[name='deal_id']").html(data.data);
			}
		}, "json");
	});
	
	// 修改总需人次
	$("select[name='buy_type']").bind("input propertychange",function(){
		var price =  $("select[name='deal_id']").find("option:selected").attr("price"); 
		var min_buy = $(this).val();
		if( (price > min_buy) && (price > 0) && (min_buy > 0) ){
			var max_buy_val = price / min_buy;
			$("input[name='max_buy']").val(Math.ceil(max_buy_val));
		}
		if($(this).val()== 100) //百元区的时候，1次=100元，所以人次不需要增加
			$(".set_min_buy").text(1);
		else
			$(".set_min_buy").text($(this).val());
	});
	
	 
	
	// 机器人生成
	$("select[name='robot_create_type']").bind("change",function(){
		var val = $(this).val();
		if(val != 1){
			$('.robot_tr').css('display', '');
		}else{
			$('.robot_tr').css('display', 'none');
			$('.robot_td').html('');
		}
	});
	
	// 设置总需要人次
	$("select[name='deal_id']").bind("change",function(){
		var min_buy = $("select[name='buy_type']").val();
		var price	= Math.ceil($(this).find("option:selected").attr('price'));
		var max_buy = Math.ceil( price / min_buy );
		
		if(!isNaN(max_buy)){
			$("input[name='max_buy']").val(max_buy);
		}
		
	});

	
	
	
	// 机器人数量限制
	$("input[name='robot_count']").bind("blur",function(){
		 if($(this).val() > 100){
			 $(this).focus();
			 alert('机器人数量最多100个');
		 }
	});
	
	// 当机器人数量input失去焦点，生成机器人
	$(".add_robot").bind("click",function(){
		var val  = $("input[name='robot_count']").val();
		var type = $("select[name='robot_create_type']").val();
		var html = '';
		
		if(type == 1){
			alert('随机分配不需要手动添加机器人');
			return false;
		}
		
		if(type != 1 && val > 0){
			for (var i=0; i<val; i++)
			{
				html += '机器人名称：<input type="text" name="robot[]" value="" /><br /><br />';
			}
			
			$(".robot_td").html(html);
		}
		 
	});

	

	//设置是否机器人必中
	$("input[name='robot_is_db']").bind("click",function(){
		show_robot_is_lottery();
		show_robot_config();
	});	
	$("select[name='fair_type']").bind("change",function(){
		show_robot_is_lottery();
	});
	$("select[name='robot_type']").bind("change",function(){
		show_robot_type();
	});
	show_robot_is_lottery();
	show_robot_config();
});


function show_robot_is_lottery()
{
	var robot_is_db = $("input[name='robot_is_db']:checked").val();
	var fair_type = $("select[name='fair_type']").val();
	if(fair_type=="yydb"&&robot_is_db==1)
	{
		$("#robot_is_lottery").show();
	}
	else
	{
		$("#robot_is_lottery").hide();
	}
}
function show_robot_config()
{
	var robot_is_db = $("input[name='robot_is_db']:checked").val();
	if(robot_is_db==1)
	{		
		$("#robot_type").show();
		show_robot_type();
	}
	else
	{
		$("#robot_type").hide();
		$("#robot_buy_time").hide();
		$("#robot_buy").hide();
		
		$("#robot_is_db_box").hide();
		
	}
}


function show_robot_type()
{
	var robot_type = $("select[name='robot_type']").val();
	if(robot_type==0)
	{
		$("#robot_buy_time").hide();
		$("#robot_buy").hide();
		
		$("#robot_is_db_box").show();
	}
	else
	{
		$("#robot_buy_time").show();
		$("#robot_buy").show();
		
		$("#robot_is_db_box").hide();
	}	
}