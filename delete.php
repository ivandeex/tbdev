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


function bark($msg) {
  stdhead($tracker_lang['error']);
  stdmsg($tracker_lang['error'], $msg);
  stdfoot();
  exit;
}

if (!mkglobal("id"))
	bark("��������� ������");

$id = 0 + $id;
if (!$id)
	die();

dbconn();

loggedinorreturn();

$res = sql_query("SELECT name,owner,seeders,image1,image2 FROM torrents WHERE id = $id");
$row = mysql_fetch_array($res);
if (!$row)
	stderr($tracker_lang['error'],"������ �������� �� ����������.");

if ($CURUSER["id"] != $row["owner"] && get_user_class() < UC_MODERATOR)
	bark("�� �� ��������! ��� ����� ����� ���������?\n");

$rt = 0 + $_POST["reasontype"];

if (!is_int($rt) || $rt < 1 || $rt > 5)
	bark("�������� ������� $rt.");

$r = $_POST["r"];
$reason = $_POST["reason"];

if ($rt == 1)
	$reasonstr = "�������: 0 ���������, 0 �������� = 0 �����";
elseif ($rt == 2)
	$reasonstr = "�������" . ($reason[0] ? (": " . trim($reason[0])) : "!");
elseif ($rt == 3)
	$reasonstr = "Nuked" . ($reason[1] ? (": " . trim($reason[1])) : "!");
elseif ($rt == 4)
{
	if (!$reason[2])
		bark("�� �� �������� ���� ������, ������� ���� ������� �������.");
  $reasonstr = "��������� ������: " . trim($reason[2]);
}
else
{
	if (!$reason[3])
		bark("�� �� �������� �������, ������ �������� �������.");
  $reasonstr = trim($reason[3]);
}

deletetorrent($id);

if ($row["image1"]) {
 $img1 = "torrents/images/$row[image1]";
 $del = unlink($img1);
}
if ($row["image2"]) {
 $img2 = "torrents/images/$row[image2]";
 $del = unlink($img2);
}

write_log("������� $id ($row[name]) ��� ������ ������������� $CURUSER[username] ($reasonstr)\n","F25B61","torrent");

stdhead("������� ������!");

if (isset($_POST["returnto"]))
	$ret = "<a href=\"" . htmlspecialchars($_POST["returnto"]) . "\">�����</a>";
else
	$ret = "<a href=\"$DEFAULTBASEURL/\">�� �������</a>";

?>
<h2>������� ������!</h2>
<p><?= $ret ?></p>
<?

stdfoot();

?>