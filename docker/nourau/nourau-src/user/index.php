<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.
// Perfil do usúario /user/index.php

	require_once '../include/start.php';
	require_once BASE . 'include/format_u.php';
	require_once BASE . 'include/page_u.php';
	
 
    $level = 0;
	$uid = $_GET['uid'];
	
    page_begin();
    breadcrumb(array($cfg_site=>'Início','user/list.php'=>'Usuários Cadastrados', ''=>'Perfil do usuário'));	
	if (empty($uid))
	  redirect("{$cfg_site}user/search.php");

	// validate input
	if (!valid_int($uid))
	  message("{$cfg_site}user/list.php", "Parâmetro Inválido !", "failure");
	
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

	 
	 $a = get_user($uid);
	 $user = $a['username'];
	 $name = $a['name'];
	 if ($a['level'] == ADM_LEVEL) {
		$level = "Administrador";
	}
	else if ($a['level'] == MNT_LEVEL)
		$level = "Curador/Publicador";
	else if ($a['level'] == MNT_LEVEL)
		$level = "Responsável";	
	else if ($a['level'] == USR_LEVEL)
		$level ="Depositante/Colaborador";

	echo "<div class=\"title\">Perfil: ".$user."</div>";
	echo "<div class =\"info-boxes\">";
	
		
	$opttopicos = array();
	
	$topicos = carrega_topicos();  
	$sql = "SELECT topic.id, topic.name, topic.parent_id FROM users INNER JOIN (topic_users INNER JOIN topic ON topic_id = topic.id) ON users.id = users_id WHERE users_id =".$uid." ORDER BY topic.id";
    //echo $sql;	
	$resulttopic = db_query($sql); 

	
	    while ($qtopic = db_fetch_array($resulttopic)){
		$opttopicos[$qtopic['id']] = $qtopic['name'];
	  }		   


	
	format_user($name, $a['email'], $a['info'], $level, $a['accessed'], $opttopicos, $topicos);
    echo "</div>";
	
	
	
page_end();	 


function carrega_topicos(){
	 global $db_conn;

	$topico = null;
	 
	 $topic = db_query("SELECT id, name FROM topic WHERE parent_id = 0 ORDER BY name");
  
	 while ($q = db_fetch_array($topic)){
		$topico[$q['id']] = "<li class=\"nivel\">".$q['name']."</li>";
		$resulttopic = db_query("SELECT id, name From topic where parent_id =".$q['id']." order by name");
		
		while ($qtopic = pg_fetch_array($resulttopic)){
			$topico[$qtopic['id']] = "<li  class=\"nivel1\">".$qtopic['name']."</li>";
			$resulttopic1 = db_query("SELECT id, name From topic where parent_id =".$qtopic['id']." order by name");
	
			while ($qtopic1 = pg_fetch_array($resulttopic1)){
				$topico[$qtopic1['id']] = "<li class=\"nivel2\">".$qtopic1['name']."</li>";
				$resulttopic2 = db_query("SELECT id, name From topic where parent_id =".$qtopic1['id']." order by name");
				
				while ($qtopic2 = pg_fetch_array($resulttopic2)){
					$topico[$qtopic2['id']] = "<li class=\"nivel3\">".$qtopic2['name']."</li>";
					$resulttopic3 = db_query("SELECT id, name From topic where parent_id =".$qtopic2['id']." order by name");
					
					while ($qtopic3 = pg_fetch_array($resulttopic3)){
						   $topico[$qtopic3['id']] = "<li class=\"nivel4\">".$qtopic3['name']."</li>";
						
					}
				}
			}
		}
	 }

	 return $topico;
	}


?>
