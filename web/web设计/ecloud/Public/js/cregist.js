	function regist_form(form){
		clearmsg();
		var cmname = $("#companyname").val();
		var cmcode = $("#companycode").val();
		var mail = $("#useremail").val();
		var pwd  = $("#password").val();
		var repwd  = $("#repassword").val();
		var code  = $("#checkcode").val();
		var reg = /^[\u4E00-\u9FA5]+$/; 
		if(!cmname){
			$("#companyname").focus();
			$("#msg-companyname").show();
			return false;
		}
		if(!reg.test(cmname)){
			$("#companyname").focus();
			$("#error-msg").html("输入正确的企业名称");
			return false;
		}
		if(!cmcode){
			$("#companycode").focus();
			$("#msg-companycode").show();
			return false;
		}
		if(!mail){
			$("#username").focus();
			$("#msg-useremail").show();
			return false;
		}
		if(!isEmail(mail)){
			$("#useremail").focus();
			$("#error-msg").html("输入正确的邮箱地址");
			return false;
		}
		if(!pwd){
			$("#password").focus();
			$("#msg-pwd").show();
			return false;
		}
		if(repwd != pwd){
			$("#repassword").focus();
			$("#msg-repwd").show();
			return false;
		}
		if(!code){
			$("#checkcode").focus();
			$("#msg-code").show();
			return false;
		}
		
		$.ajax({
			url:APP + '/Index/cregist',
			type:'post',
			async:false,
			data:$("#regist").serialize(),
			success:function(data){
				if(data.info=='success'){
//					$("#error-msg").html(data.data);
//					setInterval(function(){
						window.location.href = APP + "/Index/company"//?id="+data.data;
//					},3000);
				}else{
					$("#error-msg").html(data.data);
				}
			},error:function(data){
				$("#error-msg").html(data.statusText);
			}
		});
	}