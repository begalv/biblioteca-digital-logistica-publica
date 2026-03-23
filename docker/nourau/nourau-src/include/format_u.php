<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

/*-------------- includes --------------*/

require_once BASE . 'include/format.php';
require_once BASE . 'include/util.php';


/*-------------- formating functions --------------*/

function format_user ($name, $email, $info, $level, $accessed, $opttopicos, $topicos)
{
  $email = convert_email($email);
  format_line("Nome", htmlspecialchars($name) . ' &lt;' .html_a($email, "mailto:$email") . '&gt;', false);
  format_line("Nivel de acesso", $level);
  
  format_line("Último Acesso", db_locale_date($accessed));
  format_block("Informações Adicionais", $info, true);
  
  
  if (!empty($opttopicos) )
     format_list("Acesso aos Tópicos", $opttopicos, $topicos, false);
  else 
	 echo "Nenhum tópico foi atributido";
 
}

function format_list ($title, $content, $topicos, $convert = true){
		
 if (empty($content))
		return;
 if ($convert)
		$content = convert_text($content);
 if (count(array($content))!=0){
	echo "<div class='info'><span class=\"label\" >$title:</b></span> \n";
	echo "<ul>";
	foreach (array_keys($topicos) as $key) {
		if (array_key_exists($key ,$content))
			echo "{$topicos[$key]}\n";
    	}	
	}
	echo "</ul>";
    echo "</div>\n";
}



?>
