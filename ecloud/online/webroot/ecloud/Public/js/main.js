$(document).ready(function(){
	/*$('.t_left li a').click(function(){
		$('.t_left li a').removeClass('actived');
		$(this).addClass('actived');
	});*/
	
	$('.t_nav li a').click(function(){
		$('.t_nav li a').removeClass('actived');
		$(this).addClass('actived');
	});
	
	$('.t_help').click(function(){
		$('.as_admin').hide();
		$('.as_notice').hide();
		$('.as_help').toggle();
	});
	$('.t_notice').click(function(){
		$('.as_admin').hide();
		$('.as_help').hide();
		$('.as_notice').toggle();
	});
	$('.t_more').click(function(){
		$('.as_help').hide();
		$('.as_notice').hide();
		$('.as_admin').toggle();
	});
	
	$('.as_admin li:last').css({
		"border-top":"1px solid #ccc", 
		"text-align":"right", 
		"padding-right":0
	});
	
	$('.menu').click(function(){
		$(this).toggleClass('on');
		$(this).siblings('div').toggle("slow");
		$(this).siblings('dl').toggle("slow");
	});
	
	$('.tree dt').click(function(){
		$('.tree dt').removeClass('actived');
		$(this).addClass('actived');
	});
	
	$('.content li:first').css("border-left","1px solid #ccc");
	
	$('.info table tr').find("td:eq(0)").addClass('in_td');
	
	$('.in_table tr').find("td:eq(0)").css("line-height","27px");
	
	$('.content table tr:even').css("background","#f6f9fe");
	$('.kz .content table tr').css("background","#fff");
	
	$('.content table tr').hover(function(){
		$('.content table tr:even').css("background","#f6f9fe");
		$('.content table tr:odd').css("background","#fff");
		$(this).css("background","#d7f5ff");
	});
	
	$('.kz .content table tr').hover(function(){
		$('.content table tr').css("background","#fff");
		$(this).css("background","#d7f5ff");
	});
	
	$('.as_admin').mouseleave(function(){ $(this).hide();});
	$('.as_help').mouseleave(function(){ $(this).hide();});
	$('.as_notice').mouseleave(function(){ $(this).hide();});
})
