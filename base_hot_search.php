<?php 
//require('../logs.php');   
header("Content-type: text/html; charset=utf-8"); 
require('../../config.php');
require('../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../back_init.php');

  
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../proxy_info.php');
mysql_query("SET NAMES UTF8");
require('../../common/tupian/CreateExpQR.php');
//require('../../auth_user.php');


$query ="select isOpenPublicWelfare from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $isOpenPublicWelfare = $row->isOpenPublicWelfare;
	}

$is_distribution=0;//渠道取消代理商功能
//代理模式,分销商城的功能项是 266
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scdl' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('Query failed: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_distribution=1;
}
$is_supplierstr=0;//渠道取消供应商功能
//供应商模式,渠道开通与不开通
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scgys' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('Query failed: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_supplierstr=1;
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>热门搜索设置</title>
<link rel="stylesheet" type="text/css" href="../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../../common/js_V6.0/assets/js/jquery.min.js"></script>

<style>
/*产品分类*/
.classificationbox{overflow:hidden;margin-bottom:20px;}
.content{width:460px;}
.classify{width:322px;}
#classify{width:330px;}
.content,.classify,.show{/*height:420px;*/background:#fff;border:1px solid #c6c6c6;margin-top:30px;margin-left:25px;float:left;position:relative;}
.conterimgbox{height:36px;background:#e7e7e7;margin-top:24px;display:block;position:relative;margin-left:10px;margin-right:10px;}
.icon-text{display:block;float:left;line-height:36px;font-size:16px;margin-left:6px;/*background:url(../images/menu_icon/icon1112/text-icon.png) no-repeat left center;*/
	padding-left:20px;}
.caozuo_right{display:block;float:right;overflow:hidden;}
.caozuo_right a,.WSY_botton_box{display:block;float:left;margin-right:10px;margin-top:10px;}
.caozuo_right a img{display:block;width:18px;height:18px;}
.caozuo_right .WSY_botton_box{margin-top:10px;}
.right_10px{margin-right:35px;}
.caozuo_right .conter_load img{width:18px;height:18px;}
.child_type{background:#fff;margin-top:-1px;border:1px solid #e7e7e7;}
.child_type .icon-text{margin-left:30px;}
.child_type .check-on{left:215px;} 
.child_type .compile{left:286px;}
.child_type .conter_delete{left:320px;}
.child_type .conter_uploading{left:350px;}
.child_type .conter_download{left:380px;}
#conterimgbox2{margin-top:10px;}
.classify_text{font-size:16px;display:block;margin-top:-10px;margin-left:30px;background:#fbfbfb;width:130px;text-align:center;}
.classify_name{float:left;display:block;margin-top:28px;margin-left:20px;font-size:14px;}
.classify_name input{width:212px;height:24px;border:solid 1px #dadada;margin-left:12px;border-radius:2px;}
.classify_span select{width:212px;height:24px;padding:3px;border-radius:3px;display:inline-block;border:solid 1px #dadada;}
.classify_span{float:left;margin-left:18px;margin-top:10px;font-size:14px;}  
.classify_span input{margin-left:-10px;}
.classify_content{width:290px;height:250px;border:solid 1px #d0d0d0;position:absolute;left:14px;top:117px;}
.white{margin-left:10px;}
.classify_content p{width:240px;font-size:14px;margin-top:16px;margin-left:10px;}
.classify_content_img{border:0;margin-left:5px;margin-top:10px;display:block;}
.classify_content-input{position:absolute; bottom:10px;text-align:center;display:block;left:40px;}

.show_text{font-size:16px;background:#fff;margin-left:30px;margin-top:-10px;display:block;width:140px;}
.show_img{overflow:hidden;margin-left:26px;margin-top:24px;}
.show_img a{float:left;margin-right:15px;margin-bottom:30px;}
.show_button{display:block;text-align:center;}
.show_button2{width:110px;height:30px;background:#07a7e1;border:1px solid #056f9f;border-radius:3px;cursor:pointer;font-size:16px;font-family:"微软雅黑";color:#fff;}
.list_right{float:right;/*margin-left:20px;*//*width:270px;*/margin-top:-58px;height:140px;}
.list_right form{padding:10px;background:f7f7f7;zoom:1;}
.list_right span{font-size:16px;background:#fbfbfb;margin-top:-15px;display:block;margin-left:10px;text-align:center;width:90px;}
.list_right .opt_item #pro-list-type2 {height:135px;}
.list_right .opt_item #pro-list-type2 li{float:left;width:100px;height:140px;overflow:hidden;padding:25px 0 15px 15px;}
.list_right .opt_item #pro-list-type2 li .item{position:relative;width:100px;height:135px;}
.list_right .opt_item #pro-list-type2 li .item .img{position:absolute;width:100px;height:135px;z-index:1;}
.list_right .opt_item #pro-list-type2 li .item .filter{position:absolute;width:100px;height:135px;z-index:2;}
.list_right .opt_item #pro-list-type2 li .item .bg{position:absolute;width:100px;height:135px;z-index:3;}
.btn_green{  background: #07a7e1;border: 1px solid #056f9f;width: 110px;height: 30px;font-size: 16px;color: #fff;font-family: "微软雅黑";border-radius: 3px;cursor: pointer;margin-top:5px;margin-left:16px;display:inline-block;}
.list_right .opt_item #pro-list-type2 li .item_on .bg{background:url(../../Common/images/Product/group/selected-icon.png) no-repeat center center;}
.list_right .opt_item #pro-list-type2 li .item_on .filter{background:#000; opacity:0.6;}
.opactiy{width:278px;height:20px;background:#000;opacity:0.5;display:block;margin-top:-22px;line-height:20px;text-align:center;color:#fff;}

.classify_name_text input{width:150px;}
.WSY_botton_box{
	width:60px;
}
</style>
</head>

<body>
<!--内容框架开始-->
<div class="WSY_content">

       <!--列表内容大框开始-->
	<div class="WSY_columnbox">
    	<?php require('head.php');?>
 <div class="WSY_data">
 
<div class="classificationbox">
        	<!--<div class="content" style="overflow-y: scroll;  padding-bottom: 20px;">-->
			<div class="content" style="padding:0 10px 20px 10px;min-height: 140px;">
			
			 <?php 
			   $query= "select id,name,relate_type_id,asort from weixin_commonshop_hot_search where isvalid=true and customer_id=".$customer_id." order by asort asc";
			   // echo $query.'<br>';
			   $result = mysql_query($query) or die('Query failed: ' . mysql_error());
			   $rnum = mysql_num_rows($result);
			   
			   if($rnum>0){
				   
				?>   
			<div style="padding:10px;text-align:right">
				<input type="button" value="保存排序" class="classify_input" id="btn_savesort" onclick="save_sort()"/>
			</div>   
				   
				<?php 

					$s_id = 0;
					$pt_name = '';
					$relate_type_id = -1;
					$asort = 0;
				   while ($row = mysql_fetch_object($result)) {
					   $s_id = $row->id;
					   $pt_name = $row->name;				  
					   $relate_type_id = $row->relate_type_id;				  
					   $asort = $row->asort;
				
				 
					
			?>
                <div class="conterimgbox hot_search" data-id="<?php echo $s_id;?>" id="hot_search_<?php echo $s_id;?>" data-sort="<?php echo $asort;?>" sort="<?php echo $asort;?>">
                         <a href="#" title="<?php echo $pt_name;?>" class="icon-text"><?php echo $pt_name;?></a>
                         <div class="caozuo_right">
						 					
                             <a href="javascript:edit('<?php echo $s_id ;?>')" class="compile" title="编辑"><img src="../../common/images_V6.0/operating_icon/icon05.png" /></a>
                             <a href="javascript:del('<?php echo $s_id ;?>')" class="conter_delete" title="删除"><img src="../../common/images_V6.0/operating_icon/icon04.png"/> </a>
						
						 <a href="javascript:up('<?php echo $s_id ;?>')" class="conter_uploading" title="向上"><img src="../../common/images_V6.0/operating_icon/icon32.png"/> </a>
                             <a href="javascript:down('<?php echo $s_id ;?>')" class="conter_download" title="向下"><img src="../../common/images_V6.0/operating_icon/icon33.png"/> </a>
                            
						 </div>
               	</div>
				  
			  <?php	} 
				}
			  ?>
            </div>
			
			
                <div class="classify">
                		<p class="classify_text">添加搜索关键词</p>
                        <p class="classify_name">关键词名称:<input type="text" name="name" value="<?php echo $producttype_name; ?>" id="name"></p>
							<span class="classify_span">关联分类：
								<select name="relate_type_id" >
                                    <option value="-1">请选择</option>
									<?php
									  $query = "select id, name from weixin_commonshop_types where isvalid=true and customer_id=".$customer_id;
									  $result = mysql_query($query) or die('Query failed: ' . mysql_error());
									   while ($row = mysql_fetch_object($result)) {
										   $types_id_id = $row->id;
										   $types_id_name = $row->name;
									 ?>
                                    <option value="<?php echo $types_id_id; ?>" ><?php echo $types_id_name; ?></option>
                                    <?php } ?>
                                </select>
                           </span>				
					
					  <div class="classify_content-input">
							<button type="button" class="classify_input" id="saveProtype" tyid=1>添加关键词</button>
                            <button type="button" class="classify_input2" id="returnBack">返回</button>
                      </div>
					   <div class="list_right">
						
						</div>
			
				<input type="hidden" id="relate_type_id" name="relate_type_id" value="<?php echo $relate_type_id; ?>" />
				</div>
 </div>

</div>
</div>
<script type="text/javascript">
customer_id_en = '<?php echo $customer_id_en;?>';
page_index = 0;
var search_type_id = null;
var relate_type_id = 0;
var keyid = 0;
var asort = 0;
var sortdata = '';

var page_index = 1;
$(function(){
	$(".WSY_columnnav a").removeClass("white1");
	$(".WSY_columnnav a").eq(page_index).addClass("white1");
});
</script>
<!--内容框架结束-->


<script type="text/javascript">


function save_sort(){
	var array = new Array();
	$('.hot_search').each(function(){
		var id = $(this).data('id');
		var sort = $(this).attr('sort');
		var arr = new Array();
			arr[0]=id;
			arr[1]=parseInt(sort);
		//console.log(arr.length); 
		array.push(arr);
	});
	//console.log(array);
	sortdata = JSON.stringify(array);
	//console.log(sortdata);
	ajax_data('save_sort');		
}

	function down(typeId){
		
		var search = $("#hot_search_"+typeId);
		var nextsearch = search.nextAll(".hot_search").first();
		var self_sort = search.attr('sort');
		var next_sort = nextsearch.attr('sort');
		//console.log(self_sort);
		//console.log(next_sort);
		search.attr("sort",next_sort);		
		nextsearch.attr("sort",self_sort);
		search.insertAfter(nextsearch);
		
		
		
	}
	
	
	
	function up(typeId){
		
		var search = $("#hot_search_"+typeId);
		var presearch = search.prevAll(".hot_search").first();
		//console.log(presearch);
		
		var self_sort = search.attr('sort');
		var pre_sort = presearch.attr('sort');
		
		//console.log(self_sort);
		//console.log(pre_sort);
		
		//交换排序
			
		search.attr("sort",pre_sort);		
		presearch.attr("sort",self_sort);
		
		search.insertBefore(presearch);

		
	}

	
	function del(id){
		if(confirm("您是否确定要删除该关键词？")){
			//console.log("search - id : "+id);
			keyid = id;
			ajax_data('del');
			$('#hot_search_'+id).hide();		
			
		}
	}
	
	//编辑
	function edit(id){
		keyid = id;
		ajax_data('detail');

		
	}
	
	$(function(){
		//修改按钮
		var name=null;
		$("#saveProtype").click(function(){
			 name = $("#name").val();
			if(name == ""){
				alert("搜索名称必填！");
				return;
			}

			var tyid = $("#saveProtype").attr('tyid');
			
			//无需var 
			name = $('#name').val();
			
			relate_type_id = $('#relate_type_id').val();
			//console.log(name);
			//console.log(relate_type_id);
			if(relate_type_id == -1 || relate_type_id == ''){
					alert("请选择分类！");
					return;
				}
			if(tyid==1){			//新增				
				ajax_data('add',name);		
			}else if(tyid==2){		//更新	
				ajax_data('update',name);
			}
			
		
		});
		//返回按钮
		$("#returnBack").click(function(){
			location.href='base_inventory_remind.php?customer_id=<?php echo $customer_id_en;?>';
		});
		$("#btn_savesort").click(function(){
			var sort_str = "";
			$(".conterimgbox").each(function(i,ele){
				sort_str = sort_str+","+$(ele).data("id");
			});
			
		});
	});
	
	function ajax_data(op,name){
		//console.log('ajax_data');

		$.ajax({
		url:"save_base_hot_search.php?op="+op, 
		data:{
		name:name,
		asort:asort,
		relate_type_id:relate_type_id,
		keyid:keyid,
		sortdata:sortdata	
		},
		type: "POST",
        dataType:'json',
	    async: true,      
		success:function(json){
				//console.log(json);
				if(json.code == 10001){			//添加				
					alert(json.msg);
					history.go();
				}else if(json.code == 10002){	//更新					
					alert(json.msg);
					history.go();
				}else if(json.code == 10003){	//删除	
					alert(json.msg);
					$('#hot_search_'+keyid).hide();
				}else if(json.code == 10005){			//查询数据											
					edit_show(json);			//显示数据
				}else if(json.code == 10006){	//保存排序	
					
					alert(json.msg);
								
				}else if(json.code == 10004){	//失败
					
					alert(json.msg);
				}
				
			}
		});
	}

// 编辑显示	
function edit_show(json){
		console.log(json.name);
		$('#name').val(json.name);
		$("select").val(json.relate_type_id); 
		$("#saveProtype").attr('tyid',2)	//改变按钮功能为更改
		$("#saveProtype").text('保存关键词');
	}
	
$(function(){
		
 $('select').change(function(){
		
		var _a = $(this).children('option:selected').val();
		console.log(_a);
		$('#relate_type_id').val(_a);
			
	}); 
	
	
});
</script>
</body>
</html>

