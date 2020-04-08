var geocoder;
var map;
function initialize() {
  geocoder = new google.maps.Geocoder();
  var latlng = new google.maps.LatLng(47.397, 22.644);
  var mapOptions = {
    zoom: 11,
    center: latlng
  }
  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
}

function codeAddress() {
  var address = document.getElementById('addr').value;
  geocoder.geocode( { 'address': address}, function(results, status) {
	  
    if (status == google.maps.GeocoderStatus.OK) {
      map.setCenter(results[0].geometry.location);
      var marker = new google.maps.Marker({
          map: map,
          position: results[0].geometry.location
          
      });
      console.log(address);console.log(results[0]);
      //console.log(results);
      $('#inp_lat').val(results[0].geometry.location.lat());
      $('#inp_lon').val(results[0].geometry.location.lng());
      
    } else {
      alert('Geocode was not successful for the following reason: ' + status);
    }
  });
}

var ajId, mapini = 0;
function tkereso() {
	
	$('.gpsbox').show();
	if(mapini==0) {
		initialize();
		mapini = 1;
	}
	$('#addr').val($('#c1').val() + ' ' + $('#c2').val() + ' ' + $('#c3').val() + ' ' + $('#c4').val());
	codeAddress();
}
