<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>My Form</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
	<h1>Calendar Input</h1>
	
	<nav id="navmenu">
		<ul>
			<li><a href="calendar.php">My Calendar</a></li>
			<li><a href="form.php">Form Input</a></li>
		</ul>
	</nav>

	<!-- PHP Form Validation -->
	<?php
		class Event
		{
			public $event_name = "";
			public $start_time = "";
			public $end_time = "";
			public $location = "";
		}

		// Error mesaages
		$event_name_err = $start_time_err = $end_time_err = $location_err = "";

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if (!empty($_POST)) {
				if (isset($_POST['Clear'])) {
					unlink("calendar.txt");
					header("Location: ./calendar.php");
				} else {
					// Do all error checking using regex---------------------------------------------------------------
					if (empty($_POST["eventname"])) {
						$event_name_err = "Please provide a value for Event Name.";
						echo '<span style="color:#FF0000;">' . $event_name_err . "<br></span>";
					}
					if (empty($_POST["starttime"]) ||
						// Case sensative matching
						!preg_match("/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/s", $_POST["starttime"], $match)) {
						$start_time_err = "Please select a valid value for Start Time. The format is HH:MM.";
						echo '<span style="color:#FF0000;">' . $start_time_err . "<br></span>";
					}
					if (empty($_POST["endtime"]) ||
						// Case sensative matching
						!preg_match("/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/s", $_POST["endtime"], $match)) {
						$end_time_err = "Please select a valid value for End Time. The format is HH:MM.";
						echo '<span style="color:#FF0000;">' . $end_time_err . "<br></span>";
					}
					if (empty($_POST["location"])) {
						$location_err = "Please provide a value for Location.";
						echo '<span style="color:#FF0000;">' . $location_err . "<br></span>";
					}
					// Do all error checking using regex---------------------------------------------------------------

					// No error
					if ($event_name_err == "" && $start_time_err == "" && $end_time_err == "" && $location_err == "") {

						$events = file_get_contents("calendar.txt");
						
						$events = json_decode($events, true);

						if (!isset($events)) {
							$events = array();
						}

						if (!isset($events[$_POST["day"]])) {
							$events[$_POST["day"]] = array();
						}

						$new_event = new Event;
						$new_event->event_name = $_POST["eventname"];
						$new_event->start_time = $_POST["starttime"];
						$new_event->end_time = $_POST["endtime"];
						$new_event->location = $_POST["location"];

						$events[$_POST["day"]][] = $new_event;

						// Sort the events using usort()

						$events = json_encode($events);
						// $myfile = fopen("calendar.txt", "r+") or die("Unable to open file!");
						$myfile = fopen("calendar.txt", "w+") or die("Unable to open file!");
						fwrite($myfile, $events);
						fclose($myfile);
						// file_put_contents("calendar.txt", $events);
						// header("Location: calendar.php");
					}
				}
			}
		}
		
	?>

	<!-- HTML Form Display -->
	<div>
		<form method="post">
			<table class="center">
				<tr>
					<td>Event Name:</td>
					<td><input type="text" name="eventname"></td>
				</tr>

				<tr>
					<td>Start Time:</td>
					<td><input type="time" name="starttime"></td>
				</tr>

				<tr>
					<td>End Time:</td>
					<td><input type="time" name="endtime"></td>
				</tr>

				<tr>
					<td>Location:</td>
					<td><input type="text" name="location"></td>
				</tr>

				<tr>
					<td>Day of the week:</td>
					<td>
						<select name="day">
							<option value="Monday">Mon</option>
							<option value="Tuesday">Tue</option>
							<option value="Wednesday">Wed</option>
							<option value="Thursday">Thu</option>
							<option value="Friday">Fri</option>
						</select>
					</td>
				</tr>

				<tr>
					<td><input type="submit" name="Clear" value="Clear"></td>
					<td><input type="submit" name="Submit" value="Submit"></td>
				</tr>
			</table>

		</form>
	</div>
</body>
</html>
