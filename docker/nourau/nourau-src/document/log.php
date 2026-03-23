<?php
require_once '../include/start.php';
require_once BASE . 'include/htdig.php';
require_once BASE . 'include/html.php';
require_once BASE . 'include/page.php';

global $cfg_dir_log;

if (!is_administrator())
  error(_('Access denied'));

if(isset($_REQUEST['nomearq']))
  $nomearq = "$cfg_dir_log/".$_REQUEST['nomearq'];

html_header("Arquivo de Log:".$nomearq);

$file = fopen($nomearq, 'r');
 while (!feof($file)) {
   $line = fgets($file);
   echo '<p>'.($line).'</p>';
}
fclose($file);

echo html_p(html_a(_('Click here to continue'),
                     "{$cfg_site}document/manage.php"));

html_footer();

?>