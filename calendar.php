<?php
	// get the data from request
	$first_name = $_GET['first_name'];
	$last_name = $_GET['last_name'];
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>My Calendar</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script type="text/javascript"
		src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyA5AMUlNDROfbsLpzBP8Kc4lszf0fdw7YY">
	</script>
	<script>
		var map;
		var markers = [];
		var uniqueLocations = [];
		var keller = new google.maps.LatLng(44.9745476, -93.23223189999999);
		function initMap() {
			map = new google.maps.Map(document.getElementById('map'), {
				center: keller,
				zoom: 16
			});

			service = new google.maps.places.PlacesService(map);

			// Get addresses from calendar in HTML form
			var locations = document.getElementsByClassName('loc');
			
			// Deduplicate locations
			for (var i = 0; i < locations.length; i++) {
				if (uniqueLocations.indexOf(locations[i].innerHTML) == -1) {
					uniqueLocations.push(locations[i].innerHTML);
				}
			}

			for(var i = 0; i < uniqueLocations.length; i++) {
				mark(uniqueLocations[i]);
			}
		}

		google.maps.event.addDomListener(window, 'load', initMap);

		/* ------------------------------------------ Add markers -----------------------------------------------*/
		function mark(loc){
			var request = {
				location: keller,
				radius: '1000',
				query:loc
			};  
			service.textSearch(request, callback);
		}

		function callback(results, status) {
			if (status == google.maps.places.PlacesServiceStatus.OK) {
				/* just take top result */
				var place = results[0];
				createMarker(results[0]);
			}
      		}

		function createMarker(place) {
			var marker = new google.maps.Marker({
				map: map,
				position: place.geometry.location,
				title: place.name
			});

			var infowindow = new google.maps.InfoWindow({
				content: place.name
			});

			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map,marker);
			});

			markers.push(marker);
		}
		/* -------------------------------------------------------------------------------------------------------*/

		/* ----------------------------------- Search nearby restaurants -----------------------------------------*/
		// Sets the map on all markers in the array.
		function setMapOnAll(map) {
			for (var i = 0; i < markers.length; i++) {
				markers[i].setMap(map);
			}
		}

		// Removes the markers from the map, but keeps them in the array.
		function clearMarkers() {
			setMapOnAll(null);
		}

		function findRestaurants() {
			var r = document.forms['locForm']['radius'].value;
			
			if ((/^[0-9 ]+$/.test(r)) == false) {
				alert("Radius must be numeric");
				return false;
			}
			
			var infowindow = new google.maps.InfoWindow();
			var service = new google.maps.places.PlacesService(map);
			service.nearbySearch({
				location: keller,//{lat: 44.974, lng: -93.234},
				radius: r,
				type: ['restaurant']
			}, callback2);
			return false;
		}

		function callback2(results, status) {
			if (status === google.maps.places.PlacesServiceStatus.OK) {
				// Removes the markers from the map, but keeps them in the array.
        			clearMarkers();
				
        			for (var i = 0; i < uniqueLocations.length; i++) {
					mark(uniqueLocations[i]);
				}

                		for (var i = 0; i < results.length; i++) {
					createMarker(results[i]);
                		}
			}
		}
		/* --------------------------------------------------------------------------------------------------------*/
	</script>
</head>
<body>
	<h1>My Calendar</h1>
	<div class="yellow">
		<nav id="navmenu">
			<ul>
				<li><a href="calendar.php">My Calendar</a></li>
				<li><a href="form.php">Form Input</a></li>
			</ul>
		</nav>
	</div>
	<div id="calendar">
		<?php
		/**************************************************************
		 * Author: Chaoran Chen
		 * Purpose: This program read the JSON data from a file and 
		 * 			set the markers onto the map
		**************************************************************/
			if (file_exists("calendar.txt")) {
				echo "<table>";
				$myfile = fopen("calendar.txt", "r") or die("Unable to open file!");
				$events = file_get_contents("calendar.txt");
				$events = json_decode($events, true);
				fclose($myfile);

				if (!isset($events)) {
					exit();
				}

				// Sort if not already sorted
				// var_dump($events["Monday"]);
				// echo "<br>";
				function compare($e1, $e2) {
					return strcmp($e1['start_time'], $e2['start_time']);
				}
				usort($events[$_POST["day"]], "compare");

				$days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday");
				for ($i = 0; $i < 5; $i++) { 
					$day = $days[$i];
					if (isset($events[$day])) {
						echo "<tr><td>";
						echo $day;
						echo "</td>";
					}

					foreach ($events[$day] as $dayevent) {
						echo "<td><p>";
						echo $dayevent["start_time"];
						echo " - ";
						echo $dayevent["end_time"];
						echo "</p>";
						echo $dayevent["event_name"];
						echo " - <span class='loc'>";
						echo $dayevent["location"];
						echo "</span></td>";
						echo "";
					}
					echo "</tr>";
				}
				echo "</table>";
			} else {
				echo "<br><div style='color:red'>Calendar has no events, use form to create events</div><br>";
			}
			
		?>
	</div>
	<form name="locForm" onSubmit="return findRestaurants()">
		<input type="text" name="radius">
		<button name="loadMarks">Search nearby restaurants</button>
	</form>
	<br>
	<div id="map"></div>
	</body>
</html>
