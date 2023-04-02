<?
define ( 'UCPCREAMLIFE', true );
Header("Content-Type: text/html; charset=UTF-8"); 
session_start();
include_once "classes/init.php";
require_once 'classes/mysql.php';
include_once "classes/functions.php";
require_once 'classes/template.php';
require 'classes/monitoring.php';
$query = new SampQueryAPI(SAMP_SERVER_IP, SAMP_SERVER_PORT);

$tpl = new template("".TEMPLATE_DIR."/".TEMPLATE."/index.tpl"); //Подключаем страницу.
$tpl->set('{SAMP_SERVER_IP}',SAMP_SERVER_IP);
$tpl->set('{SAMP_SERVER_PORT}',SAMP_SERVER_PORT);
$tpl->set('{SITE_TITLE}', SITE_NAME);
$tpl->set('{SITE_TAGS}', SITE_TAGS);
$tpl->set('{COPYRIGHT}','Powered by YourGame.su');
$tpl->set('{USERNAME}', $_SESSION['name']);
$tpl->set('{PREFIX}', PREFIX);

$tpl->set('{TEMPLATE_DIR}', TEMPLATE_DIR); //Папка с шаблонами.
$tpl->set('{TEMPLATE}', TEMPLATE); //Текущий шаблон.
$db->connect(MySQL_USER,MySQL_PASSWORD,MySQL_DB,MySQL_HOSTNAME);
mysql_query("set names utf8");


if($user_class->IsLogin()) 
{
	$admin = $db->super_query("SELECT `AdminLevel` FROM `".TABLE_USERS."` WHERE `ID`='".SESSION_ID."'");
	if ($admin['AdminLevel'] == 2) $ADMIN_MENU = '
	<div class="box_top"><div class="box_top_text">Админ-меню</div></div>
	<div>
	<div class="box_content" style="padding:0px;">
	<div id="menu_content"><a href="?'.PREFIX.'=admin&requests=1"><b>Заявки на регистрацию</b></a></div><div id="line_menu"></div>
	</div></div><br>';
	else if ($admin['AdminLevel'] == 3 or $admin['AdminLevel'] == 4 or $admin['AdminLevel'] == 5 or $admin['AdminLevel'] == 6) $ADMIN_MENU = '<div class="box_top"><div class="box_top_text">Админ-меню</div></div> 
	<div><div class="box_content" style="padding:0px;">
	<div id="menu_content"><a href="?'.PREFIX.'=admin&requests=1"><b>Заявки на регистрацию</b></a></div><div id="line_menu"></div>
	<div id="menu_content"><a href="?'.PREFIX.'=admin&players=1&all=1"><b>Список пользователей</b></a></div><div id="line_menu"></div>
	<div id="menu_content"><a href="?'.PREFIX.'=admin&players=1&active=1"><b>Активированные пользователи</b></a></div><div id="line_menu"></div>
	<div id="menu_content"><a href="?'.PREFIX.'=admin&players=1&noactive=1"><b>Неактив.пользователи</b></a></div><div id="line_menu"></div>
	<div id="menu_content"><a href="?'.PREFIX.'=admin&players=1&banned=1"><b>Заблокированные пользователи</b></a></div><div id="line_menu"></div>
	<div id="menu_content"><a href="?'.PREFIX.'=admin&info=1"><b>Информация о сервере</b></a></div><div id="line_menu"></div>

</div></div><br>';
	$tpl->set('{MENU}','
	<div class="box_top">
	<div class="box_top_text">Юзер-меню</div></div> 
	<div>
	<div class="box_content" style="padding:0px;">
	<div id="menu_content"><a href="?'.PREFIX.'=cp&stats=1"><b>Моя статистика</b></a></div><div id="line_menu"></div>
	<div id="menu_content"><a href="?'.PREFIX.'=user&name='.SESSION_NAME.'"><b>Мой профиль</b></a></div><div id="line_menu"></div>
	<div id="menu_content"><a href="?'.PREFIX.'=cp&history=1"><b>Лог посещений</b></a></div><div id="line_menu"></div>
	<div id="menu_content"><a href="?'.PREFIX.'=private"><b>Приватность</b></a></div><div id="line_menu"></div>
	<div id="menu_content"><a href="?'.PREFIX.'=biography"><b>Биография</b></a></div><div id="line_menu"></div>
	<div id="menu_content"><a href="?'.PREFIX.'=change"><b>Сменить пароль</b></a></div><div id="line_menu"></div>
	<div id="menu_content"><a href="?'.PREFIX.'=cp&logout=1"><b>Выход</b></a></div><div id="line_menu"></div>
	</div></div><br>
	'.$ADMIN_MENU.'
	');
}
else $tpl->set('{MENU}','
<div class="box_top">
<div class="box_top_text">Юзер-меню</div></div> 
<div>
<div class="box_content" style="padding:0px;">
<div id="menu_content"><a href="index.php?act=login"><b>Вход</b></a></div><div id="line_menu"></div>
<div id="menu_content"><a href="index.php?act=register"><b>Регистрация</b></a></div><div id="line_menu"></div>
</div></div>
');
if($user_class->IsLogin())
{
	switch ($_GET[PREFIX])
	{ 
		case 'admin' : include "pages/admin.php"; break; 
		case 'cp' : include "pages/cp.php"; break; 
		case 'user' : include "pages/user.php"; break; 
		case 'rules' : include "pages/rules.php"; break; 
		case 'lastpassword' : include "pages/lastpass.php"; break; 
		case 'change' : include "pages/changepass.php"; break; 
		case 'users' : include "pages/users.php"; break; 
		case 'private' : include "pages/private.php"; break; 
		case 'biography' : include "pages/biography.php"; break; 
		default :  include "pages/general.php"; 
	}
}
else
{
	switch ($_GET[PREFIX])
	{ 
		case 'register' : include "pages/register.php"; break;
		case 'login' : include "pages/login.php"; break;
		case 'user' : include "pages/user.php"; break; 
		case 'rules' : include "pages/rules.php"; break; 
		case 'users' : include "pages/users.php"; break; 
		default :  include "pages/general.php"; 
	}
}

$tpl->parse(); 
$db->close ();?>