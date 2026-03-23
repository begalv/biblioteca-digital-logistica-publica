<?php

require_once '../include/start.php';
require_once BASE . 'include/util.php';
 
$periodoI=isset($_GET['periodoI'])?$_GET['periodoI']:'05-10-2002';
$periodoF=isset($_GET['periodoF'])?$_GET['periodoF']:date('Y-m-d');

//echo $periodoI .' and '. $periodoF ."<br>";

if (empty($periodoI))
  $periodoI = date('Y-m-d', strtotime('05-10-2002'));
else 
  $periodoI = date('Y-m-d', strtotime(str_replace('/', '-',$periodoI)));

if (empty($periodoF)) {
	$periodoF =  date('Y-m-d', strtotime('+1 days'));
}	
else { 

    $periodoF = date('Y-m-d', strtotime(str_replace('/', '-', $periodoF).'+1 days'));

}
 
 
 $data = array();

 $sql = "SELECT EXTRACT(Year FROM  created ) as ano , count(*) AS total 
 FROM nr_document 
 WHERE status ='a' AND topic_id not in (629, 785) 
 AND created BETWEEN '".$periodoI."' and '".$periodoF."'
 GROUP BY ano 
 ORDER BY ano desc";
 $result = db_query($sql);
 
 while($row = pg_fetch_assoc($result)){
   $data[] = $row;
}

//now print the data
print json_encode($data);


?>