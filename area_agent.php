<?php
header("Content-type: text/html; charset=utf-8");
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]

//require('../back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../proxy_info.php');
require('select_skin.php');
/*require('../common/jssdk.php');
$jssdk = new JSSDK($customer_id);
$signPackage = $jssdk->GetSignPackage();*/
//头文件----start
require('../common/common_from.php');
//头文件----end

$p_people           =  0;				
$p_order            =  0;							
$c_people           =  0;				
$c_order            =  0;								
$a_people           =  0;					
$a_order            =  0;				
$condition2         =  0;
$is_showuplevel    	=  1;
$p_all_people       =  0;
$c_all_people       =  0;
$a_all_people       =  0;
$is_showcustomer    =  1;
$p_customer         =  '省代';
$c_customer         =  '市代';
$a_customer         =  '区代';		
$is_diy_area        =  0;		
$diy_people         =  0;
$diy_order          =  0;
$diy_all_people     =  0;
$diy_customer       =  '自定义区域';
$rule       		=  '';
$query = "select p_people,p_order,c_percent,c_people,c_order,a_percent,a_people,a_order,p_customer,c_customer,a_customer,is_showcustomer,condition2,is_showuplevel,p_all_people,c_all_people,a_all_people,is_diy_area,diy_people,diy_order,diy_all_people,diy_customer,rule from weixin_commonshop_team where isvalid = true and customer_id = ".$customer_id." limit 0,1";
$result = mysql_query($query) or die ("query failed".mysql_error());
while($row = mysql_fetch_object($result)){
		$p_people          = $row->p_people;				//省代直推人数
		$p_order           = $row->p_order;					//省代团队订单数
		$c_people          = $row->c_people;				//市代直推人数
		$c_order           = $row->c_order;					//市代团队订单数
		$a_people          = $row->a_people;				//区代直推人数
		$a_order           = $row->a_order;					//区代团队订单数
		$condition2        = $row->condition2;				//条件二选择
		$is_showuplevel    = $row->is_showuplevel;			//是否显示升级
		$p_all_people      = $row->p_all_people;			//升级省代所需推广员人数
		$c_all_people      = $row->c_all_people;			//升级市代所需推广员人数
		$a_all_people      = $row->a_all_people;			//升级区代所需推广员人数
		$is_showcustomer   = $row->is_showcustomer;	        //是否开启区域代理自定义
		$p_customer        = $row->p_customer;				//省代自定义名称
		$c_customer        = $row->c_customer;				//市代自定义名称
		$a_customer        = $row->a_customer;				//区代自定义名称
		$is_diy_area       = $row->is_diy_area;				//开启自定义区域
		$diy_people        = $row->diy_people;				//自定义级别直推人数
		$diy_order         = $row->diy_order;               //自定义级别团队订单数
		$diy_all_people    = $row->diy_all_people;          //升级自定义级别所需团队推广员人数
		$diy_customer      = $row->diy_customer;	        //自定义级别自定义名称
		$rule     		   = $row->rule;	       			//规则
}

if($is_showcustomer){
	if(empty($p_customer)){
		$p_customer = '省代';
	}
	if(empty($c_customer)){
		$c_customer = '市代';
	}
	if(empty($a_customer)){
		$a_customer = '区代';
	}
	if(empty($diy_customer)){
		$diy_customer = '自定义区域';
	}
}else{
	$p_customer   = '省代';
	$c_customer   = '市代';
	$a_customer   = '区代';
	$diy_customer = '自定义区域';
}

$isAgent 		= -1;	//5：区代，6：市代，7：省代，8:自定义区域	
$team_order 	= 0;	//团队订单数
$query2 = "select isAgent,team_order from promoters where isvalid=true and status=1 and customer_id=".$customer_id." and user_id=".$user_id;
$result2 = mysql_query($query2) or die('query failed2'.mysql_error());
while($row2 = mysql_fetch_object($result2)){
	$isAgent 		= $row2->isAgent;
	$team_order 	= $row2->team_order;
}
if($team_order<0){
	$team_order = 0;
}

