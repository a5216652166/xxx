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

		$("select[name=StockHouseName]").val('<?php echo ($data["StockHouseName"]); ?>').trigger('chosen:updated');
		$("select[name=GroupID]").val('<?php echo ($data["GroupID"]); ?>').trigger('chosen:updated');

		$("#cancelbutton").click(function(){
			back('<?php echo ($return_url); ?>');
		});

		$("form").submit(function(){
			var ret = true;
			return ret;
		});

	});

</script>

</head>
<body>
	<div id="container">
		<form action="__URL__/edit" method="post">
			<input type="hidden" name="ID" value="<?php echo ($data["ID"]); ?>" />

			<table class="table" cellspacing="0" cellpadding="0" width="100%" >
			    <tr>
			      <th colspan="2" class="ui_text_lt">
			      	修改池
			      </th>
			    </tr>
			    <tr>
			      <td width="100px">池编号：</td>
			      <td class="ui_text_lt">
			      	<input type="text" name="PoolCode" class="ui_input_txt03" value="<?php echo ($data["PoolCode"]); ?>" />
			      </td>
			    </tr>
			    <tr>
			      <td width="100px">所属池组：</td>
			      <td class="ui_text_lt">
			      	<select name="GroupID" class="chzn-select ui_select03">
			      		<?php if(is_array($groups)): $i = 0; $__LIST__ = $groups;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["ID"]); ?>"><?php echo ($vo["Name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			      	</select>
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
			      <td width="100px">数据：</td>
			      <td class="ui_text_lt">
			      	<textarea name="Data" style="width:90%;height:200px;"><?php echo ($data["Data"]); ?></textarea>
			      </td>
			    </tr>
			    <tr>
			      <td width="100px">公网Vlan段：</td>
			      <td class="ui_text_lt">
			      	<input type="text" name="PublicVlanBegin" class="ui_input_txt02" value="<?php echo ($data["PublicVlanBegin"]); ?>" />
			      	-- 
			      	<input type="text" name="PublicVlanEnd" class="ui_input_txt02" value="<?php echo ($data["PublicVlanEnd"]); ?>" />
			      </td>
			    </tr>
			    <tr>
			      <td width="100px">内网Vlan段：</td>
			      <td class="ui_text_lt">
			      	<input type="text" name="PrivateVlanBegin" class="ui_input_txt02" value="<?php echo ($data["PrivateVlanBegin"]); ?>" />
			      	-- 
			      	<input type="text" name="PrivateVlanEnd" class="ui_input_txt02" value="<?php echo ($data["PrivateVlanEnd"]); ?>" />
			      </td>
			    </tr>
			    <tr>
			      <td width="100px">内网IP段：</td>
			      <td class="ui_text_lt">
			      	<input type="text" name="PrivateIPBegin" class="ui_input_txt02" value="<?php echo ($data["PrivateIPBegin"]); ?>" />
			      	-- 
			      	<input type="text" name="PrivateIPEnd" class="ui_input_txt02" value="<?php echo ($data["PrivateIPEnd"]); ?>" />
			      </td>
			    </tr>
			    <tr>
			      <td width="100px">掩码：</td>
			      <td class="ui_text_lt">
			      	<input type="text" name="Mask" class="ui_input_txt03" value="<?php echo ($data["Mask"]); ?>" />
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