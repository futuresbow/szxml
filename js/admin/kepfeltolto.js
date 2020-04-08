function keptolto(o,id, szallasid) {
    len = o.files.length;
    var i = 0;
    for ( ; i < len; i++ ) {
      file = o.files[i];
     
      if (file.type.indexOf('image')==-1) {
        $('#response').html('Nem kép: '+file.type);
      } else {
        $('#response').html('Feltöltés...'); 
        
        if ( window.FileReader ) {
            reader = new FileReader();
            reader.onloadend = function (e) { 
            
            showUploadedItem(e.target.result);
        } 
        
            reader.readAsDataURL(file);
        } else alert('Képfeltöltési hiba, kérem használjon Crome vagy Firefox böngészőt!');
         if (window.FormData) {
            formdata = new FormData();
            
        } else alert('Képfeltöltési hiba, kérem használjon Crome vagy Firefox böngészőt!');
        if (formdata) {
            /*
            * Itt adjuk meg a feltöltés adatait, hogy tudjuk milyen mappa (ajánlat) és milyen képindex:
            */
            
            formdata.append("kepek[]", file);
            formdata.append("szallasid", szallasid);
            formdata.append("kepid", id);
            
        }
        if (formdata) {
            
            $.ajax({
            url: '/hkadmin/kepek_feltolto.php',
            type: "POST",
            data: formdata,
            processData: false,
            contentType: false,
            success: function (res) {
                // EZ FUT LE HA MEGY A FELTÖLTÉS
                $('span[data-kepid="'+id+'"]').css('display', 'inline');
				$('img[data-kepid="'+id+'"]').css('display', 'inline').attr('src', res);
	
                //debug
                //$('#keptoltoForm').html(res);
                
                
                $('.kepDiv').fadeOut();
            }
        });
}
      }
    }
}
$().ready(function(){
	
	kepek = $('img[src=""]') ;
	for(i = 0; i < kepek.length;i++) {
		$('span[data-kepid="'+$(kepek[i]).attr('data-kepid')+'"]').css('display', 'none');
		$('img[data-kepid="'+$(kepek[i]).attr('data-kepid')+'"]').css('display', 'none');
	}
	
	
});
function showUploadedItem (source) {
	
  $('.kepDiv').html('Feltöltés<br><img src="'+source+'" />').fadeIn();
  
}
function keptorles(szallasid,kepid ) {
	
	$.post('/hkadmin/kepek_torles.php', {'szallasid': szallasid, 'kepid':kepid}, function(res){
		$('span[data-kepid="'+kepid+'"]').css('display', 'none');
		$('img[data-kepid="'+kepid+'"]').css('display', 'none');
	
	});
	
}
