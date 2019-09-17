<?php
 header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../../../proxy_info.php');
require('../../../common/utility_4m.php');

/***********4m start*********/

$u4m = new Utiliy_4m();
$rearr = $u4m->is_4M($customer_id);

//是4m分销
$is_shopgeneral = $rearr[0]  ;
//厂家编号
$adminuser_id = $rearr[1] ;
//是否是厂家总店
$is_samelevel = $rearr[2] ;
//总店模板编号
$general_template_id = $rearr[3] ;
//总店商家编号
$general_customer_id = $rearr[4] ;

//是否本身就是厂家总店
//1：厂家总店； 2：代理商总店
$owner_general = $rearr[5] ;

$orgin_adminuser_id = $rearr[6] ;

//获取下级所有的权限控制 by @ye
$getAllSubcontrol = $u4m->getAllSubcontrol($adminuser_id);

//var_dump($getAllSubcontrol);
//查询商家是否有上传产品权限
$is_upload_pros = $u4m->check_cus_authority($customer_id,$getAllSubcontrol,1);

//查询商家是否有修改产品价格权限
$is_change_pros_price = $u4m->check_cus_authority($customer_id,$getAllSubcontrol,2);


/***********4m end*********/


$head = 0;//
$p_id = -1;
$query = "select * from product_code_customer_info_t where isvalid=true and class=-1 and customer_type=-1 and customer_id=".$customer_id." limit 0,1";
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
$customer_name    	 = ""; //企业名称
$brand 		  	  	 = ""; //产品品牌
$area 			  	 = ""; //限售区域
$institution_code 	 = ""; //组织机构代码
$license_code 	  	 = ""; //营业执照号码
$website 		  	 = ""; //企业网址
$address		 	 = ""; //企业地址
$customer_phone	  	 = ""; //客服热线
$report_phone 	 	 = ""; //打假热线
$kid  		 	 	 = -1; //id

$is_Cname  		  	 = 1; //商家公司名称开关
$is_brand  		  	 = 1; //产品品牌开关
$is_area  		  	 = 1; //区域名称开关
$is_institution_code = 1; //组织机构代码开关
$is_license_code	 = 1; //营业执照号码开关
$is_website  		 = 1; //企业网址开关
$is_address  		 = 1; //企业地址开关
$is_customer_phone	 = 1; //客服热线开关
$is_report_phone	 = 1; //打假热线开关

while ($row = mysql_fetch_object($result)) {
	$kid		 	  	 = $row->id;
	$customer_name 	  	 = $row->customer_name;
	$brand 			  	 = $row->brand;
	$area 			 	 = $row->area;
	$institution_code 	 = $row->institution_code;
	$license_code 	  	 = $row->license_code;
	$website 		  	 = $row->website;
	$address 		  	 = $row->address;
	$customer_phone   	 = $row->customer_phone;
	$report_phone     	 = $row->report_phone;
	
	$is_Cname        	 = $row->is_Cname;
	$is_brand        	 = $row->is_brand;
	$is_area        	 = $row->is_area;
	$is_institution_code = $row->is_institution_code;
	$is_license_code	 = $row->is_license_code;
	$is_website          = $row->is_website;
	$is_address          = $row->is_address;
	$is_customer_phone	 = $row->is_customer_phone;
	$is_report_phone	 = $row->is_report_phone;
}
	
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Base/basicdesign/base_set.css">
<script type="text/javascript" src="../../../common/js/jquery-2.1.0.min.js"></script>

