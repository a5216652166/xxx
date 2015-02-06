<!-- VPN盒子 -->
<!DOCTYPE HTML>
<?php 

	session_start(); 
	
	if(empty($_SESSION['code'])){
		echo '<script type="text/javascript">window.location.href = "./index.html";</script>';
	}
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" >
<title></title>

<link rel="stylesheet" href="./css/gift.css?200" />
<style type="text/css">
	
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

				//layer.msg('需同意本协议',2,5);

				/*$.layer({
					title: false,
					time: 2,
					dialog: {
					    type: 5,
					    msg: '需同意本协议'
					},
					end: function(index){
						var $div = $("#item_2");
						$div.scrollTop($div[0].scrollHeight);
					}
				});*/

				return;
			}
			$("#regist-process").attr('class','step3');
		}else if(str=='ok'){

			var ReceiverName = $("#ReceiverName").val(),
				CompanyName = $("#CompanyName").val(),
				ReceiverMail = $("#ReceiverMail").val(),
				ReceiverPhone = $("#ReceiverPhone").val(),
				ReceiverAdd = $("#ReceiverAdd").val(),
				SelProvince = $("#SelProvince").val(),
				SelCity = $("#SelCity").val(),
				SelArea = $("#SelArea").val(),Add_str = "";
			if(!ReceiverName){
				$("#ReceiverName").focus();
				return ;
			}
			if(!CompanyName){
				$("#CompanyName").focus();
				return ;
			}
			if(!ReceiverPhone){
				$("#ReceiverPhone").focus();
				return ;
			}
			if(!isPhone(ReceiverPhone)){
				$("#ReceiverPhone").focus();
				layer.msgClose('手机格式输入有误',2,5);
				return ;
			}
			if(!ReceiverMail){
				$("#ReceiverMail").focus();
				return ;
			}
			if(!isEmail(ReceiverMail)){
				$("#ReceiverMail").focus();
				layer.msgClose('邮箱格式输入有误',2,5);
				return ;
			}
			if(SelProvince == "请选择" || SelCity == "请选择"){
				$("#SelProvince").focus();
				layer.msgClose('请选择省份城市',2,5);
				return ;
			}
			if(!ReceiverAdd){
				$("#ReceiverAdd").focus();
				return ;
			}
			if(SelProvince != "请选择" && SelCity != "请选择"){
				if(SelProvince == SelCity){
					Add_str += SelProvince ;
				}else{
					Add_str += SelProvince + SelCity ;
				}
			}
			if(SelArea != "请选择"){
				Add_str += SelArea;
			}
			//通过验证
			$.ajax({
				url:'receive.php?opt=insert',
				type:'post',
				data:{
						'ID':$("#ID").val(),
						'ReceiverName':ReceiverName,
						'CompanyName':CompanyName,
						'ReceiverPhone':ReceiverPhone,
						'ReceiverMail':ReceiverMail,
						'ReceiverAdd':Add_str + ReceiverAdd
				},
				dataType: "json", 
				success:function(data){
					if(data.info=="success"){						
						/*layer.msgClose('礼品领取完毕，耐心等待快递。',2,1);
						setInterval(function(){window.parent.location.reload();},2000);*/

						showConfirmReceiveInfo();//再次确认收货信息
					}else{
						layer.msgClose(data.data,3,5);
						setInterval(function(){window.location.reload();},2000);
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
		$.ajax({
			url: "./xml/area.xml",
			dataType: "xml",
			success: function (xml) {
				$(xml).find("province").each(function () {                                                  //找到(province)省份节点;
					$("<option></option>").html($(this).attr("name")).appendTo("#SelProvince");             //加载(province)省份信息到列表中
				})
			}
		});
		//省份列表信息更改时，加载城市列表信息
		$("#SelProvince").change(function () {
		var value = $("#SelProvince").val();                                                            //省份值;
		if (value != "请选择") {
			$("#SelCity").css("display", "inline-block").find("option").remove();                              //显示城市下拉列表框删除城市下拉列表中的数据;
			$("#SelCity").html("<option>请选择</option>");                                              //加载城市列表中的请选择;
			$("#SelArea").find("option").remove();                                                      //删除地区下拉列表中的数据;
			$("#SelArea").html("<option>请选择</option>")                                               //加载地区列表中的请选择;
			$.ajax({
				url: "./xml/area.xml",
				dataType: "xml",
				success: function (xml) {
					$(xml).find("[name='" + value + "']").find("city").each(function () {               //根据省份name属性得到子节点City节点name属性;
						$("<option></option>").html($(this).attr("name")).appendTo("#SelCity");         //加载City(城市)信息到下拉列表中;
					})
				}
			})
		}
		});
		//城市列表信息改变时，加载地区列表信息
		$("#SelCity").change(function () {
			var value = $("#SelCity").val();                                                                //城市值;
			if (value != "请选择") {
				$("#SelArea").css("display", "inline-block").find("option").remove();                              //显示地区下拉列表框删除地区下拉列表中的数据;
				$("#SelArea").html("<option>请选择</option>");                                              //加载地区列表中的请选择;
				$.ajax({
					url: "./xml/area.xml",
					dataType: "xml",
					success: function (xml) {
						$(xml).find("[name='" + value + "']").find("country").each(function () {            //根据城市节点name得到子节点Area节点name属性;
							$("<option></option>").html($(this).attr("name")).appendTo("#SelArea");         //加载到Area(地区)下拉列表中;
						})
					}
				})
			}
		});
		
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

    function showConfirmReceiveInfo(){
    	$.ajax({
			url: "./xml/area.xml",
			async: false,
			dataType: "xml",
			success: function (xml) {
				$(xml).find("province").each(function () {                                                  //找到(province)省份节点;
					$("<option></option>").html($(this).attr("name")).appendTo("#SelProvince2");             //加载(province)省份信息到列表中
				});

				//初始化省
				$("#SelProvince2").val($("#SelProvince").val());

				//初始化市
				var value = $("#SelProvince2").val();                                                            //省份值;
				if (value != "请选择") {
					$("#SelCity2").css("display", "inline-block").find("option").remove();                              //显示城市下拉列表框删除城市下拉列表中的数据;
					$("#SelCity2").html("<option>请选择</option>");                                              //加载城市列表中的请选择;
					$("#SelArea2").find("option").remove();                                                      //删除地区下拉列表中的数据;
					$("#SelArea2").html("<option>请选择</option>")                                               //加载地区列表中的请选择;

					$(xml).find("[name='" + value + "']").find("city").each(function () {               //根据省份name属性得到子节点City节点name属性;
						$("<option></option>").html($(this).attr("name")).appendTo("#SelCity2");         //加载City(城市)信息到下拉列表中;
					});

					$("#SelCity2").val($("#SelCity").val());
				}

				//初始化区
				var value = $("#SelCity2").val();                                                                //城市值;
				if (value != "请选择") {
					$("#SelArea2").css("display", "inline-block").find("option").remove();                              //显示地区下拉列表框删除地区下拉列表中的数据;
					$("#SelArea2").html("<option>请选择</option>");                                              //加载地区列表中的请选择;
					
					$(xml).find("[name='" + value + "']").find("country").each(function () {            //根据城市节点name得到子节点Area节点name属性;
						$("<option></option>").html($(this).attr("name")).appendTo("#SelArea2");         //加载到Area(地区)下拉列表中;
					});

					$("#SelArea2").val($("#SelArea").val());
				}
			}
		});

		//省份列表信息更改时，加载城市列表信息
		$("#SelProvince2").change(function () {
			var value = $("#SelProvince2").val();                                                            //省份值;
			if (value != "请选择") {
				$("#SelCity2").css("display", "inline-block").find("option").remove();                              //显示城市下拉列表框删除城市下拉列表中的数据;
				$("#SelCity2").html("<option>请选择</option>");                                              //加载城市列表中的请选择;
				$("#SelArea2").find("option").remove();                                                      //删除地区下拉列表中的数据;
				$("#SelArea2").html("<option>请选择</option>")                                               //加载地区列表中的请选择;
				$.ajax({
					url: "./xml/area.xml",
					dataType: "xml",
					success: function (xml) {
						$(xml).find("[name='" + value + "']").find("city").each(function () {               //根据省份name属性得到子节点City节点name属性;
							$("<option></option>").html($(this).attr("name")).appendTo("#SelCity2");         //加载City(城市)信息到下拉列表中;
						})
					}
				})
			}
		});

		//城市列表信息改变时，加载地区列表信息
		$("#SelCity2").change(function () {
			var value = $("#SelCity2").val();                                                                //城市值;
			if (value != "请选择") {
				$("#SelArea2").css("display", "inline-block").find("option").remove();                              //显示地区下拉列表框删除地区下拉列表中的数据;
				$("#SelArea2").html("<option>请选择</option>");                                              //加载地区列表中的请选择;
				$.ajax({
					url: "./xml/area.xml",
					dataType: "xml",
					success: function (xml) {
						$(xml).find("[name='" + value + "']").find("country").each(function () {            //根据城市节点name得到子节点Area节点name属性;
							$("<option></option>").html($(this).attr("name")).appendTo("#SelArea2");         //加载到Area(地区)下拉列表中;
						})
					}
				})
			}
		});

		$("#ReceiverName2").val($("#ReceiverName").val());
    	$("#ReceiverPhone2").val($("#ReceiverPhone").val());
    	$("#ReceiverAdd2").val($("#ReceiverAdd").val());

    	$.layer({
		    type: 1,
		    //shade: [0],
		    shade: [0.8, '#000'],
		    area: ['auto', 'auto'],
		    offset:['100px', ''],
		    title: false,
		    page: {
		    	dom : '#item_4'
		    },
		    close: function(index){
		    	goToHelp();
			}
		});
    }


    function goToHelp(){
    	var dlgHtml = '<span class="xubox_msg xulayer_png32 xubox_msgico1 xubox_msgtype1" style="top:19px;"></span><p style="margin-left:37px;">确认成功，礼品领取完毕。<br/>依照指引设置好盒子之后，您就可以开始使用了。</p>';

    	layer.msgClose(dlgHtml,2,-1, function(){
			//window.top.location = 'http://192.168.85.166/vpn/help.html?num=0';
    		window.top.location = 'http://www.efly.cc/EflyVPN/help.html?num=0';
		});
    }

    function confirmReceiveInfo(){
    	var ReceiverName = $("#ReceiverName2").val(),
				ReceiverPhone = $("#ReceiverPhone2").val(),
				ReceiverAdd = $("#ReceiverAdd2").val(),
				SelProvince = $("#SelProvince2").val(),
				SelCity = $("#SelCity2").val(),
				SelArea = $("#SelArea2").val(),Add_str = "";
		if(!ReceiverName){
			$("#ReceiverName2").focus();
			return ;
		}
		if(!ReceiverPhone){
			$("#ReceiverPhone2").focus();
			return ;
		}
		if(!isPhone(ReceiverPhone)){
			$("#ReceiverPhone2").focus();
			layer.msgClose('手机格式输入有误',2,5);
			return ;
		}
		if(SelProvince == "请选择" || SelCity == "请选择"){
			$("#SelProvince2").focus();
			layer.msgClose('请选择省份城市',2,5);
			return ;
		}
		if(!ReceiverAdd){
			$("#ReceiverAdd2").focus();
			return ;
		}
		if(SelProvince != "请选择" && SelCity != "请选择"){
			if(SelProvince == SelCity){
				Add_str += SelProvince ;
			}else{
				Add_str += SelProvince + SelCity ;
			}
		}
		if(SelArea != "请选择"){
			Add_str += SelArea;
		}


		var ReceiverNameS = $("#ReceiverName").val() === ReceiverName,
				ReceiverPhoneS = $("#ReceiverPhone").val() === ReceiverPhone,
				ReceiverAddS = $("#ReceiverAdd").val() === ReceiverAdd,
				SelProvinceS = $("#SelProvince").val() === SelProvince,
				SelCityS = $("#SelCity").val() === SelCity,
				SelAreaS = $("#SelArea").val() === SelArea;

		//检查是否变更, 无变更直接结束
		if(ReceiverNameS && ReceiverPhoneS && ReceiverAddS && SelProvinceS && SelCityS && SelAreaS){
			goToHelp();
			
			return;
		}

		//通过验证
		$.ajax({
			url:'receive.php?opt=confirmReceiveInfo',
			type:'post',
			data:{
				'ID':$("#ID").val(),
				'ReceiverName':ReceiverName,
				'ReceiverPhone':ReceiverPhone,
				'ReceiverAdd':Add_str + ReceiverAdd
			},
			dataType: "json", 
			success:function(data){
				if(data.info=="success"){				
					goToHelp();
				}else{
					layer.msgClose(data.data,3,5);
				}
			},
			error:function(data){				
				layer.msgClose(data.statusText,3,5);
			}
		});
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
            <p><span>VPN盒子：活动价：免费使用一年</span></p>
            <p><span>标准价：600元/M/月；超值套餐：1M国际带宽，年付3000元</span></p>
            <p><span>活动优惠价：300元/M月</span></p>
            <p><span>恭喜您获得睿江"国际互联盒子"礼品一份</span></p>
            <table>
                <tr>
                    <th>名称</th>
                    <th>数量</th>
                    <th>说明</th>
                </tr>
                <tr>
                    <td>睿江国际互联盒子</td>
                    <td>x 1</td>
                    <td>国际互联盒子</td>
                </tr>
                <tr>
                    <td>电源适配器</td>
                    <td>x 1</td>
                    <td>国际互联盒子的电源适配器</td>
                </tr>
                <tr>
                    <td>使用说明书</td>
                    <td>x 1</td>
                    <td>国际互联盒子使用说明书</td>
                </tr>
                <tr>
                    <td>新年贺卡</td>
                    <td>x 1</td>
                    <td>祝您新年快乐</td>
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
                	<input type="checkbox" style="margin: 10px 8px 0 300px;" id="agreeBox" />
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
        	<div style="width: 530px;margin: 0 auto;margin-top: 20px;">
                <form action="" method="post">
                    <p>
                        <input type="hidden" id="ID"/>
                        <label>用户姓名：</label>
                        <input id="ReceiverName" tabindex="1" />
                        <span>* 请输入用户姓名</span>
                    </p>
                    <p>
                        <label>单位名称：</label>
                        <input id="CompanyName" tabindex="2"/>
                        <span>* 请输入单位名称</span>
                    </p>
                    <p>
                        <label>手机号码：</label>
                        <input id="ReceiverPhone" tabindex="3"/>
                        <span>* 请输入手机号码</span>
                    </p>
                    <p>
                        <label>邮箱地址：</label>
                        <input id="ReceiverMail" tabindex="4"/>
                        <span>* 请输入邮箱地址</span>
                    </p>
                    <p>
                        <label>用户地址：</label>
                        <select id="SelProvince" tabindex="5">
                            <option>请选择</option>
                        </select>
                        <select id="SelCity" tabindex="6">
                            <option>请选择</option>
                        </select>
                        <select id="SelArea" tabindex="7">
                            <option>请选择</option>
                        </select>
                    </p>
                    <p>
                        <input style="margin-left:110px;"  id="ReceiverAdd" tabindex="8"/>
                        <span>* 请输入详细地址</span>
                    </p>
             	</form>
           	</div>
            <div class="btn">
                <a href="javascript:void(0);" onClick="next('ok')">完成</a>
            </div>
        </div>


	    <div id="item_4">
	    	<p style="margin: 15px 0 30px;">
	    		<span class="xubox_msg xulayer_png32 xubox_msgico xubox_msgtype1" style="top:30px;"></span>
	    		<div style="margin: -10px 0 30px 115px;">
	    			礼品领取成功，请最后确认您的个人信息，以便激活VPN服务
		    		<br/>
		    		（用户个人信息仅用于用户账户资料设置）
	    		</div>
	    	</p>
	    	<div style="width: 530px;margin: 0 auto;">
	            <form action="" method="post">
	                <p>
	                    <input type="hidden" id="ID"/>
	                    <label>用户姓名：</label>
	                    <input id="ReceiverName2" tabindex="1" />
	                    <span>* 请输入用户姓名</span>
	                </p>
	                <p>
	                    <label>手机号码：</label>
	                    <input id="ReceiverPhone2" tabindex="3"/>
	                    <span>* 请输入手机号码</span>
	                </p>
	                <p>
	                    <label>用户地址：</label>
	                    <select id="SelProvince2" tabindex="5">
	                        <option>请选择</option>
	                    </select>
	                    <select id="SelCity2" tabindex="6">
	                        <option>请选择</option>
	                    </select>
	                    <select id="SelArea2" tabindex="7">
	                        <option>请选择</option>
	                    </select>
	                </p>
	                <p>
	                    <input style="margin-left:110px;"  id="ReceiverAdd2" tabindex="8"/>
	                    <span>* 请输入详细地址</span>
	                </p>
	         	</form>
	       	</div>
	        <div class="btn">
	            <a href="javascript:void(0);" onClick="confirmReceiveInfo();">确认</a>
	        </div>
	    </div>

    </div>
    
</body>
</html>
