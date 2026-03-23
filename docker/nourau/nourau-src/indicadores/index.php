<?php

/*
 Desenvolvido por: SBU-DTI
 Data da última modificação: 12/03/2010

 Página principal - Indicadores da Biblioteca Digital

*/
require_once '../include/start.php';
require_once BASE . 'include/format.php';
require_once BASE . 'include/format_n.php';
require_once BASE . 'include/page.php';
require_once BASE . 'include/page_d.php';
require_once BASE . 'include/util.php';
require_once 'utilInd.php';

global $cfg_base_zeus, $cfg_host, $cfg_user,$cfg_pass, $cfg_port, $cfg_site, $db_conn2, $lang;

$settings = "host=".$cfg_host." dbname=".$cfg_base_zeus." user=".$cfg_user." password=".$cfg_pass." port=".$cfg_port;
$db_conn2 = pg_pconnect($settings);

echo "<script src=\"https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js\" integrity=\"sha512-ElRFoEQdI5Ht6kZvyzXhYG9NqjtkmlkfYk0wr6wHxU9JEHakS7UJZNeml5ALk+8IKlU6jDgMabC3vkumRokgJA==\" crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\"></script>";

echo "<script type=\"text/javascript\" src=\"https://www.gstatic.com/charts/loader.js\"></script>";




$ano = date("Y");

page_begin(); 

//echo "<p class='breadcrumb'><a href='{$cfg_site}'>In&iacute;cio</a><span>&gt;&gt;</span>Estat&iacute;sticas</p>";
breadcrumb(array($cfg_site=>'Início', ''=>'Indicadores'));

echo html_h("Biblioteca Digital - Indicadores");


echo "<div class =\"info-boxes\">";

echo "<div id=\"spinner-div\">";
echo "<img id=\"loading-image\" src=\"$cfg_site\images\loading.gif\" alt=\"Carregando...\" />";
echo "</div>";

  echo "<div class=\"box_chart\" >";
  echo "<div id=\"regions_div\" style=\"width: 900px; height: 500px;\">";
  echo "</div>";

	
	echo "<canvas id=\"insert_doc\"></canvas>";
	echo "</div>";

	echo "<div class=\"box_chart\" >";
	echo "<canvas id=\"table_countryV\"></canvas>";
	echo "</div>"; 
	
	echo "<table align = \"center\" id=\"table\" border=\"1\">";
    echo " </table>";
	 
	echo  "<span id=\"code\">108917</span>";
	
echo "</div>";


echo "</div><!--end #mainContent1-->";
page_end();

pg_close($db_conn2);
?>


<script type="text/javascript">
  
  $(document).ready(function(){

    var code=jQuery("#code").text(); 
 
    var periodoI='2002-10-05';
	var periodoF='2023-08-31';		
	var tipo = 'v';
 
	$.ajax({
		url: "https://www.bibliotecadigital.unicamp.br/manager/indicadores/graficos5.php",
	  	method: "GET",
		data: {periodoI:periodoI,periodoF:periodoF,tipo:tipo},
		success: function(data) {
		  console.log(data);
		  let json = JSON.parse(data);
		  
		  constructTable('#table_countryV',json);
		  
		},
		error: function(data) {
		  console.log(data);
		}, 
		 complete: function () {
			$('#spinner-div').hide();//Request is complete so hide spinner
		}
	  });
  }) 
 
 function constructTable(selector,json) {
             
            // Getting the all column names
            var cols = Headers(json, selector); 
  
            // Traversing the JSON data
            for (var i = 0; i < json.length; i++) {
                var row = $('<tr/>');  
                for (var colIndex = 0; colIndex < cols.length; colIndex++)
                {
                    var val = json[i][cols[colIndex]];
                     
                    // If there is any key, which is matching
                    // with the column name
                    if (val == null) val = ""; 
                        row.append($('<td/>').html(val));
                }
                 
                // Adding each row to the table
                $(selector).append(row);
            }
        }
         
        function Headers(json, selector) {
            var columns = [];
            var header = $('<tr/>');
             
            for (var i = 0; i < json.length; i++) {
                var row = json[i];
                 
                for (var k in row) {
                    if ($.inArray(k, columns) == -1) {
                        columns.push(k);
                         
                        // Creating the header
                        header.append($('<th/>').html(k));
                    }
                }
            }
             
            // Appending the header to the table
            $(selector).append(header);
                return columns;
        }
 
		
