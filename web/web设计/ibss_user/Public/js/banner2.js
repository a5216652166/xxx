$(function(){
	
	$("#kinMaxShow").kinMaxShow({
	
		height:350,
		button:{
			showIndex:false,
			normal:{background:'url(../images/btn.png) no-repeat -14px 0',marginRight:'8px',border:'0',bottom:'53px',right:'0px'},
			focus:{background:'url(../images/btn.png) no-repeat 0 0',border:'0'}
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
	
	$("ul.kinMaxShow_button").appendTo("div.slidebtm");
	
	$("ul.kinMaxShow_button li").click(function(){
		if(this.className == "focus"){
			return;
		}
		
		var index = $("ul.kinMaxShow_button li").index(this);
		
		var $newUL = $('<ul class="kinMaxShow_button"><li class=""> </li><li class=""> </li><li class=""> </li></ul>');
		console.log($newUL.html());
		$newUL.children("li").eq(index).addClass("focus");
		$newUL.appendTo("div.slidebtm");
		
		$("ul.kinMaxShow_button:eq(0)").remove();
	});

});



