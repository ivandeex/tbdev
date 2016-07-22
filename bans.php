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

require "include/bittorrent.php";

dbconn(false);

loggedinorreturn();

if (get_user_class() < UC_MODERATOR)
  die;

$remove = intval($_GET['remove']);
if (is_valid_id($remove))
{
  $res = sql_query("SELECT first, last FROM bans WHERE id=$remove") or sqlerr(__FILE__, __LINE__);
  $ip = mysql_fetch_array($res);
  $first = long2ip($ip["first"]);
  $last = long2ip($ip["last"]);
  sql_query("DELETE FROM bans WHERE id=$remove") or sqlerr(__FILE__, __LINE__);
  write_log("��� IP ������ ����� $remove (".($first == $last?$fisrt:"������ � $first �� $last").") ��� ����� ������������� $CURUSER[username].");
}

function is_good_ip($ip_addr) {
	if (preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/", $ip_addr)) {
		$parts = explode(".",$ip_addr);
		foreach ($parts as $ip_parts) {
			if (intval($ip_parts) > 255 || intval($ip_parts) < 0)
				return false;
		}
		return true;
	} else
		return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && get_user_class() >= UC_ADMINISTRATOR)
{
	$first = trim($_POST["first"]);
	$last = trim($_POST["last"]);
	$comment = trim($_POST["comment"]);
	if (!$first || !$last || !$comment)
		stderr($tracker_lang['error'], $tracker_lang['missing_form_data']);
	if (!is_good_ip($first) || !is_good_ip($last))
		stderr('������', '� �� ��� �� ����� ������ ���������� ����?');
	$first = ip2long($first);
	$last = ip2long($last);
	if ($first == -1 || $last == -1)
		stderr($tracker_lang['error'], $tracker_lang['invalid_ip']);
	$comment = sqlesc(htmlspecialchars($comment));
	$added = sqlesc(get_date_time());
	sql_query("INSERT INTO bans (added, addedby, first, last, comment) VALUES($added, $CURUSER[id], $first, $last, $comment)") or sqlerr(__FILE__, __LINE__);
	write_log("IP ������ �� ".long2ip($first)." �� ".long2ip($last)." ���� �������� ������������� $CURUSER[username].");
	header("Location: $DEFAULTBASEURL/bans.php");
	die;
}

gzip();

$res = sql_query("SELECT * FROM bans ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);

stdhead($tracker_lang['bans']);

if (mysql_num_rows($res) == 0)
  print("<p align=\"center\"><b>".$tracker_lang['nothing_found']."</b></p>\n");
else
{
  //print("<table border=1 cellspacing=0 cellpadding=5>\n");
  begin_table();
  print("<tr><td class=\"colhead\" colspan=\"6\">��������� IP</td></tr>\n");
  print("<tr><td class=\"colhead\">��������</td><td class=\"colhead\" align=\"left\">������ IP</td><td class=\"colhead\" align=\"left\">��������� IP</td>".
    "<td class=\"colhead\" align=\"left\">���</td><td class=\"colhead\" align=\"left\">�����������</td><td class=\"colhead\">����� ���</td></tr>\n");

  while ($arr = mysql_fetch_assoc($res))
  {
  	$r2 = sql_query("SELECT username FROM users WHERE id=$arr[addedby]") or sqlerr(__FILE__, __LINE__);
  	$a2 = mysql_fetch_assoc($r2);
	$arr["first"] = long2ip($arr["first"]);
	$arr["last"] = long2ip($arr["last"]);
 	  print("<tr><td class=\"row1\">$arr[added]</td><td class=\"row1\" align=\"left\">$arr[first]</td><td  class=\"row1\" align=\"left\">$arr[last]</td><td  class=\"row1\" align=\"left\"><a href=\"userdetails.php?id=$arr[addedby]\">$a2[username]".
 	    "</a></td><td  class=\"row1\" align=\"left\">".$arr["comment"]."</td><td  class=\"row1\"><a href=\"bans.php?remove=$arr[id]\">����� ���</a></td></tr>\n");
  }
  end_table();
}

if (get_user_class() >= UC_ADMINISTRATOR)
{
	//print("<table border=1 cellspacing=0 cellpadding=5>\n");
  print("<br />\n");
  print("<form method=\"post\" action=\"bans.php\">\n");
  begin_table();
	print("<tr><td class=\"colhead\" colspan=\"2\">�������� IP �����</td></tr>");
	print("<tr><td class=\"rowhead\">������ IP</td><td class=\"row1\"><input type=\"text\" name=\"first\" size=\"40\"/></td></tr>\n");
	print("<tr><td class=\"rowhead\">��������� IP</td><td class=\"row1\"><input type=\"text\" name=\"last\" size=\"40\"/></td></tr>\n");
	print("<tr><td class=\"rowhead\">�����������</td><td class=\"row1\"><input type=\"text\" name=\"comment\" size=\"40\"/></td></tr>\n");
	print("<tr><td class=\"row1\" align=\"center\" colspan=\"2\"><input type=\"submit\" value=\"��������\" class=\"btn\"/></td></tr>\n");
	end_table();
	print("</form>\n");
}

stdfoot();

?>