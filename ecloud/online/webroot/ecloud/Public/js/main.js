$(document).ready(function(){
	$('html').bind('click',function(e){
		$(".more").hide();	
	});
	$("#console :checkbox").bind('click',function(){		
		if($("#console :checked").length!=0){
			$("#console button").attr('disabled',false);	
		}else{
			$("#console button").attr('disabled',true);	
		}
	});
	$('.t_left li a').click(function(){
		$(".tree ul").hide();
		var val = $(this).html();

		if(val == '首页'){
			$(".t_nav").hide();
			$(".tree").hide();
		}else{
			$(".t_nav").show();
			$(".tree").show();
		}

		$(".t_nav ul").hide();
		switch(val){
			case '首页':
			$("#page").attr('src',APP+'/Index/main');
			$(".t_nav ul").hide();
			break;	
			case '云监控':
			$("#page").attr('src',APP+'/CloudMonitor');			
			$(".t_nav ul:eq(0)").show();
			$(".tree ul:eq(0),.tree ul:eq(1),.tree ul:eq(2)").show();
			break;	
			case '云服务':
			$("#page").attr('src',APP+'/CloudService');
			$(".tree ul:eq(3)").show();
			$(".t_nav ul:eq(1)").show();
			break;	
			case '我的云':
			$("#page").attr('src',APP+'/CloudProperty');
			$(".tree ul:eq(4)").show();
			$(".t_nav ul:eq(2)").show();
			break;	
		}
		$('.t_left li a').removeClass('actived');
		$(this).addClass('actived');
	});
	
	$('.t_nav li a').click(function(){		
		$('.t_nav li a').removeClass('actived');
		$(this).addClass('actived');
	});
	
	$('.t_help').click(function(){
		$('.as_admin').hide();
		$('.as_notice').hide();
		$('.as_help').toggle();
	});
	$('.t_notice').click(function(){
		$('.as_admin').hide();
		$('.as_help').hide();
		$('.as_notice').toggle();
	});
	$('.t_more').click(function(){
		$('.as_help').hide();
		$('.as_notice').hide();
		$('.as_admin').toggle();
	});
	
	$('.as_admin li:last').css({
		"border-top":"1px solid #ccc", 
		"text-align":"right", 
		"padding-right":0
	});
	
	$('.menu').click(function(){
		$(this).toggleClass('on');
		$(this).siblings('div').toggle("slow");
		$(this).siblings('dl').toggle("slow");
	});
	
	$('.tree dt').click(function(){
		$('.tree dt').removeClass('actived');
		$(this).addClass('actived');
	});
	
	$('.content li:first').css("border-left","1px solid #ccc");
	
	$('.info table tr').find("td:eq(0)").addClass('in_td');
	
	$('.in_table tr').find("td:eq(0)").css("line-height","27px");
	
	$('.content table tr:even').css("background","#f6f9fe");
	$('.kz .content table tr').css("background","#fff");
	
	$('.content table tr').hover(function(){
		$('.content table tr:even').css("background","#f6f9fe");
		$('.content table tr:odd').css("background","#fff");
		$(this).css("background","#d7f5ff");
	});
	
	$('.kz .content table tr').hover(function(){
		$('.content table tr').css("background","#fff");
		$(this).css("background","#d7f5ff");
	});
	
	$('.as_admin').mouseleave(function(){ $(this).hide();});
	$('.as_help').mouseleave(function(){ $(this).hide();});
	$('.as_notice').mouseleave(function(){ $(this).hide();});
})

