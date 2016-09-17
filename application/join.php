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

if (isset($_REQUEST["join-member-input"])) {
	$member = json_decode($_REQUEST["join-member-input"], true);
}
else {
	$member = array
		(
			"nom_adh" => "",
			"prenom_adh" => "",
			"pseudo_adh" => "",
			"adresse_adh" => "",
			"adresse2_adh" => "",
			"cp_adh" => "",
			"ville_adh" => "",
			"pays_adh" => "FRANCE",
			"tel_adh" => "",
			"gsm_adh" => "",
			"email_adh" => ""
		);	
}

?>
<body>

<div class="gradient-black-invisible pull-right" style="width: 100px;">
	<div class="bg-white" style="height: 20px; margin: 0 0 0 1px; width: 99px;"></div>
	<div class="bg-white text-center" style="margin-bottom: 0; padding: 0; border: 1px solid black; border-bottom: 0; border-left-color: white;"><a href="https://adhesion.partipirate.org/join.php">Adhérer</a></div>
	<div class="bg-white text-center" style="margin-bottom: 0; padding: 0; border: 1px solid black; border-right-color: #B0B0B0; border-bottom-color: #B0B0B0;"><a href="https://don.partipirate.org/donate.php">Donner</a></div>
	<div class="bg-white" style="height: 100px; margin: 0 0 0 1px; width: 99px;"></div>
</div>

<nav><a href="https://www.partipirate.fr/" target="_blank"><img src="assets/img/logo_pp.png" /></a></nav>

<main class="clearfix">
<form id="form" action="do_join.php">
<h1><?php echo lang("pp_join_title"); ?></h1>

<div id="righters" class="pull-right">
	<div>
		<h3><?php echo lang("pp_money_mail_join_title"); ?></h3>
		<p><?php echo lang("pp_money_mail_join_content"); ?></p>
	</div>
	<?php if (lang("pp_money_reducing_taxes_show") == "1") {?>
	<div>
		<h3 class="text-center"><?php echo lang("pp_money_reducing_taxes_title"); ?></h3>
		<p><?php echo lang("pp_money_reducing_taxes_content"); ?></p>
	</div>
	<?php }?>
</div>

<div id="chooseDiv">
	<p class="pull-right"><?php echo lang("pp_money_joinType_explanation"); ?></p>
	<label><input tabindex="10" type="radio" name="joinType" id="firstTimeRadio" value="firstTime" checked="checked" /><?php echo lang("pp_money_firstname_label"); ?></label>
	<label><input tabindex="11" type="radio" name="joinType" id="reaccessionRadio" value="reaccession" /><?php echo lang("pp_money_reaccession_label"); ?></label>
</div>

<ul id="joinType">
	<li><label><input tabindex="12" type="radio" name="costRadio" id="cost24Radio" value="24" checked="checked" /><strong>24&euro; : </strong>plein tarif</label></li>
	<li><label><input tabindex="13" type="radio" name="costRadio" id="cost12Radio" value="12" /><strong>12&euro; : </strong>demi-tarif (étudiant, chômeur)</label></li>
	<li><label><input tabindex="14" type="radio" name="costRadio" id="cost6Radio" value="6" /><strong>6&euro; : </strong>tarif réduit (personne en difficulté, à votre appréciation)</label></li>
</ul>


