       
function dropdownmenu(obj, e, menucontents, menuwidth){
 
	if (window.event) event.cancelBubble=true;
	else if (e.stopPropagation) e.stopPropagation();
 
	var menudiv = $('#dropmenudiv');
 
	if (menudiv.is(':visible')) { clearhidemenu(); menudiv.fadeOut('fast'); return false; }
 
	menudiv.remove();
 
	$('body').append('<div id="dropmenudiv" style="display:none;position:absolute;z-index:100;width:165px;"></div>');
 
	menudiv = $('#dropmenudiv');
 
	menudiv.html(menucontents.join(""));
 
	if (menuwidth) menudiv.width(menuwidth);
 
	var windowx = $(document).width() - 30;
	var offset = $(obj).offset();
 
	if (windowx-offset.left < menudiv.width())
			offset.left = offset.left - (menudiv.width()-$(obj).width());
 
	menudiv.css( {
		left : offset.left + "px",
		top : offset.top+$(obj).height()+"px"
	});
 
	menudiv.fadeTo('fast', 0.9);
 
	menudiv.mouseenter(function(){
	      clearhidemenu();
	    }).mouseleave(function(){
	      delayhidemenu();
	});
 
	$(document).one("click", function() {
		hidemenu();
	});
 
	return false;
};
 
function hidemenu(e){
	$("#dropmenudiv").fadeOut("fast");
};
 
function delayhidemenu(){
	delayhide=setTimeout("hidemenu()",1000);
};
 
function clearhidemenu(){
 
	if (typeof delayhide!="undefined")
		clearTimeout(delayhide);
};
