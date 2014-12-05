	
	function regist_form(form){
		clearmsg();
		var mail = $("#username").val();
		var pwd  = $("#password").val();
		var repwd  = $("#repassword").val();
		var code  = $("#checkcode").val();
		if(!mail){
			$("#username").focus();
			$("#msg-username").show();
			return false;
		}if(!isEmail(mail)){
			$("#username").focus();
			$("#msg-username").html("输入正确的邮箱地址");
			$("#msg-username").show();
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
			url:APP + '/Index/regist',
			type:'post',
			async:false,
			data:$("#regist").serialize(),
			success:function(data){
				if(data.info=='success'){
//					$("#error-msg").html(data.data);
//					setInterval(function(){
						window.location.href = APP + "/Index/person"//?id="+data.data;
//					},3000);
				}else{
					$("#error-msg").html(data.data);
				}
			},error:function(data){
				$("#error-msg").html(data.statusText);
			}
		});
	}