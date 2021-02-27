<?php
$json_content = trim(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?language=th&latlng=18.728378,98.959331&sensor=true&key=AIzaSyDR8HOgNdg-TmLRquiQxHZ8Pa4XEhJZdJ0"));
echo $json_content;
$array_geocode = (array)json_decode($json_content);
$array_results = (array)($array_geocode[results][0]);
$formatted_address = $array_results[formatted_address];
echo $formatted_address;
?>