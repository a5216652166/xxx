<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link href="__ROOT__/Public/css/basic_layout.css?200" rel="stylesheet" type="text/css">
<link href="__ROOT__/Public/css/common_style.css?200" rel="stylesheet" type="text/css">

<script type="text/javascript" src="__ROOT__/Public/js/jquery/jquery-1.7.1.js"></script>
<script type="text/javascript" src="__ROOT__/Public/js/authority/commonAll.js?200"></script>

<link href="__ROOT__/Public/js/jquery.chosen/chosen.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="__ROOT__/Public/js/jquery.chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="__ROOT__/Public/js/artDialog/artDialog.js?skin=twitter"></script>

<!-- Add fancyBox main JS and CSS files -->
<script type="text/javascript" src="__ROOT__/Public/js/fancyBox-2.1.5/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="__ROOT__/Public/js/fancyBox-2.1.5/jquery.fancybox.css?v=2.1.5" media="screen" />

<script type="text/javascript">
	$(document).ready(function(){
		$(".chzn-select").chosen();

		//初始化
		$("select[name=PoolCode]").val('<?php echo ($_POST['PoolCode']); ?>').trigger('chosen:updated');

		$("#addBtn").click(function(){
			location = '__URL__/add';
		});


		$("#importBtn").click(function(){
			parent.$.fancybox.open({
				href  : '__URL__/add_vm_ip',
				type : 'ajax',

				padding: [5,0,5,0],
				openEffect : 'elastic',
				openSpeed  : 150,
				closeEffect : 'elastic',
				closeSpeed  : 150,

		        closeClick : false,
		        afterClose : function() {
		        	//location.reload();
		       	}
		   	});
		});

		/*$("#importBtn").fancybox({
			href  : '__URL__/add_vm_ip',
			type : 'ajax',
	        hideOnOverlayClick : false,
	        onClosed : function() {
	        	location.reload();
	       	}
		});
*/
	});


	/** 模糊查询来电用户  **/
	function search(){
		$("#submitForm").attr("action", "__URL__/vm_list?page=1").submit();
	}
	
	/** Excel导出  **/
	function exportExcel(){
		if( confirm('您确定要导出吗？') ){
			var fyXqCode = $("#fyXq").val();
			var fyXqName = $('#fyXq option:selected').text();
//	 		alert(fyXqCode);
			if(fyXqCode=="" || fyXqCode==null){
				$("#fyXqName").val("");
			}else{
//	 			alert(fyXqCode);
				$("#fyXqName").val(fyXqName);
			}
			$("#submitForm").attr("action", "/xngzf/archives/exportExcelFangyuan.action").submit();	
		}
	}
	
	/** 删除 **/
	function del(id){
		// 非空判断
		if(id == '') return;
		if(confirm("您确定要删除吗？")){
			$("#submitForm").attr("action", "__URL__/del?id=" + id).submit();
		}
	}
	
	/** 批量删除 **/
	function batchDel(){
		if($("input[name='IDCheck']:checked").size()<=0){
			art.dialog({icon:'error', title:'友情提示', drag:false, resize:false, content:'至少选择一条', ok:true,});
			return;
		}
		// 1）取出用户选中的checkbox放入字符串传给后台,form提交
		var allIDCheck = "";
		$("input[name='IDCheck']:checked").each(function(index, domEle){
			bjText = $(domEle).parent("td").parent("tr").last().children("td").last().prev().text();
// 			alert(bjText);
			// 用户选择的checkbox, 过滤掉“已审核”的，记住哦
			if($.trim(bjText)=="已审核"){
// 				$(domEle).removeAttr("checked");
				$(domEle).parent("td").parent("tr").css({color:"red"});
				$("#resultInfo").html("已审核的是不允许您删除的，请联系管理员删除！！！");
// 				return;
			}else{
				allIDCheck += $(domEle).val() + ",";
			}
		});
		// 截掉最后一个","
		if(allIDCheck.length>0) {
			allIDCheck = allIDCheck.substring(0, allIDCheck.length-1);
			// 赋给隐藏域
			$("#allIDCheck").val(allIDCheck);
			if(confirm("您确定要批量删除这些记录吗？")){
				// 提交form
				$("#submitForm").attr("action", "/xngzf/archives/batchDelFangyuan.action").submit();
			}
		}
	}


</script>
<style>
	.alt td{ background:black !important;}