/********************云服务器部分******************/

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
		url:INDEX+'/Index/renewPay',
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
					area: ['650px', '558px'],
					iframe: {src: INDEX+'/Index/renewPay?cpu=' + data.data.CPU + '&ram=' + data.data.RAM + '&sys=' + data.data.OS + '&disk=' + data.data.DISK + '&iplc=' + data.data.BandWidthIPLC + '&bgp=' + data.data.BandWidthBGP + '&count=' + data.data.Count + '&oid=' + oid + '&house=' + data.data.HouseName}
				});
			}
		},
		error:function(data){					
			layer.msg(data.statusText,3,3);
		}
	});
}
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
function showMore(obj,e){
	$(obj).next().slideToggle();
	stopPropagation(e);
}
function operationVM(obj,code,id){
	if($("#"+id).attr('expire')=='yes'){
		layer.msg('提示信息：云主机到期，不能继续操作，请续费。',3,7);
		return false;
	}
	var val = $(obj).attr('title'),opt="";	
	layer.load('正在' + val,3);
	switch(val){
		case '开机':opt="on"; break;
		case '关机':opt="off"; break;
		case '重启':opt="reboot"; break;
	}	
	$.ajax({
		url:INDEX+'/Index/operationVM',
		type:'post',
		data:{'opt':opt,'code':code},
		success:function(data){
			if(data.info=='success'){
				if(data.data==0){
					layer.msg("云主机【"+val+"】成功",2,1);
					switch(val){								
						case '开机':
						$("#ico_"+id).html('<span class="run_ico" style="background:url('+ROOT+'/Public/images/run_status.png) no-repeat;" >运行中</span>');
						$(obj).parent().next().html('<a href="javascript:void(0);" onClick="operationVM(this,\''+code+'\','+id+')" title="关机">关机</a>');
						$(obj).parent().html('<a href="javascript:void(0);" style="color:#ccc" title="开机">开机</a>');
						break;
						case '关机':								
						$("#ico_"+id).html('<span class="run_ico" style="background:url('+ROOT+'/Public/images/stop_status.png) no-repeat;" >已关机</span>');
						$(obj).parent().prev().html('<a href="javascript:void(0);" onClick="operationVM(this,\''+code+'\','+id+')" title="开机">开机</a>');
						$(obj).parent().html('<a href="javascript:void(0);" style="color:#ccc" title="关机">关机</a>');
						break;
						case '重启':
						$("#ico_"+id).html('<span class="run_ico" style="background:url('+ROOT+'/Public/images/run_status.png) no-repeat;" >运行中</span>');
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
		case '启动':opt="on"; break;
		case '停止':opt="off"; break;
		case '重启':opt="reboot"; break;
	}
	$("#console table :checked").each(function(i){
		if(i!=$("#console table :checked").length-1){
			code += $(this).attr('code') + "," ;
		}else{
			code += $(this).attr('code') ;
		}
	});
	$.ajax({
		url:INDEX+'/Index/batchOperationVM',
		type:'post',
		data:{'opt':opt,'code':code},
		success:function(data){
			if(data.info=='success'){
				$("#console table :checked").each(function(){
					var id = $(this).attr('id');
					switch(val){								
						case '启动':
						$("#ico_"+id).html('<span class="run_ico" style="background:url('+ROOT+'/Public/images/run_status.png) no-repeat;" >运行中</span>');
						$(this).parent().parent().find('.more ul li:eq(0)').html('<a href="javascript:void(0);" style="color:#ccc" title="开机">开机</a>');
						$(this).parent().parent().find('.more ul li:eq(1)').html('<a href="javascript:void(0);" onClick="operationVM(this,\''+code+'\','+id+')" title="关机">关机</a>');
						break;								
						case '停止':												
						$("#ico_"+id).html('<span class="run_ico" style="background:url('+ROOT+'/Public/images/stop_status.png) no-repeat;" >已关机</span>');								
						$(this).parent().parent().find('.more ul li:eq(0)').html('<a href="javascript:void(0);" onClick="operationVM(this,\''+code+'\','+id+')" title="开机">开机</a>');
						$(this).parent().parent().find('.more ul li:eq(1)').html('<a href="javascript:void(0);" style="color:#ccc" title="关机">关机</a>');
						break;								
						case '重启':
						$("#ico_"+id).html('<span class="run_ico" style="background:url('+ROOT+'/Public/images/run_status.png) no-repeat;" >运行中</span>');								
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
function first(num){
	if(page!=num){
		selectBy(num);
	}
}
function last(num){
	if(page!=num){
		selectBy(num);
	}
}
function prev(num){
	if(num>=1){
		selectBy(num);
	}
}
function next(num){
	if(num <= pageCount){
		selectBy(num);
	}
}
function goto(obj){
	var n = $(obj).val();
	selectBy(n);
}
function selectBy(n){
	var url = APP + "/CloudProperty/vps?p=" + n,status = $("#sel_status").val() ,code = $("#txt_code").val();	
	if(code){
		url += "&code=" + code;
	}
	if(status){
		url += "&status=" + status;
	}
	window.location.href = url;
}
function searchVPS(){
	selectBy(1);
}