</script>


<?php




//page_head("Biblioteca Digital - Indicadores (2004 - ".$ano.")",$cfg_site);

/*echo "<fieldset>";
	echo "<legend><b>Teses e Dissertações</b></legend>";

echo "<table width=\"98%\" align=\"center\" border=\"0\" cellspacing=\"2\" cellpadding=\"2\">\n";
echo "<tr>";
echo "<td colspan =\"3\">";
 // quantidade_visitas();
echo "</td></tr>";
echo "<tr>\n";
	echo "<td valign=\"top\" width=\"30%\">\n";
      //   quantidade_downloads();
	echo "</td>\n";
	echo "<td valign=\"top\" width=\"30%\">\n";
	//	 download_user(_('Countries with the most Registered Users'));
	echo "</td>\n";
		echo "<td valign=\"top\" width=\"30%\">\n";
      //    download_pais(_('Countries with the most Downloads'));
	echo "</td>\n";

echo "</tr>";

echo "<tr>\n";
	echo "<td valign=\"top\" width=\"20%\" colspan =\"2\">\n";
	   	//	escolhe_unidade(_('Access and Downloads Indicators'),7);
	echo "</td>\n";
    echo "<td valign=\"top\" width=\"30%\">\n";
 	    graficos(_('Access/Downloads Graphs'));
	echo "</td>";
echo "</tr>";

echo "<tr>\n";
	echo "<td valign=\"top\" colspan =\"3\" width=\"80%\">\n";
			teses_acessadas();
	echo "</td>";
echo "</tr>\n";

echo"</table>\n";
echo "</fieldset>";

echo "</br>";

echo "<fieldset>";
	echo "<legend><b>Trabalho de Conclusão de Curso</b></legend>";
echo "<table width=\"98%\" align=\"center\" border=\"0\" cellspacing=\"2\" cellpadding=\"2\">\n";
echo "<tr>";

echo "<tr>\n";
	echo "<td valign=\"top\" width=\"20%\" colspan =\"2\">\n";
	   		escolhe_unidade(_('Access and Downloads Indicators'),498);
	echo "</td>\n";
echo "</tr>";

echo "<tr>\n";
	echo "<td valign=\"top\" colspan =\"3\" width=\"80%\">\n";
			documentos_acessados(498);
	echo "</td>";

echo"</table>\n";
echo "</fieldset>";

echo "</br>";

echo "<fieldset>";
	echo "<legend><b>Produção Técnico-Científica Digital</b></legend>";
echo "<table width=\"98%\" align=\"center\" border=\"0\" cellspacing=\"2\" cellpadding=\"2\">\n";
echo "<tr>";

echo "<tr>\n";
	echo "<td valign=\"top\" width=\"20%\" colspan =\"2\">\n";
	   		escolhe_unidade(_('Access and Downloads Indicators'),105);
	echo "</td>\n";
echo "</tr>";

echo "<tr>\n";
	echo "<td valign=\"top\" colspan =\"3\" width=\"80%\">\n";
			documentos_acessados(105);
	echo "</td>";

echo"</table>\n";
echo "</fieldset>";*/




/*-------------- functions --------------*/

