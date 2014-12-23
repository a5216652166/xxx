<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link href="__ROOT__/Public/css/basic_layout.css?200" rel="stylesheet" type="text/css">
<link href="__ROOT__/Public/css/common_style.css?200" rel="stylesheet" type="text/css">

<script type="text/javascript" src="__ROOT__/Public/js/jquery/jquery-1.7.1.js"></script>
<script type="text/javascript" src="__ROOT__/Public/js/authority/commonAll.js?200"></script>

<link href="__ROOT__/Public/js/jquery.chosen/chosen.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="__ROOT__/Public/js/jquery.chosen/chosen.jquery.js"></script>

<script type="text/javascript">
	$(function() {
		$(".chzn-select").chosen();

		$("#cancelbutton").click(function(){
			back('<?php echo ($return_url); ?>');
		});

	});

</script>

</head>

<body>
	<div id="container">
		<form action="__URL__/add" method="post">
			<table class="table" cellspacing="0" cellpadding="0" width="100%" >
			    <tr>
			      <th colspan="2" class="ui_text_lt">
			      	添加模板
			      	<span id="errorMsg" class="errmsg"><?php echo ($errorMsg); ?></span>
			      </th>
			    </tr>
			    <tr>
			      <td width="100px">模板编号：</td>
			      <td class="ui_text_lt">
			      	<input type="text" name="TemplateCode" class="ui_input_txt03" />
			      </td>
			    </tr>
			    <tr>
			      <td width="100px">机房名称：</td>
			      <td class="ui_text_lt">
			      	<select name="StockHouseName" class="chzn-select ui_select03">
			      		<?php if(is_array($house)): $i = 0; $__LIST__ = $house;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["Name"]); ?>"><?php echo ($vo["Name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			      	</select>
			      </td>
			    </tr>
			    <tr>
			      <td width="100px">系统名：</td>
			      <td class="ui_text_lt">
			      	<input type="text" name="SystemName" class="ui_input_txt03" />
			      </td>
			    </tr>
			    <tr>
			      <td width="100px">描述：</td>
			      <td class="ui_text_lt">
			      	<textarea name="About" style="width:90%;height:400px;"></textarea>
			      </td>
			    </tr>
			    <tr>
					<td class="ui_text_ct" colspan="2">
						      <input id="submitbutton" type="submit" value="提交" class="ui_input_btn01"/>
						&nbsp;<input id="cancelbutton" type="button" value="取消" class="ui_input_btn01"/>
					</td>
			    </tr>
			</table>
		</form>
	</div>
</body>
</html>