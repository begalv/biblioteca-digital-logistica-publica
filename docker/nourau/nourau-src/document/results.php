<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

require_once '../include/start.php';
require_once BASE . 'include/defs_d.php';
require_once BASE . 'include/format_d.php';
require_once BASE . 'include/page_d.php';

// validate input

$page = isset($_GET['page']) ? $_GET['page'] : '0';


if (empty($_GET['words']))
  $palavras = 'null';
else {
  $palavras = clean(addslashes($_GET['words']));
  

}
if ($page < 1)
  $page = 1;


page_begin();

echo html_h("Resultado da Pesquisa");

 
 
 if ($palavras == "null") {
	  
 // $sqlq = "SELECT nr_document.title, nr_document.code, nr_document.created, topic.id, topic.name FROM nr_document INNER JOIN (topic INNER JOIN topic_users on topic.id = topic_users.topic_id) on nr_document.topic_id = topic.id WHERE nr_document.status='a' AND topic_users.users_id = {$_SESSION['suid']} ORDER BY nr_document.title ASC";
  
  $sqlq = "SELECT nr_document.title, nr_document.capa, nr_document.code, nr_document.author, nr_document.created, topic.id as tid, topic.name FROM nr_document INNER JOIN (topic INNER JOIN topic_users on topic.id = topic_users.topic_id) on nr_document.topic_id = topic.id WHERE nr_document.status='a' AND topic_users.users_id = {$_SESSION['suid']} ORDER BY nr_document.title";
  
  $sqln =  "SELECT count(nr_document.id) FROM nr_document INNER JOIN (topic INNER JOIN topic_users on topic.id = topic_users.topic_id) on nr_document.topic_id = topic.id WHERE nr_document.status='a' AND topic_users.users_id = {$_SESSION['suid']} ";
 } 
 else {
 // echo '<b>' . htmlspecialchars(stripslashes($palavras)) . '</b><br>';	 
  $sqlq = "SELECT nr_document.id,  nr_document.capa, nr_document.title, nr_document.code, nr_document.author, nr_document.created, topic.id as tid, topic.name FROM nr_document INNER JOIN (topic INNER JOIN topic_users on topic.id = topic_users.topic_id) on nr_document.topic_id = topic.id WHERE nr_document.status='a' AND   TRANSLATE(title,'áéíóúàèìòùãõâêîôôäëïöüçÁÉÍÓÚÀÈÌÒÙÃÕÂÊÎÔÛÄËÏÖÜÇ','aeiouaeiouaoaeiooaeioucAEIOUAEIO') ILIKE '%$palavras%' AND topic_users.users_id = {$_SESSION['suid']}
  ORDER BY title ";
  
  $sqln =  "SELECT count(nr_document.id) FROM nr_document INNER JOIN (topic INNER JOIN topic_users on topic.id = topic_users.topic_id) on nr_document.topic_id = topic.id WHERE nr_document.status='a' AND TRANSLATE(title,'áéíóúàèìòùãõâêîôôäëïöüçÁÉÍÓÚÀÈÌÒÙÃÕÂÊÎÔÛÄËÏÖÜÇ','aeiouaeiouaoaeiooaeioucAEIOUAEIO') ILIKE '%$palavras%' AND topic_users.users_id = {$_SESSION['suid']}";

	 
 }	 

$lim = 25;
$off = ($page - 1) * $lim;

$q = db_query($sqlq, $lim, $off);
$n = db_simple_query($sqln);


$r =0;

if ($n == 1){
  if (db_simple_query($sqlq) == '')
    $r= 1;
}

$right =''; 

if ($n and $r == 0) {
  if ($n == 1) {   
    $left = "Existe <b>1</b> documento disponível";
    $first = $last = 1;
  }
  else {
     $first = min($n, $off + 1);
     $last = min($n, $off + $lim);
     $left ="Existem <b> $n </b> documentos disponíveis" ;
     $right = "Exibindo os documentos <b>$first</b> - <b>$last</b>";
    }
 

format_bar($left, $right);
 
    echo "<div class='Resultado'>";
  
    while ($a = db_fetch_array($q)){
     
	 format_result($a['tid'], $a['code'], $a['title'], $a['author'],$a['capa']); 
		
	}	
    
	echo "</div>";
   
   format_page_list(ceil($n/$lim), $page);
}
else
  echo html_p(html_b("O termo ". $palavras. " não foi encontrado."));


page_end();




/*-------------- functions --------------*/

function format_result ($tid, $did, $title, $author, $capa )
{
  global $cfg_site, $cfg_dir_image, $cfg_dir_image_capas;

  $title = htmlspecialchars($title);
  $topic = get_topic($tid, 'name'); // FIXME needs a db query; too costly?
  
  echo "<div class=\"linha\">";
    echo "<div class=\"coluna1\">";
	if (!empty($capa)) {
		if (file_exists($cfg_dir_image_capas."/".$capa)) {
			echo "<img src=\"{$cfg_dir_image}/$capa\" width=\"40%\" height=\"60%\">";
		}
   }		
	echo "</div>";
	 echo "<div class=\"coluna2\">";
	 
	 echo "<p><a href=\"{$cfg_site}document/?code=$did\">$title</a></br>";
     echo "$author<br>";
	 echo "<small>Disponível em <a href=\"{$cfg_site}document/list.php?tid=$tid\">$topic</a></small></<br></p>";
	 
	 echo "</div>";
  echo "</div>";
			

}


function clean($string) {
	
   $search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u,ã,õ,");
   $replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u,a,o");
   $string = str_replace($search, $replace, $string);	
	
   return  $string; // Removes special chars.
}

?>