function quantidade_downloads()
{
   global $unit,$aUnit, $db_conn, $db_conn2;


    $sql = "SELECT nr_document_teses.topic_id, nr_document_teses.visits as visits, nr_document_teses.visits2004 as visits2004, nr_document_teses.visits2010 as visits2010, nr_document_teses.downloads as downloads, nr_document_teses.downloads2004 as downloads2004, nr_document_teses.downloads2010 as downloads2010 FROM nr_document_teses Inner join topic ON nr_document_teses.topic_id = topic.id where topic.parent_id= 7 And nr_document_teses.status = 'a'";

    $q = pg_query($db_conn, $sql);

   	$t_total = $d_total = $v_total = 0;
    $t_bio = $t_hum = $t_exa = $t_tec = 0;
    $v_bio = $v_hum = $v_exa = $v_tec = 0;


   	 for ($i = 0; $i < pg_numrows($q); $i++) {
   		$a = pg_fetch_array($q, $i);

     	/*$t_total = $t_total + 1;
   	 	$d_total = $d_total+ $a['downloads'] + $a['downloads2004']+$a['downloads2010'];
        $v_total = $v_total+ $a['visits'] + $a['visits2004']+$a['visits2010'];*/

   	  	// count thesis per area
   	   	if ($a['topic_id'] == 32 or $a['topic_id'] == 33 or $a['topic_id'] == 34 or $a['topic_id'] == 35) 
   	   		$t_bio++;
   	   	else if ($a['topic_id'] == 27 or $a['topic_id'] == 28 or $a['topic_id'] == 29 or $a['topic_id'] == 30 or $a['topic_id'] == 31)
   	    		$t_hum++ ;
   	   	else if ($a['topic_id'] == 36 or $a['topic_id'] == 37 or $a['topic_id'] == 38 or $a['topic_id'] == 39 or $a['topic_id'] == 48)
   	    		$t_exa++ ;
   	   	else if ($a['topic_id'] == 40 or $a['topic_id'] == 41 or $a['topic_id'] == 42 or $a['topic_id'] == 43 or $a['topic_id'] == 44 or $a['topic_id'] == 45 or $a['topic_id'] == 543)
   	   		$t_tec++ ;

   	   	// count thesis per downloads
   	   	if ($a['topic_id'] == 32 or $a['topic_id'] == 33 or  $a['topic_id'] == 34 or $a['topic_id'] == 35)
   	  		$d_bio = $d_bio + $a['downloads'] + $a['downloads2004']+ $a['downloads2010'];
   	   	else if ($a['topic_id'] == 27 or $a['topic_id'] == 28 or $a['topic_id'] == 29 or $a['topic_id'] == 30 or $a['topic_id'] == 31)
   	   		$d_hum = $d_hum + $a['downloads'] + $a['downloads2004']+ $a['downloads2010'];
   	   	else if ($a['topic_id'] == 36 or $a['topic_id'] == 37 or $a['topic_id'] == 38 or $a['topic_id'] == 39 or $a['topic_id'] == 48)
   	   		$d_exa = $d_exa + $a['downloads'] + $a['downloads2004']+ $a['downloads2010'];
   	   	else if ($a['topic_id'] == 40 or $a['topic_id'] == 41 or $a['topic_id'] == 42 or $a['topic_id'] == 43 or $a['topic_id'] == 44 or $a['topic_id'] == 45 or $a['topic_id'] == 543)
   			$d_tec = $d_tec + $a['downloads'] + $a['downloads2004']+ $a['downloads2010'];
			
	     	// count thesies per visits
   	   	if ($a['topic_id'] == 32 or $a['topic_id'] == 33 or  $a['topic_id'] == 34 or $a['topic_id'] == 35)
   	  		$v_bio = $v_bio + $a['visits'] + $a['visits2004']+ $a['visits2010'];
   	   	else if ($a['topic_id'] == 27 or $a['topic_id'] == 28 or $a['topic_id'] == 29 or $a['topic_id'] == 30 or $a['topic_id'] == 31)
   	   		$v_hum = $v_hum + $a['visits'] + $a['visits2004']+ $a['visits2010'];
   	   	else if ($a['topic_id'] == 36 or $a['topic_id'] == 37 or $a['topic_id'] == 38 or $a['topic_id'] == 39 or $a['topic_id'] == 48)
   	   		$v_exa = $v_exa + $a['visits'] + $a['visits2004']+ $a['visits2010'];
   	   	else if ($a['topic_id'] == 40 or $a['topic_id'] == 41 or $a['topic_id'] == 42 or $a['topic_id'] == 43 or $a['topic_id'] == 44 or $a['topic_id'] == 45 or $a['topic_id'] == 543)
   			$v_tec = $v_tec + $a['visits'] + $a['visits2004']+ $a['visits2010'];	

 }


  	$t_total = $t_bio + $t_hum + $t_exa + $t_tec;
  	$d_total = $d_bio + $d_hum + $d_exa + $d_tec;
	$v_total = $v_bio + $v_hum + $v_exa + $v_tec;

	echo "<span id='indtitle'>"._('Number of Theses and Downloads per Area')."</span>";
	echo "<table id ='ind'  valign=\"top\" >\n";
	echo "<tr>\n";
	echo "<th>"._('Area')."</th>\n";
	echo "<th>"._('Theses')."</th>\n";
    echo "<th>"._('Visits')."</th>\n";
	echo "<th>"._('Downloads')."</th>\n";
	echo "</tr>\n";

    echo "<tr>\n";
	echo "<td class=\"left\">"._('Biomedical')."</td>\n";
	echo "<td class=\"center\">".number_format($t_bio,0,'.','.')."</td>\n";
	echo "<td class=\"center\">".number_format($v_bio,0,'.','.')."</td>\n";
	echo "<td class=\"center\">".number_format($d_bio,0,'.','.')."</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class=\"left\">"._('Humanities / Arts')."</td>\n";
	echo "<td class=\"center\">".number_format($t_hum,0,'.','.')."</td>\n";
	echo "<td class=\"center\">".number_format($v_hum,0,'.','.')."</td>\n";
	echo "<td class=\"center\">".number_format($d_hum,0,'.','.')."</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class=\"left\">"._('Exact Sciences')."</td>\n";
	echo "<td class=\"center\">".number_format($t_exa,0,'.','.')."</td>\n";
	echo "<td class=\"center\">".number_format($v_exa,0,'.','.')."</td>\n";
	echo "<td class=\"center\">".number_format($d_exa,0,'.','.')."</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class=\"left\">"._('Technological')."</td>\n";
	echo "<td class=\"center\">".number_format($t_tec,0,'.','.')."</td>\n";
	echo "<td class=\"center\">".number_format($v_tec,0,'.','.')."</td>\n";
	echo "<td class=\"center\">".number_format($d_tec,0,'.','.')."</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<th>"._('Total')."</th>\n";
	echo "<th>".number_format($t_total,0,'.','.')."</th>\n";
	echo "<th>".number_format($v_total,0,'.','.')."</th>\n";
	echo "<th>".number_format($d_total,0,'.','.')."</th>\n";
	echo "</tr>\n";
	echo "</table>";
  }


