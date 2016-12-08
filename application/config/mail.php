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
@include_once("config/mail.config.php");
require_once("engine/utils/PHPMailerAutoload.php");

function getMailInstance() {
    global $config;

    $mail = new PHPMailer();
    $mail->isSMTP();
//    $mail->CharSet = 'UTF-8';
    $mail->CharSet = 'ISO-8859-1';

    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;

    //Ask for HTML-friendly debug output
    $mail->Debugoutput = "html";

    $mail->Host = $config["smtp"]["host"];
    $mail->Port = $config["smtp"]["port"];

    ////Set the encryption system to use - ssl (deprecated) or tls
    $mail->SMTPSecure = "tls";
//    $mail->SMTPSecure = "ssl";

    $mail->SMTPAuth = true;
    $mail->Username = $config["smtp"]["username"];
    $mail->Password = $config["smtp"]["password"];

    return $mail;
}

function prepareAttachment($path) {
        $rn = "\r\n";

        if (file_exists($path))
	{
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $ftype = finfo_file($finfo, $path);
            $file = fopen($path, "r");
            $attachment = fread($file, filesize($path));
            $attachment = chunk_split(base64_encode($attachment));
            fclose($file);

            $msg = 'Content-Type: \'' . $ftype . '\'; name="' . basename($path) . '"' . $rn;
            $msg .= "Content-Transfer-Encoding: base64" . $rn;
            $msg .= 'Content-ID: <' . basename($path) . '>' . $rn;
//            $msg .= 'X-Attachment-Id: ebf7a33f5a2ffca7_0.1' . $rn;
            $msg .= $rn . $attachment . $rn . $rn;
            return $msg;
        }
	else
	{
            return false;
        }
    }

function sendMail($from, $to, $subject, $content, $path = '', $cc = '', $bcc = '', $_headers = false) {

        $rn = "\r\n";
        $boundary = md5(rand());
        $boundary_content = md5(rand());

// Headers
        $headers = 'From: ' . $from . $rn;
        $headers .= 'Mime-Version: 1.0' . $rn;
        $headers .= 'Content-Type: multipart/related;boundary=' . $boundary . $rn;

        //adresses cc and ci
        if ($cc != '')
	{
            $headers .= 'Cc: ' . $cc . $rn;
        }
        if ($bcc != '')
	{
            $headers .= 'Bcc: ' . $bcc . $rn;
        }
        $headers .= $rn;

// Message Body
        $msg = $rn . '--' . $boundary . $rn;
        $msg.= "Content-Type: multipart/alternative;" . $rn;
        $msg.= " boundary=\"$boundary_content\"" . $rn;

//Body Mode text
        $msg.= $rn . "--" . $boundary_content . $rn;
        $msg .= 'Content-Type: text/plain; charset=ISO-8859-1' . $rn;
        $msg .= strip_tags($content) . $rn;

//Body Mode Html
        $msg.= $rn . "--" . $boundary_content . $rn;
        $msg .= 'Content-Type: text/html; charset=ISO-8859-1' . $rn;
        $msg .= 'Content-Transfer-Encoding: quoted-printable' . $rn;
        if ($_headers)
	{
            $msg .= $rn . '<img src=3D"cid:template-H.PNG" />' . $rn;
        }
        //equal sign are email special characters. =3D is the = sign
        $msg .= $rn . '<div>' . nl2br(str_replace("=", "=3D", $content)) . '</div>' . $rn;
        if ($_headers)
	{
            $msg .= $rn . '<img src=3D"cid:template-F.PNG" />' . $rn;
        }
        $msg .= $rn . '--' . $boundary_content . '--' . $rn;

//if attachement
        if ($path != '' && file_exists($path))
	{
            $conAttached = prepareAttachment($path);
            if ($conAttached !== false)
	    {
                $msg .= $rn . '--' . $boundary . $rn;
                $msg .= $conAttached;
            }
        }

//other attachement : here used on HTML body for picture headers/footers
        if ($_headers)
	{
            $imgHead = dirname(__FILE__) . '/../../../../modules/notification/ressources/img/template-H.PNG';
            $conAttached = self::prepareAttachment($imgHead);
            if ($conAttached !== false)
	    {
                $msg .= $rn . '--' . $boundary . $rn;
                $msg .= $conAttached;
            }

            $imgFoot = dirname(__FILE__) . '/../../../../modules/notification/ressources/img/template-F.PNG';
            $conAttached = self::prepareAttachment($imgFoot);
            if ($conAttached !== false)
	    {
                $msg .= $rn . '--' . $boundary . $rn;
                $msg .= $conAttached;
            }
        }

// Fin
        $msg .= $rn . '--' . $boundary . '--' . $rn;

// Function mail()
        return mail($to, $subject, $msg, $headers);
}

function subjectEncode($subject) {
//	echo "subjectEncode($subject)\n";
	global $config;

	if (isset($config["server"]["line"]) && $config["server"]["line"]) {
//		echo "Not a prod line\n";
		$subject = "[".$config["server"]["line"]."]" . $subject;
//		echo "\ttransform into => $subject \n";
	}

	$subject = mb_encode_mimeheader(utf8_decode($subject), "ISO-8859-1");

//	echo "end subjectEncode($subject)\n";

	return $subject;
}

?>
