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
include_once("language/language.php");
$title = lang("pp_done_title");
include_once("header.php");

?>
<body>

<nav><a href="https://www.partipirate.fr/" target="_blank"><img src="assets/img/logo_pp.png" /></a></nav>

<main class="clearfix">
<h1><?php echo $title; ?></h1>

<div id="righters" class="pull-right">
	<div>
		<h3><?php echo lang("pp_money_mail_join_title"); ?></h3>
		<p><?php echo lang("pp_money_mail_join_content"); ?></p>
	</div>
	<div>
		<h3><?php echo lang("pp_money_mail_donate_title"); ?></h3>
		<p><?php echo lang("pp_money_mail_donate_content"); ?></p>
	</div>
	<?php if (lang("pp_money_reducing_taxes_show") == "1") {?>
	<div>
		<h3 class="text-center"><?php echo lang("pp_money_reducing_taxes_title"); ?></h3>
		<p><?php echo lang("pp_money_reducing_taxes_content"); ?></p>
	</div>
	<?php }?>
</div>

<div class="explanation">
	<p><?php

	$content = lang("pp_money_done_content");
	$content = explode("\n", $content);
	$content = implode("</p><p>", $content);

	echo $content;

	?></p>
</div>

</main>

<script type="text/javascript">
</script>
<?php include("footer.php");?>
</body>
</html>