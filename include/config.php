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

# IMPORTANT: Do not edit below unless you know what you are doing!
if(!defined('IN_TRACKER') && !defined('IN_ANNOUNCE'))
  die('Hacking attempt!');

$SITE_ONLINE = true;
//$SITE_ONLINE = local_user();
//$SITE_ONLINE = false;

$max_torrent_size = 1024 * 1024;
$announce_interval = 60 * 30;
$signup_timeout = 86400 * 3;
$minvotes = 1;
$max_dead_torrent_time = 6 * 3600;

// Max users on site
$maxusers = 10000;
$torrent_dir = "torrents";

$doxpath = "dox";

// Email for sender/return path.
$SITEEMAIL = "noreply@" . $_SERVER["HTTP_HOST"];

$SITENAME = "TBDev Yuna Scatari Edition";

$autoclean_interval = 900;
$pic_base_url = "./pic/";

// [BEGIN] Custom variables from Yuna Scatari
// TTL
$use_ttl = 1; // ������������ TTL.
$ttl_days = 28; // ������� ���� ������� ����� ���� �� TTL.
$avatar_max_width = 100; // ������������ ������ �������.
$avatar_max_height = 100; // ������������ ������ �������.
$points_per_hour = 1; // ������� ��������� ������� � ���, ���� ������������ ��������.
$points_per_cleanup = $points_per_hour*($autoclean_interval/3600); // Don't change it!
$default_theme = "TBDev"; // ���� �� ��������� ��� ������.
$nc = "no"; // �� ���������� �� ������ ����� � ��������� �������.
$default_language = "russian"; // ���� ������� �� ���������.
$deny_signup = 0; // ��������� �����������. 1 = ����������� ���������, 0 = ����������� ��������.
$allow_invite_signup = 1; // ��������� ����������� ����� �����������. 1 = ���������, 0 = �� ���������.
$ctracker = 1; // Use CrackerTracker - anti-cracking system. I personaly think it's un-needed...
$use_email_act = 1; // ������������ ��������� �� �����, ����� - �������������� ��������� ��� �����������.
$use_wait = 1; // ������������ �������� �� ������������� ������� ����� ������ �������.
$use_lang = 1; // �������� �������� �������. ��������� ���� �� ������ ��������� ������� � ������ ����� - ����� ��� ����� �� ������� ������ ������ ������.
$use_captcha = 1; // ������������ ������ �� ����-�����������.
$use_blocks = 1; // ������������ ������� ������. 1 - ��, 0 - ���. ���� �� ��������� �� �����-������ � �� ������� ������ �� ������ ��������� �������� ��� ������ � �������.
$use_gzip = 1; // ������������ ������ GZip �� ���������.
$use_ipbans = 1; // ������������ ������� ������������ IP-�������. 0 - ���, 1 - ��.
$use_sessions = 1; // ������������ ������. 0 - ���, 1 - ��.
$smtptype = "advanced";
// [END] Custom variables from Yuna Scatari

?>