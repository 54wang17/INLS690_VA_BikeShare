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
    $mysqli = new mysqli("localhost", "root", "1234", "divvybikes");
    $result = $mysqli->query("select DISTINCT S.station_id,name,latitude,longitude,dpcapacity,dateCreated,inflow,outflow 
      from station AS S
      LEFT JOIN
        (SELECT station_id AS station_id, inflow, outflow FROM
          (SELECT to_station_id AS station_id,count(*) AS inflow from trip group by to_station_id) AS I
          LEFT JOIN
            (SELECT from_station_id,count(*) AS outflow from trip group by from_station_id) AS O
            ON I.station_id = O.from_station_id) AS IO
      ON S.station_id = IO.station_id;");
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
