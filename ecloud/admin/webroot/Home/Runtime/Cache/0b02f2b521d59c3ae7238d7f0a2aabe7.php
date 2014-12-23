<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link href="__ROOT__/Public/css/authority/basic_layout.css?200" rel="stylesheet" type="text/css">
<link href="__ROOT__/Public/css/authority/common_style.css?200" rel="stylesheet" type="text/css">

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

		$("select[name=PoolCode]").change(function(){
			var poolCode = $(this).val();

			$.ajax({
				url : '__URL__/get_vps',
				type : 'GET',
				data : {'PoolCode':poolCode},
				dataType : 'json',
				success : function(data){
					var $p = $("select[name=PropertyCode]");
					$p.html('');

					for(var k in data){
						$p.append('<option value="' + data[k].Code + '">' + data[k].Code + '</option>');
					}

					$p.trigger('chosen:updated');
				}
			});
		});

		$("select[name=PoolCode]").val('<?php echo ($data["PoolCode"]); ?>').trigger('chosen:updated');


		var poolCode = $("select[name=PoolCode]").val();
		$.ajax({
			url : '__URL__/get_vps',
			type : 'GET',
			data : {'PoolCode':poolCode},
			dataType : 'json',
			success : function(data){
				var $p = $("select[name=PropertyCode]");
				$p.html('');

				for(var k in data){
					$p.append('<option value="' + data[k].Code + '">' + data[k].Code + '</option>');
				}

				$p.val('<?php echo ($data["PropertyCode"]); ?>').trigger('chosen:updated');
			}
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
			      	修改主机
			      	<span id="errorMsg" class="errmsg"><?php echo ($errorMsg); ?></span>
			      </th>
			    </tr>
			    <tr>
			      <td width="100px">池编号：</td>
			      <td class="ui_text_lt">
			      	<select name="PoolCode" class="chzn-select ui_select03">
			      		<?php if(is_array($pools)): $i = 0; $__LIST__ = $pools;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["PoolCode"]); ?>"><?php echo ($vo["PoolCode"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			      	</select>
			      </td>
			    </tr>
			    <tr style="height: 300px; vertical-align: top;">
			      <td width="100px">资产编号：</td>
			      <td class="ui_text_lt">
			      	<select name="PropertyCode" class="chzn-select ui_select03">
			      	</select>
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