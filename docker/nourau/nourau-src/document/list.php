<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

require_once '../include/start.php';
require_once BASE . 'include/defs_d.php';
require_once BASE . 'include/format.php';
require_once BASE . 'include/format_d.php';
require_once BASE . 'include/format_t.php';
require_once BASE . 'include/page_d.php';
require_once BASE . 'include/util_d.php';
require_once BASE . 'include/util.php';

global $lang;
$i=0;
$tid = isset($_GET['tid']) ? $_GET['tid'] : '0';
$page = isset($_GET['page']) ? $_GET['page'] : '0';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 't';
$desc = isset($_GET['desc']) ? $_GET['desc'] : 'n';
$nameOrder = 'name';


// start page
    page_begin();	
	
// validate input
if (!valid_opt_int($page) || !valid_opt_int($tid))
  message("{$cfg_site}document/list.php","Parâmetro Inválido !", "failure");

if ($page < 1)
  $page = 1;


?>
<script type="text/javascript"> 

var msg = sessionStorage.getItem("my_report");
  var type = sessionStorage.getItem("type");
   if( msg ){
	   mostraDialogo(msg, type, 4500); 	
       sessionStorage.removeItem("my_report");
	   sessionStorage.removeItem("type");
}


	$(document).ready(function() {
		$('#remove a').each(function() {
			var $link = $(this);
			var $dialog = $('<div></div>')
				.load($link.attr('href'))
				.dialog({
					autoOpen: false,
					modal:true,
					title: $link.attr('title'),
					width: 600
					
				});
 
			$link.click(function() {
				$dialog.dialog('open');
 
				return false;
			});
		});
	});
	</script> 

<?php
    
	

	// show topic path
	format_path($tid, "{$cfg_site}document/list.php", false, 'Coleções', "{$cfg_site}document/list.php");
	
	if (empty($tid))
		  $topic = 'Coleções';
	else {
		  $a = get_topic($tid);
		  $topic = htmlspecialchars($a['name']);
	}
	   
    $msg = $topic;
	echo html_h($msg);

	// list only main topics
	if (empty($tid)) {
	  $q = db_query("SELECT topic.id, $nameOrder as name, description, parent_id, tipo_acesso, archieve  FROM topic  INNER JOIN topic_users ON topic.id = topic_id  WHERE parent_id = 0 and users_id ={$_SESSION['suid']} Order by  $nameOrder");
	
      if (is_administrator() )
		echo html_button(format_action("Criar um nova Coleção", "{$cfg_site}topic/edit.php?pid=0"));
		
	  if (db_rows($q)){
	   list_topics('Coleções', $q);
	   
	}
	  else
		echo html_p(html_b('Não há Coleções.'));

	  
	  // finish
	  page_end();  
	  exit();
}

$pid = $a['parent_id'];

// list subtopics, if any

$sql = "SELECT T.id, T.".$nameOrder." as name ,T.description, T.url, T.tipo_acesso, T.parent_id , T.archieve FROM topic T INNER JOIN topic_users on T.id = topic_users.topic_id WHERE T.parent_id='$tid' and users_id ={$_SESSION['suid']}  ORDER BY ".$nameOrder;

$q = db_query($sql);

//$q = db_query("SELECT T.id, T.".$nameOrder." as name ,T.description, T.url, COUNT(D.id) AS documents, T.tipo_acesso, T.parent_id, T.archieve FROM topic T LEFT OUTER JOIN nr_document D ON (T.id=D.topic_id AND D.status='a') WHERE T.parent_id='$tid' GROUP BY T.id,T.".$nameOrder.",T.description, T.url, T.tipo_acesso ORDER BY ".$nameOrder);

    echo "<div class=\"button\"> "; 
    // permit topic creation
	  if (is_administrator() || is_responsable()) 
		 echo html_button(format_action("Criar uma nova sub-coleção", "{$cfg_site}topic/edit.php?pid=$tid"));
	  
	  if($a['archieve'] == 's'){
 		 
	     echo html_button(format_action('Arquivar um novo documento nesta Coleção',"{$cfg_site}document/put.php?tid=$tid")); 
	  }	 
		
 	echo "</div>";

	if (db_rows($q)){
		
		
	  //list_topics(_('Subtopics'), $q);
	  list_topics('Coleções', $q);
	 
	 }	
