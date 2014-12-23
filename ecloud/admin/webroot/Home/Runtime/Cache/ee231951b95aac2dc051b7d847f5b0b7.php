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

		var ipsnow = [];
		<?php if(is_array($ipsnow)): $i = 0; $__LIST__ = $ipsnow;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>ipsnow.push("<?php echo ($vo["IPBegin"]); ?>;<?php echo ($vo["IPEnd"]); ?>");<?php endforeach; endif; else: echo "" ;endif; ?>

		$("select[name='IPS\[\]']").val(ipsnow).trigger('chosen:updated');


		$("#cancelbutton").click(function(){
			//parent.$.fancybox.close();
			console.log(parent);
		});

	});

</script>

</head>

<body>
	<div id="container">
		<form action="__URL__/set_group_ip" method="post">
			<input type="hidden" name="ID" value="<?php echo ($data["ID"]); ?>" />

			<table class="table" cellspacing="0" cellpadding="0" width="100%" >
			    <tr>
			      <th colspan="2" class="ui_text_lt">
			      	设置IP
			      	<span id="errorMsg" class="errmsg"><?php echo ($errorMsg); ?></span>
			      </th>
			    </tr>
			    <tr>
			      <td width="100px">池组名：</td>
			      <td class="ui_text_lt">
			      	<input type="text" name="Name" class="ui_input_txt03" value="<?php echo ($data["Name"]); ?>" readonly="readonly" />
			      </td>
			    </tr>
			    <tr>
			    	<td width="100px">IP：</td>
					<td class="ui_text_lt">
						<select name="IPS[]" class="chzn-select ui_select04" multiple="multiple">
							<?php if(is_array($ips)): $i = 0; $__LIST__ = $ips;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["Begin"]); ?>;<?php echo ($vo["End"]); ?>"><?php echo ($vo["Begin"]); ?> ~ <?php echo ($vo["End"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
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