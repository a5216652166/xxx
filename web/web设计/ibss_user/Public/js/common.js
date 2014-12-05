$(function(){	
	$("#cash-tbinfo .colorTr:odd").css("background-color","#F7F9FB");	
	//移动变色
	$(".colorTr").mouseover(function(){
		$(this).css('backgroundColor','#f3f3f3');
	}).mouseout(function(){
		$(this).css('backgroundColor','');
		$("#cash-tbinfo .colorTr:odd").css("background-color","#F7F9FB");	
	});	
});
//获取地址上的参数
String.prototype.getQuery = function (name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
	var r = window.location.search.substr(1).match(reg);
	if (r != null) return unescape(r[2]); return null;
}
//验证邮箱
function checkemail(val){
	var reg =/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
		if(!reg.test(val)){
			return false;
		}
	return true;
}
//当前时间
function getNowFormatDate() {
    var date = new Date();
    var seperator1 = "-";
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate;
    return currentdate;
}
//格式化货币
function formatCurrency(num) { 
	var sign=""; 
	if(isNaN(num)){ 
		num = 0; 
	} 
	if(num<0){ 
		sign="-"; 
	} 
	var strNum=num+""; 
	var arr1 = strNum.split("."); 
	var hasPoint=false;//是否有小数部分 
	var piontPart="00";//小数部分 
	var intPart=strNum;//整数部分 
	if(arr1.length>=2){ 
		hasPoint=true; 
		piontPart= arr1[1]; 
		intPart=arr1[0]; 
	} 
	var res='';//保存添加逗号的部分 
	var intPartlength=intPart.length;//整数部分长度 
	var maxcount=Math.ceil(intPartlength/3);//整数部分需要添加几个逗号 
	for (var i = 1; i <=maxcount;i++){ //每三位添加一个逗号
		var startIndex=intPartlength-i*3;//开始位置 
		if(startIndex<0){//开始位置小于0时修正为0 
			startIndex=0;
		} 
		var endIndex=intPartlength-i*3+3;//结束位置 
		var part=intPart.substring(startIndex,endIndex)+","; 
		res=part+res; 
	} 
	res=res.substr(0,res.length-1);//去掉最后一个逗号 
	//if(hasPoint){ 
		return sign+res+"."+piontPart; 
	//}else { 
	//	return sign+res; 
	//}
}
//添加购物车
function addCart(type,id){
	if($("#error_msg").html()!=""){
		$("#error_msg").html('');
	}
	if(USER==''){
		$.layer({
			type: 2,
			maxmin: true,
			title: '您还尚未登录',
			offset: ['200px',''],
			area: ['500px', '400px'],
			iframe: {src: APP+'/Index/login_div'}
		}); 
	}else{
		//查看用户是否通过审核
		if(Audit==0){
			$("#error_msg").css('color','red').html("提示信息：该用户还没通过审核，没有购买资格");
		}else{
			$.ajax({
				url:APP+'/Index/addCart',
				type:'post',
				data:{'id':id,'type':type},
				async:false,
				success:function(data){
					if(data.info=='success'){
						bindDiv(data);
					}else{
						$("#error_msg").css('color','red').html("提示信息："+data.data);
						$(document).scrollTop(0);
					}
					getTotalPrice();
				},
				error:function(data){
					$("#error_msg").css('color','red').html("提示信息："+data.statusText);
				}
			});
		}
	}
}
//删除购物车
function deleteCart(type,id){
	if($("#error_msg").html()!=""){
		$("#error_msg").html('');
	}
	$.ajax({
		url:APP+'/Index/deleteCart',
		type:'post',
		data:{'id':id,'type':type},
		async:false,
		success:function(data){
			if(data.info=='success'){
				bindDiv(data);
			}else{
				//$("#myOrder").parent().parent().css('overflow-y','hidden');
				//$("#myOrder").html('<div class="order">暂无商品信息</div>');	
				$("#error_msg").css('color','red').html("提示信息："+data.data);
			}
			getTotalPrice();
		},
		error:function(data){
			$("#error_msg").css('color','red').html("提示信息："+data.statusText);
		}
	});
}
//绑定购物车div
function bindDiv(data){
	var str = '';
	if(!data.data['dev'] && !data.data['vps'] && !data.data['ip'] && !data.data['bandwidth'] && !data.data['bandwidthExt'] && !data.data['cabinet']){		
		$("#myOrder").parent().parent().css('overflow-y','hidden');
		$("#myOrder").html('<div class="order">暂无商品信息</div>');
	}else{
		//云服务器
		if(data.data['vps']){
			for(var i=0;i<data.data['vps'].length;i++){
				str +='<div class="order"><div class="order-row"><a href="javascript:void(0);" rel="nofollow" target="_self" class="lnk-delete-order fn-right" title="删除" hidefocus=""><i class="icon-wrong" style="font-family:Arial; float:right;" onclick="deleteCart(\'dev\','+data.data['vps'][i].ID+')">X</i></a><span class="order-price"><span class="cny">¥</span><span>'+formatCurrency(data.data['vps'][i].Price)+'</span></span></div><div class="order-row"><label>品牌：</label><div class="order-info">'+data.data['vps'][i].Brand+'</div></div><div class="order-row"><label>CPU：</label><div class="order-info">'+data.data['vps'][i].CPU+'</div></div><div class="order-row"><label>内存：</label><div class="order-info">'+data.data['vps'][i].Ram+'</div></div><div class="order-row"><label>硬盘：</label><div class="order-info">'+data.data['vps'][i].Disk+'</div></div></div>';
			}
		}
		//设备
		if(data.data['dev']){
			for(var i=0;i<data.data['dev'].length;i++){
				str +='<div class="order"><div class="order-row"><a href="javascript:void(0);" rel="nofollow" target="_self" class="lnk-delete-order fn-right" title="删除" hidefocus=""><i class="icon-wrong" style="font-family:Arial; float:right;" onclick="deleteCart(\'dev\','+data.data['dev'][i].ID+')">X</i></a><span class="order-price"><span class="cny">¥</span><span>'+formatCurrency(data.data['dev'][i].Price)+'</span></span></div><div class="order-row"><label>品牌：</label><div class="order-info">'+data.data['dev'][i].Brand+'</div></div><div class="order-row"><label>CPU：</label><div class="order-info">'+data.data['dev'][i].CPU+'</div></div><div class="order-row"><label>内存：</label><div class="order-info">'+data.data['dev'][i].Ram+'</div></div><div class="order-row"><label>硬盘：</label><div class="order-info">'+data.data['dev'][i].Disk+'</div></div></div>';
			}
		}
		//ip
		if(data.data['ip']){
			for(var i=0;i<data.data['ip'].length;i++){
				str +='<div class="order"><div class="order-row"><a href="javascript:void(0);" rel="nofollow" target="_self" class="lnk-delete-order fn-right" title="删除" hidefocus=""><i class="icon-wrong" style="font-family:Arial; float:right;" onclick="deleteCart(\'ip\','+data.data['ip'][i].ID+')">X</i></a><span class="order-price"><span class="cny">¥</span><span>'+formatCurrency(data.data['ip'][i].Price)+'</span></span></div><div class="order-row"><label>套餐类型：</label><div class="order-info">'+data.data['ip'][i].ProName+'</div></div><div class="order-row"><label>类型：</label><div class="order-info">'+data.data['ip'][i].Type+'</div></div><div class="order-row"><label>数量：</label><div class="order-info">'+data.data['ip'][i].Count+'</div></div></div>';
			}
		}
		//带宽
		if(data.data['bandwidth']){
			for(var i=0;i<data.data['bandwidth'].length;i++){
				str +='<div class="order"><div class="order-row"><a href="javascript:void(0);" rel="nofollow" target="_self" class="lnk-delete-order fn-right" title="删除" hidefocus=""><i class="icon-wrong" style="font-family:Arial; float:right;" onclick="deleteCart(\'bandwidth\','+data.data['bandwidth'][i].ID+')">X</i></a><span class="order-price"><span class="cny">¥</span><span>'+formatCurrency(data.data['bandwidth'][i].Price)+'</span></span></div><div class="order-row"><label>套餐类型：</label><div class="order-info">'+data.data['bandwidth'][i].ProName+'</div></div><div class="order-row"><label>类型：</label><div class="order-info">'+data.data['bandwidth'][i].Type+'</div></div><div class="order-row"><label>单位：</label><div class="order-info">'+data.data['bandwidth'][i].Unit+'</div></div></div>';
			}
		}
		//带宽Ext
		if(data.data['bandwidthExt']){
			for(var i=0;i<data.data['bandwidthExt'].length;i++){
				str +='<div class="order"><div class="order-row"><a href="javascript:void(0);" rel="nofollow" target="_self" class="lnk-delete-order fn-right" title="删除" hidefocus=""><i class="icon-wrong" style="font-family:Arial; float:right;" onclick="deleteCart(\'bandwidthExt\','+data.data['bandwidthExt'][i].ID+')">X</i></a><span class="order-price"><span class="cny">¥</span><span>'+formatCurrency(data.data['bandwidthExt'][i].Price)+'</span></span></div><div class="order-row"><label>套餐类型：</label><div class="order-info">'+data.data['bandwidthExt'][i].ProName+'</div></div><div class="order-row"><label>类型：</label><div class="order-info">'+data.data['bandwidthExt'][i].Type+'</div></div><div class="order-row"><label>单位：</label><div class="order-info">'+data.data['bandwidthExt'][i].Unit+'</div></div></div>';
			}
		}
		//机柜
		if(data.data['cabinet']){
			for(var i=0;i<data.data['cabinet'].length;i++){
				str +='<div class="order"><div class="order-row"><a href="javascript:void(0);" rel="nofollow" target="_self" class="lnk-delete-order fn-right" title="删除" hidefocus=""><i class="icon-wrong" style="font-family:Arial; float:right;" onclick="deleteCart(\'cabinet\','+data.data['cabinet'][i].ID+')">X</i></a><span class="order-price"><span class="cny">¥</span><span>'+formatCurrency(data.data['cabinet'][i].Price)+'</span></span></div><div class="order-row"><label>套餐类型：</label><div class="order-info">'+data.data['cabinet'][i].ProName+'</div></div><div class="order-row"><label>机房：</label><div class="order-info">'+data.data['cabinet'][i].Location+'</div></div><div class="order-row"><label>数量：</label><div class="order-info">'+data.data['cabinet'][i].Count+'</div></div></div>';
			}
		}
		
		$("#myOrder").empty();
		$("#myOrder").html(str);
		var vps_count=0,dev_count=0,ip_count=0,bandwidthExt_count=0,bandwidth_count=0,cabinet_count=0,count=0;
		if(data.data['vps']){
			vps_count = data.data['vps'].length;
		}
		if(data.data['dev']){
			vps_count = data.data['dev'].length;
		}
		if(data.data['ip']){
			vps_count = data.data['ip'].length;
		}
		if(data.data['bandwidthExt']){
			vps_count = data.data['bandwidthExt'].length;
		}
		if(data.data['bandwidth']){
			vps_count = data.data['bandwidth'].length;
		}
		if(data.data['cabinet']){
			vps_count = data.data['cabinet'].length;
		}
		var count = vps_count + dev_count + ip_count + bandwidthExt_count + bandwidth_count + cabinet_count;
		if(count>2){
			$("#myOrder").parent().parent().css('overflow-y','scroll');
		}else{
			$("#myOrder").parent().parent().css('overflow-y','hidden');
		}
	}
}
function getTotalPrice(){
	$.ajax({
		url:APP+'/Index/getTotalPrice',
		type:'post',
		async:false,
		success:function(data){
			if(data.data==0){
				$("#totalPrice").html('0.00');
			}else{
				$("#totalPrice").html(data.data);
			}
		},
		error:function(data){
			$("#error_msg").css('color','red').html("提示信息："+data.statusText);
		}
	});
}
function getDevSlide(obj,id){
	var val=0;									
	switch(obj["dev"][id].length){
		case 3:
			val = 360;break;
		case 5:
			val = 180;break;
		case 9:
			val = 90;break;
		case 25:
			val = 30;break;	
	}
	$("#slider_dev_txt_"+id).html(obj["dev"][id][0]["to"]);									
	$("#slider_dev_"+id).slider({
		values: [val],
		max:730,
		stop:function(evt,ui){
			var stopval = ui.values,money=obj["dev"][id][0]["money"];
			$.each(obj["dev"][id],function(i,item){
				if(item.point < stopval){
				  money = money+item.money;
				}
				if(item.point >= stopval){
				  $("#slider_dev_txt_"+id).html(item.to);
				  $("#dev_price_"+id).html(formatCurrency(money));
				  $("#slider_dev_"+id).slider("values",[item.point]);
				  return false;
				}
			 });
			 getSumPrice();
		}
	});	
}
function getVpsSlide(obj,id){
	var val=0;									
	switch(obj["vps"][id].length){
		case 3:
			val = 360;break;
		case 5:
			val = 180;break;
		case 9:
			val = 90;break;
		case 25:
			val = 30;break;	
	}
	$("#slider_vps_txt_"+id).html(obj["vps"][id][0]["to"]);									
	$("#slider_vps_"+id).slider({
		values: [val],
		max:730,
		stop:function(evt,ui){
			var stopval = ui.values,money=obj["vps"][id][0]["money"];
			$.each(obj["vps"][id],function(i,item){
				if(item.point < stopval){
				  money = money+item.money;
				}
				if(item.point >= stopval){
				  $("#slider_vps_txt_"+id).html(item.to);
				  $("#vps_price_"+id).html(formatCurrency(money));
				  $("#slider_vps_"+id).slider("values",[item.point]);
				  return false;
				}
			 });
			 getSumPrice();
		}
	});	
}