function quantidade_visitas()
{
	global $cfg_site, $db_conn,  $db_conn2;

   //Valores no Debian
   // $linvalor = 61;
   // $linmesano = 52;

  //Valores para o Centos
/*
    $linvalor = 57;
    $linmesano = 50;


    //Cria um array com as linhas do arquivo
	$linhas = file("http://libdigi.unicamp.br/estatisticas/sbu/index.html");

    //Monta o vetor $visitas com os meses e os valores

	for ($i=1;$i<=12;$i++){

    	//Extrair a qtde de visitas diarias
		// divide a linha em partes
		$partes = explode(">", $linhas[$linvalor]);
		//Valor da visita por diaria
	   	$vdiarias[$i] = substr($partes[2],0,strpos($partes[2],"<"));

      	//Extrair o mes e ano
    	// divide a linha em partes
    	$partes = explode(">", $linhas[$linmesano]);
    	$mesano[$i] = substr($partes[4],0,strpos($partes[4],"<"));
    	if(strlen($mesano[$i])!=8)
    		$mesano[$i]  = substr($mesano[$i],strpos($partes[4],"|")+1 ,8);

          //Centos
					$linvalor= $linvalor + 11;
			      	$linmesano = $linmesano + 11;


       // $linvalor= $linvalor + 13;
	  //	$linmesano = $linmesano + 13;
    }


    //"Mar 2010 - poussue o total de 2004 até março de 2010



      for ($i=1;$i<=12;$i++){

		  	if ($mesano[$i] != "Mar 2010" && $mesano[$i] != "Feb 2010" && $mesano[$i] != "Jan 2010" && substr($mesano[$i], 4 ,8) != 2009 ){

        	//inserir ou altererar o registro com os valores da cosulta
    		$result = pg_query($db_conn,"SELECT mesano,valor FROM visita WHERE mesano='".$mesano[$i]."'");
			if (pg_num_rows($result)==0){

        		$sql = "INSERT INTO visita (valor, mesano) VALUES (".$vdiarias[$i].",'".$mesano[$i]."')";
        		$insert = pg_query($db_conn,$sql);
    		}
    		else {
					$valor =  pg_fetch_result($result, 0, 'valor');
					if ($valor != $vdiarias)
				{
						$sql = "UPDATE visita SET valor = ".$vdiarias[$i]." WHERE mesano='".$mesano[$i]."'";
						$update = pg_query($db_conn,$sql);
				}
    	    }
        }

  	}*/

    //Calcula o valor total de visitas
   /* $soma = pg_query($db_conn,"SELECT SUM(valor) AS soma FROM visita");

	$QtdeVisitasTotal = pg_fetch_result($soma, 0, 0);

    $QtdeVisitas = $QtdeVisitasTotal;

    $data_anterior = mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));
    $data_anterior =date("d/m/Y", $data_anterior);

    echo "<div id='indspan'>&nbsp;Quantidade de Visitas ao site: ".number_format($QtdeVisitas,0,'.','.'). " (até ".$data_anterior.") </div>\n";*/
    // echo "<div id='indspan'>&nbsp;Quantidade de Visitas: ".number_format($QtdeVisitas,0,'.','.')."</div>\n";
	//Calcula o valor total de visitas

	$soma = pg_query($db_conn,"SELECT count(ip) AS qtde FROM visita_site");

	$QtdeVisitasTotal = pg_fetch_result($soma, 0, 0);

	echo "<div id='indspan'>&nbsp;"._('Number of Site Visits').": ".number_format($QtdeVisitasTotal,0,'.','.')."</div>\n";

	$somaUsuario = pg_query($db_conn2,"SELECT count(id) AS qtde FROM z_user");

	$QtdeTotalUsuario = pg_fetch_result($somaUsuario, 0, 0);

	echo "<div id='indspan'>&nbsp;"._('Number of Registered Users').": ".number_format($QtdeTotalUsuario,0,'.','.')."</div>\n";

}

