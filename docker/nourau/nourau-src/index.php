<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.



define('TOPLEVEL', true);

require_once 'include/start.php';
require_once BASE . 'include/format.php';
require_once BASE . 'include/format_n.php';
require_once BASE . 'include/page.php';
require_once BASE . 'include/util.php';

	
page_begin_aux();
page_menu_begin();
page_head();

?>
<script type="text/javascript">
  var msg = sessionStorage.getItem("my_report");
  var type = sessionStorage.getItem("type");
   if( msg ){
	   mostraDialogo(msg, type, 4500); 	
       sessionStorage.removeItem("my_report");
	   sessionStorage.removeItem("type");
}
</script>
<?php

	
  if (is_maintainer () || is_administrator() || is_responsable() ) { 	
  
  
    $sql = "SELECT count(*) as total FROM nr_document INNER JOIN (topic INNER JOIN topic_users on topic.id =  topic_users.topic_id) on nr_document.topic_id = topic.id WHERE status='w' AND topic_users.users_id = {$_SESSION['suid']}";
    $qt = db_query($sql);
    $a_tot=db_fetch_array($qt);
    $total = $a_tot['total'];
    
	echo "<div class='home-content'>";
      echo "<div class='overview-boxes'>";
	/*      echo "<div class='box'>";
	        echo "<div class='right-side'>";
              echo "<div class='box-topic'>Há </div>";
                echo "<div class='number'>".$total."</div>";
	           echo "<div class='indicator'>";
                    echo "<span class='box-topic'>Documentos para Aprovação</span>";
                echo " </div>"; 
	   echo "<i class='bx bx-library'></i>";
	 echo "</div>";		
	
	echo "</div>";*/
	
	
	echo "<div class=\"box\">";
      echo "<div class=\"right-side\">";
        echo "<div class=\"box-topic\">Documentos<br> para aprovar </div>";
		   
        echo "<div class=\"number\">".$total."</div>";
		/*	echo "<div class=\"indicator\">";
                  echo "<i class='bx bx-up-arrow-alt'></i>";
             echo "<span class=\"text\">para aprovar</span>";
            echo "</div>";*/
          echo "</div>";
          echo "<i class='bx bxs-archive cart two' ></i>";
        echo "</div>";

  echo "</div>";
	
	
	 
	 include('maintainer_index.ini');
  }

  
 
 if (is_collab()) {
	
  include('collab_index.ini');
	
 }	
	


page_end();


function list_documents ( $query, $op)
{
  global $cfg_site;
  $i = 0;

  		while ($a = db_fetch_array($query)) {
        	  $class = ($i++ & 1) ? 'odd' : 'even';
			  
			  if ($op == 'E') 
				 $url = "{$cfg_site}document/edit.php?did=" . rawurlencode($a['did']);
			  else 
				  $url = "{$cfg_site}document/?code={$a['code']}";

			   echo "<tr class=".$class.">";
			   echo "<td class=\"ndoc\">".db_locale_date($a['created'])."</td>";
			   
			  if ($a['status'] != 'i') 
			    echo "<td class=\"texto\">". html_a($a['title'], $url)."</td>";
              else 				   
  			    echo "<td class=\"texto\">". html_a((!empty($a['title']))?$a['title']:"Sem Título", $url)."</td>";
			   
			   echo "<td class=\"texto\">". "em". "&nbsp;" . html_a($a['name'], "{$cfg_site}document/list.php?tid={$a['id']}")."</td>";
			   echo"</tr>";
		}
    
 }

?>
