<?php

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

dbconn();
loggedinorreturn();

if (get_user_class() < UC_ADMINISTRATOR)
	stderr($tracker_lang['error'], "Permission denied.");

$action = $_GET["action"];

//   Delete News Item    //////////////////////////////////////////////////////

if ($action == 'delete')
{
	$newsid = (int)$_GET["newsid"];
  if (!is_valid_id($newsid))
  	stderr($tracker_lang['error'],"Invalid news item ID - Code 1.");

  $returnto = htmlentities($_GET["returnto"]);

  $sure = $_GET["sure"];
  if (!$sure)
    stderr("������� �������","�� ������������� ������ ������� ��� �������? �������\n" .
    	"<a href=?action=delete&newsid=$newsid&returnto=$returnto&sure=1>����</a> ���� �� �������.");

  sql_query("DELETE FROM news WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

	if ($returnto != "")
		header("Location: $returnto");
	else
		$warning = "������� <b>�������</b> �������";
}

//   Add News Item    /////////////////////////////////////////////////////////

if ($action == 'add')
{

	$subject = $_POST["subject"];
	if (!$subject)
		stderr($tracker_lang['error'],"���� ������� �� ����� ���� ������!");

	$body = $_POST["body"];
	if (!$body)
		stderr($tracker_lang['error'],"���� ������� �� ����� ���� ������!");

	$added = $_POST["added"];
	if (!$added)
		$added = sqlesc(get_date_time());

  sql_query("INSERT INTO news (userid, added, body, subject) VALUES (".
  	$CURUSER['id'] . ", $added, " . sqlesc($body) . ", " . sqlesc($subject) . ")") or sqlerr(__FILE__, __LINE__);
	if (mysql_affected_rows() == 1)
		$warning = "������� <b>������� ���������</b>";
	else
		stderr($tracker_lang['error'],"������-��� ��������� ���-�� ����������.");
}

//   Edit News Item    ////////////////////////////////////////////////////////

if ($action == 'edit')
{

	$newsid = (int)$_GET["newsid"];

  if (!is_valid_id($newsid))
  	stderr($tracker_lang['error'],"Invalid news item ID - Code 2.");

  $res = sql_query("SELECT * FROM news WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) != 1)
	  stderr($tracker_lang['error'], "No news item with ID.");

	$arr = mysql_fetch_array($res);

  if ($_SERVER['REQUEST_METHOD'] == 'POST')
  {
  	$body = $_POST['body'];
  	$subject = $_POST['subject'];


	$subject = $_POST["subject"];
	if ($subject == "")
		stderr($tracker_lang['error'],"���� ������� �� ����� ���� ������!");

    if ($body == "")
    	stderr($tracker_lang['error'], "���� ������� �� ����� ���� ������!");

    $body = sqlesc($body);

    $subject = sqlesc($subject);

    $editedat = sqlesc(get_date_time());

    sql_query("UPDATE news SET body=$body, subject=$subject WHERE id=$newsid") or sqlerr(__FILE__, __LINE__);

    $returnto = htmlentities($_POST['returnto']);

		if ($returnto != "")
			header("Location: $returnto");
		else
			$warning = "������� <b>�������</b> ���������������";
  }
  else
  {
 	 	$returnto = htmlentities($_GET['returnto']);
	  stdhead("�������������� �������");
	  print("<form method=post name=news action=?action=edit&newsid=$newsid>\n");
	  print("<table border=1 cellspacing=0 cellpadding=5>\n");
	  print("<tr><td class=colhead>�������������� �������<input type=hidden name=returnto value=$returnto></td></tr>\n");
	  print("<tr><td>����: <input type=text name=subject maxlength=70 size=50 value=\"" . htmlspecialchars($arr["subject"]) . "\"/></td></tr>");
	  print("<tr><td style='padding: 0px'>");
	  textbbcode("news","body",htmlspecialchars($arr["body"]));
	  //<textarea name=body cols=145 rows=5 style='border: 0px'>" . htmlspecialchars($arr["body"]) . 
	  print("</textarea></td></tr>\n");
	  print("<tr><td align=center><input type=submit value='���������������'></td></tr>\n");
	  print("</table>\n");
	  print("</form>\n");
	  stdfoot();
	  die;
  }
}

//   Other Actions and followup    ////////////////////////////////////////////

stdhead("�������");
if ($warning)
	print("<p><font size=-3>($warning)</font></p>");
print("<form method=post name=news action=?action=add>\n");
print("<table border=1 cellspacing=0 cellpadding=5>\n");
print("<tr><td class=colhead>�������� �������</td></tr>\n");
print("<tr><td>����: <input type=text name=subject maxlength=40 size=50 value=\"" . htmlspecialchars($arr["subject"]) . "\"/></td></tr>");
print("<tr><td style='padding: 0px'>");
textbbcode("news","body","");
//<textarea name=body cols=145 rows=5 style='border: 0px'>
print("</textarea></td></tr>\n");
print("<tr><td align=center><input type=submit value='��������' class=btn></td></tr>\n");
print("</table></form><br /><br />\n");

$res = sql_query("SELECT * FROM news ORDER BY added DESC") or sqlerr(__FILE__, __LINE__);

if (mysql_num_rows($res) > 0)
{


 	begin_main_frame();
	begin_frame();

	while ($arr = mysql_fetch_array($res))
	{
		$newsid = $arr["id"];
		$body = $arr["body"];
		$subject = $arr["subject"];
	  $userid = $arr["userid"];
	  $added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " �����)";

    $res2 = sql_query("SELECT username, donor FROM users WHERE id = $userid") or sqlerr(__FILE__, __LINE__);
    $arr2 = mysql_fetch_array($res2);

    $postername = $arr2["username"];

    if ($postername == "")
    	$by = "���������� [$userid]";
    else
    	$by = "<a href=userdetails.php?id=$userid><b>$postername</b></a>" .
    		($arr2["donor"] == "yes" ? "<img src=pic/star.gif alt='Donor'>" : "");

	  print("<p class=sub><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>");
    print("��������� $added&nbsp;-&nbsp;$by");
    print(" - [<a href=?action=edit&newsid=$newsid><b>�������������</b></a>]");
    print(" - [<a href=?action=delete&newsid=$newsid><b>�������</b></a>]");
    print("</td></tr></table></p>\n");

	  begin_table(true);
      print("<tr valign=top><td><b>".$subject."</b></td></tr>\n");
	  print("<tr valign=top><td class=comment>".format_comment($body)."</td></tr>\n");
	  end_table();
	}
	end_frame();
	end_main_frame();
}
else
  stdmsg("��������", "�������� ���!");
stdfoot();
die;
?>