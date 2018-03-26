<!DOCTYPE html>
<html>
<?php
	$name=$_POST["name"];
	$add=$_POST["add"];
	$mn=$_POST["med_name"];
	$q=$_POST["quantity"];
?>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" ;/>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false&key=AIzaSyD0X4v7eqMFcWCR-VZAJwEMfb47id9IZao">
    </script>
    <script type="text/javascript">
	var map;
	$(document).ready(function () {
			
			//draw a map centered at ISM Dhanbad
		    var latlng = new google.maps.LatLng(40.748492,-73.985496);
	        var myOptions = {
	            zoom: 15,
	            center: latlng,
	            mapTypeId: google.maps.MapTypeId.ROADMAP
	        };
	        map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	
			$("#btnSearch").click(function(){
				//Convert Address Into LatLng and Retrieve Address Near by
				convertAddressToLatLng($("#txtAddress").val());
			});
	});				  
      
	function convertAddressToLatLng(address){
	 	var geocoder = new google.maps.Geocoder();
		
		geocoder.geocode({ 'address': address }, function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				//Empty div before re-populating
				$("#divStores").html('');
			    searchStores(results[0].geometry.location);
			} else {
			 	$("#divStores").html(getEmbedHTML('No Stores Found','',''));
			}
		});
	}
	
	function searchStores(location){
		var latlng = new google.maps.LatLng(location.lat(),location.lng());
	    var myOptions = {
	    	zoom: 15,
	        center: latlng,
	        mapTypeId: google.maps.MapTypeId.ROADMAP
	    };
	    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);	
		
		//Marker at the address typed in
		var image = 'images/townhouse.png'
        var marker = new google.maps.Marker({
                position: latlng,
                map: map,
				icon: image
        });		
		var q=document.getElementById("qty").value;
		var mname=document.getElementById("medname").value;
		//hard coded the radius to 10 miles, if you get the value from a field if required
		var parameters = 'lat='+ location.lat() + '&lng=' + location.lng() + '&medname=' + mname + '&qty=' + q;  
		//window.alert(parameters);
	
		$.ajax({
            type: "POST",
            dataType: "json",
            url: "store_locator.php",
            data: parameters,
            success: function(msg){
							displayStores(msg);
            shop_},
            error: function(xhr,ajaxOptions,thrownError){
                alert(thrownError);
            }
    });
	}
	
	function displayStores(result){
		if (result.length > 0){
			for (i=0;i<result.length;i++){
				//Append Store Address on Sidebar
				var html = getEmbedHTML(i+1,result[i].shop_name,result[i].address,result[i].distance);
				$("#divStores").append(html);
				//place a marker
				var image = 'images/number_' + parseInt(i+1) + '.png';
				var latlng = new google.maps.LatLng(result[i].lat,result[i].lng);
				var marker = new google.maps.Marker({
					position: latlng,
					map: map,
					icon: image
				});	
				
				var msg = 'Location : ' + result[i].shop_name + '<br/> ';
				msg = msg + 'Address : ' + result[i].address + '<br/> ';
            	attachMessage(marker, msg);
			}
		} else {
			$("#divStores").html(getEmbedHTML('No Stores Found','',''));
		}
	}
	
	function attachMessage(marker, msg) {
		var infowindow = new google.maps.InfoWindow({
			content: msg,
			size: new google.maps.Size(120, 150)
		});
		google.maps.event.addListener(marker, 'click', function () {
			infowindow.open(map, marker);
		});
	}
	
	function getEmbedHTML(seqno,name,address,distance) {
		var	strhtml = '<div class="row">';
		strhtml  =  strhtml + '<img src="images/number_' + seqno + '.png" border="0" width="24" height="24" style="padding-right:10px;" /><label>' + name + '</label><br/>'
		strhtml  =  strhtml + '<span>' + address + '<span><br/>'
		strhtml  =  strhtml + '<span> Distance : ' + parseFloat(distance).toFixed(2) + ' miles<span><br/>'
		strhtml  =  strhtml + '</div><div class="separator"></div>';
		
		return strhtml;
	}
    </script>
  </head>
  <body>
    <div id="container" class="shadow">
        <div id="map_canvas"></div>
        <div id="sidebar">
            <div class="row" style="background:#E3EDFA">
								
								<div class="form-group">
										<label for="address"> Address Entered</label>
										<input type="text" id="txtAddress" class="form-control" value="<?php if (isset($add)) echo $add ?>" />
								</div>
								<span style="padding-left: 100px;">Sample :IIT(ISM) Dhanbad</span>
								<br><br>
							  <div class="form-group">		
										<label>Medicine Name:</label>
										<input type="text" id="medname" class="text" value="<?php if (isset($mn)) echo $mn ?>"/><br>
								</div>
									<br>
								<div class="form-group">	
										<label>Quantity:</label>
										<input type="number" id="qty" class="text" value="<?php if (isset($q)) echo $q ?>"/><br>
								</div>
									<br>
								<div>
										<label>Are your entries Correct?</label><br>
										<button id="btnSearch" border="0" width="24" height="24" style="vertical-align:middle;">
									YES
								</button>
								</div>	<br>
							<div>
										<button id="btnSearch" border="0" width="24" height="24" style="vertical-align:middle;">
											<a  href="../index1.php">Home Page</a>
								</button>
								</div>	
							
            </div>
            <div class="separator"></div>
			<div id="divStores">
			<!--
			<div class="row">
				<label>Store 1<label><br/>
				<span> Address Here<span>
            </div>
            <div class="separator"></div>
			-->			
			</div>

        </div>
    </div>
  </body>
</html>