function getIPSlide(obj,id){
	var val=0;									
	switch(obj["ip"][id].length){
		case 3:
			val = 360;break;
		case 5:
			val = 180;break;
		case 9:
			val = 90;break;
		case 25:
			val = 30;break;	
	}
	$("#slider_ip_txt_"+id).html(obj["ip"][id][0]["to"]);									
	$("#slider_ip_"+id).slider({
		values: [val],
		max:730,
		stop:function(evt,ui){
			var stopval = ui.values,money=obj["ip"][id][0]["money"];
			$.each(obj["ip"][id],function(i,item){
				if(item.point < stopval){
				  money = money+item.money;
				}
				if(item.point >= stopval){
				  $("#slider_ip_txt_"+id).html(item.to);
				  $("#ip_price_"+id).html(formatCurrency(money));
				  $("#slider_ip_"+id).slider("values",[item.point]);
				  return false;
				}
			 });
			 getSumPrice();
		}
	});	
}

function getBandWidthSlide(obj,id){
	var val=0;									
	switch(obj["bandwidth"][id].length){
		case 3:
			val = 360;break;
		case 5:
			val = 180;break;
		case 9:
			val = 90;break;
		case 25:
			val = 30;break;	
	}
	$("#slider_bandwidth_txt_"+id).html(obj["bandwidth"][id][0]["to"]);									
	$("#slider_bandwidth_"+id).slider({
		values: [val],
		max:730,
		stop:function(evt,ui){
			var stopval = ui.values,money=obj["bandwidth"][id][0]["money"];
			$.each(obj["bandwidth"][id],function(i,item){
				if(item.point < stopval){
				  money = money+item.money;
				}
				if(item.point >= stopval){
				  $("#slider_bandwidth_txt_"+id).html(item.to);
				  $("#bandwidth_price_"+id).html(formatCurrency(money));
				  $("#slider_bandwidth_"+id).slider("values",[item.point]);
				  return false;
				}
			 });
			 getSumPrice();
		}
	});
}
function getBandWidthExtSlide(obj,id){
	var val=0;									
	switch(obj["bandwidthExt"][id].length){
		case 3:
			val = 360;break;
		case 5:
			val = 180;break;
		case 9:
			val = 90;break;
		case 25:
			val = 30;break;	
	}
	$("#slider_bandwidthExt_txt_"+id).html(obj["bandwidthExt"][id][0]["to"]);									
	$("#slider_bandwidthExt_"+id).slider({
		values: [val],
		max:730,
		stop:function(evt,ui){
			var stopval = ui.values,money=obj["bandwidthExt"][id][0]["money"];
			$.each(obj["bandwidthExt"][id],function(i,item){
				if(item.point < stopval){
				  money = money+item.money;
				}
				if(item.point >= stopval){
				  $("#slider_bandwidthExt_txt_"+id).html(item.to);
				  $("#bandwidthExt_price_"+id).html(formatCurrency(money));
				  $("#slider_bandwidthExt_"+id).slider("values",[item.point]);
				  return false;
				}
			 });
			 getSumPrice();
		}
	});
}
function getCabinetSlide(obj,id){
	var val=0;									
	switch(obj["cabinet"][id].length){
		case 3:
			val = 360;break;
		case 5:
			val = 180;break;
		case 9:
			val = 90;break;
		case 25:
			val = 30;break;	
	}
	$("#slider_cabinet_txt_"+id).html(obj["cabinet"][id][0]["to"]);									
	$("#slider_cabinet_"+id).slider({
		values: [val],
		max:730,
		stop:function(evt,ui){
			var stopval = ui.values,money=obj["cabinet"][id][0]["money"];
			$.each(obj["cabinet"][id],function(i,item){
				if(item.point < stopval){
				  money = money+item.money;
				}
				if(item.point >= stopval){
				  $("#slider_cabinet_txt_"+id).html(item.to);
				  $("#cabinet_price_"+id).html(formatCurrency(money));
				  $("#slider_cabinet_"+id).slider("values",[item.point]);
				  return false;
				}
			 });
			 getSumPrice();
		}
	});
}





