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
include_once("header.php");

?>
<body>

<nav><a href="https://www.partipirate.fr/" target="_blank"><img src="assets/img/logo_pp.png" /></a></nav>

<main class="clearfix">
<form id="form" action="do_donate.php">
<h1><?php echo lang("pp_donate_title"); ?></h1>
<p><?php echo lang("pp_money_donation_baseline"); ?></p>

<div id="righters" class="pull-right">
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

<div class="explanation" id="donationDiv">
	<h2><?php echo lang("pp_money_donation_title"); ?></h2>
	<div>
		<div class="pull-left" id="iDonateDiv">
			<?php echo lang("pp_money_donation_i_donate"); ?><br />
			<input tabindex="10" type="text" name="donation" id="donationInput" value="" /> &euro;
		</div>

		<?php if (lang("pp_money_reducing_taxes_value") != "0") {?>
		<div class="pull-right" id="realCostDiv">
			<span class="pull-right" id="realCostSpan"></span>
			<?php echo lang("pp_money_donation_real_cost2"); ?>
		</div>
		<?php }?>

	</div>
</div>

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
	<input tabindex="400" id="iDonateButton" type="submit" value="<?php echo lang("pp_money_i_donate_button"); ?>">
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