/*
if (is_user()) {
	 	//if (is_maintainer() && db_simple_query("SELECT id FROM topic_users WHERE topic_id='$tid' AND users_id={$_SESSION['suid']}"))
	if  (is_maintainer() && get_topic($tid, 'maintainer_id') == $_SESSION['suid'])
		echo html_button(format_action("Criar um novo tópico", "{$cfg_site}topic/edit.php?pid=0"));
	    //echo html_p(format_action(_('Create a new subtopic'),"{$cfg_site}topic/edit.php?pid=$tid"));
	else if (is_administrator())
	     echo html_p(format_action(_('Create a new subtopic'),"{$cfg_site}topic/edit.php?pid=$tid"));
}*/

// set sort column
switch ($sort) {
 case 't':
   $ord = 'title';
   break;
 case 's':
   $ord = 'size';
   break;
 case 'c':
   $ord = 'category';
   break;
 case 'd':
    $ord = 'downloads';
   break;
  case 'p':
    $ord = 'dataPublic';
   break;     
  case 'v': 
  $ord = 'visits';
   break;  
 default:
   $ord = 'updated';
};
$ord .= ($desc == 'y') ? ' DESC' : ' ASC';

$lim = 25;
$off = ($page - 1) * $lim;

   $q = db_query("SELECT D.title,D.code,D.updated,D.size,D.remote  FROM nr_document D WHERE D.status='a' AND D.topic_id='$tid' ORDER BY $ord ", $lim, $off);
  

$n = db_simple_query("SELECT count(*) FROM nr_document D WHERE D.status='a' AND D.topic_id='$tid'");

if ($n) {
  if ($n == 1) {
    $left =  "Existe <b>1</b> documento disponível";
    $first = $last = 1;
	$right = '';
  }
  else {
    $first = min($n, $off + 1);
    $last = min($n, $off + $lim);
	$left ="Existem <b> $n </b> documentos disponíveis" ;
    $right = "Exibindo os documentos <b>$first</b> - <b>$last</b>";
}
  
  format_bar($left, $right);
  echo "<p>\n";

  if ($page == 1)
    $url = "{$_SERVER['PHP_SELF']}?tid=$tid";
  else
    $url = "{$_SERVER['PHP_SELF']}?tid=$tid&page=$page";
  
  // format_page_list(ceil($n/$lim), $page);
	
  echo '<table class="tabela-bases" cellpadding="0" cellspacing="1">';
  //echo '<tr><td></td>';
  echo '<tr>';
  
  
  format_header('Título',    $url,             $sort == 't', $desc == 'y');
  format_header('Atualizado',  $url . '&sort=u', $sort == 'u', $desc == 'y');
  echo '</tr>';
  while ($a = db_fetch_array($q)){
	    $class = ($i++ & 1) ? 'odd' : 'even';
    format_item($a['title'], $a['code'], $a['updated'], $a['size'],
                $a['remote'],  $class);
	}
   
  echo '</table><p>';

  format_page_list(ceil($n/$lim), $page);
}
else
 // echo html_p(html_b("Sem documentos arquivados"));


/*if (is_user()) {
	if (is_collab() && db_simple_query("SELECT COUNT(category_id) FROM nr_topic_category WHERE topic_id='$tid'") && db_simple_query("SELECT id FROM topic_users where topic_id='$tid' and users_id={$_SESSION['suid']}"))
	 echo html_p(format_action(_('Archive a new document in this topic'),"{$cfg_site}document/put.php?tid=$tid"));
}*/


page_end($_SERVER['PHP_SELF'] . (($pid) ? "?tid=$pid" : ''));


/*-------------- functions --------------*/

