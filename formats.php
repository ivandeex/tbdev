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

loggedinorreturn();

if ($_GET["format"])
{
     if ($_GET["format"]==1)
     header("Location: formatss.php?form=mov");
     elseif ($_GET["format"]==2)
     header("Location: formatss.php?form=all");
 exit();
}

stdhead("�������");
?>


<form action="<?=$PHP_SELF;?>" method="get" name="form1">
<table border="1" cellspacing="0" cellpadding="10" width=20%>

<tr><td align="center" colspan="2"><p align=center><select name=format><option value='1' >������� �����</option><option value='2' >������� ������</option></select></p><br /><input type="submit" class=btn value="����������!"></td></tr>
</table>
</form>

<?
stdfoot();

?>