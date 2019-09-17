<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../back_init.php');
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$resultArr = array(); //用于JSON返回的结果

$op=$configutil->splash_new($_GET['op']);
if($op == 1){ //修改产品名
	$id=$configutil->splash_new($_GET['id']);
	$val=$configutil->splash_new($_GET['val']);
	$sql="update weixin_commonshop_products set name = '".$val."' where isvalid = true and  id=".$id;
	mysql_query($sql);
	$error=mysql_error();
	if($error!=""){
		$resultArr["code"] = 0;
		$resultArr["msg"] = "失败";
	}else{
		$resultArr["code"] = 1;
		$resultArr["msg"] = "成功";
	}
}else if($op == 2){ //获取产品的父分类
	$type_id=$configutil->splash_new($_GET['type_id']);
	//echo "  type_id : ".$type_id;
	$sql="select parent_id from weixin_commonshop_types where isvalid = true and id = ".$type_id;
	$result = mysql_query($sql);
	$parent_id = -1;
	if($row = mysql_fetch_object($result)){
		$parent_id = $row->parent_id;
	}
	//echo "  parent_id : ".$parent_id;
	if($parent_id <= 0){
		$resultArr["parent_id"] = $type_id;
		$resultArr["child_id"] = -1;
	}else{
		$resultArr["parent_id"] = $parent_id;
		$resultArr["child_id"] = $type_id;
	}
	$error=mysql_error();
	if($error!=""){
		$resultArr["code"] = 0;
		$resultArr["msg"] = "失败";
	}else{
		$resultArr["code"] = 1;
		$resultArr["msg"] = "成功";
	}
}else if($op == 3){ //修改产品分类
	$id=$configutil->splash_new($_GET['id']);
	$type_id=$configutil->splash_new($_GET['type_id']);
	$sql="update weixin_commonshop_products set type_ids = '".$type_id."' where isvalid = true and id=".$id;
	mysql_query($sql);
	$error=mysql_error();
	if($error!=""){ 
		$resultArr["code"] = 0;
		$resultArr["msg"] = "失败:".$error;
	}else{
		$resultArr["code"] = 1;
		$resultArr["msg"] = "修改成功";
	}
}else if($op == 4){ //修改产品属性
	$id          = $configutil->splash_new($_GET['id']);
	$isnew       = $configutil->splash_new($_GET['isnew']);
	$isout       = $configutil->splash_new($_GET['isout']);
	$ishot       = $configutil->splash_new($_GET['ishot']);
	$issnapup    = $configutil->splash_new($_GET['issnapup']);
	$isvp        = $configutil->splash_new($_GET['isvp']);
	$is_virtual  = $configutil->splash_new($_GET['is_virtual']);
	$isout_status = 0;	//供应商商品是否平台已确认上架:1.已上架 0.未上架
	
	if(0 == $isout){	//isout 0:上架 ,1:下架
		$isout_status = 1;	//将供应商字段改为上架
		
		
		// 查找供应商产品成本价和库存是否设置为0 ----start
			$sql_product="select cost_price,storenum,is_supply_id from weixin_commonshop_products where isvalid = true and id=".$id;
			$result_product = mysql_query($sql_product);
			$is_supply_id  = -1;  //成本价
			$tc_price  	   = -1;  //成本价
			$ts_num        = -1;  //库存
			$c_check       =  0;  //判断供货价是否为0 ; c_check 0:可修改上架 1:不可修改上架
			$num_check     =  0;  //判断库存是否为0 ; num_check 0:可修改上架 1:不可修改上架
			while($row = mysql_fetch_object($result_product)){
				$tc_price      = $row->cost_price;
				$ts_num        = $row->storenum;
				$is_supply_id  = $row->is_supply_id;
				/* if(0 >= $tc_price && 0 < $is_supply_id ){
					$resultArr["code"] = 0;
					$error = "供应商产品供货价不可为0";
					$resultArr["msg"] = "失败:".$error;
					echo json_encode($resultArr);
					exit;
				}  */
				if(0 >= $ts_num){
					$resultArr["code"] = 0;
					$error = "库存不可为0";
					$resultArr["msg"] = "失败:".$error;
					echo json_encode($resultArr);
					exit;
				}
				break;
			}
			$sql_price = "select cost_price,storenum from weixin_commonshop_product_prices where product_id=".$id;
			$result_price = mysql_query($sql_price) or die('L104 :'.mysql_error());
			$c_price = -1;
			$s_num   = -1;
			while($row = mysql_fetch_object($result_price)){
				$c_price = $row->cost_price;
				$s_num   = $row->storenum;
				/* if(0 >= $tc_price && 0 < $is_supply_id){
					 $resultArr["code"] = 0;
					 $error = "供应商产品供货价不可为0";
					 $resultArr["msg"] = "失败:".$error;
					 echo json_encode($resultArr);
					 exit;
				} */
				if(0 >= $s_num){
					$resultArr["code"] = 0;
					$error = "库存不可为0";
					$resultArr["msg"] = "失败:".$error;
					echo json_encode($resultArr);
					exit;
				}
			}
		// 查找供应商产品供货价和库存是否设置为0 ----end
	}

	$sql="update weixin_commonshop_products set isout_status = ".$isout_status.", isnew = ".$isnew.",isout = ".$isout.",ishot = ".$ishot.",issnapup = ".$issnapup.",isvp = ".$isvp.",is_virtual = ".$is_virtual." where isvalid = true and id=".$id;
	mysql_query($sql);
	$error=mysql_error();
	if($error!=""){
		$resultArr["code"] = 0;
		$resultArr["msg"] = "失败:".$error;
	}else{
		$resultArr["code"] = 1;
		$resultArr["msg"] = "修改成功";
	}
}else if($op == 5){ //获取价格
	$tpid=$configutil->splash_new($_GET['id']);
	$sql_product="select orgin_price,now_price,cost_price,need_score,for_price,storenum from weixin_commonshop_products where isvalid = true and id=".$tpid;
	$result_product = mysql_query($sql_product);
	$to_price = -1;
	$tn_price = -1;
	$tc_price = -1;
	$tb_price = -1;
	$tn_score = -1;
	$ts_num = -1;
	while($row = mysql_fetch_object($result_product)){
		$to_price = $row->orgin_price;
		$tn_price = $row->now_price;
		$tc_price = $row->cost_price;
		$tb_price = $row->for_price;
		$tn_score = $row->need_score;
		$ts_num = $row->storenum;
		
	}//总价相关
		
		
	$resultArr[0]['fpid'] = 0;
	$resultArr[0]['pid'] = $tpid;
	$resultArr[0]['proids'] = '产品';
	$resultArr[0]['o_price'] = $to_price;
	$resultArr[0]['n_price'] = $tn_price;
	$resultArr[0]['c_price'] = $tc_price;
	$resultArr[0]['b_price'] = $tb_price;
	$resultArr[0]['n_score'] = $tn_score;	
	$resultArr[0]['s_num'] = $ts_num;//库存
	
	$sql_price = "select id,proids,orgin_price,now_price,cost_price,need_score,storenum,for_price from weixin_commonshop_product_prices where product_id=".$tpid;
	$result_price = mysql_query($sql_price) or die('L104 :'.mysql_error());
	$i = 1;
	$opid = -1;
	$proids = -1;
	$o_price = -1;
	$n_price = -1;
	$c_price = -1;
	$b_price = -1;
	$n_score = -1;
	$s_num = -1;
	while($row = mysql_fetch_object($result_price)){
		$pid = $row->id;
		$proids = $row->proids;
		$o_price = $row->orgin_price;
		$n_price = $row->now_price;
		$c_price = $row->cost_price;
		$b_price = $row->for_price;
		$n_score = $row->need_score;
		$s_num = $row->storenum;
		//单价相关
		$resultArr[$i]['fpid'] = $pid;
		$resultArr[$i]['pid'] = $tpid;
		$resultArr[$i]['o_price'] = $o_price;
		$resultArr[$i]['n_price'] = $n_price;
		$resultArr[$i]['c_price'] = $c_price;
		$resultArr[$i]['b_price'] = $b_price;
		$resultArr[$i]['n_score'] = $n_score;
		$resultArr[$i]['s_num'] = $s_num;
		
		
		$proid = -1;
		if(strpos($proids,"_")){
			$proid = explode("_",$proids);
			$pname_a = "";
			$pname = "";
			foreach ($proid as $v=>$a){ 
				$sql_pname = "select name from weixin_commonshop_pros where isvalid = true and id=".$a." and customer_id=".$customer_id;
				$result_pname = mysql_query($sql_pname);				
				while($row = mysql_fetch_object($result_pname)){
					$pname_a = $row->name;				
				}
				if($pname_a!=""){
					$pname .=$pname_a.'/'; 
				}
			 }
			 $pname = substr($pname,0,-1);
		}else{
			$proid = $proids;
			$sql_pname = "select name from weixin_commonshop_pros where isvalid = true and id=".$proid." and customer_id=".$customer_id;
			$result_pname = mysql_query($sql_pname);
			$pname = "";
			while($row = mysql_fetch_object($result_pname)){
				$pname = $row->name;
			}
		}
		
		$resultArr[$i]['proids'] = $pname;	
		
		
		$i++;		
	}
}else if($op == 6){ //修改价格
	$pid=$configutil->splash_new($_GET['id']);
	$aids=$configutil->splash_new($_GET['aids']);
	$val_os=$configutil->splash_new($_GET['val_os']);
	$val_ns=$configutil->splash_new($_GET['val_ns']);
	$val_cs=$configutil->splash_new($_GET['val_cs']);
	$val_bs=$configutil->splash_new($_GET['val_bs']);
	$val_ss=$configutil->splash_new($_GET['val_ss']);

	$arr_ids = explode(",",$aids); //属性ID
	$arr_os = explode(",",$val_os); //原价
	$arr_ns = explode(",",$val_ns); //现价
	$arr_cs = explode(",",$val_cs); //供货价
	$arr_bs = explode(",",$val_bs); //成本价
	$arr_ss = explode(",",$val_ss); //所需积分
	
	for($i = 0 ; $i < count($arr_ids) ; $i++ ){
		$id = $arr_ids[$i]; 
		$arr_os[$i] = round($arr_os[$i], 2);
		$arr_ns[$i] = round($arr_ns[$i], 2);
		$arr_cs[$i] = round($arr_cs[$i], 2);
		$arr_bs[$i] = round($arr_bs[$i], 2);
		if($id == 0){
			$sql="update weixin_commonshop_products set orgin_price = '".$arr_os[$i]."',now_price = '".$arr_ns[$i]."',cost_price = '".$arr_cs[$i]."',need_score = '".$arr_ss[$i]."',for_price = '".$arr_bs[$i]."' where isvalid = true and  id=".$pid;
		}else{
			$sql="update weixin_commonshop_product_prices set orgin_price = '".$arr_os[$i]."',now_price = '".$arr_ns[$i]."',cost_price = '".$arr_cs[$i]."',need_score = '".$arr_ss[$i]."',for_price = '".$arr_bs[$i]."' where id=".$id;
		}mysql_query($sql);
	}$error=mysql_error();
	if($error!=""){
		$resultArr["code"] = 0;
		$resultArr["msg"] = "失败";
	}else{
		$resultArr["code"] = 1;
		$resultArr["msg"] = "成功";
	}
}else if($op == 7){ //修改库存
	$pid=$configutil->splash_new($_GET['id']);
	$aid=$configutil->splash_new($_GET['aid']);
	$val_s=$configutil->splash_new($_GET['val_s']);
	
	$arr_ids = explode(",",$aid); //属性ID
	$arr_s = explode(",",$val_s); //库存
	for($i = 0 ; $i < count($arr_ids) ; $i++ ){
		$id = $arr_ids[$i]; 
		if($id == 0){
			$sql="update weixin_commonshop_products set storenum = '".$arr_s[$i]."' where isvalid = true and id=".$pid;
		}else{
			$sql="update weixin_commonshop_product_prices set storenum = '".$arr_s[$i]."' where id=".$id;
		}mysql_query($sql);
	}$error=mysql_error();
	if($error!=""){
		$resultArr["code"] = 0;
		$resultArr["msg"] = "失败";
	}else{
		$resultArr["code"] = 1;
		$resultArr["msg"] = "成功";
	}
}
echo json_encode($resultArr);
?>