function list_topics ($title, $query)
{
  global  $cfg_site;
 
    /*html_table_begin(false, 'right', true);
    html_table_item(html_b($title), 'title');*/
	 echo '<table class="tabela-bases" cellpadding="3" cellspacing="0">';
	 echo "<tr><th colspan='4'>".$title."</th></tr>"; 
      $i =0;
      while ($a = db_fetch_array($query)) {
       	// html_table_row_begin();
	    	$class = ($i++ & 1) ? 'odd' : 'even';
     	 	echo "<tr class=".$class.">"; 
	  
			//	if(is_maintainer() && db_simple_query("SELECT id FROM topic_users WHERE topic_id={$a['id']} AND users_id={$_SESSION['suid']}")) {
			if (is_administrator() || is_responsable() ) {
					  
					   echo "<td class='icon'><a href='{$cfg_site}topic/edit.php?tid={$a['id']}&pid={$a['parent_id']}' alt=\"Editar Coleção\" title=\"Editar coleção\"><i class='bx bx-edit-alt'></i></a></td>";
				       
					 // check if topic is empty
  						$doc = db_simple_query("SELECT COUNT(ID) FROM nr_document WHERE topic_id={$a['id']} AND status = 'a'");
					    $top = db_simple_query("SELECT COUNT(ID) FROM topic WHERE parent_id={$a['id']}");
  						if ($doc + $top == 0)
							echo "<td class='icon' id='remove'><a href='{$cfg_site}topic/action.php?op=d&tid={$a['id']}&pid={$a['parent_id']}' alt=\"Remover Coleção\" title=\"Remover Coleção\"> <i class='bx bx-trash' ></i></a></td>";
						else  
					    	echo "<td><span></span></td>";
				}else {
					echo "<td><span></span></td>";
					echo "<td><span></span></td>";
				}
			
		  	 if (!empty($a['url']))	
				 echo "<td><a href='{$a['url']}' target='_blank'>".convert_line($a['name'])."</a></td>";
			 else
                  echo "<td><a href='{$_SERVER['PHP_SELF']}?tid={$a['id']}'>".convert_line($a['name'])."</a></td>";
			
			echo (isset($a['documents']) ? "<td style = 'text-align: right;'>". _('Doc:') . $a['documents'].'</td>': '<td> </td>');
     
 
	  echo"</tr>";
    }
	echo "</table>";
	echo "<br>";
 }

function format_header ($title, $url, $active, $descending)
{
  global $cfg_site;
 

  if ($active && !$descending)
    $url .= '&desc=y';
  echo '<th><b>' . html_a($title, $url). '</b>';
  if ($active) {
    if ($descending)
      echo " <img src=\"{$cfg_site}images/desc.gif\"></th>";
    else
      echo " <img src=\"{$cfg_site}images/asc.gif\"></th>";
  }
  else
    echo '</th>';
}

function format_item ($title, $code, $updated, $size, $remote, $class)
{
  global $cfg_site;

  $title = convert_line($title, 65);
  $updated = db_locale_date($updated);
 
  $code = rawurlencode($code);
  $details  = "{$cfg_site}document/?code=$code";
  $view     = "{$cfg_site}document/?view=$code";
  $download = "{$cfg_site}document/?down=$code";

  $msg1 = _('View');
  $msg2 = _('Download');

echo <<<HTML
<tr class="$class">
<td><a href="$details">$title</a></td>
<td>$updated</td>
<!--<td width="1%"><a href="$view"><img alt="$msg1" border="0" src="{$cfg_site}images/view.gif" title="$msg1"></a></td>
<td width="1%"><a href="$download"><img alt="$msg2" border="0" src="{$cfg_site}images/download.gif" title="$msg2"></a></td>-->
</tr>
HTML;
}


function format_item_or ($title, $code, $datapublic, $updated, $class)
{
  global $cfg_site;
  
  $title = convert_line($title, 65);
  $code = rawurlencode($code);
  $updated = db_locale_date($updated);
  $details  = "{$cfg_site}document/?code=$code&opt=1";
  
echo <<<HTML
<tr class="$class">
<td><a href="$details">$title</a></td>
<td>$datapublic</td>
<td>$updated</td>
</tr>
HTML;
}


?>