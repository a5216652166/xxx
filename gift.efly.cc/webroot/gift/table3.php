<!-- 代金券 -->
<!DOCTYPE HTML>
<?php 

	session_start(); 
	
	if(empty($_SESSION['user'])){
		echo '<script type="text/javascript">window.location.href = "./index.html";</script>';
	}
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>

<link rel="stylesheet" href="./css/gift.css" />
<style type="text/css">
	/* jump register/login link
	a:link {text-decoration:none; color:#00aadd} 
	a:visited {text-decoration:none;color:#00aadd}
	a:hover{text-decoration:none;color:#00aadd}
	a:active {text-decoration:none;color:#00aadd} 
	*/

	#item_1{ margin-top:50px;}
</style>

<script type="text/javascript" src="js/jquery-1.8.1.min.js"></script>
<script type="text/javascript" src="js/layer/layer.js"></script>
<script type="text/javascript" >
	function next(str){
		if(str=='item_2'){
			$("#regist-process").attr('class','step2');
		}else if(str=='item_3'){
			if($("#item_2 :checkbox").attr('checked')!="checked"){
				layer.msgClose('需同意本协议',2,5,function(index){
                    var $div = $("#item_2");
                    $div.scrollTop($div[0].scrollHeight);
                });

				return;
			}
			$("#regist-process").attr('class','step3');
		}else if(str=='ok'){
			var mail = $("#mail").val(),
				pwd = $("#pwd").val(),
				reg =/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
			if(!mail){
				$("#mail").focus();
				return ;
			}
			if(!reg.test(mail)){
				$("#mail").focus();
				layer.msgClose('输入的睿江云账户格式不正确',2,5);
				return ;
			}
			if(!pwd){
				$("#pwd").focus();
				return ;
			}
			//通过验证
			$.ajax({
				url:'receive.php?opt=recharge',
				type:'post',
				data:{
						'mail':mail,
						'pwd':pwd,
						'ID':$("#ID").val()
				},
				dataType: "json", 
				success:function(data){
					if(data.info=="success"){						
						layer.msgClose('礼品领取完毕，可登陆睿江云平台使用代金券。',2,1);
						//setInterval(function(){window.parent.location.reload();},2000);
                        setInterval(function(){window.parent.location='http://cloud.efly.cc';},2000);
					}else{
						//layer.msgClose(data.data,2,5);
                        //setInterval(function(){window.location.reload();},2000);

                        var msg = "充值失败，请联系客服人员<br/>电话：12345678910<br/>QQ：123456789";
                        $.layer({
                            dialog : {
                                msg : msg, type : 5
                            },
                            title : false,
                            area: ['auto', 'auto'],
                            close: function(index){
                                window.parent.location.reload();
                            }
                        });
					}
				},
				error:function(data){				
					layer.msgClose(data.statusText,3,5);
				}
			});
		}
		$(".table #item_1,.table #item_2,.table #item_3").hide();
		$("#"+str).show();
	}
	
	function GetQueryString(name){
		 var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
		 var r = window.location.search.substr(1).match(reg);
		 if(r!=null)return  unescape(r[2]); return null;
	}
	$(function(){
		$("#ID").val(GetQueryString('ID'));
	});
	function isEmail(str){
        var myReg = /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/;
        if(myReg.test(str)) return true; 
        return false; 
    }
	function isPhone(str){
        var myReg = /^1\d{10}$/;
        if(myReg.test(str)) return true; 
        return false; 
    }

    function downIt(){
    	location = 'receive.php?opt=download';
    }
	
</script>

