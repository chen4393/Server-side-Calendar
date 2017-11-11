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
						echo "</td>";
					}
					echo "</tr>";
				}
				echo "</table>";
			} else {
				echo "<div>Calendar has no events, use form to create events</div>";
			}
			
		?>
	</div>
	<div id="map"></div>
</body>
</html>