function graficos($titulo)
{
   global $grafico;
	echo "<span id='indtitle'>$titulo</span>\n";
    	echo "<table border=\"0\" cellspacing=\"3\" cellpadding=\"3\">\n";
			   foreach (array_keys($grafico) as $key) {
	   			echo "<tr class=\"even\"><td class=\"left\" height=\"20%\"><a href=\"graficos.php?graf=$key\">$grafico[$key]</a></td></tr>\n";
   }

   echo "</table>";
}

function download_user($titulo)
{
	global $cfg_site, $db_conn2;

	echo "<span id='indtitle'>$titulo</span>\n";
	echo "<table id ='ind' align=\"center\" valign=\"top\" cellspacing=\"2\" cellpadding=\"2\">\n";
	echo "<tr>";
	echo "<th>"._('Country')."</th>";
	echo "<th>"._('Number')."</th>";
	echo "</tr>\n";

	$i = 0;

   	$sql = "SELECT INITCAP(LOWER(TRIM(country))) as pais,count(id) as qtde FROM z_user GROUP BY pais ORDER BY qtde DESC LIMIT 5";

	 //   $sql = "SELECT * FROM download_user";

	$qcountry = pg_query($db_conn2, $sql);

	while($result= pg_fetch_array($qcountry)){
		/*if ($i++ & 1)
			echo "<tr class=\"even\">\n";
		else
			echo "<tr class=\"odd\">\n";*/

        if (empty($result['pais'])) 
		  echo "<td class=\"left\">N/A</td>\n";
		else 	
		  echo "<td class=\"left\">{$result['pais']}</td>\n";
		
		echo "<td class=\"center\">".number_format($result['qtde'],0,'.','.')."</td>\n";
		echo "</tr>\n";

		//$total += $result['qtde'];
	}

/*	$sql = "SELECT INITCAP(LOWER(TRIM(country))) as pais,count(id) as qtde FROM z_user GROUP BY pais ORDER BY qtde DESC LIMIT ALL OFFSET 6";
	$qcountry = pg_query($db_conn2, $sql);

	while($result= pg_fetch_array($qcountry)){

		$toutros += $result['qtde'];
	}

	if ($i++ & 1)
	   echo "<tr class=\"even\">\n";
	else
	   echo "<tr class=\"odd\">\n";

	echo "<td class=\"left\">Others</td>\n";
	echo "<td class=\"center\">".number_format($toutros,0,'.','.')."</td>\n";
	echo "</tr>\n";

	pg_close($db_conn2);
	echo "<tr class='title'>";
	echo "<td>Total</td>\n";
	echo "<td>".number_format($total+$toutros,0,'.','.')."</td>";
	echo "</tr>\n";*/
	echo "</table>";
	echo "</div>";
}

