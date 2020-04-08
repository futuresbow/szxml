window.onload = function() {
	urlAtiras();
}
/**
 * urlAtiras
 * meta description ékezettelenítése, url formába hozása
 * @param forceformupdate mindenképpen elvégzi az átírást és beillesztést
 */
function urlAtiras(forceformupdate) {
    
    urlinp 	= document.getElementById('aurl');
    val 	= urlinp.value;
    if(val=='' || forceformupdate == true) {
	ajanlatid 	= document.getElementById('ajanlatid').value;
	forras 		= document.getElementById('metainfo').value;
	
	$.post('/admin/ajanlaturl', {'str':forras, 'ajanlatid':ajanlatid},function(e){
		urlinp.value 	= e;
	});
    }
}
