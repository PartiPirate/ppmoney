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

var counterpartTemplate;
var counterpartOffset = 0;

function addCounterpartClickHandler(event) {
	event.stopPropagation();
	event.preventDefault();

	counterpartOffset++;
//	console.log("counterpartOffset : " + counterpartOffset);

	var counterpart = counterpartTemplate.clone();

	counterpart.find("*[name*='counterparties[']").each(function() {
		var name = $(this).attr("name");
//		debugger;
		name = name.replace("[0]", "[" + counterpartOffset + "]");
//		console.log(name);
		$(this).attr("name", name);
	});

	$("#counterparts").append(counterpart);
}

$(function() {
	counterpartTemplate = $("#counterpart-0");
	counterpartTemplate.remove();

	counterpartOffset = $("*[id*='counterpart-']").length;

	$("#addCounterpart").click(addCounterpartClickHandler);
});