function download_pais($titulo)
{
	global $cfg_site, $db_conn2;

    	echo "<span id='indtitle'>$titulo</span>\n";
		echo "<table id ='ind' cellspacing=\"2\" cellpadding=\"2\">\n";
		echo "<tr>";
		echo "<th>"._('Country')."</th>";
		echo "<th>"._('Number')."</th>";
		echo "</tr>\n";
		$i = 0;

         $sql = "SELECT country as pais, count(user) as qtde FROM z_user INNER JOIN z_log on id = user_id  GROUP by pais Order by qtde DESC Limit 5";

         //$sql = "SELECT * FROM download_pais Limit 5";
		$qcountry = pg_query($db_conn2, $sql);

		while($result= pg_fetch_array($qcountry)){
			/*if ($i++ & 1)
				echo "<tr class=\"even\">\n";
			else
				echo "<tr class=\"odd\">\n";*/

            echo "<tr>\n";
			
			if (empty($result['pais'])) 
			  echo "<td class=\"left\">N/A</td>\n";
			else 
			  echo "<td class=\"left\">{$result['pais']}</td>\n";
			echo "<td class=\"center\">".number_format($result['qtde'],0,'.','.')."</td>\n";
			echo "</tr>\n";

			//$total += $result['qtde'];
		}

		/*$sql = "SELECT pais, qtde FROM pais_donwload ORDER BY qtde DESC LIMIT ALL OFFSET 6";
		$qcountry = pg_query($db_conn2, $sql);

		/while($result= pg_fetch_array($qcountry)){

			$toutros += $result['qtde'];
		}

		if ($i++ & 1)
		   echo "<tr class=\"even\">\n";
		else
		   echo "<tr class=\"odd\">\n";

		echo "<td class=\"left\">Others</td>\n";
		echo "<td class=\"center\">".number_format($toutros,0,'.','.')."</td>\n";
		echo "</tr>\n";

		pg_close($db_conn2);
		echo "<tr class='title'>";
		echo "<td>Total</td>\n";
		echo "<td>".number_format($total+$toutros,0,'.','.')."</td>";
		echo "</tr>\n";*/
		echo "</table>";
}


