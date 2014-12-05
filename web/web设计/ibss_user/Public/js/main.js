// JavaScript Document
// Name:Javie Chan
// Create:2014-07-11
// Upgate:2014-07-22
// Project:Ecloud

$(document).ready(function() {
    $('.h_btm a.onenav').hover(function(){
		/*alert(1);*/
		$('.secondnav').hide();
		$('.h_btm a.onenav').removeClass("actived");
		$(this).addClass('actived')
		$(this).next('div').show();	
	},function(){
		if($(this).next().is(":visible")){
			$(this).addClass('actived');
		}else{
			$(this).removeClass('actived');
		}
	});
	
	$('.secondnav').live('mouseleave', function(){
		$(this).hide();
		$(this).prev().removeClass("actived");
	});
	
	$("div.pright").click(function(){
		var $ul = $(this).siblings("ul");
		var left = $ul.position().left;
		if(Math.floor($ul.children().size() / 4) * -1200 == left){
			return;
		}
		
		var $div = $(this).clone();
		$div.appendTo($("div.p_wrapper"));
		
		$(this).siblings("ul").animate({"left": left - 1200 + "px"}, "normal", function(){
			$("div.pright:last").remove();
		});
	});
	
	$("div.pleft").click(function(){
		var $ul = $(this).siblings("ul");
		var left = $ul.position().left;
		if(left == 0){
			return;
		}

		var $div = $(this).clone();
		$div.appendTo($("div.p_wrapper"));
		
		$(this).siblings("ul").animate({"left": left + 1200 + "px"}, "normal", function(){
			$("div.pleft:last").remove();
		});
	});
	
	$(window).scroll(function(){
		var t = $(window).scrollTop();
		if($('.cartbox').is(":animated")){
			$('.cartbox').stop();
		}
		if(t <= 300){
			$('.cartbox').animate({'top' : 176}, "normal");
		}else{
			$('.cartbox').animate({'top' : t-70}, "normal");
		}
	});
	
	$(".tree li").find("a").each(function(){
		var url = $("#purl").val();
		if($(this).attr("href").indexOf(url) > 0){
			$(this).addClass("actived");
		}else{
			$(this).removeClass("actived");
		}
	});
	
});