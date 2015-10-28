<?php
date_default_timezone_set("America/New_York");

require_once("./orm/Station.php");
require_once("./orm/Trip.php");


//if(isset($_SERVER['PATH_INFO'])) {
	$path_components = explode('/', $_SERVER['PATH_INFO']);
//} 



if($_SERVER['REQUEST_METHOD'] == 'GET') {
	// query
	if (count($path_components) >= 2 && $path_components[1]!='') {
		if ($path_components[1] == 'station') {
			if (count($path_components) == 2){
				//url: ~/station
				$all_stations = Station::getAllStations();// return all station
				if ($all_stations == null) {
					// Stations info not found
					header("HTTP/1.1 404 Not Found");
					print("Data for all stations are null");
					exit();
				}
				
				header("Content-type: application/json");
				print(json_encode($all_stations));
				exit();
			}if (count($path_components) >= 4 && count($path_components) <= 7) {
				$date1 = '';
				$date2 = '';

				if (count($path_components) == 7){
					$date1 = $path_components[5];
					$date2 = $path_components[6];
				}
				//url:	~/station/top/N/in  or ~/station/top/N/out  or  ~/station/top/N [in & out]
				//url:	~/station/bottom/N/in or ~/station/bottom/N/out or  ~/station/bottom/N [in & out]
				if (($path_components[2] == 'top' || $path_components[2] == 'bottom')
					&& $path_components[3] !=''){
					$order = $path_components[2];					// top or bottom
					$num_of_station = intval($path_components[3]);	// N station
					//incoming or ourcoming or all
					if (count($path_components) == 4){
						$weight = 'all';
					}else{
						$weight = $path_components[4];
					} 					
					$all_stations = Station::getNStations($num_of_station,$order,$weight,$date1,$date2);
					if ($all_stations == null) {
						//Station info not found
						header("HTTP/1.1 404 Not Found");
						print("Data for ".$order." ".$weight." ".$num_of_station." stations are null;");
						exit();
					}

					header("Content-type: application/json");
					print(json_encode($all_stations));
					exit();
				}
			}
		}else if ($path_components[1] == 'trip') {
			if (count($path_components) == 2){
				//url:	~/trip 
				//Default return: top 50 trips without datetime limit
				$all_trips = Trip::getNTrips(50,'top','','');	// return all trips
				if ($all_trips == null) {
					// Trip info not found
					header("HTTP/1.1 404 Not Found");
					print("Data for all trips are null;");
					exit();
				}

				header("Content-type: application/json");
				print(json_encode($all_trips));
				exit();
			}elseif (count($path_components) > 3) {
				//url:	~/trip/top/N[/date1/date2] or ~/trip/bottom/N[/date1/date2]
				if (($path_components[2] == 'top' || $path_components[2] == 'bottom')
					&& $path_components[3] != ''){
					$date1 = '';
					$date2 = '';
					if (count($path_components) == 6){
						$date1 = $path_components[4];
						$date2 = $path_components[5];
					}
					$order = $path_components[2];					// top or bottom
					$num_of_trip = intval($path_components[3]);		// N trip
					$all_trips = Trip::getNTrips($num_of_trip,$order,$date1,$date2);
					if ($all_trips == null) {
						// Trip info not found
						header("HTTP/1.1 404 Not Found");
						print("Data for ".$order." ".$num_of_trip." trips are null;");
						exit();
					}

					header("Content-type: application/json");
					print(json_encode($all_trips));
					exit();

				}else if (($path_components[2] == 'from' || $path_components[2] == 'to') 
					&& $path_components[3]!=''){
					//url:	~/trip/from/sid[/date1/date2] or ~/trip/to/sid[/date1/date2]
					$dir = $path_components[2];
					$sid = intval($path_components[3]);
					$date1 = '';
					$date2 = '';
					if (count($path_components) == 6){
						$date1 = $path_components[4];
						$date2 = $path_components[5];
					}
					$all_trips_of_sid = Trip::getTripsBySid($dir,$sid,$date1,$date2);
					if ($all_trips_of_sid == null) {
						//trip from/to station with id 'sid' not found
						header("HTTP/1.1 404 Not Found");
						print("Trip data for station ".$sid." from date: ".$date1." to date :".$date2." are null");
						exit();
					}

					header("Content-type: application/json");
					print(json_encode($all_trips_of_sid));
					exit();
				}else{
					//url:	~/trip/date1/date2
					$date1 = $path_components[2];
					$date2 = $path_components[3];
					$all_trips_within_range = Trip::getTripsByDateRange($date1,$date2);
					if ($all_trips_within_range == null) {
						// trip from date 1 to date 2 not found
						header("HTTP/1.1 404 Not Found");
						print("Trip data from date: ".$date1." to date :".$date2." are null.");
						exit();
					}

					header("Content-type: application/json");
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