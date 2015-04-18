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
?>
<html>
<head>
<?php
		$host = $_SERVER["HTTP_HOST"];
		if (strpos($host, "don") !== false) {
?>
<meta http-equiv="refresh" content="0;URL=donate.php">
<?php 	}
		else if (strpos($host, "adhesion") !== false) {
?>
<meta http-equiv="refresh" content="0;URL=join.php">
<?php 	}?>
</head>
<body>
</body>
</html>
