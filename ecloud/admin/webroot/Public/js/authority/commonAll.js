/**菜单跳转**/
function rightMain(url){
	$('#rightMain').attr('src', url);
}

/**后腿**/
//返回
function back(url){
	if(url){
		location = url;
	}else{
		history.back();
	}
}


/** 普通跳转 **/
function jumpNormalPage(page, url){
	var linkChr = url.indexOf('?') >= 0 ? '&' : '?';

	$("#submitForm").attr("action", url + linkChr + "page=" + page).submit();
}

/** 输入页跳转 **/
function jumpInputPage(pageTotal, url){
	var linkChr = url.indexOf('?') >= 0 ? '&' : '?';

	// 如果“跳转页数”不为空
	if($("#jumpNumTxt").val() != ''){
		var pageNum = parseInt($("#jumpNumTxt").val());
		// 如果跳转页数在不合理范围内，则置为1
		if(pageNum < 1 || pageNum > pageTotal){
			art.dialog({icon:'error', title:'友情提示', drag:false, resize:false, content:'输入的页数不合法，2秒后自动为您跳到首页', ok:true, time: 2, close: function () {
		    		$("#submitForm").attr("action", url + linkChr + "page=1").submit();
		    	}
			});
		}else{
			$("#submitForm").attr("action", url + linkChr + "page=" + pageNum).submit();
		}
	}else{
		// “跳转页数”为空
		art.dialog({icon:'error', title:'友情提示', drag:false, resize:false, content:'输入的页数不合法，2秒后自动为您跳到首页', ok:true, time: 2, close: function () {
				$("#submitForm").attr("action", url + linkChr + "page=1").submit();
			}
		});
	}
}


//---------------------------------------------------  
// 日期格式化  
// 格式 YYYY/yyyy/YY/yy 表示年份  
// MM/M 月份  
// W/w 星期  
// dd/DD/d/D 日期  
// hh/HH/h/H 时间  
// mm/m 分钟  
// ss/SS/s/S 秒  
//---------------------------------------------------  
Date.prototype.Format = function(formatStr)   
{   
    var str = formatStr;   
    var Week = ['日','一','二','三','四','五','六'];  
  
    str=str.replace(/yyyy|YYYY/,this.getFullYear());   
    str=str.replace(/yy|YY/,(this.getYear() % 100)>9?(this.getYear() % 100).toString():'0' + (this.getYear() % 100));   
  
    str=str.replace(/MM/,this.getMonth()>9?this.getMonth().toString():'0' + this.getMonth());   
    str=str.replace(/M/g,this.getMonth());   
  
    str=str.replace(/w|W/g,Week[this.getDay()]);   
  
    str=str.replace(/dd|DD/,this.getDate()>9?this.getDate().toString():'0' + this.getDate());   
    str=str.replace(/d|D/g,this.getDate());   
  
    str=str.replace(/hh|HH/,this.getHours()>9?this.getHours().toString():'0' + this.getHours());   
    str=str.replace(/h|H/g,this.getHours());   
    str=str.replace(/mm/,this.getMinutes()>9?this.getMinutes().toString():'0' + this.getMinutes());   
    str=str.replace(/m/g,this.getMinutes());   
  
    str=str.replace(/ss|SS/,this.getSeconds()>9?this.getSeconds().toString():'0' + this.getSeconds());   
    str=str.replace(/s|S/g,this.getSeconds());   
  
    return str;   
}

/** 异步加载栋号列表 * */
function getFyDhListByFyXqCode() {
	var fyXq = $("#fyXq").val();
	if (fyXq == "" || fyXq == null) {
		$("#fyDh").html('<option value="">--请选择--</option>');
	} else {
		/** 异步加载栋号列表 * */
		$.ajax({
			type : "POST",
			url : "getFyDhListByFyXqCode.action",
			data : {
				"fyXqCode" : fyXq
			},
			dataType : "json",
			success : function(data) {
				// 如果返回数据不为空，更改“房源信息”
				if (data == null || data == '') {// 如果为空
					alert("该小区下暂无栋号列表，请联系\n管理员维护数据哦！！！");
					$("#fyDh").html('<option value="">--请选择--</option>');
				} else {
					var str = '<option value="">--请选择--</option>';
					// 将返回的数据赋给zTree
					for (var i = 0; i < data.length; i++) {
						str += '<option value="' + data[i].weiduID + '">'
								+ data[i].weiduName + '</option>';
					}
					// alert(str);
					$("#fyDh").html(str);
				}
			}
		});
	}
}

/*
 * 是否全选
 */
function selectOrClearAllCheckbox(obj) {
	var checkStatus = $(obj).attr("checked");
	if (checkStatus == "checked") {
		$("input[type='checkbox']").attr("checked", true);
	} else {
		$("input[type='checkbox']").attr("checked", false);
	}
}

/** 日期函数，加几天，减几天 **/
function getNextDay(dd, dadd) {
	// 可以加上错误处理
	var a = new Date(dd);
	a = a.valueOf();
	a = a + dadd * 24 * 60 * 60 * 1000;
	a = new Date(a);
	var m = a.getMonth() + 1;
	if (m.toString().length == 1) {
		m = '0' + m;
	}
	var d = a.getDate();
	if (d.toString().length == 1) {
		d = '0' + d;
	}
	return a.getFullYear() + "-" + m + "-" + d;
}



/** 检测IP是否正确 **/
function checkValidIP(ip){
	var a = ip.split('.');
	if(a.length != 4){
		return false;
	}

	for(var i = 0; i < a.length; i++){
		var n = parseInt(a[i]);
		if(isNaN(n)){
			return false;
		}
		if(n < 0 || n > 255){
			return false;
		}
	}

	return true;
}

/** 将IP字符串转换为数字 **/
function IPStrToNum(s){
	if(!checkValidIP(s)){
		return false;
	}

	var a = s.split('.');
	var n = parseInt(a[0]) << 24;
	n |= parseInt(a[1]) << 16;
	n |= parseInt(a[2]) << 8;
	n |= parseInt(a[3]);
	n = n >>> 0;

	return n;
}

/** 将IP数字转换为字符串 **/
function IPNumToStr(n){
	var s = [];
	s[0] = n >>> 24;
	s[1] = (n << 8) >>> 24;
	s[2] = (n << 16) >>> 24;
	s[3] = (n << 24) >>> 24;

	return s.join(".");
}


/* table鼠标悬停换色 
$(function() {
	// 如果鼠标移到行上时，执行函数
	$(".table tr").mouseover(function() {
		$(this).css({background : "#CDDAEB"});
		$(this).children('td').each(function(index, ele){
			$(ele).css({color: "#1D1E21"});
		});
	}).mouseout(function() {
		$(this).attr('style', $(this).attr('style').replace(/background.*;/g, ''));
		//$(this).css({background : "#FFF"});
		$(this).children('td').each(function(index, ele){
			$(ele).attr('style', $(this).attr('style').replace(/color.*;/g, ''));
			//$(ele).css({color: "#909090"});
		});
	});
});
*/