<title>商家资料</title>
<style>
.WSY_remind_dl02 dd input{
	float:left;
	margin-right:10px;
}
</style>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
</head>
<body>
<div class="WSY_content">
	<div class="WSY_columnbox">
		<?php
			require('public/head.php');
		?>		
	<form action="code/save_base.php?customer_id=<?php echo $customer_id_en; ?>" enctype="multipart/form-data" method="post" id="saveFrom" name="saveFrom">
		<input type=hidden name="kid" id="kid" value="<?php echo $kid; ?>" />
		<input type=hidden name="p_id" id="p_id" value="<?php echo $p_id; ?>" />
		<input type=hidden name="customer_type" id="customer_type" value="-1" />
		<div class="WSY_remind_main">
			<dl class="WSY_remind_dl02"> 
				<dt>企业名称：</dt>
				<dd>
					<input type="text" name="customer_name" value="<?php echo $customer_name; ?>" maxlength="30" notnull="">
					<?php if($is_Cname==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="chage_Cname(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="chage_Cname(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="chage_Cname(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="chage_Cname(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="is_Cname" id="is_Cname" value="<?php echo $is_Cname; ?>" />	
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>产品品牌：</dt>
				<dd>
					<input type="text" name="brand" value="<?php echo $brand; ?>" maxlength="30" notnull="">
					<?php if($is_brand==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="chage_brand(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="chage_brand(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="chage_brand(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="chage_brand(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="is_brand" id="is_brand" value="<?php echo $is_brand; ?>" />
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>限售区域：</dt>
				<dd>
					<input type="text" name="area" value="<?php echo $area; ?>" maxlength="30" notnull="">
					<?php if($is_area==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="chage_area(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="chage_area(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="chage_area(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="chage_area(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="is_area" id="is_area" value="<?php echo $is_area; ?>" />
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>组织机构代码：</dt>
				<dd>
					<input type="text" name="institution_code" value="<?php echo $institution_code; ?>" maxlength="30" notnull="">
					<?php if($is_institution_code==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="chage_institution_code(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="chage_institution_code(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="chage_institution_code(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="chage_institution_code(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="is_institution_code" id="is_institution_code" value="<?php echo $is_institution_code; ?>" />
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>营业执照号码：</dt>
				<dd>
					<input type="text" name="license_code" value="<?php echo $license_code; ?>" maxlength="30" notnull="">
					<?php if($is_license_code==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="chage_license_code(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="chage_license_code(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="chage_license_code(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="chage_license_code(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="is_license_code" id="is_license_code" value="<?php echo $is_license_code; ?>" />
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>企业网址：</dt>
				<dd>
					<input type="text" name="website" value="<?php echo $website; ?>" maxlength="30" notnull="">
					<?php if($is_website==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="chage_website(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="chage_website(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="chage_website(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="chage_website(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="is_website" id="is_website" value="<?php echo $is_website; ?>" />
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>企业地址：</dt>
				<dd>
					<input type="text" name="address" value="<?php echo $address; ?>" maxlength="30" notnull="">
					<?php if($is_address==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="chage_address(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="chage_address(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="chage_address(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="chage_address(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="is_address" id="is_address" value="<?php echo $is_address; ?>" />
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>客服热线：</dt>
				<dd>
					<input type="text" name="customer_phone" value="<?php echo $customer_phone; ?>" maxlength="30" notnull="">
					<?php if($is_customer_phone==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="chage_customer_phone(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="chage_customer_phone(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="chage_customer_phone(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="chage_customer_phone(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="is_customer_phone" id="is_customer_phone" value="<?php echo $is_customer_phone; ?>" />
				</dd>
			</dl>
			<dl class="WSY_remind_dl02"> 
				<dt>打假热线：</dt>
				<dd>
					<input type="text" name="report_phone" value="<?php echo $report_phone; ?>" maxlength="30" notnull="">
					<?php if($is_report_phone==1){ ?>
						<ul style="background-color: rgb(255, 113, 112);margin-top:2px;">
							<p style="color: rgb(255, 255, 255); margin: 0px 0px 0px 22px;">开</p>
							<li onclick="chage_report_phone(0)" class="WSY_bot" style="left: 0px;"></li>
							<span onclick="chage_report_phone(1)" class="WSY_bot2" style="display: none; left: 0px;"></span>
						</ul>																
					<?php }else{ ?>
						<ul style="background-color: rgb(203, 210, 216);margin-top:2px;">
							<p style="color: rgb(127, 138, 151); margin: 0px 0px 0px 6px;">关</p>
							<li onclick="chage_report_phone(0)" class="WSY_bot" style="display: none; left: 30px;"></li>
							<span onclick="chage_report_phone(1)" class="WSY_bot2" style="display: block; left: 30px;"></span>
						</ul>						
					<?php } ?>
					<input type="hidden" name="is_report_phone" id="is_report_phone" value="<?php echo $is_report_phone; ?>" />
				</dd>
			</dl>
		</div>
		
	</form>
	<div class="submit_div">
			<input type="button" class="WSY_button" value="提交" onclick="formSubmit()" style="cursor:pointer;">
		</div>
	</div>
</div> 
<script type="text/javascript">
page_index = 5;
function formSubmit()
{
	document.getElementById("saveFrom").submit()
}
function chage_Cname(obj){
	$("#is_Cname").val(obj);
}
function chage_brand(obj){
	$("#is_brand").val(obj);
}
function chage_area(obj){
	$("#is_area").val(obj);
}
function chage_institution_code(obj){
	$("#is_institution_code").val(obj);
}
function chage_license_code(obj){
	$("#is_license_code").val(obj);
}
function chage_website(obj){
	$("#is_website").val(obj);
}
function chage_address(obj){
	$("#is_address").val(obj);
}
function chage_customer_phone(obj){
	$("#is_customer_phone").val(obj);
}
function chage_report_phone(obj){
	$("#is_report_phone").val(obj);
}
</script>
<script type="text/javascript" src="../../../common/js_V6.0/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../Common/js/Product/product_common.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/content.js"></script>

</body>
</html>