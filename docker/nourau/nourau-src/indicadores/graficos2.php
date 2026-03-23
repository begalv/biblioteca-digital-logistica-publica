<?php

require_once '../include/start.php';
require_once BASE . 'include/util.php';

if (isset ($_GET['code']))
   $code =  $_GET['code']; 
/*else
	$code  =8278;*/


$periodoI=isset($_GET['periodoI'])?$_GET['periodoI']:'05-10-2020';
$periodoF=isset($_GET['periodoF'])?$_GET['periodoF']:date('Y-m-d');

//echo $periodoI .' and '. $periodoF ."<br>";

if (empty($periodoI))
  $periodoI = date('Y-m-d', strtotime('05-10-2020'));
else 
  $periodoI = date('Y-m-d', strtotime(str_replace('/', '-',$periodoI)));

if (empty($periodoF)) {
	$periodoF =  date('Y-m-d', strtotime('+1 days'));
}	
else { 

    $periodoF = date('Y-m-d', strtotime(str_replace('/', '-', $periodoF).'+1 days'));

}

//echo $periodoI .' and '. $periodoF ."<br>";

$data = array();

$sql = "select DISTINCT t.Ano, sum(t.visitas) as Acessos, sum (t.downloads) as Downloads
from (
Select EXTRACT(Year FROM  data ) as ano,  count(code) as visitas, 0 as downloads
FROM visitas_downloads 
WHERE tipo = 'v' and code = '".$code."' 
AND data BETWEEN '".$periodoI."' and '".$periodoF."'
GROUP BY ano
Union 
Select EXTRACT(Year FROM  data ) as ano, 0 as visitas, count(code) as downloads
FROM visitas_downloads
WHERE tipo = 'd' and code = '".$code."' 
AND data BETWEEN '".$periodoI."' and '".$periodoF."'
GROUP BY ano
)as t
GROUP BY ano
order by ano desc";
		 
		 
//print $sql;		 
		 
		 
 $q = pg_query($db_conn2, $sql);		 

 
while($row = pg_fetch_assoc($q)){
   $data[] = $row;
}

//now print the data
print json_encode($data);


?>