<div class="explanation join" id="coordinatesDiv">
	<h2><?php echo lang("pp_money_coordinates_title"); ?></h2>
	<div>
		<p class="error"><?php echo lang("pp_money_coordinates_explanation"); ?></p>

		<div id="identityDiv">
			<div id="lastnameInputDiv" class="pull-left"><?php echo lang("pp_money_coordinates_lastname"); ?> <span class="error">*</span><br/><input tabindex="100" type="text" name="lastname" /></div>
			<div id="firstnameInputDiv" class="pull-right"><?php echo lang("pp_money_coordinates_firstname"); ?> <span class="error">*</span><br/><input tabindex="101" type="text" name="firstname" /></div>
		</div>

		<div id="identity2Div">
			<div id="emailInputDiv" class="pull-left"><?php echo lang("pp_money_coordinates_email"); ?> <span class="error">*</span><br/><input tabindex="102" type="text" name="email" /></div>
			<div id="pseudoInputDiv" class="pull-right"><?php echo lang("pp_money_forum_pseudo"); ?><br/><input tabindex="103" type="text" name="pseudo" /></div>
		</div>

		<div id="emailDiv">
			<div id="emailInputDiv" class="pull-left"><?php echo lang("pp_money_coordinates_email"); ?> <span class="error">*</span><br/><input tabindex="103" type="text" name="renewEmail" /></div>
			<div id="emailExplanationDiv" class="pull-left">
				<?php echo lang("pp_money_coordinates_email_explanation"); ?>
			</div>
		</div>

		<div id="renewAddressDiv">
			<label><input tabindex="104" class="" type="radio"
							name="renewAddress"
							id="renewAddressCheckbox" value="no" checked="checked"/><?php echo lang("pp_money_coordinates_renew"); ?></label>
			<label><input tabindex="105" class="" type="radio"
							name="renewAddress"
							id="newAddressCheckbox" value="yes" /><?php echo lang("pp_money_coordinates_new"); ?></label>
		</div>

		<div id="address1Div"><?php echo lang("pp_money_coordinates_fiscal_address"); ?> <span class="error">*</span><br/>
			<input tabindex="106" type="text" name="address" /></div>

		<div id="address2Div">
			<div id="zipcodeInputDiv" class="pull-left"><?php echo lang("pp_money_coordinates_zipcode"); ?> <span class="error">*</span><br/><input tabindex="107" type="text" name="zipcode" /></div>
			<div id="cityInputDiv" class="pull-left"><?php echo lang("pp_money_coordinates_city"); ?> <span class="error">*</span><br/><input tabindex="108" type="text" name="city" /></div>
			<div id="telephoneInputDiv" class="pull-right"><?php echo lang("pp_money_coordinates_telephone"); ?><br/><input tabindex="110" type="text" name="telephone" /></div>
			<div id="countryInputDiv" class="pull-right"><?php echo lang("pp_money_coordinates_country"); ?> <span class="error">*</span><br/><input tabindex="109" type="text" name="country" value="France" /></div>
		</div>

	</div>
</div>

<div class="explanation" id="localSectionDiv">
	<h2><?php echo lang("pp_money_local_section_title"); ?></h2>
	<div>
		<div id="localSectionGuide" class="pull-right">
			<?php echo lang("pp_money_local_section_guide"); ?>
		</div>
		<div id="localSectionJoinDiv" class="pull-left">
			<?php echo lang("pp_money_local_section_join"); ?><br/><select tabindex="400" type="text" name="localSection">
<?php
	$sections = explode(",", lang("pp_money_local_section_sections"));
	foreach($sections as $section) {
		echo "<option value=\"" . strtolower($section) . "\">$section</option>";
	}
?>
			</select>
		</div>
		<div id="localSectionDonationDiv" class="pull-left">
			<?php echo lang("pp_money_local_section_donation"); ?><br/><input tabindex="401" type="text" name="localDonation" id="localDonationInput" />
			&euro;
		</div>
	</div>
</div>

<div class="explanation" id="donationDiv">
	<h2><?php echo lang("pp_money_donation_title"); ?></h2>
	<div>
		<div class="pull-left">
			<?php echo lang("pp_money_donation_another_one"); ?><br />
			<input tabindex="500" type="text" name="donation" id="donationInput" value="" />
			&euro;
		</div>

		<?php if (lang("pp_money_reducing_taxes_value") != "0") {?>
		<div class="pull-right" id="realCostDiv">
			<span class="pull-right" id="realCostSpan"></span>
			<?php echo lang("pp_money_donation_real_cost"); ?>
		</div>
		<?php }?>

	</div>
</div>

<div class="explanation" id="otherInformationDiv">
	<h2><?php echo lang("pp_money_other_information_title"); ?></h2>
	<div>
	<?php echo lang("pp_money_other_information_content"); ?>
	<textarea tabindex="650" rows="6" id="comment" name="comment"></textarea>
	</div>
</div>

<div class="explanation" id="iCertifyDiv">
	<label><input tabindex="700" class="pull-left" type="checkbox" name="iCertify" id="iCertifyCheckbox" value="yes" /><p><?php

	$content = lang("pp_money_i_certify_content");
	$content = explode("\n", $content);
	$content = implode("</p><p>", $content);

	echo $content;

	?></p>
</label>
</div>

<div class="explanation text-center">
	<input tabindex="800" id="iJoinButton" type="submit" value="<?php echo lang("pp_money_i_join_button"); ?>">
</div>

<div class="explanation">
	<h2><?php echo lang("pp_money_to_know_title"); ?></h2>

	<p><?php

	$content = lang("pp_money_to_know_content");
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

	var member = <?php echo json_encode($member); ?>;
		
</script>
<?php include("footer.php");?>
</body>
</html>
