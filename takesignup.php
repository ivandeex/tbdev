<?

/*
// +--------------------------------------------------------------------------+
// | Project:    TBDevYSE - TBDev Yuna Scatari Edition                        |
// +--------------------------------------------------------------------------+
// | This file is part of TBDevYSE. TBDevYSE is based on TBDev,               |
// | originally by RedBeard of TorrentBits, extensively modified by           |
// | Gartenzwerg.                                                             |
// |                                                                          |
// | TBDevYSE is free software; you can redistribute it and/or modify         |
// | it under the terms of the GNU General Public License as published by     |
// | the Free Software Foundation; either version 2 of the License, or        |
// | (at your option) any later version.                                      |
// |                                                                          |
// | TBDevYSE is distributed in the hope that it will be useful,              |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
// | GNU General Public License for more details.                             |
// |                                                                          |
// | You should have received a copy of the GNU General Public License        |
// | along with TBDevYSE; if not, write to the Free Software Foundation,      |
// | Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA            |
// +--------------------------------------------------------------------------+
// |                                               Do not remove above lines! |
// +--------------------------------------------------------------------------+
*/

require_once("include/bittorrent.php");

dbconn();

if ($deny_signup && !$allow_invite_signup)
	stderr($tracker_lang['error'], "��������, �� ����������� ��������� ��������������.");

if ($CURUSER)
	stderr($tracker_lang['error'], sprintf($tracker_lang['signup_already_registered'], $SITENAME));

$users = get_row_count("users");
if ($users >= $maxusers)
	stderr($tracker_lang['error'], sprintf($tracker_lang['signup_users_limit'], number_format($maxusers)));

if (!mkglobal("wantusername:wantpassword:passagain:email"))
	stderr($tracker_lang['error'], "������ ������ � ����� ����� �� ��������.");

if ($deny_signup && $allow_invite_signup) {
	if (empty($_POST["invite"]))
		stderr("������", "��� ����������� ��� ����� ������ ��� �����������!");
	if (strlen($_POST["invite"]) != 32)
		stderr("������", "�� ����� �� ���������� ��� �����������.");
	list($inviter) = mysql_fetch_row(sql_query("SELECT inviter FROM invites WHERE invite = ".sqlesc($_POST["invite"])));
	if (!$inviter)
		stderr("������", "��� ����������� ��������� ���� �� �������.");
	list($invitedroot) = mysql_fetch_row(sql_query("SELECT invitedroot FROM users WHERE id = $inviter"));
}

function bark($msg) {
	global $tracker_lang;
	stdhead();
	stdmsg($tracker_lang['error'], $msg, 'error');
	stdfoot();
	exit;
}

function validusername($username)
{
	if ($username == "")
	  return false;

	// The following characters are allowed in user names
	$allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_".
		"���������������������������������Ũ����������������������";

	for ($i = 0; $i < strlen($username); ++$i)
	  if (strpos($allowedchars, $username[$i]) === false)
	    return false;

	return true;
}

$gender = $_POST["gender"];
$website = htmlspecialchars_uni($_POST["website"]);
$country = $_POST["country"];
$year = $_POST["year"];
$month = $_POST["month"];
$day = $_POST["day"];

$icq = unesc($_POST["icq"]);
if (strlen($icq) > 10)
    bark("����, ����� icq ������� �������  (���� - 10)");

$msn = unesc($_POST["msn"]);
if (strlen($msn) > 30)
    bark("����, ��� msn ������� �������  (���� - 30)");

$aim = unesc($_POST["aim"]);
if (strlen($aim) > 30)
    bark("����, ��� aim ������� �������  (���� - 30)");

$yahoo = unesc($_POST["yahoo"]);
if (strlen($yahoo) > 30)
    bark("����, ��� yahoo ������� �������  (���� - 30)");

$mirc = unesc($_POST["mirc"]);
if (strlen($mirc) > 30)
    bark("����, ��� mirc ������� �������  (���� - 30)");

$skype = unesc($_POST["skype"]);
if (strlen($skype) > 20)
    bark("����, ��� skype ������� �������  (���� - 20)");

if (empty($wantusername) || empty($wantpassword) || empty($email) || empty($gender) || empty($country))
	bark("��� ���� ����������� ��� ����������.");

if (strlen($wantusername) > 12)
	bark("��������, ��� ������������ ������� ������� (�������� 12 ��������)");

if ($wantpassword != $passagain)
	bark("������ �� ���������! ������ �� ��������. ���������� ���.");

if (strlen($wantpassword) < 6)
	bark("��������, ������ ������� ������� (������� 6 ��������)");

if (strlen($wantpassword) > 40)
	bark("��������, ������ ������� ������� (�������� 40 ��������)");

if ($wantpassword == $wantusername)
	bark("��������, ������ �� ����� ���� �����-�� ��� ��� ������������.");

if (!validemail($email))
	bark("��� �� ������ �� �������� email �����.");

