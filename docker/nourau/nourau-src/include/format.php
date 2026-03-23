<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

/*-------------- includes --------------*/

require_once BASE . 'include/html.php';
require_once BASE . 'include/util.php';


/*-------------- formating functions --------------*/

function format_action ($action, $url)
{
  return html_a($action, $url);
}

function format_block ($title, $content, $convert = true)
{
  if (empty($content))
    return;
  if ($convert)
    $content = convert_text($content);

echo "<div class='info'><span class=\"label\" >$title:</span> \n";
echo "<blockquote> $content </blockquote></div>\n";

}

function format_line ($title, $content, $convert = true)
{
  if (empty($content))
    return;
  if ($convert)
    $content = htmlspecialchars($content);

  echo "<div class='info'>\n <span class=\"label\" >$title:</span>\n <span class=\"content\" > $content</span></div>\n";
 
}


function format_warning ($msg)
{
  if (!empty($msg))
    echo html_p(html_error(html_big($msg)));
}



?>
