<?
if(!defined('UCPCREAMLIFE')) die("Hacking attempt!");
class admin_Functions
{
	function AcceptUser($name)
	{
		$db = new Database;
		$name = $db->safesql( htmlspecialchars( trim( $name ) ) );
		$name = preg_replace('#\s+#i', ' ', $name);
		$tm = time();
		$one = $db->query("INSERT INTO `admin_logs` (`type`,`Admin_Name`,`Player_Name`,`Date`) VALUES (1,'".$_SESSION['name']."','$name','$tm')");
		$two = $db->query("UPDATE `".TABLE_USERS."` SET `Activated`='1' WHERE `".TABLE_USERS_NAME."`='$name'");
		if(!$one or !$two) return false;
		$array = $db->super_query("SELECT `Email` FROM `".TABLE_USERS."` WHERE `".TABLE_USERS_NAME."` = '$name'");
		$email = $array['Email'];
		$result_email = mail($email, "CreamLife RP", "Администрация успешно одобрила вашу заявку на создание аккаунта!\nВаши данные:\nНик: $name\nПароль: Указан при регистрации.\nIP сервера: 77.220.182.172:7777.\nПриятной игры!");
		return true;
	}
	function DeclineUser($name,$reason)
	{
		$db = new Database;
		$name = $db->safesql( htmlspecialchars( trim( $name ) ) );
		$name = preg_replace('#\s+#i', ' ', $name);
		$reason = $db->safesql( htmlspecialchars( trim( $reason ) ) );
		$tm = time();
		if($name != "" or $reason != "")
		{
			$one = $db->query("UPDATE `".TABLE_USERS."` SET `Activated`='2' WHERE `".TABLE_USERS_NAME."`='$name'");
			$two = $db->query("UPDATE `".TABLE_OTHER."` SET `reason`='$reason' WHERE `author`='$name'");
			$rub = $db->query("UPDATE `".TABLE_OTHER."` SET `admin_decline`='".$_SESSION['name']."' WHERE `author`='$name'");
			$four = $db->query("INSERT INTO `admin_logs` (`type`,`Admin_Name`,`Player_Name`,`Reason`,`Date`) VALUES (2,'".$_SESSION['name']."','$name','$reason','$tm')");
			$five = $db->query("UPDATE `".TABLE_USERS."` SET `attempts`=attempts - 1 WHERE `".TABLE_USERS_NAME."`='$name'");
			if(!$one or !$two or !$rub or !$four or !$five) return false;
		}
		$array = $db->super_query("SELECT `Email`,`attempts` FROM `".TABLE_USERS."` WHERE `".TABLE_USERS_NAME."` = '$name'");
		if($array['attempts'] == 0)
		{
			$one = $db->query("INSERT INTO `black_list` (`nick`,`email`) VALUES ('$name','$array[Email]')");
			$two = $db->query("DELETE FROM `".TABLE_USERS."` WHERE `".TABLE_USERS_NAME."`='$name' LIMIT 1");
			$rub = $db->query("DELETE FROM `".TABLE_OTHER."` WHERE `author`='$name' LIMIT 1");
			$msg = "Ваш аккаунт удален.\nПричина: Исчерпание попыток на подавание заявок.";
			if(!$one or !$two or !$rub) return false;
		}
		else
		{
			$msg = "Ваша заявка была отклонена администрацией.\nПричина: $reason";
		}
		$email = $array['Email'];
		$result_email = mail($email, "CreamLife RP", $msg);
		return true;
	}
	function DeleteUser($name,$reason)
	{
		$db = new Database;
		$tm = time();
		$name = $db->safesql( htmlspecialchars( trim( $name ) ) );
		$name = preg_replace('#\s+#i', ' ', $name);
		$reason = $db->safesql( htmlspecialchars( trim( $reason ) ) );
		$array = $db->super_query("SELECT `Email` FROM `".TABLE_USERS."` WHERE `".TABLE_USERS_NAME."` = '$name'");
		$one = $db->query("INSERT INTO `black_list` (`nick`,`email`) VALUES ('$name','$array[Email]')");
		$two = $db->query("DELETE FROM `".TABLE_USERS."` WHERE `".TABLE_USERS_NAME."`='$name' LIMIT 1");
		$rub = $db->query("DELETE FROM `".TABLE_OTHER."` WHERE `author`='$name' LIMIT 1");
		$four = $db->query("INSERT INTO `admin_logs` (`type`,`Admin_Name`,`Player_Name`,`Reason`,`Date`) VALUES (3,'".$_SESSION['name']."','$name','$reason','$tm')");
		if(!$one or !$two or !$rub or !$four) return false;
		else return true;
	}
}


