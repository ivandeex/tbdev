<?php
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}
$count = get_row_count("indexreleases");
$blocktitle = "������".(get_user_class() >= UC_MODERATOR ? "<font class=\"small\"> - [<a class=\"altlink\" href=\"indexadd.php\"><b>�����</b></a>]</font>" : "");
//$content .= "<table cellspacing=\"0\" cellpadding=\"5\" width=\"100%\"><tr><td>";
if (!$count) {
	$content .= "��� ������� �� �������...";
} else {
	include "include/codecs.php";
	$perpage = 5;
	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $_SERVER["PHP_SELF"] . "?" );
	$content .= $pagertop;
	$content .= "</td></tr>";
	$res = sql_query("SELECT indexreleases.*, categories.id AS catid, categories.name AS catname, categories.image AS catimage FROM indexreleases LEFT JOIN categories ON indexreleases.cat = categories.id ORDER BY id DESC $limit") or sqlerr(__FILE__, __LINE__);
	while ($release = mysql_fetch_array($res)) {
		$catid = $release["catid"];
		$catname = $release["catname"];
		$catimage = $release["catimage"];
		$content .= "<tr><td>";
		$content .= "<table width=\"100%\" class=\"main\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">";
		$content .= "<tr><td class=\"colhead\" colspan=\"2\" align=\"center\">".htmlspecialchars($release["name"]).(get_user_class() >= UC_MODERATOR ? "<font class=\"small\"> - [<a class=\"altlink_white\" href=\"indexedit.php?action=edit&id=$release[id]&returnto=" . urlencode($_SERVER['PHP_SELF']) . "\"><b>�������������</b></a>][<a class=\"altlink_white\" href=\"indexdelete.php?action=delete&id=$release[id]&returnto=" . urlencode($_SERVER['PHP_SELF']) . "\"><b>�������</b></a>]</font>" : "")."</td></tr>";
		$content .= "<tr valign=\"top\"><td align=\"center\" width=\"160\"><img src=\"$release[poster]\" width=\"160\" border=\"0\" /></td>";
		$content .= "<td><div align=\"left\">".(!empty($catname) ? "<a href=\"browse.php?cat=$catid\"><img src=\"pic/cats/$catimage\" alt=\"$catname\" title=\"$catname\" align=\"right\" border=\"0\" /></a>" : "")."<b>����: </b>".htmlspecialchars($release["genre"])."<br /><b>��������: </b>".htmlspecialchars($release["director"])."<br /><b>� �����: </b>".htmlspecialchars($release["actors"])."<br /></div><div align=\"left\"><hr align=\"left\" width=\"85%\" color=\"#000000\" size=\"1\"></div><div align=\"left\"><b>� ������: </b>".htmlspecialchars($release["descr"])."<br /></div><div align=\"left\"><hr align=\"left\" width=\"85%\" color=\"#000000\" size=\"1\"></div><div align=\"left\"><b>��������: </b>".$release_quality[$release["quality"]]."<br /><b>�����: </b>".$video_codec[$release["video_codec"]].", $release[video_kbps] ��/�, $release[video_size]<br /><b>�����: </b>".$audio_codec[$release["audio_codec"]].", $release[audio_kbps] ��/�<br /><b>�����������������: </b>$release[time]<br /><b>����: </b>".$audio_lang[$release["audio_lang"]]."<br /><b>�������: </b>".$audio_trans[$release["audio_trans"]]."</div><div align=\"right\">".($release[imdb] ? "[<a href=\"$release[imdb]\" class=\"online\">IMDB</a>] " : "")."[<a href=\"details.php?id=$release[torrentid]\" alt=\"$release[name]\" title=\"$release[name]\"><b>������</b></a>]</div></td>";
		$content .= "</tr>";
		$content .= "</table>";
		$content .= "</td></tr>";
	}
	$content .= "<tr><td>";
	$content .= $pagerbottom;
	$content .= "</td></tr>";
}
//$content .= "</table>";
?>