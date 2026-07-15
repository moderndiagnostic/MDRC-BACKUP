<?php
class _demo extends controller
{
	function init()
	{
		
	}

	function onload()
	{

        $originLat = 28.7041;
        $originLng = 77.1025; // Delhi
        $destLat = 27.1767;
        $destLng = 78.0081;   // Agra
        
        $apiKey = 'AIzaSyCMdIvv5Ajq0gtKqb7G3Yf-wHqsXZkq2rI';
        
        $directionsUrl = "https://maps.googleapis.com/maps/api/directions/json?origin=$originLat,$originLng&destination=$destLat,$destLng&key=$apiKey";
        
        $response = file_get_contents($directionsUrl);
        $data = json_decode($response, true);
        
        // Extract distance in KM
        if (!empty($data['routes'][0]['legs'][0]['distance']['text'])) {
            $distanceText = $data['routes'][0]['legs'][0]['distance']['text'];
            echo "Distance between the two points: $distanceText";
        } else {
            echo "Could not fetch distance.";
        }
        exit;
    }
}
?>