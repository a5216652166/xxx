	function validate_form(form){
		onBlue();
		var mail = $("#username").val();
		var pwd  = $("#password").val();
		//var code  = $("#checkcode").val();
		if(!mail){
			$("#username").focus();
			$("#error-msg").html("请输入用户名");
			return false;
		}
		if(!isEmail(mail)){
			$("#username").focus();
			$("#error-msg").html("请输入正确的邮箱格式");	
			return false;
		}
		if(!pwd){
			$("#password").focus();
			$("#error-msg").html("请输入密码");
			return false;
		}
		
		/*$.ajax({
			url:APP+'/Index/login',
			type:'post',
			async:false,
			data:$("#regist").serialize(),
			success:function(data){
				if(data.info=='success'){
					
					//setInterval(function(){
						window.location.href = APP + "/Index/buy";
					//},3000);
				}else{
					$("#error-msg").html(data.data);
					return false;
				}
			},error:function(data){
				$("#error-msg").html(data.statusText);
			}
		});*/
		/*if(!code){
			$("#checkcode").focus();
			$("#msg-code").show();
			return false;
		}
		*/
		return true;
	}
	function isEmail(str){
		var myReg = /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/;
		if(myReg.test(str)) return true; 
		return false; 
	}
	