</head>
<body>
	<div id="regist-process" class="step1">
        <div id="regist-process-bar"></div>
        <div id="regist-process-step1">礼品信息</div>
        <div id="regist-process-step2">安全协议</div>
        <div id="regist-process-step3">领取礼品</div>
    </div>
    <div class="table">
    	<div id="item_1">
            <p><span>VPN账号：活动价：免费使用一年</span></p>
            <!--p><span>标准价：600元/M/月；超值套餐：1M国际带宽，年付3000元</span></p>
            <p><span>活动优惠价：400元/M月</span></p-->
            <p><span>恭喜您获得睿江"VPN账号"礼品一份</span></p>
            <table>
                <tr>
                    <th>名称</th>
                    <th>数量</th>
                    <th>说明</th>
                </tr>
                <tr>
                    <td>睿江云代金券账号</td>
                    <td>x 1</td>
                    <td>睿江云代金券账号</td>
                </tr>
                <tr>
                    <td>睿江云代金券密码</td>
                    <td>x 1</td>
                    <td>睿江云代金券账号密码</td>
                </tr>
            </table>
            <div class="btn">
                <a href="javascript:void(0);" onClick="next('item_2')">下一步</a>
            </div>
		</div>
        <div id="item_2">
        	<div style="width:730px; margin:0 auto;margin-top: 10px;">
                <h3>EFLY  NETWORK网络优化服务</h3>
                <h3>网络信息安全承诺书</h3>
                <p>EFLY NETWORK LIMITED：</p>
                <p>关于睿江科技的网络优化服务,本单位（个人）郑重承诺遵守本承诺书的有关条款，如有违反本承诺书有关条款的行为，由本单（个人）位承担由此带来的一切民事、行政和刑事责任。</p>
                <p>一、本单位（个人）承诺遵守《中华人民共和国计算机信息系统安全保护条例》和《计算机信息网络国际联网安全保护管理办法》及其他国家有关法律、法规和行政规章制度。</p>
                <p>二、本单位（个人）已知悉并承诺遵守《电信业务经营许可管理办法》、《互联网IP地址备案管理办法》、《非经营性互联网信息服务备案管理办法》、等国家相关部门有关文件的规定。</p>
                <p>三、本单位（个人）保证不通过使用睿江科技的网络优化服务设备或带宽危害国家安全、泄露国家秘密，不侵犯国家的、社会的、集体的利益和第三方的合法权益，不从事违法犯罪活动。</p>
                <p>四、本单位（个人）承诺不通过使用睿江科技的网络优化服务设备或带宽制作、复制、查阅和传播下列信息：</p>
                <p>1、反对宪法所确定的基本原则的；</p>
                <p>2、危害国家安全，泄露国家秘密，颠覆国家政权，破坏国家统一的；</p>
                <p>3、损害国家荣誉和利益的；</p>
                <p>4、煽动民族仇恨、民族歧视，破坏民族团结的；</p>
                <p>5、破坏国家宗教政策，宣扬邪教和封建迷信的；</p>
                <p>6、散布谣言，扰乱社会秩序，破坏社会稳定的；</p>
                <p>7、散布淫秽、色情、赌博、暴力、凶杀、恐怖或者教唆犯罪的；</p>
                <p>8、侮辱或者诽谤他人，侵害他人合法权益的；</p>
                <p>9、含有法律、行政法规禁止的其他内容的。</p>
                <p>五、本单位（个人）承诺不通过使用睿江科技的网络优化服务设备或带宽从事下列危害计算机信息网络安全的活动：</p>
                <p>1、未经允许，进入计算机信息网络或者使用计算机信息网络资源的；</p>
                <p>2、未经允许，对计算机信息网络功能进行删除、修改或者增加的；</p>
                <p>3、未经允许，对计算机信息网络中存储或者传输的数据和应用程序进行删除、修改或者增加的；</p>
                <p>4、故意制作、传播计算机病毒等破坏性程序的；</p>
                <p>5、其他危害计算机信息网络安全的。</p>
                <p>六、若违反本承诺书有关条款和国家相关法律法规的，本单位（个人）直接承担相应法律责任，造成财产损失的，由本单位（个人）直接赔偿。你单位有权停止服务。</p>
                <p>七、本承诺书自签署之日起生效。</p>
                <p>
                    <input type="checkbox" style="margin: 10px 8px 10px 300px; top: 2px; position: relative;" id="agreeBox" />
                    <label for="agreeBox" style="font-size: 14px;">我同意本协议</label>
                </p>
            </div>            
            <div class="btn_download">
                <a href="javascript:void(0);" onClick="downIt();">下载协议</a>
            </div>
            <div class="btn">
                <a href="javascript:void(0);" onClick="next('item_3')">下一步</a>
            </div>
        </div>
        <div id="item_3">        	
        	<div style="width: 530px;margin: 0 auto;margin-top: 90px;">
                <form action="" method="post">
                    <p>
                        <input type="hidden" id="ID"/>
                        <label>睿江云账户：</label>
                        <input id="mail" tabindex="1" />
                        <span>* 请输入睿江云账户</span>
                    </p>
                    <p style="margin-top:20px;">
                        <label>睿江云密码：</label>
                        <input id="pwd" type="password" tabindex="2"/>
                        <span>* 请输入睿江云密码</span>
                    </p>
                    <p style="text-align:right;margin-top:20px">充值成功？跳到 <a class="jump" href="http://ecloud.efly.cc" target="_blank">睿江云客户平台</a></p>
                    <p style="text-align:right;margin-top:20px">尚未注册？点击 <a class="jump" href="http://ecloud.efly.cc/index.php/Index/regist" target="_blank">马上注册</a></p>
             	</form>
           	</div>        
            <div class="btn">
                <a href="javascript:void(0);" onClick="next('ok')">完成</a>
            </div>
        </div>
    </div>
    
</body>
</html>