if (!validusername($wantusername))
	bark("�������� ��� ������������.");

if ($year=='0000' || $month=='00' || $day=='00')
        stderr($tracker_lang['error'],"������ �� ������� �������� ���� ��������");
	$birthday = date("$year.$month.$day");

// make sure user agrees to everything...
if ($_POST["rulesverify"] != "yes" || $_POST["faqverify"] != "yes" || $_POST["ageverify"] != "yes")
	stderr($tracker_lang['error'], "��������, �� �� ��������� ��� ���� ���-�� ����� ������ ����� �����.");

// check if email addy is already in use
$a = (@mysql_fetch_row(@sql_query("SELECT COUNT(*) FROM users WHERE email=".sqlesc($email)))) or die(mysql_error());
if ($a[0] != 0)
	bark("E-mail ����� $email ��� ��������������� � �������.");

if ($use_captcha) {
	$b = get_row_count("captcha", "WHERE imagehash = ".sqlesc($_POST["imagehash"])." AND imagestring = ".sqlesc($_POST["imagestring"]));
	sql_query("DELETE FROM captcha WHERE imagehash = ".sqlesc($_POST["imagehash"])) or die(mysql_error());
	if ($b == 0)
		bark("�� ����� ������������ ��� �������������.");
}

$ip = getip();

if (isset($_COOKIE["uid"]) && is_numeric($_COOKIE["uid"]) && $users) {
    $cid = intval($_COOKIE["uid"]);
    $c = sql_query("SELECT enabled FROM users WHERE id = $cid ORDER BY id DESC LIMIT 1");
    $co = @mysql_fetch_row($c);
    if ($co[0] == 'no') {
		sql_query("UPDATE users SET ip = '$ip', last_access = NOW() WHERE id = $cid");
		bark("��� IP ������� �� ���� �������. ����������� ����������.");
    } else
		bark("����������� ����������!");
} else {
    $b = (@mysql_fetch_row(@sql_query("SELECT enabled, id FROM users WHERE ip LIKE '$ip' ORDER BY last_access DESC LIMIT 1")));
    if ($b[0] == 'no') {
		$banned_id = $b[1];
        setcookie("uid", $banned_id, "0x7fffffff", "/");
		bark("��� IP ������� �� ���� �������. ����������� ����������.");
    }
}

$secret = mksecret();
$wantpasshash = md5($secret . $wantpassword . $secret);
$editsecret = (!$users?"":mksecret());

if ((!$users) || (!$use_email_act == true))
	$status = 'confirmed';
else
	$status = 'pending';

$ret = sql_query("INSERT INTO users (username, passhash, secret, editsecret, gender, country, icq, msn, aim, yahoo, skype, mirc, website, email, status, ". (!$users?"class, ":"") ."added, birthday, invitedby, invitedroot) VALUES (" .
		implode(",", array_map("sqlesc", array($wantusername, $wantpasshash, $secret, $editsecret, $gender, $country, $icq, $msn, $aim, $yahoo, $skype, $mirc, $website, $email, $status))).
		", ". (!$users?UC_SYSOP.", ":""). "'". get_date_time() ."', '$birthday', '$inviter', '$invitedroot')");// or sqlerr(__FILE__, __LINE__);

if (!$ret) {
	if (mysql_errno() == 1062)
		bark("������������ $wantusername ��� ���������������!");
	bark("����������� ������. ����� �� ������� mySQL: ".htmlspecialchars(mysql_error()));
}

$id = mysql_insert_id();

sql_query("DELETE FROM invites WHERE invite = ".sqlesc($_POST["invite"]));

write_log("��������������� ����� ������������ $wantusername","FFFFFF","tracker");

$psecret = md5($editsecret);

$body = <<<EOD
�� ������������������ �� $SITENAME � ������� ���� ����� ��� �������� ($email).

���� ��� ���� �� ��, ��������� �������������� ��� ������. ������� ������� ����� ��� E-Mail ������ ����� IP ����� {$_SERVER["REMOTE_ADDR"]}. ���������, �� ���������.

��� ������������� ����� �����������, ��� ����� ������ �� ��������� ������:

$DEFAULTBASEURL/confirm.php?id=$id&secret=$psecret

����� ���� ��� �� ��� ��������, �� ������� ������������ ��� �������. ���� �� ����� �� ��������,
 ��� ����� ������� ����� ������ ����� ���� ����. �� ����������� ��� ��������� �������
� ���� ������ ��� �� ������� ������������ $SITENAME.
EOD;

if($use_email_act && $users) {
	if (!sent_mail($email, $SITENAME, $SITEEMAIL,"������������� ����������� �� $SITENAME", $body, false)) {
		stderr($tracker_lang['error'], "���������� ��������� E-Mail. ���������� �����");
	}
} else {
	logincookie($id, $wantpasshash);
}

header("Refresh: 0; url=ok.php?type=". (!$users?"sysop":("signup&email=" . urlencode($email))));

?>