class user_Functions
{
	function IsBanned($nick)
	{
		$db = new Database;
		$post = $nick;
		$name = $db->safesql( htmlspecialchars( trim( $post ) ) );
		$name = preg_replace('#\s+#i', ' ', $name);
		if($name != "")
		{
			$result = $db->super_query("SELECT * FROM `".TABLE_USERS."` WHERE `".TABLE_USERS_NAME."` = '$name' and `Banned` = '1'");
			if($result != false) return 1;
			else return 0;
		}
	}
	function IsLockNick($nick)
	{
		$db = new Database;
		$post = $nick;
		$name = $db->safesql( htmlspecialchars( trim( $post ) ) );
		$name = preg_replace('#\s+#i', ' ', $name);
		if($name != "")
		{
			$result = $db->super_query("SELECT * FROM `black_list` WHERE `nick` = '$name'");
			if($result != false) return 1;
			else return 0;
		}
	}
	function IsLockMail($mail)
	{
		$db = new Database;
		$post = $mail;
		$not_allow_symbol = array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "¬", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'", " " );
		$email = $db->safesql(trim( str_replace( $not_allow_symbol, '', strip_tags( stripslashes( $post ) ) ) ) );
		if($email != "")
		{
			$result = $db->super_query("SELECT * FROM `black_list` WHERE `email` = '$email'");
			if($result != false) return 1;
			else return 0;
		}
	}
	function IsExist($nick)
	{
		$db = new Database;
		$post = $nick;
		$name = $db->safesql( htmlspecialchars( trim( $post ) ) );
		$name = preg_replace('#\s+#i', ' ', $name);
		if($name != "")
		{
			$result = $db->super_query("SELECT * FROM `".TABLE_USERS."` WHERE `".TABLE_USERS_NAME."` = '$name'");
			if($result != false) return 1;
			else return 0;
		}
	}
	function IsRequest($nick)
	{
		$db = new Database;
		$post = $nick;
		$name = $db->safesql( htmlspecialchars( trim( $post ) ) );
		$name = preg_replace('#\s+#i', ' ', $name);
		if($name != "")
		{
			$result = $db->super_query("SELECT * FROM `".TABLE_OTHER."` WHERE `author` = '$name'");
			if($result != false) return true;
			else return false;
		}
	}
	function DeleteRequest($nick)
	{
		$db = new Database;
		$array = $db->query("DELETE FROM `".TABLE_OTHER."` WHERE `author`='$nick' LIMIT 1");
		if($array) return true;
		else return false;
	}
	function IsRegisterID($id)
	{
		$db = new Database;
		$id = $db->safesql( htmlspecialchars( trim( $id ) ) );
		$id = preg_replace('#\s+#i', ' ', $id);
		if($id != "")
		{
			$result = $db->super_query("SELECT * FROM `".TABLE_USERS."` WHERE `".TABLE_USERS_ID."` = '$id'");
			if($result != false) return 1;
			else return 0;
		}
	}
	function IsExist2($mail)
	{
		$db = new Database;
		$post = $mail;
		$not_allow_symbol = array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "¬", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'", " " );
		$email = $db->safesql(trim( str_replace( $not_allow_symbol, '', strip_tags( stripslashes( $post ) ) ) ) );
		if($email != "")
		{
			$result = $db->super_query("SELECT * FROM `".TABLE_USERS."` WHERE `Email` = '$email'");
			if($result != false) return 1;
			else return 0;
		}
	}
	function IsExist3($login,$email)
	{
		$db = new Database;
		$post = $login;
		$post2 = $email;
		$name = $db->safesql( htmlspecialchars( trim( $post ) ) );
		$name = preg_replace('#\s+#i', ' ', $name);
		$not_allow_symbol = array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "¬", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'", " " );
		$mail = $db->safesql(trim( str_replace( $not_allow_symbol, '', strip_tags( stripslashes( $post2 ) ) ) ) );
		if($mail != "" or $name != "")
		{
			$result = $db->super_query("SELECT * FROM `".TABLE_USERS."` WHERE `".TABLE_USERS_NAME."` = '$name' AND `Email` = '$mail'");
			if($result != false) return 1;
			else return 0;
		}
	}
	function Register($nick,$password,$mail,$sex,$rasa,$lang,$vopros1,$vopros2,$vopros3,$vopros4,$vopros5)
	{
		if(($pass_result = $this->check_pass($password)) != 1)
		return $pass_result;
		$db = new Database;
		$user_nick = $nick;
		$user_pass = $password;
		$user_ip = $this->getIP();
		$user_nick = $db->safesql(htmlspecialchars(trim($user_nick)));
		$user_nick = preg_replace('#\s+#i', ' ', $user_nick);
		$user_pass = $db->safesql(trim($user_pass));
		$sex = $db->safesql($sex);
		$rasa = $db->safesql($rasa);
		$rasa = $rasa-1;
		$lang = $db->safesql($lang);
		$vopros1 = $db->safesql(htmlspecialchars(trim($vopros1)));
		$vopros2 = $db->safesql(htmlspecialchars(trim($vopros2)));
		$vopros3 = $db->safesql(htmlspecialchars(trim($vopros3)));
		$vopros4 = $db->safesql(htmlspecialchars(trim($vopros4)));
		$vopros5 = $db->safesql(htmlspecialchars(trim($vopros5)));
		$user_nick = str_replace("/","",$user_nick);
		$user_nick = str_replace(".","",$user_nick);
		$user_nick = str_replace("`","",$user_nick);
		$user_nick = str_replace(" ","",$user_nick);
		$user_nick = preg_replace('#\s+#i', ' ', $user_nick);
		$post = $mail;
		$not_allow_symbol = array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "¬", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'", " " );
		$email = $db->safesql(trim( str_replace( $not_allow_symbol, '', strip_tags( stripslashes( $post ) ) ) ) );
		switch($lang)
		{
			case 1: $Languane = "Italian"; break;
			case 2: $Languane = "Espanol"; break;
			case 3: $Languane = "Japanese"; break;
			case 4: $Languane = "France"; break;
			case 5: $Languane = "Russian"; break;
			case 6: $Languane = "German"; break;
			default: return 1;
		}
		$tm = time();
		$one = $db->query("INSERT INTO `".TABLE_USERS."` (`".TABLE_USERS_NAME."`,`".TABLE_USERS_PASSWORD."`,`Email`,`Activated`,`Sex`,`Age`,`Race`,`$Languane`,`Level`,`ip_reg`,`reg_date`,`last_date`,`attempts`) VALUES ('$user_nick','$user_pass','$email','0','$sex','$age','$rasa','1','1','$user_ip','$tm','$tm','3')");
		$two = $db->query("INSERT INTO `".TABLE_OTHER."` (`author`,`otvet1`,`otvet2`,`otvet3`,`otvet4`,`otvet5`) VALUES ('$user_nick','$vopros1','$vopros2','$vopros3','$vopros4','$vopros5')");
		if(!$one or !$two) return 0;
		else return 1;
	}
	function lastpassword($email,$login)
	{
		$db = new Database;
		$post = $login;
		$post2 = $email;
		$name = $db->safesql( htmlspecialchars( trim( $post ) ) );
		$name = preg_replace('#\s+#i', ' ', $name);
		$not_allow_symbol = array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "¬", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'", " " );
		$mail = $db->safesql(trim( str_replace( $not_allow_symbol, '', strip_tags( stripslashes( $post2 ) ) ) ) );
		$array = $db->super_query("SELECT `".TABLE_USERS_PASSWORD."` FROM `".TABLE_USERS."` WHERE `".TABLE_USERS_NAME."` = '$name' and `Email` = '$mail'");
		$pass = $array[TABLE_USERS_PASSWORD];
		$title = 'Напоминание пароля игрока '.$login.' [Cream Life] ';
		$headers  = "Content-type: text/plain; charset=utf-8\r\n";
		$headers .= "Администрация Cream Life";
		$letter = 	"Здравствуйте $login,\nВы запросили напоминание вашего пароля для аккаунта $login,\nВаш текущий пароль: $pass";
		if(mail($email, $title, $letter, $headers) AND $array)
		{
			echo "<div class='box_top_login'><div class='box_top_text_error'><div class='eTitle'>Ваш текущий пароль отправлен на ваш e-mail!</div></div></div>";
		}
	}