function escolhe_unidade($titulo, $parent_id)
{
  global $db_conn, $lang;

  if ($lang == 'en_US')
	$name = 'name_en';
  else if ($lang =='es_ES')
    $name = 'name_es';
  else
    $name = 'name';


  echo "<form  name='form'method=\"GET\" ACTION =\"acessodownload.php\">\n";
  echo "<INPUT TYPE=HIDDEN NAME=\"link\" value=1>\n";
  echo "<INPUT TYPE=HIDDEN NAME=\"parent_id\" value=$parent_id>\n";
 
 echo "<span id='indtitle'>$titulo</span>\n";
  echo "<table width=\"100%\" align=\"center\">\n";
  echo "<tr>\n";
  echo "<td class=\"center\" valign='top' width=\"35%\">"._('Teaching and Research Unit').":</td>\n";
  echo "<td class=\"form_caixa\" width=\"65%\"><b><Select id='unidade' NAME='unidade' size='6'>\n";

   $result = pg_query($db_conn,"SELECT id, $name as name From topic where parent_id = $parent_id order by $name;");

   while($unit = pg_fetch_array($result)){

	echo "<OPTION ".($unit['id'] == 32?"selected='selected'":'')." VALUE =".$unit['id'].">".$unit['name']."</option><br>\n";
 }
 echo "</SELECT></center>";
 echo "</td></tr>\n";
 echo "<tr>\n";
 echo "<td colspan=\"2\" class=\"form_botao\"><input type=\"submit\" name=\"sent\" value=\"Consultar\"><br>\n";
 echo "</td></tr>\n";
 echo "</table>";
 echo "</form>\n";

 }

function teses_acessadas()
{
	global $cfg_base,$cfg_other_base, $cfg_user;
	global $db_conn;

	$anoinicial = 2013;
	$ano = 2016;

    $lim = 5;
    echo "<span id='indtitle'>"._M('The Top @1 downloaded Thesis',$lim)."\n</span>";
    echo "<table id ='ind' width=\"100%\" align=\"center\">\n";
    echo "<tr>\n";
    echo "<th rowspan='2'>"._('Title')."</th>\n";
    echo "<th rowspan='2'>"._('Author')."</th>\n";
    echo "<th rowspan='2'>"._('Unit')."</th>\n";
    echo "<th colspan='3'>"._('Downloads')."</th>\n";
    echo "<th rowspan='2'>"._('Total Downloads')."</th>\n";
    echo "</tr>\n";
    echo "<tr><th>2004-2009</th>";
    echo "<th>2010-2012</th>";
    echo "<th>".($anoinicial==$ano?$anoinicial:$anoinicial."-".$ano)."</th></tr>";

 	$i = 0;

 	$sqlcons = "SELECT nr_document_teses.author, nr_document_teses.title, nr_document_teses.code, COALESCE(nr_document_teses.downloads2004, 0) As downloads2004 ,COALESCE(nr_document_teses.downloads, 0) as downloads, COALESCE(nr_document_teses.downloads2010, 0) As downloads2010 ,(COALESCE(nr_document_teses.downloads, 0) + COALESCE(nr_document_teses.downloads2004, 0)+COALESCE(nr_document_teses.downloads2010, 0)) as total, split_part(topic.description,'-', 2) as unidade
    		    FROM topic Inner join nr_document_teses ON topic.id = nr_document_teses.topic_id
    		    WHERE parent_id = 7 AND nr_document_teses.status = 'a'
    		    ORDER BY total DESC LIMIT $lim";


   	$q = pg_query($db_conn, $sqlcons);

	while ($a = db_fetch_array($q)) {

	 /*    if ($i++ & 1)
	           echo "<tr class=\"even\">\n";
	      else
	            echo "<tr class=\"odd\">\n";*/

          echo "<tr>";
	      echo "<td class=\"left\">{$a['title']}</a></td>\n";
	      echo "<td class=\"left\">{$a['author']}</td>\n";
	      echo "<td class=\"left\">{$a['unidade']}</td>\n";
	      echo "<td class=\"right\">".number_format($a['downloads2004'],0,'.','.')."</td>\n";
		  echo "<td class=\"right\">".number_format($a['downloads2010'],0,'.','.')."</td>\n";
	      echo "<td class=\"right\">".number_format($a['downloads'],0,'.','.')."</td>\n";
	      echo "<td class=\"right\"><b>".number_format($a['total'],0,'.','.')."</b></td>\n";
	      echo "</tr>\n";
  	}
	echo "</table>";
	echo "<center><a href ='{$cfg_site}teses.php'>"._('See more')."</a></center><br>";
}

