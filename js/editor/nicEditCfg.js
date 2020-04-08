bkLib.onDomLoaded(function() {
    if(document.getElementById('levelszoveg')!=null){
	new nicEditor({iconsPath : '/img/admin/nicEditorIcons.gif',fullPanel : true}).panelInstance('levelszoveg'); 
    }
    if(document.getElementById('extraleiras')!=null){
	new nicEditor({iconsPath : '/img/admin/nicEditorIcons.gif',fullPanel : true}).panelInstance('extraleiras'); 
    }
    if(document.getElementById('bovebbinfo')!=null){
	//new nicEditor({iconsPath : '/img/admin/nicEditorIcons.gif', buttonList : ['bold','italic','underline','left','right','justify','superscript','ol','ul','forecolor']}).panelInstance('bovebbinfo');
	new nicEditor({iconsPath : '/img/admin/nicEditorIcons.gif',fullPanel : true}).panelInstance('bovebbinfo'); 
    }
    if(document.getElementById('tovabbiinfo')!=null){
	//new nicEditor({iconsPath : '/img/admin/nicEditorIcons.gif', buttonList : ['bold','italic','underline','left','right','justify','superscript','ol','ul','forecolor']}).panelInstance('tovabbiinfo');
	new nicEditor({iconsPath : '/img/admin/nicEditorIcons.gif', fullPanel : true}).panelInstance('tovabbiinfo'); 
	//new nicEditor({iconsPath : '/img/admin/nicEditorIcons.gif', buttonList : ['bold','italic','underline','left','right','justify','superscript','ol','ul','forecolor']}).panelInstance('leiras');
	new nicEditor({iconsPath : '/img/admin/nicEditorIcons.gif', fullPanel : true}).panelInstance('leiras'); 
    }
});