</style>
</head>
<body>
	<form id="submitForm" name="submitForm" action="" method="post">
		<input type="hidden" name="allIDCheck" value="" id="allIDCheck"/>
		<input type="hidden" name="fangyuanEntity.fyXqName" value="" id="fyXqName"/>

		<div id="container">
			<div class="ui_content">
				<div class="ui_text_indent">
					<div id="box_border">
						<div id="box_top">筛选</div>
						<div id="box_center">
							池编号&nbsp;
							<select name="PoolCode" class="chzn-select ui_select03">
								<option value="">全部</option>
					      		<?php if(is_array($pools)): $i = 0; $__LIST__ = $pools;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["PoolCode"]); ?>"><?php echo ($vo["PoolCode"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
					      	</select>
						</div>
						<div id="box_bottom">
							<input type="button" value="查询" class="ui_input_btn01" onclick="search();" /> 
							<input type="button" value="新增" class="ui_input_btn01" id="addBtn" /> 
							<input type="button" value="删除" class="ui_input_btn01" onclick="batchDel();" /> 
							<input type="button" value="导入" class="ui_input_btn01" id="importBtn" />
							<input type="button" value="导出" class="ui_input_btn01" onclick="exportExcel();" />
						</div>
					</div>
				</div>
			</div>

			<div class="ui_content">
				<div class="ui_tb">
					<table class="table" cellspacing="0" cellpadding="0" width="100%" align="center" border="0">
						<tr>
							<th width="30"><input type="checkbox" id="all" onclick="selectOrClearAllCheckbox(this);" />
							</th>
							<th>ID</th>
							<th>池编号</th>
							<th>虚拟机编号</th>
							<th>CPU</th>
							<th>内存</th>
							<th>硬盘</th>
							<th>模板编号</th>
							<th>公网Vlan</th>
							<th>虚拟机IP</th>
							<th>是否可用</th>
							<th>操作</th>
						</tr>

						<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
								<td><input type="checkbox" name="IDCheck" value="" class="acb" /></td>
								<td><?php echo ($vo["ID"]); ?></td>
								<td><?php echo ($vo["PoolCode"]); ?></td>
								<td><?php echo ($vo["VMCode"]); ?></td>
								<td><?php echo ($vo["Cpu"]); ?></td>
								<td><?php echo ($vo["Ram"]); ?></td>
								<td><?php echo ($vo["Disk"]); ?></td>
								<td><?php echo ($vo["TemplateCode"]); ?></td>
								<td><?php echo ($vo["PublicVlan"]); ?></td>
								<td>vmip</td>
								<td><?php echo ($vo["State"]); ?></td>
								<td>
									<a href="__URL__/edit?id=<?php echo ($vo["ID"]); ?>" class="edit">编辑</a> 
									<a href="javascript:del(<?php echo ($vo["ID"]); ?>);">删除</a>
								</td>
							</tr><?php endforeach; endif; else: echo "" ;endif; ?>

					</table>
				</div>

				<div class="ui_tb_h30">
					<div class="ui_flt" style="height: 30px; line-height: 30px;">
						共有
						<span class="ui_txt_bold04"><?php echo ($pager["count"]); ?></span>
						条记录，当前第
						<span class="ui_txt_bold04"><?php echo ($pager["pageNow"]); ?>/<?php echo ($pager["pageTotal"]); ?></span>
						页
					</div>
					<div class="ui_frt">
						<!--    如果是第一页，则只显示下一页、尾页 -->
						
							<input type="button" value="首页" class="ui_input_btn01" onclick="jumpNormalPage(<?php echo ($pager["first"]); ?>, '<?php echo ($pager["url"]); ?>');" />
							<input type="button" value="上一页" class="ui_input_btn01" onclick="jumpNormalPage(<?php echo ($pager["prev"]); ?>, '<?php echo ($pager["url"]); ?>');" />
							<input type="button" value="下一页" class="ui_input_btn01" onclick="jumpNormalPage(<?php echo ($pager["next"]); ?>, '<?php echo ($pager["url"]); ?>');" />
							<input type="button" value="尾页" class="ui_input_btn01" onclick="jumpNormalPage(<?php echo ($pager["last"]); ?>, '<?php echo ($pager["url"]); ?>');" />
						<!--     如果是最后一页，则只显示首页、上一页 -->
						
						转到第<input type="text" id="jumpNumTxt" value="<?php echo ($pager["pageNow"]); ?>" class="ui_input_txt01" />页
							 <input type="button" class="ui_input_btn01" value="跳转" onclick="jumpInputPage(<?php echo ($pager["pageTotal"]); ?>, '<?php echo ($pager["url"]); ?>');" />
					</div>
				</div>
				
			</div>

		</div>
	</form>
</body>
</html>