<?php
  header("Content-type: text/html; charset=utf-8"); 
  require('../config.php');
  require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
 require('../back_init.php');   
 
  $allipay_orderid = $configutil->splash_new($_GET["allipay_orderid"]);

 
 
  $allipay_isconsumed = 0;
  $paystatus = 0;
  $total_amount =0;
  $mobile_phone = "";
  $user_id = -1;
 $link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
 mysql_select_db(DB_NAME) or die('Could not select database');
 mysql_query("SET NAMES UTF8");
 
 
  $query = 'SELECT version FROM allinpays where isvalid=true and customer_id='.$customer_id." limit 0,1";
 
 
 $version=1;
 $result = mysql_query($query) or die('Query failed: ' . mysql_error());  
 while ($row = mysql_fetch_object($result)) {
	$version = $row->version;
	break;
 }
 
 $consumetime="";
 
 $query = "SELECT id,allipay_isconsumed,paystatus,totalprice,user_id,consumetime FROM weixin_commonshop_orders where isvalid=true and allipay_orderid='".$allipay_orderid."'";
 $result = mysql_query($query) or die('Query failed: ' . mysql_error());  
 while ($row = mysql_fetch_object($result)) {
	$allipay_isconsumed = $row->allipay_isconsumed;
	$paystatus = $row->paystatus;
	$user_id = $row->user_id;
	$total_amount = $row->totalprice;
	$consumetime = $row->consumetime;
	break;
 }
 $total_amount = $total_amount;
 //$total_amount=1;
 $patystatus_str="待支付";
  
  
  $op="";
  if(!empty($_GET["op"])){
      $op = $configutil->splash_new($_GET["op"]);
	  if($op=="consume"){
	  	      
	      //确认消费
		 $query="select phone from weixin_users where id=".$user_id;
		 $result = mysql_query($query) or die('Query failed: ' . mysql_error());  
		 $mobile_phone="13790131605";
		 while ($row = mysql_fetch_object($result)) {
		     $mobile_phone = $row->phone;
		 }
		  if(empty($mobile_phone)){
			
			$mobile_phone = "13790131605";
		 }
		 $query = 'SELECT id,vendor_id,ison FROM allinpays where isvalid=true and customer_id='.$customer_id." limit 0,1";
		 $allinpay_id = -1;
		 
		 $appkey="";
		 $result = mysql_query($query) or die('Query failed: ' . mysql_error());  
		 while ($row = mysql_fetch_object($result)) {
			$allinpay_id = $row->id;
			$vendor_id = $row->vendor_id;
			break;
		 }
		 $appkey= ALLIPLAY_APPKEY;
		 $pwd = ALLIPLAY_PWD;
		 $timestamp =  date('YmdHis',time());
		 //echo $timestamp."<br/>";
		 $sign_method="md5";
		 $format="json";
		 $app_type = 6;
		 $session_id="";
		 $para_token = array(
				"timestamp" => $timestamp,
				"format" => "json",
				"app_key" => $appkey,
				"sign_method"	=> "md5",
				"mobile_phone"	=> $mobile_phone,
				"total_amount"	=> $total_amount,
				"app_type"	=> "6",
				"vendor_id"	=> $vendor_id,
				"order_id"	=> $allipay_orderid,
				"session_id"	=> "",
		);
		ksort($para_token);
		$sign  = '';
		foreach ($para_token AS $key => $value)
		{
			$sign  .= "{$value}";
		}
		$sign = $sign."&".$pwd;
		$sign = md5($sign);
        $openhost = "http://openapi.allinpaymall.com/aih";

		//$MENU_URL="http://116.236.252.102:1084/v2/order/create_outapp_order?timestamp=".$timestamp."&format=json&app_key=".$appkey."&sign=".$sign."&sign_method=".$sign_method."&session_id=&total_amount=".$totalprice."&app_type=6&vendor_id=".$vendor_id;
		$MENU_URL=$openhost."/v2/order/update_outapp_order?timestamp=".$timestamp."&format=json&app_key=".$appkey."&sign=".$sign."&sign_method=".$sign_method."&mobile_phone=".$mobile_phone."&session_id=&total_amount=".$total_amount."&app_type=6&vendor_id=".$vendor_id."&order_id=".$allipay_orderid;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1); 
		curl_setopt($ch, CURLOPT_URL, $MENU_URL);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$info = curl_exec($ch);
		curl_close($ch);
		$bus_order_id = "";
		$r = json_decode($info);
		if(!empty($r->{"Content"})){
		    $allipay_isconsumed= $r->{"Content"}->{"order_status"};
			if($allipay_isconsumed==1){
				$query="update weixin_commonshop_orders set allipay_isconsumed=".$allipay_isconsumed.",consumetime=now() where allipay_orderid='".$allipay_orderid."'";
				mysql_query($query);
				$consumetime=date("Y-m-d H:i:s",strtotime(time()));
			}
		}
	  }
  }
	
  if($paystatus==1){
     $patystatus_str="已支付";
  }
  
  $allipay_isconsumed_str="待消费";
  if($allipay_isconsumed){
     $allipay_isconsumed_str ="已消费";
  }
 
?>
<html>
<head>
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../css/css2.css" media="all">
<link href="../common/add/css/global.css" rel="stylesheet" type="text/css">
<link href="../common/add/css/main.css" rel="stylesheet" type="text/css">
<link href="../common/add/css/shop.css" rel="stylesheet" type="text/css">
<link type="text/css" rel="stylesheet" rev="stylesheet" href="../css/inside.css" media="all">
<script type="text/javascript" src="../common/js/jquery.js"></script>
<script type="text/javascript" src="../common/js/inside.js"></script>

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
	    通联支付单号：<?php echo $allipay_orderid; ?>
	</div>
	<div id="products" class="r_con_wrap">
	 <div class="r_con_form" >
		<div class="rows">
			<label>支付状态</label>
			<span class="input">
			   <?php  echo $patystatus_str; ?>
			</span>
			<div class="clear"></div>
		</div>
	   <?php if($version==1){ ?>
			<div class="rows">
				<label>是否已经消费</label>
				<span class="input">
				   <?php  echo $allipay_isconsumed_str; ?>
				  <?php  
					 if(!$allipay_isconsumed and $paystatus==1){
					  
					 ?>
						(<a href="javascript: G.ui.tips.confirm('您确定该笔订单已经付款了吗？确认后，不能取消','allipay_detail.php?op=consume&allipay_orderid=<?php echo $allipay_orderid; ?>')" style="color:#4d88d3">确认消费</a>)
			   <?php } ?>
				 <?php if($allipay_isconsumed){ ?>
					确认时间：<span style="color:red"><?php echo $consumetime; ?></span>
				 <?php } ?>
				</span>
				<div class="clear"></div>
			</div>
	   <?php } ?>
		
		<div class="rows">
			<label> </label>
			<span class="input">
				<input type=button class="button"  value="返回" onclick="document.location='order.php?customer_id=<?php echo $customer_id_en ?>';" />
      		</span>
			 <div class="clear"></div>
		</div>
	<input type=hidden name="order_id" value="<?php echo $order_id ?>" />
	</div>
</div>

<div style="width:100%;height:20px;">
</div>
<?php 

mysql_close($link);

?>
</div>
<script>
 function confirm_consume(order_id){
	 if(confirm)
	 url=""+order_id;
 }
</script>
</body>
</html>

