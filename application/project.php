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
include_once("config/database.php");
require_once("engine/bo/GaugeBo.php");
require_once("engine/bo/ProjectBo.php");
require_once("engine/bo/TransactionBo.php");

$connection = openConnection();

$gaugeBo = GaugeBo::newInstance($connection);
$projectBo = ProjectBo::newInstance($connection);
$transactionBo = TransactionBo::newInstance($connection);

$projectId = intval($_REQUEST["id"]);
$project = $projectBo->getProject($projectId);

if (!$project) {
	// rajout d'un header location vers donate.php
	header("Location: donate.php");
	exit();
}

include_once("language/language.php");
$title = str_replace("{project}", $project["pro_label"], lang("pp_project_title"));

$gauge = array();
$gauge["gau_searched_purpose"] = '"project":{"code":"'.$project["pro_code"].'"';
$gauge["gau_from_date"] = "2015-01-01";
$gauge["gau_amount_path"] = "join";
$gauge["gau_amount_goal"] = intval($project["pro_amount_goal"]);

$transactionBo->getGaugeTransactions($gauge);
GaugeBo::normalize($gauge);

include_once("header.php");

?>
<body>

<nav><a href="https://www.partipirate.fr/" target="_blank"><img src="assets/img/logo_pp.png" /></a></nav>

<main class="clearfix">
<form id="form" action="do_donate.php">
	<input type="hidden" name="projectId" value="<?php echo $projectId; ?>" />
<h1><?php echo $title ?></h1>

<div id="righters" class="pull-right">
	<div>
		<h3><?php echo lang("pp_money_project_goal_title"); ?></h3>
		<p>
			<?php echo lang("pp_money_gauge_goal"); ?> : <?php echo number_format($gauge["gau_percent_goal"], 0); ?>%<br/>
			<?php echo str_replace("{amount}", number_format($gauge["gau_amount"], 0), str_replace("{amount_goal}", number_format($gauge["gau_amount_goal"], 0), $lang["pp_money_gauge_amounts"])) ; ?><br/>
			<?php echo $gauge["gau_number_of_transactions"] . " " . ($gauge["gau_number_of_transactions"] > 1 ? lang("pp_money_gauge_donations") : lang("pp_money_gauge_donation")); ?><br/>
		</p>
	</div>

	<div>
		<h3><?php echo lang("pp_money_project_participation_title"); ?></h3>
		<?php 	$index = 10;
				foreach($project["counterparties"] as $counterparty) {
					echo "<p><label><input tabindex='$index' type='radio' name='projectDonation' value='".$counterparty["cpa_amount"]."' /> ".$counterparty["cpa_amount"]."&euro;</label><br/>";

					echo $counterparty["cpa_content"];

					echo "</p>";

					$index++;
				} ?>
		<p>
			<?php  echo lang("pp_money_donation_another_one"); ?>
			<input tabindex="99" type="text" name="projectAdditionalDonation" id="donationInput" value="" /> &euro;
		</p>
	</div>

	<?php if (lang("pp_money_reducing_taxes_value") != "0") {?>
	<div>
		<?php echo lang("pp_money_donation_real_cost2"); ?>
		<span class="pull-right" style="position: relative; top: -22px; height: 26px; font-size: 16px;" id="realCostSpan"></span>
	</div>
	<?php }?>

	<?php if (lang("pp_money_reducing_taxes_show") == "1" && false) {?>
	<div>
		<h3 class="text-center"><?php echo lang("pp_money_reducing_taxes_title"); ?></h3>
		<p><?php echo lang("pp_money_reducing_taxes_content"); ?></p>
	</div>
	<?php }?>

		<div class="text-center">
		<?php
			$url = $config["server"]["base"];
			$url .= substr($_SERVER["REQUEST_URI"], 1);

		    $qrPath = generateQR($url, 'project_'.$projectId);

//		    echo $url;

			// displaying
    		echo '<img src="'.$qrPath.'" />';
		?>
	</div>
</div>

<div class="explanation" id="projectDiv">
	<h2><?php echo lang("pp_money_project_intro_title"); ?></h2>
	<p><?php

	$content = $project["pro_content"];
	$content = explode("\n", $content);
	$content = implode("</p><p>", $content);

	echo $content; ?></p>
</div>