$t_id 		 = -1;		//区域代理申请ID
$aplay_grate = -1;		//0：区代；1：市代；2：省代 3：自定义
$status 	 = -1;		//状态：0审核，1确认
$query3 = "select id,aplay_grate,status from weixin_commonshop_team_aplay where isvalid=true and customer_id=".$customer_id." and aplay_user_id=".$user_id." limit 0,1";
$result3 = mysql_query($query3) or die('query failed3'.mysql_error());
while($row3 = mysql_fetch_object($result3)){
	$t_id 		 = $row3->id;
	$aplay_grate = $row3->aplay_grate;
	$status 	 = $row3->status;
}

$generation=1;
$query4 = "select generation from weixin_users where isvalid=true and id=".$user_id;
$result4 = mysql_query($query4) or die('query failed:4'. mysql_error());
while ($row4 = mysql_fetch_object($result4)) {
	$generation = $row4->generation;     //当前用户的代数
}

$reward_level = 3;	//商城分佣级数
$is_team	  = 0;	//是否开启团队奖励
$query5 = "select reward_level,is_team from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result5 = mysql_query($query5) or die('Query failed5' . mysql_error());  
while ($row5 = mysql_fetch_object($result5)) {
	$reward_level = $row5->reward_level;
	$is_team 	  = $row5->is_team;
}

//商城分佣级数之内的团队推广员人数
$query6 = "SELECT count(distinct p.user_id) as count_prom FROM promoters p INNER JOIN weixin_users w ON w.id=p.user_id  WHERE  p.isvalid=TRUE AND p.status=1  AND match(w.gflag) against (',".$user_id.",') and w.generation between ".($generation+1)." and ".($generation+$reward_level);
$result6 = mysql_query($query6) or die('query failed6' . mysql_error());
$team_promoter_count = 0;
while ($row6 = mysql_fetch_object($result6)) {	 
	$team_promoter_count = $row6->count_prom;  
}
if($team_promoter_count<0){
	$team_promoter_count = 0;
}

//一级推广员人数
// $query7 = "SELECT count(distinct p.user_id) as count_prom FROM promoters p INNER JOIN weixin_users w ON w.id=p.user_id  WHERE  p.isvalid=TRUE AND p.status=1 and p.customer_id=".$customer_id." and w.customer_id=".$customer_id."  AND w.gflag LIKE '%,".$user_id.",%' and w.generation=".($generation+1);
$query7 = "SELECT count(distinct p.user_id) as count_prom FROM promoters p INNER JOIN weixin_users w ON w.id=p.user_id  WHERE  p.isvalid=TRUE AND p.status=1 and p.customer_id=".$customer_id." and w.customer_id=".$customer_id."  AND w.parent_id=".$user_id;
$result7 = mysql_query($query7) or die('query failed7' . mysql_error());
$promoter_count = 0;
while ($row7 = mysql_fetch_object($result7)) {	 
	$promoter_count = $row7->count_prom;  
}
if($promoter_count<0){
	$promoter_count = 0;
}

//是否申请过代理商
$query8 = "select id from weixin_commonshop_applyagents where isvalid=true and status=0 and user_id=".$user_id;
$result8 = mysql_query($query8) or die('query failed8'.mysql_error());
$ag_id = -1;
while($row8 = mysql_fetch_object($result8)){
	$ag_id = $row8->id;
}
//是否申请过供应商
$query9 = "select id from weixin_commonshop_applysupplys where isvalid=true and status=0 and user_id=".$user_id;
$result9 = mysql_query($query9) or die('query failed9'.mysql_error());
$as_id = -1;
while($row9 = mysql_fetch_object($result9)){
	$as_id = $row9->id;
}

