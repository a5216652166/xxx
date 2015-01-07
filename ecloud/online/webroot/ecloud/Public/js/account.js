		/*var pay_layer;
    	function tab(obj){
			$(".content_tab ul li").each(function(){
				$(this).hide();
			});
			switch($(obj).html()){
				case '订单详情':
					$(".content_tab ul li:eq(0)").show();
					hide('订单详情');
				break;
				case '用户信息':
					$(".content_tab ul li:eq(1)").show();
					hide('用户信息');
				break;
				case '控制面板':
					$(".content_tab ul li:eq(2)").show();					
					hide('控制面板');
				break;				
				default:
					$(".content_tab ul li:eq(0)").show();
					hide('订单详情');
				break;
			}
		}
		function hide(str){
			$("#main-tab li").each(function(i){
				if($(this).find('a').html()==str){
					$(this).attr('class','active');
				}else{
					$(this).attr('class','no_active');
				}
			});
		}*/
		var pay_layer;
		function payment(id,type,oid,time,money){
			pay_layer = $.layer({
				type: 1,
				closeBtn: [0, false],
				title: false,
				area: ['520px', '360px'],
				shift: 'left', //从左动画弹出
				page: {
					html: '<div id="pay_div" style="margin:40px 0px 0px 50px;"><h4>请选择付款方式：</h4><br/>账户余额：'+ balance +' 元<br/><br/>应付金额：<span class="label label-danger" id="price_span">'+ money +'</span>&nbsp; 元<br/><br/><div><input type="radio" name="payType" checked value="balance"/><img src="'+ROOT+'/Public/images/esslogo.png" width="115" height="40" style="margin-left:10px;cursor:pointer" onClick="payType(this)" alt="睿江云账户" id="ess_img" title="睿江云账户"/><span style="line-height:30px;color: #f76120;margin:0px 0px 0px 5px;font-size:14px;">使用余额支付 ' + money + ' 元</span></div><br/><div><input type="radio" id="alipay_rdo" value="alipay" name="payType" /><img src="'+ROOT+'/Public/images/alipay.jpg" onClick="payType(this)" width="115" height="40" style="margin-left:10px;border:1px solid #ccc; cursor:pointer" alt="支付宝账户" title="支付宝账户"/><span style="line-height:30px;color: #f76120;margin:0px 0px 0px 5px;font-size:14px;">使用支付宝支付 ' + money + ' 元</span></div><div class="modal-footer" style="width: 400px;"><a href="javascript:void(0);" onClick="cancelOrder()" class="btn btn-default cancel">取消</a><a href="javascript:void(0);" onClick="gotoPay('+ id +',\''+ type +'\','+ oid +',\''+ time +'\')" class="btn ok btn-primary">去支付</a></div></div>'
				}
			});
			if(balance<money){
				$("#alipay_rdo").attr('checked','checked');
				$('#ess_img').attr({'src':ROOT+'/Public/images/mergePay.png','alt':'睿江云账户、支付宝合并付款','title':'睿江云账户、支付宝合并付款'});
				$('#ess_img').next().html("使用余额支付 "+ balance + " 元，使用支付宝支付 " + (money - balance) + " 元");
			}
		}
		function cancelOrder(){
			layer.close(pay_layer);	
		}
		function payType(obj){
			$(obj).prev().attr('checked','checked');
		}
		function gotoPay(id,type,oid,time){			
			if($(":radio[name='payType']:checked").val()=='alipay'){
				if(type=='续费'){
					window.open(APP+'/Index/renewOrder?id='+id+'&type='+$(":radio[name='payType']:checked").val());
					_interval = setInterval(function (){finishPayment2(id,time,oid);},3000);										
				}else{
					window.open(APP+'/Index/payment?id='+id);
					_interval = setInterval(function (){finishPayment(id);},3000);
				}
			}else{
				if(type=='续费'){
					$.ajax({
						url:APP+'/Index/gotopay',
						type:'post',
						async:false,
						data:{'id':id,'type':'balance','time':time,'oid':oid},
						success:function(data){
							if(data.info=='success'){
								$("#pay_div").html('<h4 style="padding: 20px 0px 0px 40px;">您的订单已经支付成功，系统正在处理中。</h4><h4 style="padding:20px 0px 0px 40px;">请耐心等待我们的邮件通知。</h4><h4 style="padding:20px 0px 0px 40px;">登录[' + USER + ']邮箱查看邮件。</h4><h4 style="padding:20px 0px 0px 40px;"> 3 秒跳转订单页面。</h4>');
								setInterval(function (){window.top.location.reload();},3000);								
							}else{
								if(confirm("提示信息："+data.data)){
									window.open(APP+'/Index/mergePay?id='+id);
									$(".regist").html('<style>a:visited{color:#00b0ec}a:hover{color:#00b0ec}a:link{color:#00b0ec}</style><div style="margin-top:100px;"><input id="id" value="'+id+'" type="hidden"/><h3 style="padding:20px 0px 0px 30px;">登录支付宝进行支付</h3><img src="'+ROOT+'/Public/images/loading.gif" style="float:right;margin-top:50px;"/><br/><div style="width:480px;height:1px; border-top:1px solid #ccc;margin-left:20px;"></div><h5 style="padding:10px 0px 0px 30px; font-weight:normal">请在新开的支付页面完成支付：</h5><br/><div style="width:70%;float:left;"><img src="'+ROOT+'/Public/images/success.png" style="float:left;padding:3px 0px 0px 30px;"/><h5 style="float:left;">支付成功</h5><h5 style="padding:0px 10px 0px 10px;float:left;font-weight:normal">|</h5><h5 style="float:left;font-weight:normal">您可以选择查看：</h5><a href="javascript:window.top.location.href=\''+APP+'/Index/account\'" style="float:left;margin:12px 0px 0px 10px;font-size:12px;text-decoration:none">我的订单</a></div><br/><div style="width:70%;float:left;"><img src="'+ROOT+'/Public/images/error.png" style="float:left;padding:3px 0px 0px 30px;"/><h5 style="float:left;">支付失败</h5><h5 style="padding:0px 10px 0px 10px;float:left;font-weight:normal">|</h5><h5 style="float:left;font-weight:normal">建议你重新支付：</h5><a href="javascript:window.top.location.reload();" style="float:left;padding:12px 0px 20px 10px;font-size:12px;text-decoration:none">刷新页面</a></div></div>');
									_interval = setInterval(function (){finishPayment2(id,time,oid);},3000);
								}
							}
						},error:function(data){
							alert("提示信息："+data.statusText);	
						}
					
					});
				}else{
					$.ajax({
						url:APP+'/Index/payment2',
						type:'post',
						async:false,
						data:{'id':id},
						success:function(data){
							if(data.info=='success'){
								insert(id);
							}else{
								if(confirm("提示信息："+data.data)){
									window.open(APP+'/Index/mergePay?id='+id);
									$(".regist").html('<style>a:visited{color:#00b0ec}a:hover{color:#00b0ec}a:link{color:#00b0ec}</style><div style="margin-top:100px;"><input id="id" value="'+id+'" type="hidden"/><h3 style="padding:20px 0px 0px 30px;">登录支付宝进行支付</h3><img src="'+ROOT+'/Public/images/loading.gif" style="float:right;margin-top:50px;"/><br/><div style="width:480px;height:1px; border-top:1px solid #ccc;margin-left:20px;"></div><h5 style="padding:10px 0px 0px 30px; font-weight:normal">请在新开的支付页面完成支付：</h5><br/><div style="width:70%;float:left;"><img src="'+ROOT+'/Public/images/success.png" style="float:left;padding:3px 0px 0px 30px;"/><h5 style="float:left;">支付成功</h5><h5 style="padding:0px 10px 0px 10px;float:left;font-weight:normal">|</h5><h5 style="float:left;font-weight:normal">您可以选择查看：</h5><a href="javascript:window.top.location.href=\''+APP+'/Index/account\'" style="float:left;margin:12px 0px 0px 10px;font-size:12px;text-decoration:none">我的订单</a></div><br/><div style="width:70%;float:left;"><img src="'+ROOT+'/Public/images/error.png" style="float:left;padding:3px 0px 0px 30px;"/><h5 style="float:left;">支付失败</h5><h5 style="padding:0px 10px 0px 10px;float:left;font-weight:normal">|</h5><h5 style="float:left;font-weight:normal">建议你重新支付：</h5><a href="javascript:window.top.location.reload();" style="float:left;padding:12px 0px 20px 10px;font-size:12px;text-decoration:none">刷新页面</a></div></div>');
									_interval = setInterval(function (){finishPayment(id);},3000);
								}
							}
						},error:function(data){
							alert("提示信息："+data.statusText);	
						}
					
					});
				}
			}			
		}
		function insert(id){
			$.ajax({
				url:APP+'/Index/insert',
				type:'post',
				async:false,
				data:{'id':id},
				success:function(data){
					if(data.info=="success"){
						$("#pay_div").html('<h4 style="padding: 20px 0px 0px 40px;">您的订单已经支付成功，系统正在处理中。</h4><h4 style="padding:20px 0px 0px 40px;">请耐心等待我们的邮件通知。</h4><h4 style="padding:20px 0px 0px 40px;">登录[' + USER + ']邮箱查看邮件。</h4><h4 style="padding:20px 0px 0px 40px;"> 3 秒跳转订单页面。</h4>');
						setInterval(function (){window.parent.location.reload();},3000);						
					}else{
						alert("提示信息："+data.data);
					}
				},
				error:function(data){
					alert("提示信息："+data.statusText);	
				}
			});	
		}
		function finishPayment2(id,time,oid){
			$.ajax({
				url:APP+'/Index/finishPayment',
				type:'post',
				async:false,
				success:function(data){
					if(data.data=="SUCC"){
						clearInterval(_interval);
						updateOrder(id,time,oid);			
					}
				},
				error:function(data){
					alert("提示信息："+data.statusText);	
				}
			});
		}
		function updateOrder(id,time,oid){
			$.ajax({
				url:APP+'/Index/updateOrder',
				type:'post',
				async:false,
				data:{'id':id,'time':time,'oid':oid},
				success:function(data){
					if(data.info=="success"){
						$("#pay_div").html('<h4 style="padding: 20px 0px 0px 40px;">您的订单已经支付成功，系统正在处理中。</h4><h4 style="padding:20px 0px 0px 40px;">请耐心等待我们的邮件通知。</h4><h4 style="padding:20px 0px 0px 40px;">登录[' + USER + ']邮箱查看邮件。</h4><h4 style="padding:20px 0px 0px 40px;"> 3 秒跳转订单页面。</h4>');
						setInterval(function (){window.top.location.reload();},3000);
					}else{
						alert("提示信息："+data.data);		
					}
				},
				error:function(data){
					alert("提示信息："+data.statusText);	
				}
			});
		}
		function finishPayment(id){
			if($(".error_msg").html()!=""){
				$(".error_msg").html('');
			}
			$.ajax({
				url:APP+'/Index/finishPayment',
				type:'post',
				data:{'id':id},
				async:false,
				success:function(data){
					if(data.data=="SUCC"){
						clearInterval(_interval);
						$.ajax({
							url:APP+'/Index/insert',
							type:'post',
							async:false,
							data:{'id':id},
							success:function(data){
								if(data.info=="success"){
									$("#pay_div").html('<h4 style="padding: 20px 0px 0px 40px;">您的订单已经支付成功，系统正在处理中。</h4><h4 style="padding:20px 0px 0px 40px;">请耐心等待我们的邮件通知。</h4><h4 style="padding:20px 0px 0px 40px;">登录[' + USER + ']邮箱查看邮件。</h4><h4 style="padding:20px 0px 0px 40px;"> 3 秒跳转订单页面。</h4>');
									setInterval(function (){window.top.location.reload();},3000);									
								}else{
									alert("提示信息："+data.data);
								}
							},
							error:function(data){
								alert("提示信息："+data.statusText);	
							}
						});
					}
				},
				error:function(data){
					alert("提示信息："+data.statusText);	
				}
			});
		}
		function deleteOrder(id){
			if($(".error_msg").html()!=""){
				$(".error_msg").html('');
			}
			var del_layer = $.layer({
				offset: ['180px',''],
				area: ['300px', '150px'],
				dialog: {
					msg: '确定取消订单？',
					btns: 2,            
					type: 4,
					btn: ['确定','关闭'],
					yes: function(){
						$.ajax({
							url:APP+'/Index/deleteOrder',
							type:'post',
							async:false,
							data:{'id':id},
							success:function(data){
								if(data.info=='success'){
									/*var str = "";
									for(var i=0;i<data.data.length;i++){
										str += '<tr style=""><td>'+ data.data[i].Code +'</td><td>'+data.data[i].CPU+'CPU '+data.data[i].RAM+'内存 '+data.data[i].DISK+'G磁盘 '
										if(data.data[i].BandWidthIPLC!=0){
											str += data.data[i].BandWidthIPLC + 'M国际BGP带宽</td>';
										}else{								
											str += data.data[i].BandWidthBGP + 'M国内BGP带宽</td>';
										}
										if(data.data[i].PayTS=='0000-00-00 00:00:00'){
											str += '<td>-</td>';
										}else{
											if(data.data[i].today==data.data[i].PayTS.substr(0,10)){
												str += '<td style="color:#0F0;">'+data.data[i].PayTS+'</td>';
											}else{
												str += '<td>'+data.data[i].PayTS+'</td>';
											}
											
										}
										str += '<td>'+data.data[i].Type+'</td>';
										str += '<td style="color:red;">-'+data.data[i].Money+'</td>';
										if(data.data[i].IsPay==0){
											str += '<td>未支付</td><td><a href="javascript:void(0);" onClick="payment('+data.data[i].Order_ID+',\''+data.data[i].Type+'\','+data.data[i].Old_Order_ID+',\''+data.data[i].Time+'\','+data.data[i].Money+')">去付款</a> | <a href="javascript:void(0);" onClick="deleteOrder('+data.data[i].Order_ID+')">取消订单</a></td>';
										}else{
											if(data.data[i].Type=="续费"){
												str += '<td>已支付</td><td><a href="javascript:void(0);">-</a></td>';
											}else{
												str += '<td>已支付</td><td><a href="javascript:void(0);" onClick="showInfo('+data.data[i].Order_ID+',this)">展开</a></td>';	
											}
										}
									}
									$("#mydata").html(str);*/
									window.location.reload();
								}else{
									alert("温馨提示："+data.data);
								}
							},
							error:function(data){
								alert("温馨提示："+data.statusText);
							}
						});
						layer.close(del_layer);
					}
				}
			});
		}
		function showInfo(oid,obj){
			if($(obj).html()=="展开"){
				$(obj).html('收起');
				$.ajax({
					url:APP+"/Index/getWorkList",
					async:false,
					type:'post',
					data:{'oid':oid},
					success:function(data){
						$(obj).parent().parent().after('<tr class="wlist_info_'+oid+'" style="height: 20px;"></tr><tr class="wlist_info_'+oid+'" style="background: #f5f5f5;"><td colspan="7"><div style="border-left:3px solid #0089b7;text-align:left;padding:2px 18px;font-weight:bold">工单详情</div></td></tr><tr class="wlist_info_'+oid+'"><td>工单编号</td><td colspan="5">描述</td><td>工单状态</td></tr>');
						for(var i=0;i<data.data.length;i++){
							if(i==data.data.length-1){
								$(obj).parent().parent().next().next().next().after('<tr class="wlist_info_'+oid+'"><td style="border-right: 1px dashed #ccc;">'+data.data[i].Code+'</td><td colspan="5" style="border-right: 1px dashed #ccc;">'+data.data[i].DoDesc+'</td><td>'+data.data[i].statusDesc+'</td></tr><tr class="wlist_info_'+oid+'" style="height: 20px;"></tr>');
							}else{
								$(obj).parent().parent().next().next().next().after('<tr class="wlist_info_'+oid+'"><td style="border-right: 1px dashed #ccc;">'+data.data[i].Code+'</td><td colspan="5" style="border-right: 1px dashed #ccc;">'+data.data[i].DoDesc+'</td><td>'+data.data[i].statusDesc+'</td></tr>');
							}
						}
					},
					error:function(data){
						layer.msg("温馨提示："+data.statusText,2,3);
					}			
				});
			}else{
				$(".wlist_info_"+oid).each(function(){
					$(this).remove();
				});
				$(obj).html('展开');
			}
			
		}
		function hideMsg(obj){
			$(obj).next().hide();
		}
		function checkName(obj){
			var val = $(obj).val(),reg_name = /^[\u4E00-\u9FA5]+$/ ;
			if(!val){
				$(obj).next().show();
				return false;
			}
			if(!reg_name.test(val)){
				$(obj).next().show();
				return false;
			}
			return true;
		}
		function checkCode(obj){
			var val = $(obj).val();
			if(!val){
				$(obj).next().show();
				return false;
			}
			if(!checkIDNum(val)){
				$(obj).next().show();
				return false;
			}
			return true;
		}
		function checkPhone(obj){
			var val = $(obj).val(),reg_phone = /^1\d{10}$/ ;
			if(!val){
				$(obj).next().show();
				return false;
			}
			if(!reg_phone.test(val)){
				$(obj).next().show();
				return false;
			}
			return true;
		}
		function saveUser(){
			//var reg_phone = /^1\d{10}$/ ,reg_name = /^[\u4E00-\u9FA5]+$/ ;
			if(USERTYPE==1){
				if(!checkName($("#psn_TrueName")) || !checkCode($("#psn_IDNum")) || !checkPhone($("#psn_Phone"))){
					layer.msg('请完整填写表单内容。',2,3);	
					return false;
				}
				$.ajax({
					url:APP+'/Index/saveUserInfo',
					type:'post',
					async:false,
					data:$("#person_form").serialize(),
					success:function(data){
						if(data.info=='success'){
							//window.location.reload();
							layer.msg(data.data,2,1)
						}else{
							layer.msg("温馨提示："+data.data,2,3);	
						}
					},
					error:function(data){
						layer.msg("温馨提示："+data.statusText,2,3);
					},
				});
			}else{
				if(!checkName($("#com_CompanyName")) || !checkName($("#com_LegalPersonName")) || !checkPhone($("#com_LegalPersonPhone")) || !checkName($("#com_LinkmanName")) || !checkCode($("#com_LinkmanIdNum")) || !checkPhone($("#com_LinkmanPhone"))){
					layer.msg('请完整填写表单内容。',2,3);	
					return false;
				}
				$.ajax({
					url:APP+'/Index/saveUserInfo',
					type:'post',
					async:false,
					data:$("#company_form").serialize(),
					success:function(data){
						if(data.info=='success'){
							//window.location.reload();
							layer.msg('修改成功',2,1)
						}else{
							layer.msg("温馨提示："+data.data,2,3);	
						}
					},
					error:function(data){
						layer.msg("温馨提示："+data.statusText,2,3);
					},
				});
			}
		}
		var uppwd_layer;
		function before_updatepwd(){
			uppwd_layer = $.layer({
				type: 1,
				title: '修改用户密码',
				area: ['400px', '260px'],
				shift: 'left', //从左动画弹出
				page: {
					html: '<style>.mytab{margin:30px 0px 0px 50px;}.mytab input[type="password"]{margin:5px 0px 0px 12px;padding-left: 10px; line-height: 23px; width:220px; height:30px; border: 1px solid #CCC; background: #FFF; vertical-align: middle; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px;} .mytab input:focus { border-color: #188AD6; }</style><form action="" id="updatePWD" method="post"><table class="mytab"><tr align="center"><td colspan="2" id="uppwd_msg">&nbsp;</td></tr><tr><td>当前密码：</td><td><input id="oldpassword" type="password" tabindex="11"/></td></tr><tr><td>新密码：</td><td><input type="password" id="newpassword" tabindex="12"/></td><tr><td>确认密码：</td><td><input id="repassword" type="password" tabindex="13"/></td></tr><tr><td></td><td><input style="margin:20px 0px 0px 150px;" type="button" value="确定" onClick="updatepwd()"/></td></tr></table></form>'
				}
			});
		}
		function updatepwd(){
			if($("#uppwd_msg").html()!=""){
				$("#uppwd_msg").html('');
			}
			var oldpwd = $("#oldpassword").val(),
				newpwd = $("#newpassword").val(),
				repwd = $("#repassword").val();
			if(!oldpwd){
				$("#oldpassword").focus();
				return false;	
			}
			if(!newpwd){
				$("#newpassword").focus();
				return false;	
			}
			if(!repwd){
				$("#repassword").focus();
				return false;	
			}
			if(newpwd != repwd){
				$("#repassword").focus();
				$("#uppwd_msg").css('color','red').html("两次输入的密码不一致");
				return false;
			}
			$.ajax({
				url:APP+"/Index/updateUserPwd",
				type:'post',
				async:false,
				data:{'oldpwd':oldpwd,'newpwd':newpwd},
				success:function(data){
					if(data.info=='success'){
						layer.close(uppwd_layer);
						layer.msg(data.data,2,1);
					}else{
						$("#uppwd_msg").css('color','red').html("温馨提示："+data.data);	
					}
				},
				error:function(data){
					$("#uppwd_msg").css('color','red').html("温馨提示："+statusText);
				},
			});
		}
		function closeLayer(){
			layer.close(uppwd_layer);
		}
		function showOpt(obj,code,id){
			if($(obj).html()=='查看详情'){
				$(".vpsinfo_"+id).show();
				$(obj).html('收起详情');				
			}else{				
				$(".vpsinfo_"+id).hide();
				$(obj).html('查看详情');
			}
		}
		function operationVM(obj,code,id){
			if($("#"+id).attr('expire')=='yes'){
				layer.msg('提示信息：云主机到期，不能继续操作，请续费。',3,7);
				return false;
			}
			var val = $(obj).attr('title'),opt="";	
			layer.load('正在' + val,3);
			switch(val){
				case '开机':opt="Run"; break;
				case '关机':opt="Halt"; break;
				case '重启':opt="Reboot"; break;
			}	
			$.ajax({
				url:APP+'/Index/operationVM',
				type:'post',
				data:{'opt':opt,'code':code},
				success:function(data){
					if(data.info=='success'){
						if(data.data==0){
							layer.msg("云主机【"+val+"】成功",2,1);
							switch(val){								
								case '开机':
								$("#ico_"+id).html('<img src="'+ROOT+'/Public/images/run_status.png" style="margin-right:5px;" alt="运行状态"/>运行中');
								$(obj).parent().next().html('<a href="javascript:void(0);" onClick="operationVM(this,\''+code+'\','+id+')" title="关机">关机</a>');
								$(obj).parent().html('<a href="javascript:void(0);" style="color:#ccc" title="开机">开机</a>');
								break;
								case '关机':								
								$("#ico_"+id).html('<img src="'+ROOT+'/Public/images/stop_status.png" style="margin-right:5px;" alt="停止状态"/>已关机');
								$(obj).parent().prev().html('<a href="javascript:void(0);" onClick="operationVM(this,\''+code+'\','+id+')" title="开机">开机</a>');
								$(obj).parent().html('<a href="javascript:void(0);" style="color:#ccc" title="关机">关机</a>');
								break;
								case '重启':
								$("#ico_"+id).html('<img src="'+ROOT+'/Public/images/run_status.png" style="margin-right:5px;" alt="运行状态"/>运行中');
								$(obj).parent().prev().html('<a href="javascript:void(0);" onClick="operationVM(this,\''+code+'\','+id+')" title="关机">关机</a>');
								$(obj).parent().prev().prev().html('<a href="javascript:void(0);" style="color:#ccc" title="开机">开机</a>');
								break;
							}
						}else{
							layer.msg("云主机【"+val+"】失败",2,3);
						}
					}
				},
				error:function(data){
					layer.msg(data.statusText,3,3);
				}
			});
					
		}
		function renewPay(type,id){
			var layer_offset_top = "110px";
			if(window.innerHeight<700){
				layer_offset_top = "0px";
			}
			var oid = "";
			if(type=='one'){
				oid = id;
			}else{
				if($("#console table :checked").length!=1){
					layer.msg('请选择一条记录',3,3);
					return false;
				}
				oid = $(":checked").attr('order_id');
			}
			$.ajax({
				url:APP+'/Index/renewPay',
				type:'post',
				async:false,
				data:{'oid':oid},
				success:function(data){
					if(data.info=="success"){
						$.layer({
							type: 2,
							maxmin: true,
							title: ' ',
							offset: [layer_offset_top,''],
							area: ['650px', '556px'],
							iframe: {src: APP+'/Index/renewPay?cpu=' + data.data.CPU + '&ram=' + data.data.RAM + '&sys=' + data.data.OS + '&disk=' + data.data.DISK + '&iplc=' + data.data.BandWidthIPLC + '&bgp=' + data.data.BandWidthBGP + '&count=' + data.data.Count + '&oid=' + oid + '&house=' + data.data.HouseName}
						});
					}
				},
				error:function(data){					
					layer.msg(data.statusText,3,3);
				}
			});
		}
		var coupon_layer;
		function useCoupon(){
			coupon_layer = $.layer({
				type: 1,
				title: '使用代金券充值',
				area: ['400px', '260px'],
				shift: 'left', //从左动画弹出
				page: {
					html: '<style>.mytab{margin:20px 0px 0px 50px;}.couponInput{margin:15px 0px 0px 12px;padding-left: 10px; line-height: 23px; width:220px; height:30px; border: 1px solid #CCC; background: #FFF; vertical-align: middle; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px;} input:focus { border-color: #188AD6; }</style><form action="" id="updatePWD" method="post"><table class="mytab"><tr align="center"><td colspan="2" id="uppwd_msg">&nbsp;</td></tr><tr><td>代金券号：</td><td><input id="code" class="couponInput" type="text" tabindex="11"/></td></tr><tr><td>代金券密码：</td><td><input type="password" id="password" class="couponInput" tabindex="12"/></td><tr><td></td><td><input style="margin:20px 0px 0px 150px;" type="button" value="下一步" onClick="nextOpt()"/></td></tr></table></form>'
				}
			});
		}
		var use_layer;
		function nextOpt(){
			var code = $("#code").val(),pwd = $("#password").val();
			if(!code){
				$("#code").focus();
				return false;
			}
			if(!pwd){
				$("#password").focus();
				return false;
			}
			$.ajax({
				url:APP+'/Index/selectCoupon',
				type:'post',
				async:false,
				data:{'code':code,'pwd':pwd},
				success:function(data){
					if(data.info=="success"){
						layer.close(coupon_layer);
						use_layer = $.layer({
							type: 1,
							area: ['430px', '260px'],
							title:false,
							page: {
								html: '<style>.mytab{margin:20px 0px 0px 60px;}.mytab tr{height:30px;}.couponInput{margin:15px 0px 0px 12px;padding-left: 10px; line-height: 23px; width:200px; height:30px; border: 1px solid #CCC; background: #FFF; vertical-align: middle; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px;} input:focus { border-color: #188AD6; }</style><h3 style="margin:20px 0px 0px 30px;">使用代金券充值</h3><form action="" id="updatePWD" method="post"><table class="mytab"><tr align="center"><td colspan="2" id="uppwd_msg">&nbsp;</td></tr><tr><td width="120">代金券号：</td><td>'+data.data.Code+'</td></tr><tr><td width="120">代金券金额：</td><td>¥ <label style="color:red">'+data.data.Money+'</label></td><tr><td></td><td><input type="button" value="确定使用" onClick="updateCoupon('+data.data.ID+','+data.data.Money+')"/></td></tr></table></form>'
							}
						});
					}else{
						layer.msg(data.data,3,3);	
					}
				},
				error:function(data){
					layer.msg(data.statusText,3,3);
				}				
			});
		}
		function updateCoupon(id,money){
			$.ajax({
				url:APP+'/Index/updateCoupon',
				type:'post',
				async:false,
				data:{'id':id},
				success:function(data){
					if(data.info=='success'){
						layer.close(use_layer);
						$(".money").html(data.data);
					}
				},
				error:function(data){
					layer.msg(data.statusText,3,3);
				}
			});
		}
		function showMore(obj,e){
			if($(obj).next().is(":visible")){
				$(obj).next().slideToggle();	
			}
			$(obj).next().slideToggle();
			stopPropagation(e);
		}
		$(function(){
			if(USERTYPE==1){
				if($("#psn_Phone").val()=="" || $("#psn_IDNum").val()=="" || $("#psn_TrueName").val()==""){
					$("#psn_info_err").show();
				}
			}else{
				if($("#com_CompanyName").val()=="" || $("#com_LegalPersonName").val()=="" || $("#com_LegalPersonPhone").val()=="" || $("#com_LinkmanName").val()=="" || $("#com_LinkmanIdNum").val()=="" || $("#com_LinkmanPhone").val()==""){
					$("#com_info_err").show();
				}
			}
			$('html').bind('click',function(e){
				//if($(".more").is(":visible")){	
					$(".more").hide();	
				//}	
			});
			$("#console :checkbox").bind('click',function(){
				if($(":checked").length!=0){
					$("#console button").attr('disabled',false);	
				}else{
					$("#console button").attr('disabled',true);	
				}
			})
		});
		var stopPropagation = function(e) {
			if(e && e.stopPropagation){         //W3C取消冒泡事件         
				e.stopPropagation();     
			}else{         //IE取消冒泡事件
				window.event.cancelBubble = true;     
			} 
		};
		function selectAll(){
			$("#console table :checkbox").each(function(){
				$(this).attr('checked',!$(this).attr('checked'));
			});
		}
		function batchOperationVM(obj){
			var is_ok = true;
			$("#console table :checked").each(function(){
				if($(this).attr('expire')=='yes'){
					is_ok = false;	
				}
			});
			if(!is_ok){
				layer.msg('提示信息：云主机到期，不能继续操作，请续费。',3,7);
				return false;	
			}
			var val = $(obj).text(),opt = "",code = "";	
			if($("#console table :checked").length==0){
				layer.msg('请选择要操作的记录',3,3);
				return false;
			}
			layer.load('正在' + val,3);
			switch(val){		
				case '启动':opt="Run"; break;
				case '停止':opt="Halt"; break;
				case '重启':opt="Reboot"; break;
			}
			$("#console table :checked").each(function(i){
				if(i!=$("#console table :checked").length-1){
					code += $(this).attr('code') + "," ;
				}else{
					code += $(this).attr('code') ;
				}
			});
			$.ajax({
				url:APP+'/Index/batchOperationVM',
				type:'post',
				data:{'opt':opt,'code':code},
				success:function(data){
					if(data.info=='success'){
						$("#console table :checked").each(function(){
							var id = $(this).attr('id');
							switch(val){								
								case '启动':
								$("#ico_"+id).html('<img src="'+ROOT+'/Public/images/run_status.png" style="margin-right:5px;" alt="运行状态"/>运行中');
								$(this).parent().parent().find('.more ul li:eq(0)').html('<a href="javascript:void(0);" style="color:#ccc" title="开机">开机</a>');
								$(this).parent().parent().find('.more ul li:eq(1)').html('<a href="javascript:void(0);" onClick="operationVM(this,\''+code+'\','+id+')" title="关机">关机</a>');
								break;								
								case '停止':												
								$("#ico_"+id).html('<img src="'+ROOT+'/Public/images/stop_status.png" style="margin-right:5px;" alt="停止状态"/>已关机');								
								$(this).parent().parent().find('.more ul li:eq(0)').html('<a href="javascript:void(0);" onClick="operationVM(this,\''+code+'\','+id+')" title="开机">开机</a>');
								$(this).parent().parent().find('.more ul li:eq(1)').html('<a href="javascript:void(0);" style="color:#ccc" title="关机">关机</a>');
								break;								
								case '重启':
								$("#ico_"+id).html('<img src="'+ROOT+'/Public/images/run_status.png" style="margin-right:5px;" alt="运行状态"/>运行中');								
								$(this).parent().parent().find('.more ul li:eq(0)').html('<a href="javascript:void(0);" onClick="operationVM(this,\''+code+'\','+id+')" title="关机">关机</a>');
								$(this).parent().parent().find('.more ul li:eq(1)').html('<a href="javascript:void(0);" style="color:#ccc" title="开机">开机</a>');
								break;
							}
							
						});
						layer.msg("云主机【"+val+"】成功",2,1);
					}else{
						layer.msg("云主机【"+val+"】失败",2,3);
					}
				},
				error:function(data){
					layer.msg(data.statusText,3,3);
				}
			});
		}
		function sendMail(){
			var send_layer = layer.load('已发送邮件，请在邮箱激活!');
			$.ajax({
				url:APP+'/Index/sendMail',
				type:'post',
				async:false,
				success:function(data){
					if(data.info=="error"){
						layer.msg('温馨提示：'+data.data,2,3);	
					}
				},
				error:function(data){
					layer.msg('温馨提示：'+data.statusText,2,3);
				}
			});
			setInterval(function (){layer.close(send_layer);},3000);			
		}
		
		function changeRechargeType(obj){
			var val = $(obj).html();
			$(".recharge_type ul li a").removeClass("active");
			$("#alipay_div,#huikuan_div").hide();
			switch(val){
				case "支付宝":
				$(".recharge_type ul li a:eq(0)").addClass("active");
				$("#alipay_div").fadeToggle();
				break;
				case "网银支付":
				$(".recharge_type ul li a:eq(1)").addClass("active");
//				$("#alipay_div").fadeToggle();
				break;
				case "线下汇款":
				$(".recharge_type ul li a:eq(2)").addClass("active");
				$("#huikuan_div").fadeToggle();
				break;
			}
		}
		function hideMoneyDesc(){
			$("#input_money").next().fadeToggle();
		}
		function recharge(){
			var money = $("#input_money").val(),reg =  /^\+?[1-9][0-9]*$/;
			if(!money){
				$("#input_money").focus();
				return false;
			}
			if(!reg.test(money)){
				$("#input_money").next().show();
				return false;
			}
			$.ajax({
				url:APP+'/Index/recharge',
				type:'post',
				async:false,
				data:{'money':money},
				success:function(data){
					if(data.info=="success"){
						window.open(APP+'/Index/rechargePayment?id='+data.data);
						_interval = setInterval(function (){finishRecharge(data.data);},3000);
					}else{
						layer.msg('温馨提示：'+data.data,2,3);		
					}
				},
				error:function(data){
					layer.msg('温馨提示：'+data.statusText,2,3);
				}
			});
		}
		var load_recharge;
		function finishRecharge(id){
			load_recharge = layer.load('等待交易完成');
			$.ajax({
				url:APP+'/Index/finishPayment',
				type:'post',
				data:{'id':id},
				async:false,
				success:function(data){
					if(data.data=="SUCC"){
						clearInterval(_interval);
						addRecharge(id);
					}
				},
				error:function(data){
					alert("提示信息："+data.statusText);	
				}
			});	
		}
		function addRecharge(id){
			$.ajax({
				url:APP+'/Index/addRecharge',
				type:'post',
				data:{'id':id},
				success:function(data){
					if(data.info=="success"){
						layer.close(load_recharge);
						layer.msg('交易完成，3秒跳转用户中心页面',3,1)
						setInterval(function (){window.location.href = APP + "/Index/account";},3000);
					}else{
						layer.msg('温馨提示：'+data.data,2,3);		
					}
				},
				error:function(data){
					alert("提示信息："+data.statusText);	
				}
			});	
		}
		function continueRecharge(id){
			window.open(APP+'/Index/rechargePayment?id='+id);
			_interval = setInterval(function (){finishRecharge(id);},3000);			
		}
		function deleteRechargeOrder(id){
			var del_layer = $.layer({
				offset: ['180px',''],
				area: ['300px', '150px'],
				dialog: {
					msg: '确定取消充值记录？',
					btns: 2,            
					type: 4,
					btn: ['确定','关闭'],
					yes: function(){
						$.ajax({
							url:APP+'/Index/deleteRechargeOrder',
							type:'post',
							async:false,
							data:{'id':id},
							success:function(data){
								if(data.info=='success'){
									window.location.reload();
								}else{
									alert("温馨提示："+data.data);
								}
							},
							error:function(data){
								alert("温馨提示："+data.statusText);
							}
						});
						layer.close(del_layer);
					}
				}
			});
		}
		