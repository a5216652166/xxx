$(function(){
	
	$("#kinMaxShow").kinMaxShow({
	
		height:350,
		button:{
			showIndex:false,
			normal:{background:'url('+ROOT+'/Public/images/btn2.png) no-repeat 0 0',marginRight:'8px',border:'0',top:'10px',right:'70px'},
			focus:{background:'url('+ROOT+'/Public/images/btn2.png) no-repeat -14px 0',border:'0'}
		},
	
		callback:function(index,action){
			switch(index){
				case 0 :
				if(action=='fadeIn'){
					$(this).find('.sub_1_1').animate({left:'-40px'},600)
					$(this).find('.sub_1_2').animate({top:'100px'},600)
					$(this).find('.sub_1_3').animate({top:'223px'},700)
					
				}else{
					$(this).find('.sub_1_1').animate({left:'-150px'},600)
					$(this).find('.sub_1_2').animate({top:'40px'},600)
					$(this).find('.sub_1_3').animate({top:'320px'},600)
					
				};
				break;
						
				case 1 :
				if(action=='fadeIn'){
					$(this).find('.sub_2_1').animate({left:'-40px'},600)
					$(this).find('.sub_2_2').animate({top:'100px'},600)
					$(this).find('.sub_2_3').animate({top:'223px'},700)
					
				}else{
					$(this).find('.sub_2_1').animate({left:'-150px'},600)
					$(this).find('.sub_2_2').animate({top:'40px'},600)
					$(this).find('.sub_2_3').animate({top:'320px'},600)
				};
				break;
						
				case 2 :
				if(action=='fadeIn'){
					$(this).find('.sub_3_1').animate({left:'-40px'},600)
					$(this).find('.sub_3_2').animate({top:'100px'},600)
					$(this).find('.sub_3_3').animate({top:'223px'},700)
					
				}else{
					$(this).find('.sub_3_1').animate({left:'-150px'},600)
					$(this).find('.sub_3_2').animate({top:'40px'},600)
					$(this).find('.sub_3_3').animate({top:'320px'},600)
				};
				break;
													
			}
		}
	
	});
});

function pageInit(){
		var uls = document.getElementsByTagName("ul");
		var operUL;
		for(var i = 0; i < uls.length; i++){
			var curUL = uls[i];
			if(curUL.className == "kinMaxShow_button"){
				operUL = curUL;
				curUL.parentNode.removeChild(curUL);
				break;
			}
		}
		if(operUL){
			var cdiv = document.createElement("div");
			cdiv.className = "slidebtm";
			
			var $ul = $('<ul class="b_t">' +
            	'<li class="newstitle">news</li>' +
                '<li class="slidearrow"></li>' +
                '<li class="slidenews"><a href="#">7月2日0点企业云邮箱升级公告 (2014-06-24)</a></li>' +
            '</ul>');
			
			cdiv.appendChild($ul[0]);
			
			cdiv.appendChild(operUL);
			document.getElementById("kinMaxShow").appendChild(cdiv);
			
		}
	}



