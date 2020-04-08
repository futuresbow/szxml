function hosszabbit(id, o) {
	tr = $(o).parent().parent();
	$(tr).find('td').addClass('torlesFolyamatban');
	$.post('/admin/ajanlathosszabbit', {'aid' : id}, function(){
		$(tr).remove();
	});
}
