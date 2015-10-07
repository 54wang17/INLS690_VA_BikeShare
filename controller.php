<?php
date_default_timezone_set("America/New_York");

require_once("./orm/Station.php");
require_once("./orm/Trip.php");


//if(isset($_SERVER['PATH_INFO'])) {
	$path_components = explode('/', $_SERVER['PATH_INFO']);
//} 



if($_SERVER['REQUEST_METHOD'] == 'GET') {
	// query or deletion
	if (count($path_components) >= 2 && $path_components[1]!='') {
		
		if ($path_components[1] == 'station') {
			header("Content-type: application/json");
			$all_stations = Station::getAllStations();
				if ($all_stations == null) {
					// restaurants not found
					header("HTTP/1.1 404 Not Found");
					print("Restaurants are null");
					exit();
				}

				print(json_encode($all_stations));
				exit();
		}else if ($path_components[1] == 'trip') {
			if (count($path_components) == 2){
				header("Content-type: application/json");
				$all_trips = Trip::getAllTrips();// return all trips
				if ($all_trips == null) {
					// Trip info not found
					header("HTTP/1.1 404 Not Found");
					print("Data for all trips are null");
					exit();
				}

				print(json_encode($all_trips));
				exit();
			}elseif (count($path_components) > 3) {
				if ($path_components[2] == 'from' and $path_components[3]!=''){
					$from_sid = intval($path_components[3]);
					$all_trips_from_sid = Trip::getOutTripsByID($from_sid);
					if ($all_trips_from_sid == null) {
					//trip from station with id 'sid' not found
					header("HTTP/1.1 404 Not Found");
					print("Data for ".$from_sid." are null");
					exit();
					}
					print(json_encode($all_trips_from_sid));
					exit();
				}elseif ($path_components[2] == 'to' and $path_components[3]!=''){
					$to_sid = intval($path_components[3]);
					$all_trips_to_sid = Trip::getInTripsByID($to_sid);
					if ($all_trips_to_sid == null) {
					// trip to station with id 'sid' not found
					header("HTTP/1.1 404 Not Found");
					print("Data for ".$to_sid." are null");
					exit();
					}
					print(json_encode($all_trips_to_sid));
					exit();
				}else{
					$date1 = $path_components[2];
					$date2 = $path_components[3];
					$all_trips_within_range = Trip::getTripsByDateRange($date1,$date2);
					if ($all_trips_within_range == null) {
					// trip from date 1 to date 2 not found
					header("HTTP/1.1 404 Not Found");
					print("Data from ".$date1."to ".$date2." are null.");
					exit();
					}
					print(json_encode($all_trips_within_range));
					exit();
				}
					
			}
				}
			

		}

}
		

// If here, none of the above applied and URL could
// not be interpreted with respect to RESTful conventions.
header("HTTP/1.1 400 Bad Request!");
print '<h1 class="title">Page Not Found<h1>';
print '<h4>We\'re sorry but the page you requested is not available on the website.&nbsp;</h4>';
// print '<h4>Please Click <span><a href="'.$base_url.'">Here</a></span> to return to Homepage.</h4>';



?>