if($ag_id>0 || $as_id>0 || $isAgent==1 || $isAgent==3){
	$is_showuplevel = 0;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>区域代理</title>
    <!-- 模板 -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="no" name="apple-touch-fullscreen">
    <meta name="MobileOptimized" content="320"/>
    <meta name="format-detection" content="telephone=no">
    <meta name=apple-mobile-web-app-capable content=yes>
    <meta name=apple-mobile-web-app-status-bar-style content=black>
    <meta http-equiv="pragma" content="nocache">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8">
    
    <link type="text/css" rel="stylesheet" href="./assets/css/amazeui.min.css" />
	<script type="text/javascript" src="./assets/js/jquery.min.js"></script> 
    
    <!-- 模板 -->
    <!-- 页联系style-->
    <link type="text/css" rel="stylesheet" href="./css/goods/global.css" />
	<link type="text/css" rel="stylesheet" href="./css/order_css/global.css" />
    <link type="text/css" rel="stylesheet" href="./css/goods/quyudailishang1-1.css" />
    <!-- 页联系style-->
    <link type="text/css" rel="stylesheet" href="./css/css_<?php echo $skin ?>.css" />  
</head>
<body data-ctrl=true style="">
	<!-- header部门-->
	<!-- <header data-am-widget="header" class="am-header am-header-default header">
		<div class="am-header-left am-header-nav header-btn">
			<img class="am-header-icon-custom"  src="./images/center/nav_bar_back.png"/><span>返回</span>
		</div>
	    <h1 class="header-title" style="font-size:18px;">区域代理商</h1>
	    <div class="am-header-right am-header-nav">
		</div>
	</header>
    <div class="topDiv" style="height:49px;"></div> -->   <!-- 暂时屏蔽头部 -->
	<!-- header部门-->
	
    <div class = "content" id="containerDiv">
	<!-- content rect -->
		<div class="content-main-title">
			<div class="leftLine" style="height: 372px;"></div>
			<span class="title-btn" style="margin-right:20px;" onclick="showAreaAgentMsg()">区域代理规则</span>
		</div>
		<!--dialog-->
		<div class="am-share shangpin-dialog dlg">
		  <!--dialog rect-->
	   </div>
		
		<?php
			if(1 == $is_diy_area){
		?>
		<div class="content-wrapper">
		<div class="leftLine" style="height: 372px;"></div>
		<div class="content-main">	
			<div class="m-chatting-body">		
				<div class="m-chatting-content">			
					<div class="content-row1">				
						<img src="./images/goods_image/20160050303.png" width="14" height="17">				
						<span><?php echo $diy_customer;?></span>			
					</div>			
					<div class="content-row2">				
						<div class="content-row2-item">					
							<div class="m-progressbar-body">						
								<div class="m-progressbar-content" style="width:<?php if(0==$diy_people){echo '100';}else{echo round(100*($promoter_count/$diy_people),2);}?>%;max-width:100%;"></div>					
							</div>					
							<div class="m-progressbar-remark">						
								<span>直接推广员数</span>						
								<div class="m-progressbar-remark-right">							
									<span><font><?php echo $promoter_count;?></font>/<font><?php echo $diy_people;?></font>人</span>						
								</div>					
							</div>				
						</div>
						<?php
							if(1 == $condition2){
						?>
						<div class="content-row2-item">					
							<div class="m-progressbar-body">						
								<div class="m-progressbar-content" style="width:<?php if(0==$diy_all_people){echo '100';}else{echo round(100*($team_promoter_count/$diy_all_people),2);}?>%;max-width:100%;"></div>					
							</div>					
							<div class="m-progressbar-remark">						
								<span>团队推广员数</span>						
								<div class="m-progressbar-remark-right">							
									<span><font><?php echo $team_promoter_count;?></font>/<font><?php echo $diy_all_people;?></font>人</span>						
								</div>					
							</div>				
						</div>
						<?php
							}else{
						?>						
						<div class="content-row2-item">					
							<div class="m-progressbar-body">						
								<div class="m-progressbar-content" style="width:<?php if(0==$diy_order){echo '100';}else{echo round(100*($team_order/$diy_order),2);}?>%;max-width:100%;"></div>					
							</div>					
							<div class="m-progressbar-remark">						
								<span>团队订单数</span>						
								<div class="m-progressbar-remark-right">							
									<span><font><?php echo $team_order;?></font>/<font><?php echo $diy_order;?></font>单</span>						
								</div>					
							</div>				
						</div>
						<?php }
						if(1==$is_team){
							if(1==$is_showuplevel && ((1==$condition2 && $promoter_count>=$diy_people && $team_promoter_count>=$diy_all_people) || (0==$condition2 && $promoter_count>=$diy_people && $team_order>=$diy_order))){	//判断是否满足条件
								if($t_id<0 && $isAgent<5){
						?>
						<div class="shengji_button apply_btn" data-grate="3" >
							<span>申请</span>
						</div>
							<?php }else if(3==$aplay_grate && 0==$status){?>
						<div class="shengji_button">
							<span>审核中</span>
						</div>
							<?php
								}else{
							?>
							<div class="shengji_button" style=" background:grey;">
								<span>申请</span>
							</div>
							<?php
								}
							}else{
							?>
						<div class="shengji_button" style=" background:grey;">
							<span>申请</span>
						</div>
						<?php 
							}
						}
						?>
					</div>		
				</div>	
			</div>
		</div>
	</div>
			<?php }?>
	<div class="content-wrapper">
		<div class="leftLine" style="height: 372px;"></div>
		<div class="content-main">
			<?php
				if(1 == $is_diy_area){
			?>
			<div class="content-main-title"></div>	
				<?php }?>
			<div class="m-chatting-body">		
				<div class="m-chatting-content">			
					<div class="content-row1">				
						<img src="./images/goods_image/20160050303.png" width="14" height="17">				
						<span><?php echo $a_customer;?></span>			
					</div>			
					<div class="content-row2">				
						<div class="content-row2-item">					
							<div class="m-progressbar-body">						
								<div class="m-progressbar-content" style="width:<?php if(0==$a_people){echo '100';}else{echo round(100*($promoter_count/$a_people),2);}?>%;max-width:100%;"></div>					
							</div>					
							<div class="m-progressbar-remark">						
								<span>直接推广员数</span>						
								<div class="m-progressbar-remark-right">							
									<span><font><?php echo $promoter_count;?></font>/<font><?php echo $a_people;?></font>人</span>						
								</div>					
							</div>				
						</div>		
						<?php
							if(1 == $condition2){
						?>
						<div class="content-row2-item">					
							<div class="m-progressbar-body">						
								<div class="m-progressbar-content" style="width:<?php if(0==$a_all_people){echo '100';}else{echo round(100*($team_promoter_count/$a_all_people),2);}?>%;max-width:100%;"></div>					
							</div>					
							<div class="m-progressbar-remark">						
								<span>团队推广员数</span>						
								<div class="m-progressbar-remark-right">							
									<span><font><?php echo $team_promoter_count;?></font>/<font><?php echo $a_all_people;?></font>人</span>						
								</div>					
							</div>				
						</div>		
						<?php
							}else{
						?>	
						<div class="content-row2-item">					
							<div class="m-progressbar-body">						
								<div class="m-progressbar-content" style="width:<?php if(0==$a_order){echo '100';}else{echo round(100*($team_order/$a_order),2);}?>%;max-width:100%;"></div>					
							</div>					
							<div class="m-progressbar-remark">						
								<span>团队订单数</span>						
								<div class="m-progressbar-remark-right">							
									<span><font><?php echo $team_order;?></font>/<font><?php echo $a_order;?></font>单</span>						
								</div>					
							</div>				
						</div>	
						<?php }
						if(1==$is_team){
							if(1==$is_showuplevel && ((1==$condition2 && $promoter_count>=$a_people && $team_promoter_count>=$a_all_people) || (0==$condition2 && $promoter_count>=$a_people && $team_order>=$a_order))){
								if(($t_id>0 && 3==$aplay_grate && ($isAgent<5 || $isAgent==8)) || ($t_id<0 && ($isAgent<5 || $isAgent==8))){
						?>
						<div class="shengji_button apply_btn" data-grate="0" >
							<span>申请</span>
						</div>
							<?php }else if(0==$aplay_grate && 0==$status){?>
						<div class="shengji_button">
							<span>审核中</span>
						</div>
							<?php
								}else{
							?>
							<div class="shengji_button" style=" background:grey;" >
								<span>申请</span>
							</div>
							<?php
								}
							}else{
							?>
							<div class="shengji_button" style=" background:grey;">
								<span>申请</span>
							</div>
							<?php
							}
						}
							?>	
					</div>		
				</div>	
			</div>
		</div>
	</div>
	
	<div class="content-wrapper">
		<div class="leftLine" style="height: 372px;"></div>
		<div class="content-main">	
			<div class="content-main-title"></div>	
			<div class="m-chatting-body">		
				<div class="m-chatting-content">			
					<div class="content-row1">				
						<img src="./images/goods_image/20160050303.png" width="14" height="17">				
						<span><?php echo $c_customer;?></span>			
					</div>			
					<div class="content-row2">				
						<div class="content-row2-item">					
							<div class="m-progressbar-body">						
								<div class="m-progressbar-content" style="width:<?php if(0==$c_people){echo '100';}else{echo round(100*($promoter_count/$c_people),2);}?>%;max-width:100%;"></div>					
							</div>					
							<div class="m-progressbar-remark">						
								<span>直接推广员数</span>						
								<div class="m-progressbar-remark-right">							
									<span><font><?php echo $promoter_count;?></font>/<font><?php echo $c_people;?></font>人</span>						
								</div>					
							</div>				
						</div>
						<?php
							if(1 == $condition2){
						?>
						<div class="content-row2-item">					
							<div class="m-progressbar-body">						
								<div class="m-progressbar-content" style="width:<?php if(0==$c_all_people){echo '100';}else{echo round(100*($team_promoter_count/$c_all_people),2);}?>%;max-width:100%;"></div>					
							</div>					
							<div class="m-progressbar-remark">						
								<span>团队推广员数</span>						
								<div class="m-progressbar-remark-right">							
									<span><font><?php echo $team_promoter_count;?></font>/<font><?php echo $c_all_people;?></font>人</span>						
								</div>					
							</div>				
						</div>
						<?php
							}else{
						?>	
						<div class="content-row2-item">					
							<div class="m-progressbar-body">						
								<div class="m-progressbar-content" style="width:<?php if(0==$c_order){echo '100';}else{echo round(100*($team_order/$c_order),2);}?>%;max-width:100%;"></div>					
							</div>					
							<div class="m-progressbar-remark">						
								<span>团队订单数</span>						
								<div class="m-progressbar-remark-right">							
									<span><font><?php echo $team_order;?></font>/<font><?php echo $c_order;?></font>单</span>						
								</div>					
							</div>				
						</div>	
						<?php }
						if(1==$is_team){
							if(1==$is_showuplevel && ((1==$condition2 && $promoter_count>=$c_people && $team_promoter_count>=$c_all_people) || (0==$condition2 && $promoter_count>=$c_people && $team_order>=$c_order))){
								if($aplay_grate!=1 && $aplay_grate!=2 && ($isAgent<6 || $isAgent==8)){
						?>
						<div class="shengji_button apply_btn" data-grate="1">
							<span>申请</span>
						</div>
							<?php }else if(1==$aplay_grate && 0==$status){?>
						<div class="shengji_button">
							<span>审核中</span>
						</div>
							<?php
								}else{
							?>
							<div class="shengji_button" style=" background:grey;">
								<span>申请</span>
							</div>
							<?php
								}
							}else{
							?>
							<div class="shengji_button" style=" background:grey;">
								<span>申请</span>
							</div>
							<?php
							}
						}
							?>				
					</div>		
				</div>	
			</div>
		</div>
	</div>
	
	<div class="content-wrapper">
		<div class="leftLine" style="height: 372px;"></div>
		<div class="content-main">	
			<div class="content-main-title"></div>	
			<div class="m-chatting-body">		
				<div class="m-chatting-content">			
					<div class="content-row1">				
						<img src="./images/goods_image/20160050303.png" width="14" height="17">				
						<span><?php echo $p_customer;?></span>			
					</div>			
					<div class="content-row2">				
						<div class="content-row2-item">					
							<div class="m-progressbar-body">						
								<div class="m-progressbar-content" style="width:<?php if(0==$p_people){echo '100';}else{echo round(100*($promoter_count/$p_people),2);}?>%;max-width:100%;"></div>					
							</div>					
							<div class="m-progressbar-remark">						
								<span>直接推广员数</span>						
								<div class="m-progressbar-remark-right">							
									<span><font><?php echo $promoter_count;?></font>/<font><?php echo $p_people;?></font>人</span>						
								</div>					
							</div>				
						</div>
						<?php
							if(1 == $condition2){
						?>
						<div class="content-row2-item">					
							<div class="m-progressbar-body">						
								<div class="m-progressbar-content" style="width:<?php if(0==$p_all_people){echo '100';}else{echo round(100*($team_promoter_count/$p_all_people),2);}?>%;max-width:100%;"></div>					
							</div>					
							<div class="m-progressbar-remark">						
								<span>团队推广员数</span>						
								<div class="m-progressbar-remark-right">							
									<span><font><?php echo $team_promoter_count;?></font>/<font><?php echo $p_all_people;?></font>人</span>						
								</div>					
							</div>				
						</div>
							<?php 
								}else{
							?>
						<div class="content-row2-item">					
							<div class="m-progressbar-body">						
								<div class="m-progressbar-content" style="width:<?php if(0==$p_order){echo '100';}else{echo round(100*($team_order/$p_order),2);}?>%;max-width:100%;"></div>					
							</div>					
							<div class="m-progressbar-remark">						
								<span>团队订单数</span>						
								<div class="m-progressbar-remark-right">							
									<span><font><?php echo $team_order;?></font>/<font><?php echo $p_order;?></font>单</span>						
								</div>					
							</div>				
						</div>
						<?php }
						if(1==$is_team){
							if(1==$is_showuplevel && ((1==$condition2 && $promoter_count>=$p_people && $team_promoter_count>=$p_all_people) || (0==$condition2 && $promoter_count>=$p_people && $team_order>=$p_order))){
								if($aplay_grate!=2 && ($isAgent!=7)){
						?>
						<div class="shengji_button apply_btn" data-grate="2">
							<span>申请</span>
						</div>
							<?php }else if(2==$aplay_grate && 0==$status){?>
						<div class="shengji_button">
							<span>审核中</span>
						</div>
							<?php
								}else{
							?>
							<div class="shengji_button" style=" background:grey;" >
								<span>申请</span>
							</div>
							<?php
								}
							}else{
							?>
							<div class="shengji_button" style=" background:grey;">
								<span>申请</span>
							</div>
							<?php
							}
						}
							?>			
					</div>		
				</div>	
			</div>
		</div>
		<div class = "content-last"></div>
	</div>
   </div>
   <div id="rule" style="display:none;"><?php echo $rule;?></div>
</body>		
<!-- 页联系js -->
<script type="text/javascript" src="./assets/js/amazeui.js"></script>
<script type="text/javascript" src="./js/global.js"></script>
<script type="text/javascript" src="./js/loading.js"></script>
<script src="./js/jquery.ellipsis.js"></script>
<script src="./js/jquery.ellipsis.unobtrusive.js"></script>
<script src="./js/goods/global.js"></script>
<script src="./js/global.js"></script>
<script src="./js/goods/area_agent.js"></script>
<script>
var ag_id 		   = '<?php echo $ag_id;?>';
var as_id 		   = '<?php echo $as_id;?>';
var isAgent 	   = '<?php echo $isAgent;?>';
var customer_id    = '<?php echo $customer_id;?>';
var customer_id_en = '<?php echo $customer_id_en;?>';
var post_data   = new Array();
var post_object  = new Array();

if(ag_id>0){
	showAlertMsg ("提示：","您已经申请了代理商","知道了");
}else if(as_id>0){
	showAlertMsg ("提示：","您已经申请了供应商","知道了");
}
</script>
<!--引入微信分享文件----start-->
<script>
<!--微信分享页面参数----start-->
debug=false;
share_url=''; //分享链接
title=""; //标题
desc=""; //分享内容
imgUrl="";//分享LOGO
share_type=3;//自定义类型
<!--微信分享页面参数----end-->
</script>
<?php require('../common/share.php');?>
<!--引入微信分享文件----end-->
<!-- 页联系js -->
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->   
</body>
</html>