<div class="explanation" id="crowdfundingDiv">
	<h2><?php echo lang("pp_money_project_crowdfunding_title"); ?></h2>
	<p><?php

	$content = lang("pp_money_project_crowdfunding_guide");
	$content = explode("\n", $content);
	$content = implode("</p><p>", $content);

	echo $content; ?></p>
</div>

<!--
<div class="explanation" id="donationDiv">
	<h2><?php echo lang("pp_money_donation_title"); ?></h2>
	<div>
		<div class="pull-left" id="iDonateDiv">
			<?php echo lang("pp_money_donation_i_donate"); ?><br />
			<input type="text" name="donation" id="donationInput" value="" />
		</div>

		<div class="pull-right" id="realCostDiv">
			<span class="pull-right" id="realCostSpan"></span>
			<?php echo lang("pp_money_donation_real_cost2"); ?>
		</div>

	</div>
</div>
 -->

<div class="explanation" id="coordinatesDiv">
	<h2><?php echo lang("pp_money_coordinates_title"); ?></h2>
	<div>
		<p class="error"><?php echo lang("pp_money_coordinates_explanation"); ?></p>

		<div id="identityDiv">
			<div id="lastnameInputDiv" class="pull-left"><?php echo lang("pp_money_coordinates_lastname"); ?> <span class="error">*</span><br/><input tabindex="100" type="text" name="lastname" /></div>
			<div id="firstnameInputDiv" class="pull-left"><?php echo lang("pp_money_coordinates_firstname"); ?> <span class="error">*</span><br/><input tabindex="101" type="text" name="firstname" /></div>
			<div id="emailInputDiv" class="pull-right"><?php echo lang("pp_money_coordinates_email"); ?> <span class="error">*</span><br/><input tabindex="102" type="text" name="email" /></div>
		</div>

		<div id="address1Div"><?php echo lang("pp_money_coordinates_fiscal_address"); ?> <span class="error">*</span><br/>
			<input tabindex="103" type="text" name="address" style="width: 100%;" /></div>

		<div id="address2Div">
			<div id="zipcodeInputDiv" class="pull-left"><?php echo lang("pp_money_coordinates_zipcode"); ?> <span class="error">*</span><br/><input tabindex="104" type="text" name="zipcode" /></div>
			<div id="cityInputDiv" class="pull-left"><?php echo lang("pp_money_coordinates_city"); ?> <span class="error">*</span><br/><input tabindex="105" type="text" name="city" /></div>
			<div id="telephoneInputDiv" class="pull-right"><?php echo lang("pp_money_coordinates_telephone"); ?><br/><input tabindex="107" type="text" name="telephone" /></div>
			<div id="countryInputDiv" class="pull-right"><?php echo lang("pp_money_coordinates_country"); ?> <span class="error">*</span><br/><input tabindex="106" type="text" name="country" value="France" /></div>
		</div>

	</div>
</div>

<div class="explanation" id="otherInformationDiv">
	<h2><?php echo lang("pp_money_other_information_title"); ?></h2>
	<div>
	<?php echo lang("pp_money_other_information_content"); ?>
	<textarea tabindex="200" rows="6" id="comment" name="comment"></textarea>
	</div>
</div>

<div class="explanation" id="iCertifyDiv">
	<label><input tabindex="300" class="pull-left" type="checkbox" name="iCertify" id="iCertifyCheckbox" value="yes" /><p><?php

	$content = lang("pp_money_i_certify_content");
	$content = explode("\n", $content);
	$content = implode("</p><p>", $content);

	echo $content;

	?></p>
</label>
</div>

<div class="explanation text-center">
	<input tabindex="400" id="iDonateButton" type="submit" value="<?php echo lang("pp_money_i_project_button"); ?>">
</div>

<div class="explanation">
	<h2><?php echo lang("pp_money_to_know_title"); ?></h2>
	<p><?php

	$content = lang("pp_money_to_know_content2");
	$content = explode("\n", $content);
	$content = implode("</p><p>", $content);

	echo $content;

	?></p>
</div>

<div class="explanation">
	<h2><?php echo lang("pp_money_legal_title"); ?></h2>
	<p style="width: 694px"><?php echo lang("pp_money_legal_content"); ?></p>
</div>
</form>
</main>

<script type="text/javascript">
	var taxReduction = <?php echo lang("pp_money_reducing_taxes_value"); ?> / 100.;
</script>
<?php include("footer.php");?>
</body>
</html>