<?
define ('UCPCREAMLIFE',true);
include_once "classes/init.php";
require_once 'classes/mysql.php';
include_once "classes/functions.php";
$db->Connect(MySQL_USER,MySQL_PASSWORD,MySQL_DB,MySQL_HOSTNAME);


	$user_nick = $_GET['image'];
	$user_nick = $user_class->safe($user_nick);
	$user_nick = $db->safesql(htmlspecialchars(stripslashes(trim($user_nick))));
	$array = $db->super_query("SELECT `Name`,`Race`,`Age`,`Model` FROM `players` WHERE `Name`='$user_nick'");
	if(!$array['Name']) {die("Такого пользователя не существует!");}
	$array = $db->super_query("SELECT `Name`,`Race`,`Age`,`Model` FROM `players` WHERE `Name`='$user_nick'");
	$pic = ImageCreateFrompng("ubs/bg.png");
	header("Content-type: image/png");
	$color = ImageColorAllocate($pic, 255, 255, 255);
	$font = "fonts/arial.ttf";
	$h = ImageSY($pic) - 4; //высота
	$w = 20; //ширина
	switch($array['Race'])
	{
		case 0:
		$Rasa = "Европеец";		
		$razmer = $w+90;
		break;
		case 1:
		$Rasa = "Афро-американец";
		$razmer = $w+65;
		break;
		case 2:
		$Rasa = "Азиат";
		$razmer = $w+94;
		break;
		case 3:
		$Rasa = "Латино-американец";
		$razmer = $w+65;
		break;
		default:
		$Rasa = "Не определено";
		$razmer = $w+88;
	}
	ImageTTFtext($pic, 8, 0, $w+105, $h-90, $color, $font, "Персонаж:");
	ImageTTFtext($pic, 8, 0, $w+170, $h-90, $color, $font, "$array[TABLE_USERS_NAME]");
	ImageTTFtext($pic, 8, 0, $w+105, $h-60, $color, $font, "Национальность:");
    ImageTTFtext($pic, 8, 0, $w+205, $h-60, $color, $font, "$Rasa");
	ImageTTFtext($pic, 8, 0, $w+105, $h-30, $color, $font, "Возраст:");
	ImageTTFtext($pic, 8, 0, $w+155, $h-30, $color, $font, "$array[Age]");
	$skin = $array[TABLE_USERS_SKIN];
	$pic_a = ImageCreateFrompng("skins/2/".$skin.".png");
	imagecopy ($pic, $pic_a, 27, +23, 0, 0, 55, 100);
	$pic_a = ImageCreateFrompng("".TEMPLATE_DIR."/".TEMPLATE."/images/nakleika.png");
	imagecopy ($pic, $pic_a, 0, +0, 0, 0, 350, 150);
	Imagepng($pic);
	ImageDestroy($pic_a);
	ImageDestroy($pic);
	$db->close ();
?>