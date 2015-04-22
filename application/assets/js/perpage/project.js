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

	if ($("input[name=projectDonation]:checked").val()) {
		cost -= -$("input[name=projectDonation]:checked").val();
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

	if ($("#donationInput").val() && !isValidAmount($("#donationInput").val())) {
		isOk = false;
		$("#donationInput").focus();
	}
	else if (computeTotalAmount() > 7500) {
		isOk = false;
		$("#donationInput").focus();
	}
	else if (!$("input[name=lastname]").val()) {
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
	else if (!$("input[name=address]").val()) {
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
	else if (!$("#iCertifyCheckbox:checked").length) {
		isOk = false;
		$("#iCertifyCheckbox").focus();
	}

	if (isOk) {
		$.post("do_setPaymentForm.php", $("#form").serialize(), function(data) {
			$("body").append($(data));
			$("#payboxForm").submit();
		}, "html");
	}
}

$(function() {
	$("input[name=projectDonation]").click(computeRealCost);
	$("#donationInput").keyup(computeRealCost);

	$("#form").submit(isCompleteFormHandler);

	computeRealCost();
});