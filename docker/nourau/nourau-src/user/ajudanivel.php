<?php
	require_once '../include/start.php';
	require_once BASE . 'include/page.php';


	if (!is_administrator())
  		error(_('Access denied'));
echo "<html>";
echo "<head>";
echo "<title>Ajuda N&iacute;vel de Acesso</title>";

echo "<link rel=\"stylesheet\" href=\"{$cfg_site}layout/estilo.css\" rel=\"StyleSheet\" type=\"text/css\" media=\"screen\">\n";

echo "<div id='ajuda'>";
echo "</head>";
echo "<body>";

echo "<table id='ajuda'>";
echo "<tr>";
echo "<thead>";
	echo "<th></th>";
			echo "<th>"._M('level @1', 1)."<br>". _('Collaborator')."</th>";
			echo "<th>"._M('level @1', 2)."<br>". _('Maintainer')."</th>";
			echo "<th>"._M('level @1', 3)."<br>". _('Administrator')."</th>";
	echo "</thead>";
	echo "</tr>";
        
	echo "<tbody>";
	echo "<tr class='peq'>";
		 echo "<td>Criar um novo tópico</td>";
		 echo "<td class = 'nivelN'>Não</td>";
		 echo "<td class = 'nivelN'>Não</td>";
		 echo "<td class = 'nivelS'>Sim</td>";
		 echo "</tr>";
		
		 echo "<tr class='peq'>";
		 echo "<td>Editar Tópico</td>";
		 echo "<td class = 'nivelN'>Não</td>";
		 echo "<td class = 'nivelN'>Não</td>";
		 echo "<td class = 'nivelS'>Sim</td>";
		 echo "</tr>";
		 
		 echo "<tr class='peq'>";
		 echo "<td>Remover tópico</td>";
		 echo "<td class = 'nivelN'>Não</td>";
		 echo "<td class = 'nivelN'>Não</td>";
		 echo "<td class = 'nivelS'>Sim</td>";
		 echo "</tr>";
		 
		 echo "<tr class='peq'>";
		 echo "<td>Criar um subtópico<sup>1</sup></td>";
		 echo "	<td class = 'nivelN'>Não</td>";
		 echo "	<td class = 'nivelN'>Não</td>";
		 echo "	<td class = 'nivelS'>Sim</td>";
		 echo "</tr>";
		 
		echo "<tr class='peq'>";
		echo "<td>"._("Archive a new document in this topic")."</td>";
		echo "<td class = 'nivelS'>Sim</td>";
		echo "<td class = 'nivelS'>Sim</td>";
		echo "<td class = 'nivelS'>Sim</td>";	
		echo "</tr>";
		
		echo "<tr>";
		echo "<td>Editar Documento</td>";
		echo "<td class = 'nivelS'>Sim</td>";
		echo "<td class = 'nivelS'>Sim</td>";
		echo "<td class = 'nivelS'>Sim</td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td>Remover Documento</td>";
		echo "<td class = 'nivelN'>Não</td>";
		echo "<td class = 'nivelN'>Não</td>";
		echo "<td class = 'nivelS'>Sim</td>";
		echo "</tr>";
		
	  
	   echo "<tr>";
	   echo "<td>Aprovar Documento</td>";
	   echo "<td class = 'nivelN'>Não</td>";
	   echo "<td class = 'nivelS'>Sim</td>";
	   echo "<td class = 'nivelS'>Sim</td>";
	   echo "</tr>";
	   
	   echo "<tr>";
	   echo "<td>Criar Usuários</td>";
	   echo "<td class = 'nivelN'>Não</td>";
	   echo "<td class = 'nivelN'>Não</td>";
	   echo "<td class = 'nivelS'>Sim</td>";
	   echo "</tr>";
	  
	   echo "<tr>";
	   echo "<td>Editar Usuários</td>";
	   echo "<td class = 'nivelS'>Sim</td>";
	   echo "<td class = 'nivelS'>Sim</td>";
	   echo "<td class = 'nivelS'>Sim</td>";
	   echo "</tr>";
	  
	  echo "<tr>";
	  echo "<td>Mudar a Senha</td>";
	  echo "<td class = 'nivelS'>Sim</td>";
	  echo "<td class = 'nivelS'>Sim</td>";
	  echo "<td class = 'nivelS'>Sim</td>";
	  echo "</tr>";
     
	 echo "<tr>";
	 echo "<td>Inserir teses </td>";
	 echo "<td class = 'nivelS'>Sim</td>";
	 echo "<td class = 'nivelS'>Sim</td>";
	 echo "<td class = 'nivelS'>Sim</td>";
	 echo " </tr>";
	 echo "</tbody>";
    echo "</table>";
    echo "<p><h5 align='center'><sup>1</sup>"._('The option to Create Subtopic appears to administrators and those responsible for topic.')."</h5></p>";
	echo "</div>";
	echo "</body>";
	echo "<!--End#container-->";
	echo "</html>";

?>