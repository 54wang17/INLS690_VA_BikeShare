<?php
date_default_timezone_set('America/New_York');

class Station
{ 
  private $station_id;
  private $name;
  private $latitude;
  private $longitude;
  private $dpcapacity;
  private $dateCreated;


  public static function getAllStations() {
    $db_info = parse_ini_file("db.ini");
    $mysqli = new mysqli($db_info["server"], $db_info["username"],$db_info["password"],$db_info["dbname"]);
    // $sql_query = "select DISTINCT S.station_id,name,latitude,longitude,dpcapacity,dateCreated,inflow,outflow 
    //   from station AS S
    //   LEFT JOIN
    //     (SELECT station_id AS station_id, inflow, outflow FROM
    //       (SELECT to_station_id AS station_id,count(*) AS inflow from trip group by to_station_id) AS I
    //       LEFT JOIN
    //         (SELECT from_station_id,count(*) AS outflow from trip group by from_station_id) AS O
    //       ON I.station_id = O.from_station_id) AS IO
    //   ON S.station_id = IO.station_id;"
    $sql_query = "select DISTINCT S.station_id,name,latitude,longitude,dpcapacity,dateCreated,inflow,outflow 
      from 
        (SELECT station_id AS station_id, sum(inflow) as inflow FROM station_inflow
          group by station_id
        ) AS I
      RIGHT JOIN station AS S
        LEFT JOIN
        (SELECT station_id AS station_id, sum(outflow) as outflow FROM station_outflow
          group by station_id
        ) AS O
        ON S.station_id = O.station_id
      ON S.station_id = I.station_id
      ;";
    // echo($sql_query);
    $result = $mysqli->query($sql_query);
    $station_array = array();

    if ($result) {
      while ($next_row = $result->fetch_array()) {
        if ($next_row['dateCreated'] == null){
          $station_date = null;
        }else {
          $station_date = new DateTime($next_row['dateCreated']);
        }

        if ($next_row['inflow'] == null){
          $inflow = 0;
        }else {
          $inflow = intval($next_row['inflow']);
        }
        if ($next_row['outflow'] == null){
          $outflow = 0;
        }else {
          $outflow = intval($next_row['outflow']);
        }
        $station = array(
          'station_id' => $next_row['station_id'],
          'name' => $next_row['name'],
          'latitude' => $next_row['latitude'],
          'longitude' => $next_row['longitude'],
          'dpcapacity' => $next_row['dpcapacity'],
          'dateCreated' => $station_date,
          'inflow' => $inflow,
          'outflow' => $outflow);

        $station_array[] = $station;
      }
    }
    return $station_array;
  }

  public static function getNStations($n,$order,$weight,$date1,$date2) {
    if ($order == 'top'){
      $order_sql = 'DESC';
    } else if($order == 'bottom'){
      $order_sql = 'ASC';
    }else{
      return null;
    }

    if ($weight == 'in') {
      $weight_sql = 'inflow';
    }
    else if($weight == 'out'){
      $weight_sql = 'outflow';
    } 
    else if ($weight == 'all'){
      $weight_sql = '(inflow+outflow)';
    }else{
      return null;
    }
    
    if ($date1 == '' && $date2 == ''){
      $date_sql = '';
    } else{
      
      
      if ($date1 == ''){
        $date1 = '2013-01';
      }else if ($date2 == ''){
        $date2 = date('Y-m');
      }
      $date_sql = "where triptime >= '".$date1."' and triptime <= '".$date2."'";
    }
    $db_info = parse_ini_file("db.ini");
    $mysqli = new mysqli($db_info["server"], $db_info["username"],$db_info["password"],$db_info["dbname"]);

    // $sql_query = "select DISTINCT S.station_id,name,latitude,longitude,dpcapacity,dateCreated,inflow,outflow 
    //   from station AS S
    //   LEFT JOIN
    //     (SELECT station_id AS station_id, inflow, outflow FROM sum_station
    //       (SELECT to_station_id AS station_id,count(*) AS inflow from trip ".$date_sql." group by to_station_id ) AS I
    //       LEFT JOIN
    //         (SELECT from_station_id,count(*) AS outflow from trip ".$date_sql." group by from_station_id) AS O
    //       ON I.station_id = O.from_station_id) AS IO
    //   ON S.station_id = IO.station_id
    //   Order by ".$weight_sql." ".$order_sql." LIMIT ".$n.";";

    $sql_query = "select DISTINCT S.station_id,name,latitude,longitude,dpcapacity,dateCreated,inflow,outflow 
      from 
        (SELECT station_id AS station_id, sum(inflow) as inflow FROM station_inflow 
          ".$date_sql."
          GROUP BY station_id
        ) AS I
      RIGHT JOIN station AS S
        LEFT JOIN
        (SELECT station_id AS station_id, sum(outflow) as outflow FROM station_outflow 
          ".$date_sql."
          GROUP BY station_id
        ) AS O
        ON S.station_id = O.station_id
      ON S.station_id = I.station_id
      ORDER BY ".$weight_sql." ".$order_sql." LIMIT ".$n."
      ;";
    // echo($sql_query);
    $result = $mysqli->query($sql_query);
    $station_array = array();

    if ($result) {
      while ($next_row = $result->fetch_array()) {
        if ($next_row['dateCreated'] == null){
          $station_date = null;
        }else {
          $station_date = new DateTime($next_row['dateCreated']);
        }

        if ($next_row['inflow'] == null){
          $inflow = 0;
        }else {
          $inflow = intval($next_row['inflow']);
        }
        if ($next_row['outflow'] == null){
          $outflow = 0;
        }else {
          $outflow = intval($next_row['outflow']);
        }
        $station = array(
          'station_id' => $next_row['station_id'],
          'name' => $next_row['name'],
          'latitude' => $next_row['latitude'],
          'longitude' => $next_row['longitude'],
          'dpcapacity' => $next_row['dpcapacity'],
          'dateCreated' => $station_date,
          'inflow' => $inflow,
          'outflow' => $outflow);

        $station_array[] = $station;
      }
    }
    return $station_array;
  }

  private function __construct($station_id, $name, $latitude, $longitude, $dpcapacity, $dateCreated) {
      $this->station_id = $station_id;
      $this->name = $name; 
      $this->latitude = $latitude;
      $this->longitude = $longitude;
      $this->dpcapacity = $dpcapacity;
      $this->dateCreated = $dateCreated;
  }

  public function getJSON() {
    $json_obj = array('station_id' => $this->station_id,
		      'name' => $this->name,
		      'latitude' => $this->latitude,
		      'longitude' => $this->longitude,
		      'dpcapacity' => $this->dpcapacity,
          'dateCreated' => $this->dateCreated);
    return json_encode($json_obj);
  }
}