	function Requests($author,$vopros1,$vopros2,$vopros3,$vopros4,$vopros5)
	{
		$db = new Database;

		$author = $db->safesql( trim($_SESSION['name'] ));
		
		$vopros1 = $db->safesql(htmlspecialchars(trim($vopros1)));
		$vopros2 = $db->safesql(htmlspecialchars(trim($vopros2)));
		$vopros3 = $db->safesql(htmlspecialchars(trim($vopros3)));
		$vopros4 = $db->safesql(htmlspecialchars(trim($vopros4)));
		$vopros5 = $db->safesql(htmlspecialchars(trim($vopros5)));

		$one = $db->query("UPDATE `".TABLE_OTHER."` SET `otvet1`='$vopros1' WHERE `author`='$author'");
		$two = $db->query("UPDATE `".TABLE_OTHER."` SET `otvet2`='$vopros2' WHERE `author`='$author'");
		$rub = $db->query("UPDATE `".TABLE_OTHER."` SET `otvet3`='$vopros3' WHERE `author`='$author'");
		$four = $db->query("UPDATE `".TABLE_OTHER."` SET `otvet4`='$vopros4' WHERE `author`='$author'");
		$five = $db->query("UPDATE `".TABLE_OTHER."` SET `otvet5`='$vopros5' WHERE `author`='$author'");
		$six = $db->query("UPDATE `".TABLE_USERS."` SET `Activated`='0' WHERE `".TABLE_USERS_NAME."`='$author'");
		
		if(!$one or !$two or !$rub or !$four or !$five or !$six) return false;
		else return true;

	}
	function SetPassword($password)
	{
		if( ($pass_result = $this->check_pass($password)) != 1 )
		return $pass_result;
		$db = new Database;
		$password = $db->safesql(trim($password));
		$session = $db->safesql(trim($_SESSION['name']));
		$one = $db->query("UPDATE `".TABLE_USERS."` SET `".TABLE_USERS_PASSWORD."`='$password' WHERE `".TABLE_USERS_NAME."`='$session'");
		if(!$one) return false;
		else return true;
	}
	function age($age) 
	{
		$db = new Database;
		$age = $db->safesql(trim($age));
		if( ($age >= 10 && $age < 20) || substr($age, -1) == 0 || substr($age, -1) >= 5) $return = ' лет';
		else if(substr($age, -1) == 1) $return = ' год';
		else if(substr($age, -1) > 1) $return = ' года';
		return "$age $return";
	}

