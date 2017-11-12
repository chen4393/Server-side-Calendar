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
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCOumeDq14JIAdEH5QtfvxlPEeu3v0LxEY&libraries=places">
	</script>
	<script>
		var map;
		var keller = new google.maps.LatLng(44.9745476,-93.23223189999999);
		function initMap() {
			map = new google.maps.Map(document.getElementById('map'), {
				center: keller,
				zoom: 14
			});

			service = new google.maps.places.PlacesService(map);

			// Get addresses from calendar in HTML form
			var locations = document.getElementsByClassName('loc');
			// console.log(locations.length);
			for (var i = 0; i < locations.length; i++) {
				// console.log(locations[i].innerHTML);
				mark(locations[i].innerHTML);
			}
		}

		google.maps.event.addDomListener(window, 'load', initMap);

		function mark(loc){
			var request = {
				location: keller,
				radius: '1000',
				query:loc
			};  
			service.textSearch(request, callback);
		}

		function callback(results, status) {
			if(status == google.maps.places.PlacesServiceStatus.OK) {
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
		}
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
							// echo "e1: ";
							// var_dump($e1);
							// echo "<br>";
							// echo "e2: ";
							// var_dump($e2);
							// echo "<br>";
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
	<form id="loc_from">
		<input type="text" id="location_box">
		<button id="load_marks">Search</button>
	</form>
	<br>
	<div id="map"></div>
	</body>
</html>
