<?php
header("Content-type: text/html; charset=utf-8");     
require('../../config.php');
require('../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../proxy_info.php');
require('../../auth_user.php');
mysql_query("SET NAMES UTF8");

$shop_id=-1;
$stock_remind = 0; 
$query = "select id,stock_remind from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
		$stock_remind=$row->stock_remind; 
		$shop_id=$row->id;
}

$is_Pinformation =  0;
$pinformation_id = -1;
$is_stockOut     =  0;
$is_division     =  0;
$is_promoter     =  0;
$sql = "select id,is_Pinformation,is_stockOut,is_division,is_promoter from weixin_commonshops_extend where isvalid=true and customer_id=".$customer_id." limit 0,1";
$result1 = mysql_query($sql) or die('Query failed: ' . mysql_error());
while ($row1 = mysql_fetch_object($result1)) {
		$is_Pinformation  = $row1->is_Pinformation;
		$is_stockOut	  = $row1->is_stockOut;
		$pinformation_id  = $row1->id;
		$is_division      = $row1->is_division;
		$is_promoter      = $row1->is_promoter;
}
?>
<!doctype html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../common/css_V6.0/content<?php echo $theme?>.css">
<script type="text/javascript" src="../../common/js/jquery-2.1.0.min.js"></script>
<title>基本设置</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="div_new_content">
<form id="config_form" action="save_inventory_remind.php?customer_id=<?php echo $customer_id_en; ?>" method="post" enctype="multipart/form-data">
	<input type=hidden name="shop_id" id="shop_id" value="<?php echo $shop_id; ?>" />
	<input type=hidden name="pinformation_id" id="pinformation_id" value="<?php echo $pinformation_id; ?>" />
	<div class="WSY_content">
		<div class="WSY_columnbox">
		
			<?php require('head.php') ;?>

			
			<div class="WSY_data">
				<div class="WSY_remind_main">
					<dl class="WSY_remind_dl02">
						<dt style="line-height:40px;" class="WSY_left"><span style="font-size:14px;">库存提醒：</span></dt>
						<dd style="line-height:40px;">
							<span>库存低于&nbsp;<input type="text" class="not_agent_tip" value="<?php echo $stock_remind; ?>" name="stock_remind" id="stock_remind" style="width:50px;height:24px;text-align:center;border:solid 1px #ccc;border-radius:2px;" >&nbsp;件提醒<span style="color:red" >（库存提醒≠0）</span></span>
						</dd>
					</dl>
					
					<dl class="WSY_remind_dl02">
						<dt style="line-height:40px;margin-top:5px;" class="WSY_left"><span style="font-size:14px;">库存不足下架开关：</span></dt>
						<dd>
							<?php if( $is_stockOut == 1 ){ ?>
								<ul style="background-color: rgb(255, 113, 112);margin-top:16px;">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="chage_stockOut(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="chage_stockOut(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>																
							<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);margin-top:16px;">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="chage_stockOut(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="chage_stockOut(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>						
							<?php } ?>
							<input type="hidden" name="is_stockOut" id="is_stockOut" value="<?php echo $is_stockOut; ?>" />
						</dd>
					</dl>
					
					<dl class="WSY_remind_dl02">
						<dt style="line-height:40px;margin-top:-8px;" class="WSY_left"><span style="font-size:14px;">必填信息开关：</span></dt>
						<dd>
							<?php if($is_Pinformation==1){ ?>
								<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="chage_Pinformation(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="chage_Pinformation(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>																
							<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="chage_Pinformation(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="chage_Pinformation(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>						
							<?php } ?>
							<input type="hidden" name="is_Pinformation" id="is_Pinformation" value="<?php echo $is_Pinformation; ?>" />
						</dd>
					</dl>
					<dl class="WSY_remind_dl02">
						<dt style="line-height:40px;margin-top:-8px;" class="WSY_left"><span style="font-size:14px;">返现与购物币显示开关：</span></dt>
						<dd class="division">
							<?php if($is_division==1){ ?>
								<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="chage_division(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="chage_division(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>																
							<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="chage_division(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="chage_division(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>						
							<?php } ?>
							<input type="hidden" name="is_division" id="is_division" value="<?php echo $is_division; ?>" />
						</dd>
					</dl>
					<dl class="WSY_remind_dl02">
						<dt style="line-height:40px;margin-top:-8px;" class="WSY_left"><span style="font-size:14px;">只有推广员显示返现与购物币开关：</span></dt>
						<dd class="promoter">
							<?php if($is_promoter==1){ ?>
								<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
									<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
									<li onclick="chage_promoter(0)" class="WSY_bot" style="left: 0px;"></li>
									<span onclick="chage_promoter(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
								</ul>																
							<?php }else{ ?>
								<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
									<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
									<li onclick="chage_promoter(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
									<span onclick="chage_promoter(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
								</ul>						
							<?php } ?>
							<input type="hidden" name="is_promoter" id="is_promoter" value="<?php echo $is_promoter; ?>" />
						</dd>
					</dl>
				<div class="WSY_text_input01">
					<div class="WSY_text_input"><input type="button" class="WSY_button" value="提交" onclick="submitV(this);" style="cursor:pointer;"/></div>
				</div>
			</div>
		</div>
	</div>
</form>

<script type="text/javascript" src="../../common/js_V6.0/content.js"></script> 
<script type="text/javascript" src="../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script charset="utf-8" src="../../common/js/layer/V2_1/layer.js"></script>
<div style="width:100%;height:20px;">
</div>  
</div>  
<?php 
mysql_close($link);
?>
<script type="text/javascript">
var page_index =0;
$(function(){
	$(".WSY_columnnav a").removeClass("white1");
	$(".WSY_columnnav a").eq(page_index).addClass("white1");
});

function chage_Pinformation(obj){
	$("#is_Pinformation").val(obj);
}
function chage_stockOut(obj){
	$("#is_stockOut").val(obj);
}
function chage_division(obj){
	$("#is_division").val(obj);
	if( obj == 0 ){
		chage_promoter(0);
		$(".promoter").find(".WSY_bot").animate({left : '30px'});
		$(".promoter").find(".WSY_bot").parent().find(".WSY_bot2").animate({left : '30px'});
		$(".promoter").find(".WSY_bot").hide();
		$(".promoter").find(".WSY_bot").parent().find(".WSY_bot2").show();
		$(".promoter").find(".WSY_bot").parent().find("p").animate({margin : '0 0 0 13px'}, 500);
		
		$(".promoter").find(".WSY_bot").parent().find("p").html('关');
		$(".promoter").find(".WSY_bot").parent().css({backgroundColor : '#cbd2d8'});
		$(".promoter").find(".WSY_bot").parent().find("p").css({color : '#7f8a97'});

	}
}
function chage_promoter(obj){
	$("#is_promoter").val(obj);
	if( obj  == 1 ){
		chage_division(1);
		$(".division").find(".WSY_bot2").parent().find(".WSY_bot").animate({left : '0px'});
		$(".division").find(".WSY_bot2").animate({left : '0px'});
		$(".division").find(".WSY_bot2").parent().find(".WSY_bot").show();
		$(".division").find(".WSY_bot2").hide();
		$(".division").find(".WSY_bot2").parent().find("p").animate({margin : '0 0 0 27px'}, 500);
		
		$(".division").find(".WSY_bot2").parent().find("p").html('开');
		$(".division").find(".WSY_bot2").parent().css({backgroundColor : '#ff7170'});
		$(".division").find(".WSY_bot2").parent().find("p").css({color : '#fff'});
	}
}


</script>
</body>
	
<script>
 function submitV(a){
	var num = document.getElementById("stock_remind").value;
	if(num==""){
	    alert('请输入库存提醒额!');
	   return;
	}
	
	if(parseInt(num) <0){
	    alert('库存不能为负数!');
	   return;
	}
    document.getElementById("config_form").submit();
 }
</script>

</html>