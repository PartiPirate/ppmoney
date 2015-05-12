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
$relativeDirectory = "../";
set_include_path(get_include_path() . PATH_SEPARATOR . $relativeDirectory);

session_start();

include_once("config/database.php");
require_once("engine/bo/GaugeBo.php");
require_once("engine/bo/ProjectBo.php");
require_once("engine/bo/TransactionBo.php");

$connection = openConnection();

include_once("header.php");

?>
<body>

<nav><a href="https://www.partipirate.fr/" target="_blank"><img src="../assets/img/logo_pp.png" /></a></nav>

<main class="clearfix">
<h1><?php echo lang("pp_back_title") ?></h1>

<?php if (isset($_SESSION["login"])) {?>
<div class="text-right">
	<?php echo $_SESSION["login"]; ?> -
	<a href="projects.php">Projets</a> -
	<a href="transactions_stats.php">Statistiques</a> -
	<a href="do_disconnect.php">Deconnecter</a>
</div>

Bienvenue sur l'interface de gestion de l'interface de paiement du PP

<?php }?>


<?php if (!isset($_SESSION["login"])) {?>
<div class="text-center">
<form action="do_connect.php" method="post">
	<label><span>Identifiant : </span><input type="text" name="login" /></label><br />
	<label><span>Mot de passe : </span><input type="password" name="password" /></label><br />
	<input type="submit" value="Connecter" />
</form>
</div>
<?php }?>

</main>


<?php include("footer.php");?>
</body>
</html>