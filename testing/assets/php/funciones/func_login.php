<?php
function verify_register($mysqli, $steamid){
	$consulta = $mysqli->prepare("SELECT steamid
		FROM users_steam
		WHERE steamid=?");
	$consulta->bind_param("s", $steamid);
	$consulta->execute();

	$resultado = $consulta->get_result();

	if ($resultado->num_rows > 0) {
		return true;
	}else {
		return false;
	}
}

function register_user_steam($mysqli, $steamid, $name, $profileurl, $avatar, $avatar_full, $time_created, $time_update, $all_games){
	$consulta = $mysqli->prepare("INSERT INTO users_steam
		(steamid, name, profileurl, avatar, avatar_full, time_created, time_update, all_games)
		VALUES
		(?,?,?,?,?,?,?,?,?)");
	if (!$consulta) {
		return $consulta;
	}
	$consulta->bind_param("sssssiii", $steamid, $name, $profileurl, $avatar, $avatar_full, $time_created, $time_update, $all_games);
	$consulta->execute();

	if ($consulta->affected_rows > 0) {
		return true;
	}else {
		return $consulta;
	}
}

function update_user_steam($mysqli, $steamid, $name, $profileurl, $avatar, $avatar_full, $time_update, $all_games){
	$consulta = $mysqli->prepare("UPDATE users_steam SET steamid=?, name=?, profileurl=?, avatar=?, avatar_full=?, time_update=?, all_games=?
	WHERE
	steamid=?");
	if (!$consulta) {
		return false;
	}
	$consulta->bind_param("sssssiis", $steamid, $name, $profileurl, $avatar, $avatar_full, $time_update, $all_games, $steamid);
	$consulta->execute();
	if ($consulta->affected_rows > 0) {
		return true;
	}else {
		return false;
	}
}
?>