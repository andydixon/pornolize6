<?php
$mysqli = new mysqli('localhost', 'root', '', 'metrics');
$stmt = $mysqli->prepare("INSERT INTO `pornolizer` (`ip`,`urlCalled`,`userAgent`,`referer`) VALUES (?,?,?,?)");
$stmt->bind_param('ssss', $_SERVER['REMOTE_ADDR'],decrypt($_GET['u']),$_SERVER['HTTP_USER_AGENT'],decrypt($_GET['r']));
$stmt->execute();
header('Content-Type: image/png');
die(hex2bin('89504e470d0a1a0a0000000d494844520000000100000001010300000025db56ca00000003504c5445000000a77a3dda0000000174524e530040e6d8660000000a4944415408d76360000000020001e221bc330000000049454e44ae426082'));

function decrypt($data) {
	return gzdecode(base64_decode($data));
}
