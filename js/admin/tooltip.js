$(document).ready(function(){
	$('.tooltip').each(function() {
		$(this).qtip({
			content: {
			text: function(event, api) {
				$.ajax({
					url: '/admin/foglalastooltip',
					data: {
						id: api.elements.target.attr('rel-foglalasid')
					},
					type: "POST"
				})
				.then(function(content) {
					// Set the tooltip content upon successful retrieval
					api.set('content.text', content);
				}, function(xhr, status, error) {
				// Upon failure... set the tooltip content to error
				api.set('content.text', status + ': ' + error);
				});
				return 'Loading...'; // Set some initial text
			},
				title: 'vevő adatai',
				button: 'Close'
			},
			show: {
				event: 'click',
				solo: true
			},

			hide: {
				//hide: false
				//event: 'click'
				event: false
			},
			position: {
				viewport: $(window)
			},
			style: 'qtip-dark'
		});
	});
});