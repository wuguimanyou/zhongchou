<?php
  header("Content-type: text/html; charset=utf-8"); 
  require('../config.php');
  require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
  require('../back_init.php');  
 
  $order_id = $configutil->splash_new($_GET["order_id"]);

 

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");

$alipay_trade_no="";
$alipay_trade_status="";
$paystatus=0;
$query ="SELECT id,alipay_trade_no,alipay_trade_status,paystatus from weixin_commonshop_orders where id=".$order_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result)) {
   $alipay_trade_no = $row->alipay_trade_no;    
   $alipay_trade_status = $row->alipay_trade_status;
   $paystatus= $row->paystatus;
}


$trade_state_str="未支付";
if($paystatus==1){
   $trade_state_str="支付成功";
}

mysql_close($link);

  
 
?>
<html>
<head>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../css/css2.css" media="all">
<link href="../common/add/css/global.css" rel="stylesheet" type="text/css">
<link href="../common/add/css/main.css" rel="stylesheet" type="text/css">
<link href="../common/add/css/shop.css" rel="stylesheet" type="text/css">

<meta http-equiv="content-type" content="text/html;charset=UTF-8">

</head>

<script>
 function submitV(){
    
	
    document.getElementById("keywordFrm").submit();
 }
 
 
</script>

<body>
<div class="div_new_content">

    <div class="add_content_one">
	    订单-支付宝支付详情
	</div>
	<div id="products" class="r_con_wrap">
	 <div class="r_con_form" >
		<div class="rows">
			<label>支付宝交易号:</label>
			<span class="input">
			<?php echo $alipay_trade_no; ?>
			</span>
			<div class="clear"></div>
		</div>
		
		<div class="rows">
			<label>交易状态：</label>
			<span class="input">
			<?php echo $alipay_trade_status; ?>
			</span>
			<div class="clear"></div>
		</div>
		
		
		
		<div class="rows">
			<label>交易状态</label>
			<span class="input">
			<?php echo $trade_state_str; ?>
			</span>
			<div class="clear"></div>
		</div>
		
	
	</div>
</div>

<div style="width:100%;height:20px;">
</div>

</div>
</body>
</html>

