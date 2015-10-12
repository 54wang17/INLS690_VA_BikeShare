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


  public static function getNTrips($n,$order,$date1,$date2) {
    if ($order == 'top'){
      $order_sql = 'DESC';
    } else if($order == 'bottom'){
      $order_sql = 'ASC';
    }else{
      return null;
    }
    if ($date1 == '' && $date2 == ''){
      $date_sql = '';
    } else{
      if ($date1 == ''){
        $date1 = '2013-01-01';
      }else{
        $date2 = date('Y-m-d');
      }
      $date_sql = "where starttime >= '".$date1."' and starttime <= '".$date2."'";
    }
    $db_info = parse_ini_file("db.ini");
    $mysqli = new mysqli($db_info["server"], $db_info["username"],$db_info["password"],$db_info["dbname"]);

    $sql_query = "select from_station_id, longitude As from_longitude, latitude AS from_latitude,
                                      to_station_id, to_longitude, to_latitude, flow, trip_id
                              from station AS S2
                              RIGHT JOIN
                                (select to_station_id, longitude As to_longitude, latitude AS to_latitude, from_station_id, flow, trip_id
                                from station AS S1
                                RIGHT JOIN
                                  (select trip_id, from_station_id, to_station_id, count(*) AS flow from trip"
                                  .$date_sql.
                                    " group by from_station_id,to_station_id
                                    ORDER BY flow ".$order_sql." LIMIT ".$n." ) AS IO
                                ON IO.to_station_id = S1.station_id) AS TMP
                              ON TMP.from_station_id = S2.station_id;";
    // echo($sql_query);
    $result = $mysqli->query($sql_query);
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

  public static function getTripsBySid($dir,$sid,$date1,$date2) {
    if ($dir == 'from'){
      // return outgoing trip of station sid 
      $group_sql = 'to';
    }else if ($dir == 'to'){
      // return incoming trip of station sid 
      $group_sql = 'from';
    }
    if ($date1 == '' && $date2 == ''){
      $date_sql = '';
    } else{
      if ($date1 == ''){
        $date1 = '2013-01-01';
      }else{
        $date2 = date('Y-m-d');
      }
      $date_sql = " and starttime >= '".$date1."' and starttime <= '".$date2."'";
    }
    // Default get top 5 station
    $order_sql = 'DESC';
    $n = 5;
    $db_info = parse_ini_file("db.ini");
    $mysqli = new mysqli($db_info["server"], $db_info["username"],$db_info["password"],$db_info["dbname"]);
    $sql_query = "select from_station_id, longitude As from_longitude, latitude AS from_latitude,
                                      to_station_id, to_longitude, to_latitude, flow, trip_id
                              from station AS S2
                              RIGHT JOIN
                                (select trip_id,to_station_id, longitude As to_longitude, latitude AS to_latitude, from_station_id, flow
                                from station AS S1
                                RIGHT JOIN
                                  (select trip_id, from_station_id, to_station_id, count(*) AS flow from trip 
                                    where ".$dir."_station_id = ".$sid
                                    .$date_sql.
                                    " group by ".$group_sql."_station_id
                                    ORDER BY flow ".$order_sql." LIMIT ".$n." ) AS IO
                                ON IO.to_station_id = S1.station_id) AS TMP
                              ON TMP.from_station_id = S2.station_id;";
    // echo($sql_query);
    $result = $mysqli->query($sql_query);
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
    $db_info = parse_ini_file("db.ini");
    $mysqli = new mysqli($db_info["server"], $db_info["username"],$db_info["password"],$db_info["dbname"]);
    // Check date format
    if ($date1 == ''){
      $date1 = '2013-01-01';
    }else{
      $date2 = date('Y-m-d');
    }
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
