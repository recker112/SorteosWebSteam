<?php
$url = file_get_contents("https://api.steampowered.com/IStoreService/GetAppList/v1/?key=4CFD20F49964B675B60A39FABAE3BED6&include_games=0&include_dlc=1&last_appid=2028850");
$json = json_decode($url, true);
$exit=0;
$max=-1;
$i=0;
require 'assets/php/connect_db.php';
while($exit==0){
	if ((isset($json['response']['apps'][$i])) && ($json['response']['apps'][$i]>$max)){
		$max=$json['response']['apps'][$i]['appid'];
		$consulta = $mysqli->prepare("INSERT INTO game_list 
			(appid, name) 
			VALUES
			(?,?)");
		$consulta->bind_param("is", $json['response']['apps'][$i]['appid'], $json['response']['apps'][$i]['name']);
		$consulta->execute();
		if ($consulta->affected_rows>=1) {
			$respuesta = array('status' => 'Añadido', 'appid' => $json['response']['apps'][$i]['appid']);
			print_r($respuesta);
			echo "</br>";
		}else {
			$respuesta = array('status' => 'Error al añadir', 'errorSQL' => $consulta->error);
			print_r($respuesta);
			echo "</br>";
		}
	}else {
		$exit=1;
	}
	$i++;
}
$mysqli->close();
print_r($max);
?>