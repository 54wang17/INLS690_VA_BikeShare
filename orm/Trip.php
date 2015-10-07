<?php
date_default_timezone_set('America/New_York');

class Trip
{ 
  private $to_station_id;
  private $from_station_id;
  private $to_latitude;
  private $to_longitude;
  private $from_latitude;
  private $from_longitude;
  private $flow;


  public static function getAllTrips() {
    $mysqli = new mysqli("localhost", "root", "1234", "divvybikes");
    $result = $mysqli->query("select from_station_id, longitude As from_longitude, latitude AS from_latitude,
                                      to_station_id, to_longitude, to_latitude, flow, trip_id
                              from station AS S2
                              RIGHT JOIN
                                (select to_station_id, longitude As to_longitude, latitude AS to_latitude, from_station_id, flow, trip_id
                                from station AS S1
                                RIGHT JOIN
                                  (select trip_id, from_station_id, to_station_id, count(*) AS flow from trip group by from_station_id,to_station_id) AS IO
                                ON IO.to_station_id = S1.station_id) AS TMP
                              ON TMP.from_station_id = S2.station_id;");
    $trip_array = array();

    if ($result) {
      while ($next_row = $result->fetch_array()) {
        
        $trip = array(
          'to_station_id' => $next_row['to_station_id'],
          'from_station_id' => $next_row['from_station_id'],
          'to_latitude' => $next_row['to_latitude'],
          'to_longitude' => $next_row['to_longitude'],
          'from_latitude' => $next_row['from_latitude'],
          'from_longitude' => $next_row['from_longitude'],
          'flow' => $next_row['flow'],
          'trip_id' => $next_row['trip_id']
          );

        $trip_array[] = $trip;
      }
    }
    return $trip_array;
  }

  public static function getOutTripsByID($from_sid) {
    $mysqli = new mysqli("localhost", "root", "1234", "divvybikes");
    $result = $mysqli->query("select from_station_id, longitude As from_longitude, latitude AS from_latitude,
                                      to_station_id, to_longitude, to_latitude, flow, trip_id
                              from station AS S2
                              RIGHT JOIN
                                (select trip_id,to_station_id, longitude As to_longitude, latitude AS to_latitude, from_station_id, flow
                                from station AS S1
                                RIGHT JOIN
                                  (select trip_id, from_station_id, to_station_id, count(*) AS flow from trip 
                                    where from_station_id = ".$from_sid."
                                    group by to_station_id) AS IO
                                ON IO.to_station_id = S1.station_id) AS TMP
                              ON TMP.from_station_id = S2.station_id;");
    $trip_array = array();

    if ($result) {
      while ($next_row = $result->fetch_array()) {
        
        $trip = array(
          'to_station_id' => $next_row['to_station_id'],
          'from_station_id' => $next_row['from_station_id'],
          'to_latitude' => $next_row['to_latitude'],
          'to_longitude' => $next_row['to_longitude'],
          'from_latitude' => $next_row['from_latitude'],
          'from_longitude' => $next_row['from_longitude'],
          'flow' => $next_row['flow'],
          'trip_id' => $next_row['trip_id']
          );

        $trip_array[] = $trip;
      }
    }
    return $trip_array;
  }

    public static function getInTripsByID($to_sid) {
    $mysqli = new mysqli("localhost", "root", "1234", "divvybikes");
    $result = $mysqli->query("select from_station_id, longitude As from_longitude, latitude AS from_latitude,
                                      to_station_id, to_longitude, to_latitude, flow, trip_id
                              from station AS S2
                              RIGHT JOIN
                                (select to_station_id, longitude As to_longitude, latitude AS to_latitude, from_station_id, flow, trip_id
                                from station AS S1
                                RIGHT JOIN
                                  (select trip_id, from_station_id, to_station_id, count(*) AS flow from trip 
                                    where to_station_id = ".$to_sid."
                                    group by from_station_id) AS IO
                                ON IO.to_station_id = S1.station_id) AS TMP
                              ON TMP.from_station_id = S2.station_id;");
    $trip_array = array();

    if ($result) {
      while ($next_row = $result->fetch_array()) {
        
        $trip = array(
          'to_station_id' => $next_row['to_station_id'],
          'from_station_id' => $next_row['from_station_id'],
          'to_latitude' => $next_row['to_latitude'],
          'to_longitude' => $next_row['to_longitude'],
          'from_latitude' => $next_row['from_latitude'],
          'from_longitude' => $next_row['from_longitude'],
          'flow' => $next_row['flow'],
          'trip_id' => $next_row['trip_id']

          );

        $trip_array[] = $trip;
      }
    }
    return $trip_array;
  }

    public static function getTripsByDateRange($date1,$date2) {
    $mysqli = new mysqli("localhost", "root", "1234", "divvybikes");
    // $dstr1 = "'".$date1->format('Y-m-d')."'"
    // Correct date format 2015-01-01
    $result = $mysqli->query("select from_station_id, longitude As from_longitude, latitude AS from_latitude,
                                      to_station_id, to_longitude, to_latitude, flow, trip_id
                              from station AS S2
                              RIGHT JOIN
                                (select to_station_id, longitude As to_longitude, latitude AS to_latitude, from_station_id, flow, trip_id
                                from station AS S1
                                RIGHT JOIN
                                  (select trip_id, from_station_id, to_station_id, count(*) AS flow from trip
                                    where starttime >= '".$date1."' and starttime <= '".$date2."' 
                                    group by from_station_id,to_station_id) AS IO
                                ON IO.to_station_id = S1.station_id) AS TMP
                              ON TMP.from_station_id = S2.station_id;");
    $trip_array = array();

    if ($result) {
      while ($next_row = $result->fetch_array()) {
        
        $trip = array(
          'to_station_id' => $next_row['to_station_id'],
          'from_station_id' => $next_row['from_station_id'],
          'to_latitude' => $next_row['to_latitude'],
          'to_longitude' => $next_row['to_longitude'],
          'from_latitude' => $next_row['from_latitude'],
          'from_longitude' => $next_row['from_longitude'],
          'flow' => $next_row['flow'],
          'trip_id' => $next_row['trip_id']
          );

        $trip_array[] = $trip;
      }
    }
    return $trip_array;
  }

  private function __construct($to_station_id, $from_station_id, $to_latitude, $to_longitude, $from_latitude, $from_longitude, $flow) {
      $this->to_station_id = $station_id;
      $this->from_station_id = $from_station_id; 
      $this->to_latitude = $to_latitude;
      $this->to_longitude = $to_longitude;
      $this->from_latitude = $from_latitude;
      $this->from_longitude = $from_longitude;
      $this->flow = $flow;
  }

  public function getJSON() {
    $json_obj = array('to_station_id' => $this->to_station_id,
          'from_station_id' => $this->from_station_id,
		      'to_latitude' => $this->to_latitude,
		      'to_longitude' => $this->to_longitude,
          'from_latitude' => $this->from_latitude,
          'from_longitude' => $this->from_longitude,
		      'flow' => $this->flow);
    return json_encode($json_obj);
  }
}
