<?php
require 'steamauth/steamauth.php';

$steamok="No registrado";

if(!isset($_SESSION['steamid'])) {

    loginbutton("rectangle"); //login button

}  else {

    include ('steamauth/userInfo.php'); //To access the $steamprofile array
    //Protected content

    logoutbutton(); //Logout Button
}     
echo $steamok;
?>