	function Login($user_nick,$user_pass)
	{
		$db = new Database;
		$user_nick = $db->safesql(trim($user_nick));
		$user_pass = $db->safesql(trim($user_pass));
		
		$array = $db->super_query("SELECT * FROM `".TABLE_USERS."` WHERE `".TABLE_USERS_NAME."` = '$user_nick' AND `".TABLE_USERS_PASSWORD."` = '$user_pass'");
		if(!$array) return false;
		$array = $db->super_query("SELECT * FROM `".TABLE_USERS."` WHERE `".TABLE_USERS_NAME."` = '$user_nick' AND `".TABLE_USERS_PASSWORD."` = '$user_pass'");

		$tm = time();

		if ( stristr($_SERVER['HTTP_USER_AGENT'], 'Firefox') ) $brauzer = 1;
		elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'Chrome') ) $brauzer = 2;
		elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'Safari') ) $brauzer = 3;
		elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'Opera') ) $brauzer = 4;
		elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0') ) $brauzer = 5;
		elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0') ) $brauzer = 6;
		elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0') ) $brauzer = 7;

		$_SESSION['id'] = $array[TABLE_USERS_ID];
		$_SESSION['name'] = $user_nick;
		$_SESSION['password'] = md5($user_pass);

		$db->query("UPDATE `".TABLE_USERS."` SET `last_date`='$tm' WHERE `".TABLE_USERS_NAME."`='$user_nick'");
		$db->query("INSERT INTO `login_log` (ID,IP,Date_Login,Brauzer) VALUES ('".$_SESSION['id']."','".$this->getIP()."','".date('d.m.Y H:i:s')."','$brauzer')");
		return true;
	}

	function Biography_Add($bio)
	{
		if($bio !== "")
		{
			$db = new Database;
			$bio = $db->safesql(htmlspecialchars(trim($bio)));
			$array = $db->query("UPDATE `".TABLE_OTHER."` SET `biography`='$bio' WHERE `author`='".SESSION_NAME."'");
			if(!$array) return false;
			else return true;
		}
		else return false;
	}

	function IsLogin() 
	{
		$db = new Database;
		if(isset($_SESSION['name'])) return true;
		else return false;
	}
  
	function getIP()
	{
		if(isset($_SERVER['HTTP_X_REAL_IP']))
		return $_SERVER['HTTP_X_REAL_IP'];
		return $_SERVER['REMOTE_ADDR'];
	}

	function test_true()
	{
		global $questions;
		if($_SESSION['test_good'] == 1) return 0;
		$iq = 0;
		for($i=0;$i<count($questions);$i++)
		{
			if($_POST['RadioGroup'.$i.''] == $questions[$i][Answer])
			{
				$iq++;
			}
		}
		if($iq == SHOW_QUESTS)
		{
			$_SESSION['test_good'] = 1;
			return 0;
		}
		return (SHOW_QUESTS-$iq);
	}
	
	function IsValidNick($nick)
	{
		if(preg_match("/[\d]+/",$nick)) return false;
		if(preg_match("/[а-я]+/i",$nick)) return false;
		return $nick;
	}

	function show_nick_error($num)
	{
		switch($num)
		{
			case 1: return "Вы можете использовать этот ник";
			case -1: return "Все поля необходимо заполнить!";
			case -2: return "Допустимый формат: Имя_Фамилия";
			case -3: return "Этот ник уже зарегистрирован";
		}
	}
	function check_nick($nick)
	{
		if($nick == "")
		{
			return -1;
		}
		if(($nick = $this->IsValidNick($nick)) == false)
		{
			return -2;
		}
		else if($this->IsExist($nick))
		{
			return -3;
		}
		else
		{
			return 1;
		}
	}

	function show_pass_error($num)
	{
		switch($num)
		{
			case 1: return "Вы можете использовать этот пароль";
			case -1: return "Все поля необходимо заполнить!";
			case -2: return "Длина пароля должна быть не меньше 3 и не больше 20 символов!";
			case -3: return "Найдены запрещённые символы!(' / \ \" *)";
		}
	}
	function check_pass($pass)
	{
		if($pass == "")
		{
			return -1;
		}
		else if(strlen($pass) < 3 || strlen($pass) > 20)
		{
			return -2;
		}
		else
		{
			$forbidden = array("'" => true, '/' => true, '\\' => true, '"' => true, '*' => true, ';' => true, '%' => true);
			$len = strlen($pass);
			for($i=0;$i<$len;$i++)
			{
				if( $forbidden[ $pass[$i] ] == true )
				{
					return -3;
				}
			}
			return 1;
		}
	}
	function GetName($name)
	{
		$name = str_replace("_"," ", $name);
		return $name;
	}
	function GetList($list)
	{
		$db = new Database;
		$info_account = '<img src="'.TEMPLATE_DIR.'/'.TEMPLATE.'/images/info.png"/>';
		switch($list)
		{
			case "all":
			$list_select = "all";
			$list_name = "";
			$list_title = "Список активированных пользователей";
			break;
			case "active":
			$list_select = "Activated";
			$list_num = 1;
			$list_name = "Активированных";
			$list_title = "Активированные пользователи";
			break;
			case "noactive":
			$list_select = "Activated";
			$list_num = 2;
			$list_name = "Неактивированных";
			$list_title = "Неактивированные пользователи";
			break;
			case "banned":
			$list_select = "Banned";
			$list_num = 1;
			$list_name = "Заблокированных";
			$list_title = "Заблокированные пользователи";
			break;
			case "requests":
			$list_select = "Activated";
			$list_other = "requests";
			$list_num = 0;
			$list_name = "Заявок на регистрацию нету.";
			$list_title = "Заявки на регистрацию";
			break;
			default: return false;
		}
		if($list_select == "none") return false;
			if($list_select == "all") $array_uc = $db->query("SELECT `".TABLE_USERS_NAME."` FROM `".TABLE_USERS."`");
			else $array_uc = $db->query("SELECT `".TABLE_USERS_NAME."` FROM `".TABLE_USERS."` WHERE `$list_select`='$list_num'");
			$history = "";
			while($array_uc = $db->get_row())
			{
				if($list_other == "requests") $url = "<td style='padding:5px 30px;color:#777777;background:#fff;border-bottom:1px solid #ddd; width:0%;'><b><font color='#777777'><a href='index.php?".PREFIX."=admin&v=".$array_uc[Name]."'>$info_account</a></font></b></td>";
				else $url = "<td style='padding:5px 30px;color:#777777;background:#fff;border-bottom:1px solid #ddd; width:0%;'><b><font color='#777777'><a href='index.php?".PREFIX."=admin&players=1&name=".$array_uc[Name]."'>$info_account</a></font></b></td>";
				$history .= "
				<td style='padding:5px 20px;color:#777777;background:#fff;border-bottom:1px solid #ddd; width:0%;'><b><font color='#777777'>".$this->GetName($array_uc[TABLE_USERS_NAME])."</font></b></td>
				<td style='padding:5px;color:#777777;background:#fff;border-bottom:1px solid #ddd; width:0%;'><b><font color='#777777'></font></b></td>
				$url
				</tr><tr>
				";
			}
			if($list_select == "all") $counts = $db->super_query("SELECT COUNT(*) as count FROM `".TABLE_USERS."`");
			else $counts = $db->super_query("SELECT COUNT(*) as count FROM `".TABLE_USERS."` WHERE `$list_select`='$list_num'");
			if($counts['count'] == 0) 
			{
				if($list_other == "requests") $error .= "$list_name";
				else $error .= "$list_name пользователей нету.";
				$history .= "<td style='padding:5px 20px;color:#777777;background:#fff;border-bottom:1px solid #ddd; width:0%;'><b><font color='#777777'>$error</font></b></td>
				<td style='padding:5px 50px;color:#777777;background:#fff;border-bottom:1px solid #ddd; width:0%;'><b><font color='#777777'></font></b></td>
				<td style='padding:5px 50px;color:#777777;background:#fff;border-bottom:1px solid #ddd; width:0%;'><b><font color='#777777'></font></b></td>
				";
			}
			if($counts['count'] >= 20) $overflow = "height: 44em; overflow: auto";
			else $overflow = "height: auto;";
			$list_return = '
			<div style="width: 100%;" class="box_top"><div style="width: 100%;" class="box_top_text"> 
			<div class="eTitle">'.$list_title.' [ '.$counts['count'].' ]</div> 
			</div></div> 
			<div style="width: 100%; border:1px solid #cccccc; text-align:justify;" />
			<div style="padding:2px 5px;" /> 
			<div style="'.$overflow.'">
			<table style="border-collapse:collapse;width:100%;padding:0px;">
			<tbody>
			<tr>
			<td style="padding:0px;"> 
			<div class="eMessage"> 
			<div><div class="box_content" style="padding:0px;border-bottom:0px;border:1px solid #dcdcdc;">
			<table style="margin-bottom:0px;" border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr>
			<td style="padding:5px 55px;color:#777777;background:#fff;border-bottom:1px solid #ddd; width:0%;"><b><font color="#777777"> Ник</font></b></td>
			<td style="padding:5px 120px; color:#777777;background:#fff;border-bottom:1px solid #ddd; width:0%;"><b><font color="#777777"></font></b></td>
			<td style="padding:5px; color:#777777;background:#fff;border-bottom:1px solid #ddd; width:0%;"><b><font color="#777777"> Посмотреть</font></b></td>
			</tr><tr>
			'.$history.'
			</tr></tbody></table>
			</div>
			</div>
			</div>
			</td></tr></tbody></table> 
			</div>
			</div>
			</div></div>
			';
			return $list_return;
	}
	function GetJobName($i)
	{
		switch($i)
		{
			case 0: $job = "Нету"; break;
			case 1: $job = "Инкосатор"; break;
			case 2: $job = "Адвокат"; break;
			case 3: $job = "Проститутка"; break;
			case 4: $job = "НаркоТорговец"; break;
			case 5: $job = "Автоугонщик"; break;
			case 6: $job = "Репортер"; break;
			case 8: $job = "Охранник"; break;
			case 9: $job = "Оружейник"; break;
			case 10: $job = "Уборщик улиц"; break;
			case 11: $job = "Мусорщик"; break;
			case 12: $job = "Боксер"; break;
			case 14: $job = "Водитель"; break;
			case 15: $job = "Доставщик газет"; break;
			case 16: $job = "Дальнобойщик"; break;
			case 17: $job = "Охранник в тюрьме"; break;
			case 18: $job = "Тренер"; break;
			case 22: $job = "Доставщик"; break;
			case 50: $job = "Строитель"; break;
			default: $job = "Нету"; break;
		}
		return $job;
	}
	function GetFractionName($i)
	{
		switch($i)
		{
			case 0: $member = "Нету"; break;
			case 1: $member = "LS-PD"; break;
			case 2: $member = "US-MS"; break;
			case 3: $member = "LS-Law"; break;
			case 4: $member = "LS-EMS"; break;
			case 5: $member = "LS-News Co."; break;
			case 6: $member = "LS-DMV"; break;
			case 7: $member = "LS-PCT"; break;
			case 8: $member = "LS-RC"; break;
			case 9: $member = "LS-MS"; break;
			case 10: $member = "US-P"; break;
			default: $member = "Нету"; break;
		}
		return $member;
	}
	function GetRaceName($i)
	{
		switch($i)
		{
			case 0: $Rasa = "Европеец"; break;
			case 1: $Rasa = "Афро-американец"; break;
			case 2: $Rasa = "Азиат"; break;
			case 3: $Rasa = "Латино-американец"; break;
			default: $Rasa = "Error"; break;
		}
		return $Rasa;
	}
	function GetBrauzerName($i)
	{
		switch($i)
		{
			case 1: $brauzer = "Firefox"; break;
			case 2: $brauzer = "Chrome"; break;
			case 3: $brauzer = "Safari"; break;
			case 4: $brauzer = "Opera"; break;
			case 5: $brauzer = "IE 6"; break;
			case 6: $brauzer = "IE 7"; break;
			case 7: $brauzer = "IE 8"; break;
			default: $brauzer = "Не определено";
		}
		return $brauzer;
	}
	function safe($user_nick)
	{
		$user_nick = str_replace("/","",$user_nick);
		$user_nick = str_replace("'","",$user_nick);
		$user_nick = str_replace(".","",$user_nick);
		$user_nick = str_replace("`","",$user_nick);
		$user_nick = str_replace(" ","",$user_nick);
		return $user_nick;
	}
	function Settings($setting_1,$setting_2)
	{
		$db = new Database;
		$nick = $db->safesql(trim($_SESSION['nick']));
	    $setting_1 = $db->safesql(trim($_POST['setting_1']));
	    $setting_2 = $db->safesql(trim($_POST['setting_2']));
		$one = $db->query("UPDATE `".TABLE_USERS."` SET `biography_setting`='$setting_2' WHERE `".TABLE_USERS_NAME."`='".SESSION_NAME."'");
		$two = $db->query("UPDATE `".TABLE_USERS."` SET `profile_setting`='$setting_1' WHERE `".TABLE_USERS_NAME."`='".SESSION_NAME."'");
		if(!$one OR !$two) return false;
		return true;
	}
}
$user_class = new user_Functions;
?>
