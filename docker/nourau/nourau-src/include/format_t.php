<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

/*-------------- includes --------------*/
require_once BASE . 'include/util_u.php';
$origem = 	array('1' => "Coleções",
	  		      '2' => "Pesquisa por",
				  '3' => "Curadoria"); 	
$origemlink = array('1' => "{$cfg_site}document/list.php",
	  		  	    '2' => "{$cfg_site}document/list_restricao.php?tid=7",
	  		        '3' => 'javascript:history.back()');
	  		     

global $cfg_site;
/*-------------- formating functions --------------*/

function format_path ($tid, $url, $link_self, $origem, $urlorigem)
{
  global $cfg_site, $lang;


  $name_array = array();
  $id_array = array();
  $id = $tid;
  while ($id) {
    $q = db_query("SELECT name, parent_id FROM topic WHERE id='$id'");
    array_push($id_array, $id);
	array_push($name_array, htmlspecialchars(db_result($q, 0, 'name')));
    $id = db_result($q, 0, 'parent_id');
  }
 
  echo "<div class='breadcrumb'><p class='left'>".html_a("Início","{$cfg_site}")."<span>&gt;&gt;</span>";
  if ($tid != 0) 
   echo  html_a($origem, $urlorigem)."<span>&gt;&gt;</span>";
  else
	 echo "Coleções";
 
 
  while ($id = array_pop($id_array)) {
    $name = array_pop($name_array);
    if ($id == $tid && !$link_self)
      echo $name;
    else
      echo html_a($name, "$url?tid=$id");
    
	if ($id != $tid)
      echo '<span>&gt;&gt;</span> ';
  }
  echo " </p>\n";
  echo "</div>";
}

function format_topic ($description, $created, $uid, $username = '')
{
  global $cfg_site;

  $description = convert_text($description);
  $created = db_locale_date($created);
  if (empty($username))
    $username = get_user($uid, 'username');
  $maintainer = _('Maintainer');
  $created_msg = _('Created');

echo <<<HTML
<blockquote><table width="90%" border="0" cellpadding="3" cellspacing="0">
<tr class="odd"><td align="left">$description</td></tr>
</table></blockquote><p>
<b>$maintainer:</b>
<b><a href="{$cfg_site}user/?uid=$uid">$username</a></b><br>
HTML;
  // <b>$created_msg:</b> $created<p>
}

?>
