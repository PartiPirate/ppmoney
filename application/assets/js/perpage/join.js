/*
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

function isValidMail(value) {
    var mailRegExp = new RegExp("^[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$");

    return mailRegExp.test(value.toUpperCase());
}

function isValidAmount(amount) {
	amount = "" + amount;
	amount = amount.replace(",", ".");

	return (amount == amount * 1.) && amount < 7500 && amount > 0;
}

function computeTotalAmount() {
	var cost = 0;
	if ($("#donationInput").val()) {
		cost -= -$("#donationInput").val();
	}

	if ($("#localDonationInput").val()) {
		cost -= -$("#localDonationInput").val();
	}

	if ($("input[name=costRadio]:checked").val()) {
		cost -= -$("input[name=costRadio]:checked").val();
	}

	return cost;
}

function computeRealCost() {
	var cost = computeTotalAmount();
	var realCost = Math.ceil(cost * (1 - taxReduction));

	$("#realCostSpan").html(realCost + "&euro;");
}

function isCompleteFormHandler(event) {
	event.preventDefault();

	var isOk = true;

	if (!$("#iCertifyCheckbox:checked").length) {
		isOk = false;
		$("#iCertifyCheckbox").focus();
	}

	if (
			$("input[name=joinType]:checked").val() == "firstTime" ||
			$("input[name=renewAddress]:checked").val() == "yes"
		) {

		if (!$("input[name=address]").val()) {
			isOk = false;
			$("input[name=address]").focus();
		}
		else if (!$("input[name=zipcode]").val()) {
			isOk = false;
			$("input[name=zipcode]").focus();
		}
		else if (!$("input[name=country]").val()) {
			isOk = false;
			$("input[name=country]").focus();
		}
	}

	if (
			$("input[name=joinType]:checked").val() == "firstTime"
		) {

		if (!$("input[name=lastname]").val()) {
			isOk = false;
			$("input[name=lastname]").focus();
		}
		else if (!$("input[name=firstname]").val()) {
			isOk = false;
			$("input[name=firstname]").focus();
		}
		else if (!$("input[name=email]").val()) {
			isOk = false;
			$("input[name=email]").focus();
		}
		else if (!isValidMail($("input[name=email]").val())) {
			isOk = false;
			$("input[name=email]").focus();
		}
	}
	else {
		if (!$("input[name=renewEmail]").val()) {
			isOk = false;
			$("input[name=renewEmail]").focus();
		}
		else if (!isValidMail($("input[name=renewEmail]").val())) {
			isOk = false;
			$("input[name=renewEmail]").focus();
		}
	}

	if ($("#localDonationInput").val() && !isValidAmount($("#localDonationInput").val())) {
		isOk = false;
		$("#localDonationInput").focus();
	}
	else if ($("#donationInput").val() && !isValidAmount($("#donationInput").val())) {
		isOk = false;
		$("#donationInput").focus();
	}
	else if (computeTotalAmount() > 7500) {
		isOk = false;
		$("#donationInput").focus();
	}

	if (isOk) {
		$.post("do_setPaymentForm.php", $("#form").serialize(), function(data) {
			try {
				var jsonData = $.parseJSON(data);
				alert(jsonData.message);
			}
			catch(error) {
				// Il n'y a pas d'erreur
				$("body").append($(data));
				$("#payboxForm").submit();
			}
		}, "html");
	}
}

$(function() {
	$("input[name=costRadio]").click(computeRealCost);
	$("#donationInput").keyup(computeRealCost);
	$("#localDonationInput").keyup(computeRealCost);

	$("input[name=joinType]").click(function() {
		if ($(this).val() == "firstTime") {
			$("#subscriptionDiv").show();
			$("#identityDiv").show();
			$("#emailDiv").hide();
			$("#renewAddressDiv").hide();
			$("#address1Div").show();
			$("#address2Div").show();

			$(".explanation").show();
			$("#localSectionDiv").show();
		}
		else {
			$("#subscriptionDiv").hide();
			$("#identityDiv").hide();
			$("#emailDiv").show();
			$("#renewAddressDiv").show();

			$(".explanation").show();
			$("#localSectionDiv").hide();

			if ($("input[name=renewAddress]:checked").val() == "no") {
				$("#address1Div").hide();
				$("#address2Div").hide();
			}
		}
	});

	$("input[name=renewAddress]").click(function() {
		if ($(this).val() == "yes") {
			$("#address1Div").show();
			$("#address2Div").show();
		}
		else {
			$("#address1Div").hide();
			$("#address2Div").hide();
		}
	});

	$("#form").submit(isCompleteFormHandler);

	// For the moment
	$("#firstTimeRadio").click();
	$("#chooseDiv").hide();
	$("#subscriptionDiv").show();
	$("#identityDiv").show();
	$("#emailDiv").hide();
	$("#renewAddressDiv").hide();
	$("#address1Div").show();
	$("#address2Div").show();

	$(".explanation").show();
	$("#localSectionDiv").show();
	// End for the moment


//	$(".explanation").hide();
	computeRealCost();
});