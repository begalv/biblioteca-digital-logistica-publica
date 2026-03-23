<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

/*-------------- mail functions --------------*/

function send_mail ($to, $subject, $message, $reply = '')
{
  global $cfg_webmaster, $cfg_subject_tag, $cfg_redirect_emails;

 // echo "<script>console.log(".$cfg_webmaster.") </script> ";  
  
  $headers  = 'MIME-Version: 1.0'."\r\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
  $headers .= "From:".$cfg_webmaster."\r\n";
  $headers .= "Reply-To:".$cfg_webmaster."\r\n";
  $headers .= "X-Mailer: PHP/ ".phpversion();
  $headers .= "X-Sender: BDU <".$cfg_webmaster.">\n";
  $headers .= "X-Priority: 3\r\n";
  $headers .= "Organization: Universidade Estadual de Campinas\r\n";

  
  if (!empty($reply))
    $headers .= "\nReply-To:  $cfg_webmaster";

  if (!$cfg_redirect_emails)
    mail($to, "[$cfg_subject_tag] $subject", $message, $headers);
  else
    mail($cfg_webmaster, "[$to] $subject", $message, $headers);
}


/*-------------- logging functions --------------*/

function add_log ($scope, $op, $info = '', $level = '')
{
  global $session;

  if (empty($level))
    $level = 'i';
  $uid = ($_SESSION['suid']) ? $_SESSION['suid'] : '0';
  db_command("INSERT INTO log (scope,op,user_id,level,info) VALUES ('$scope','$op','$uid','$level','$info')");
}


/*-------------- validation functions --------------*/

function valid_email ($email)
{
 // return eregi('^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\.[a-z]{2,3}$', $email);
 return preg_match ("/^[A-Za-z0-9]+([_.-][A-Za-z0-9]+)*@[A-Za-z0-9]+([_.-][A-Za-z0-9]+)*\\.[A-Za-z0-9]{2,4}$/", $email);
}

function valid_int ($int)
{
  return preg_match("/^\d+$/", $int);
}

function valid_opt_int ($int)
{
  return preg_match('/^\d*$/', $int);
}

function valid_char ($char)
{
  return preg_match('/^\w+$/', $char);
}

/*-------------- conversion functions --------------*/

function convert_email ($email)
{
  global $cfg_obfuscate_emails;
  global $session;

  if ($cfg_obfuscate_emails && !$_SESSION['slevel'])
    $email = preg_replace('/@/', ' ' . _('at') . ' ', $email);
  return $email;
}

function convert_line ($str, $size = '')
{
  if (!empty($size) && strlen($str) > $size)
    $str = substr($str, 0, $size) . '...';
  //return htmlspecialchars($str);
  return $str;
}

function convert_text ($str)
{
  // convert special HTML characters
  $str = htmlspecialchars($str);

  // insert links for valid URLs (based on code by Tom Christiansen)
  $sch = '(http|ftp|https)';               // URL schemes
  $any = '\w\-.!~*\'();/?:@&=+$,%#'; // valid characters (RFC 2396)
  $pun = '.!);?,';                   // punctuation that can be at URL end
  $ent = '&(gt|lt|quot);';           // HTML entities to ignore at URL end
  $str = preg_replace("¬\\b($sch://[$any]+?)(?=[$pun]*([^$any]|$ent|$))¬i",
                      '<a href="\1" target="_blank">\1</a>', $str);

  // insert linebreaks
  return nl2br($str);
}


/*-------------- miscellaneous functions --------------*/

function insert_text_file ($file)
{
  global $cfg_language, $cfg_locale_dir;

  if (is_readable("$cfg_locale_dir/$cfg_language/$file"))
    readfile("$cfg_locale_dir/$cfg_language/$file");
  else if (is_readable("$cfg_locale_dir/en_US/$file"))
    readfile("$cfg_locale_dir/en_US/$file"); // fallback
  echo "\n";
}

function random_string ($size)
{
  $str = '';
  $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
  mt_srand((double)microtime()*1000000);
  while ($size--)
    $str .= substr($chars, mt_rand(0, 61), 1);
  return $str;
}

function redirect ($url) {

			exit(header("Location: $url"));
	}

function rot13 ($str)
{
  return strtr($str,
               'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
               'NOPQRSTUVWXYZABCDEFGHIJKLMnopqrstuvwxyzabcdefghijklm'); 
}


// returns 1 if $ip is part of $network

function IP_Match($network, $ip) {
   $ip_arr = explode("/",$network);
   $network_long=ip2long($ip_arr[0]);

   $mask_long= pow(2,32)-pow(2,(32-$ip_arr[1]));
   $ip_long=ip2long($ip);

   if (($ip_long & $mask_long) == $network_long) {
       return 1;
   } else {
       return 0;
   }
}

function breadcrumb($url_pieces = array(), $divisor = '&gt;&gt;') {
	global $cfg_site;
 	$url_crumb = $url_pieces; 
	$http = $link = $href = null;
	$count = sizeof($url_crumb);
	 //inicia contador
 	$i = 1;
 	foreach($url_crumb as $link=>$inner) {
 		//verifica se é o primeiro fragmento da url
 		if($i == 1) {
 			$href .= $http.$link;
 		} else {
 			$href .= $link;
 		}
		
		//verifica se a palavra javascript existe
		
		  if($link == 'back')
			  $href = 'javascript:history.back()';
		
 		//verifica se é o ultimo fragmento da url
 		if($i == $count) {
 			//mostrar fragmento sem link
 			$crumb[] = $inner;
 		}else {
 			//mostrar fragmento com link para a pagina
 			$crumb[] = '<a href="'.$href.'" title="'.$inner.'">'.$inner.'</a><span>'.$divisor.'</span>';
 		}
 		$i++;
 }
 	//mostrar breadcrumb na tela
 	echo "<div class=\"breadcrumb\">";
		echo "<div class=\"box\">";
			foreach($crumb as $crumb) {
				echo "<p class='left'>".$crumb;
			}
		echo "</div>";
	echo "</div>";
}
?>
