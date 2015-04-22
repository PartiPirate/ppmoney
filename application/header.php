<?php /*
	Copyright 2015 Cédric Levieux, Parti Pirate

	This file is part of PPMoney.

    PPMoney is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    PPMoney is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with PPMoney.  If not, see <http://www.gnu.org/licenses/>.
*/
session_start();
$language = "fr";

include_once("language/language.php");
//require_once("engine/utils/SessionUtils.php");

$page = $_SERVER["SCRIPT_NAME"];
if (strrpos($page, "/") !== false) {
	$page = substr($page, strrpos($page, "/") + 1);
}
$page = str_replace(".php", "", $page);

?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php

if ($page == "join") {
	echo lang("pp_join_title");
}
else if ($page == "donate") {
	echo lang("pp_donate_title");
}
else if (isset($title)) {
	echo $title;
}
else {
	echo lang("pp_title");
}

?></title>

<!-- Bootstrap -->
<link href="assets/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/fonts.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">

<link rel="shortcut icon" type="image/png" href="favicon.png" />

<?php
include_once("config/config.php");

if (isset($config["server"]["line"]) && $config["server"]["line"]) { ?>
<style>
body:before {
	content: "<?php echo $config["server"]["line"]; ?>";
	color: red;
	font-size: 30px;
	margin-bottom: -33px;
}
</style>
<?php
}?>

</head>