function documentos_acessados($unidade)
{
	global $cfg_base,$cfg_other_base, $cfg_user;
	global $db_conn;

	$anoinicial = 2013;
	$ano = date("Y");

    $lim = 5;
    echo "<span id='indtitle'>Os 5 Trabalhos de Conclusão de Curso mais acessados\n</span>";
    echo "<table id ='ind' width=\"100%\" align=\"center\">\n";
    echo "<tr>\n";
    echo "<th rowspan='2'>"._('Title')."</th>\n";
    echo "<th rowspan='2'>"._('Author')."</th>\n";
    echo "<th rowspan='2'>"._('Unit')."</th>\n";
    echo "<th colspan='3'>"._('Downloads')."</th>\n";
    echo "<th rowspan='2'>"._('Total Downloads')."</th>\n";
    echo "</tr>\n";
    echo "<tr><th>2004-2009</th>";
    echo "<th>2010-2012</th>";
    echo "<th>".($anoinicial==$ano?$anoinicial:$anoinicial."-".$ano)."</th></tr>";

 	$i = 0;
	
 	$sqlcons = "SELECT nr_document.author, nr_document.title, nr_document.code, COALESCE(nr_document.downloads2004, 0) As downloads2004 ,COALESCE(nr_document.downloads, 0) as downloads, COALESCE(nr_document.downloads2010, 0) As downloads2010 ,(COALESCE(nr_document.downloads, 0) + COALESCE(nr_document.downloads2004, 0)+COALESCE(nr_document.downloads2010, 0)) as total, split_part(topic.description,'-', 2) as unidade
    		    FROM topic Inner join nr_document ON topic.id = nr_document.topic_id  where parent_id=".$unidade." And nr_document.status = 'a' ORDER BY total DESC LIMIT $lim";

   	$q = pg_query($db_conn, $sqlcons);

	while ($a = db_fetch_array($q)) {

	 /*    if ($i++ & 1)
	           echo "<tr class=\"even\">\n";
	      else
	            echo "<tr class=\"odd\">\n";*/
		  $title = "<a href='http://www.bibliotecadigital.unicamp.br/document/?code=".$a['code']."'>".convert_line($a['title'], 65)."</a>";
          echo "<tr>";
	      echo "<td class=\"left\">".$title."</td>\n";
	      echo "<td class=\"left\">{$a['author']}</td>\n";
	      echo "<td class=\"left\">{$a['unidade']}</td>\n";
	      echo "<td class=\"right\">".number_format($a['downloads2004'],0,'.','.')."</td>\n";
		  echo "<td class=\"right\">".number_format($a['downloads2010'],0,'.','.')."</td>\n";
	      echo "<td class=\"right\">".number_format($a['downloads'],0,'.','.')."</td>\n";
	      echo "<td class=\"right\"><b>".number_format($a['total'],0,'.','.')."</b></td>\n";
	      echo "</tr>\n";
  	}
	echo "</table>";
	echo "<center><a href ='{$cfg_site}documentos.php?unidade={$unidade}'>"._('See more')."</a></center><br>";
}

?>