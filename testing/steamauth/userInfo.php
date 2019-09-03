<?php
if (empty($_SESSION['steam_uptodate']) or empty($_SESSION['steam_personaname'])) {
	require 'SteamConfig.php';
	//Url para datos de steam
	$url = file_get_contents("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v1/?key=".$steamauth['apikey']."&steamids=".$_SESSION['steamid']);
	//Url para datos de los juegos en steam
	$url2 = file_get_contents("https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/?key=".$steamauth['apikey']."&steamid=".$_SESSION['steamid']);
	if ($url && $url2){
		//URL 1
		$content = json_decode($url, true);
		$_SESSION['steam_steamid'] = $content['response']['players']['player'][0]['steamid'];
		$_SESSION['steam_personaname'] = $content['response']['players']['player'][0]['personaname'];
		$_SESSION['steam_profileurl'] = $content['response']['players']['player'][0]['profileurl'];
		$_SESSION['steam_avatar'] = $content['response']['players']['player'][0]['avatar'];
		$_SESSION['steam_avatarfull'] = $content['response']['players']['player'][0]['avatarfull'];
		$_SESSION['steam_timecreated'] = $content['response']['players']['player'][0]['timecreated'];
		$_SESSION['steam_uptodate'] = time();

		//URL 2
		$content2 = json_decode($url2, true);

		if (!isset($content2['response']['game_count']) || !isset($content2['response']['games'][0])) {
			$_SESSION['steam_total_games'] = 0;
			$_SESSION['steam_games'] = 0;
		}else {
			$_SESSION['steam_total_games'] = $content2['response']['game_count'];
			$i=0;
			while(isset($content2['response']['games'][$i]['appid'])){
				$_SESSION['steam_games'][$i] = $content2['response']['games'][$i]['appid'];
				$i++;
			}
		}

		$steamlogin = array('status' => 'ok');
	}else {
		$steamlogin = array('status' => 'error', 'descrip' => 'Error en la peticion de datos hacia la api de steam');
	}


}

//Verificar no errores al tomar datos del api
if(isset($steamlogin['status']) && $steamlogin['status'] == 'ok'){
	$steamprofile['steamid'] = $_SESSION['steam_steamid'];
	$steamprofile['name'] = $_SESSION['steam_personaname'];
	$steamprofile['profileurl'] = $_SESSION['steam_profileurl'];
	$steamprofile['avatar'] = $_SESSION['steam_avatar'];
	$steamprofile['avatarfull'] = $_SESSION['steam_avatarfull'];
	$steamprofile['timecreated'] = $_SESSION['steam_timecreated'];
	$steamprofile['uptodate'] = $_SESSION['steam_uptodate'];
	$steamprofile['total_games'] = $_SESSION['steam_total_games'];
	$i=0;
	while (isset($_SESSION['steam_games'][$i])) {
		$steamprofile['games'][$i] = $_SESSION['steam_games'][$i];
		$i++;
	}

	//Comienzo del registro
	//Archivos necesarios
	require 'assets/php/funciones/func_login.php';
	require 'assets/php/connect_db.php';

	//Algoritmos
	if (verify_register($mysqli, $steamprofile['steamid'])) {
		$update = update_user_steam($mysqli, $steamprofile['steamid'], $steamprofile['name'], $steamprofile['profileurl'], $steamprofile['avatar'], $steamprofile['avatarfull'], $steamprofile['uptodate'], $steamprofile['total_games']);

		if ($update) {
			$steamok="update_OK";
		}else {
			$steamok="error_update";
		}
	} else {
		$registro = register_user_steam($mysqli, $steamprofile['steamid'], $steamprofile['name'], $steamprofile['profileurl'], $steamprofile['avatar'], $steamprofile['avatarfull'], $steamprofile['timecreated'], $steamprofile['uptodate'], $steamprofile['total_games']);

		if ($registro) {
			$steamok="register_OK";
		}else {
			$steamok="error_register";
		}
	}
}else if (isset($steamlogin['status']) && $steamlogin['status'] == "error"){
	$steamok="error_get_data";
}else {
	//Al recargar la página
	$steamprofile['steamid'] = $_SESSION['steam_steamid'];
	$steamprofile['name'] = $_SESSION['steam_personaname'];
	$steamprofile['profileurl'] = $_SESSION['steam_profileurl'];
	$steamprofile['avatar'] = $_SESSION['steam_avatar'];
	$steamprofile['avatarfull'] = $_SESSION['steam_avatarfull'];
	$steamprofile['timecreated'] = $_SESSION['steam_timecreated'];
	$steamprofile['uptodate'] = $_SESSION['steam_uptodate'];
	$steamprofile['total_games'] = $_SESSION['steam_total_games'];
	$i=0;
	while (isset($_SESSION['steam_games'][$i])) {
		$steamprofile['games'][$i] = $_SESSION['steam_games'][$i];
		$i++;
	}
	$steamok="steam_login";
}

// Version 3.2
?>
    
