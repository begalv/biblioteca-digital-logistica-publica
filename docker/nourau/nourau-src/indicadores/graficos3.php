<?php
require_once '../include/start.php';
require_once BASE . 'include/util.php';
 
$paises = [ 'AF' => 'Afeganistão',
            'ZA' => 'África do Sul',
            'AX' => 'Åland, Ilhas',
            'AL' => 'Albânia',
            'DE' => 'Alemanha',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antárctida',
            'AG' => 'Antigua e Barbuda',
            'AN' => 'Antilhas Holandesas',
            'SA' => 'Arábia Saudita',
            'DZ' => 'Argélia',
            'AR' => 'Argentina',
            'AM' => 'Arménia',
            'AW' => 'Aruba',
            'AU' => 'Austrália',
            'AT' => 'Áustria',
            'AZ' => 'Azerbeijão',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BE' => 'Bélgica',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BY' => 'Bielo-Rússia',
            'BO' => 'Bolívia',
            'BA' => 'Bósnia-Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet, Ilha',
            'BR' => 'Brasil',
            'BN' => 'Brunei',
            'BG' => 'Bulgária',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'BT' => 'Butão',
            'CV' => 'Cabo Verde',
            'KH' => 'Cambodja',
            'CM' => 'Camarões',
            'CA' => 'Canadá',
            'KY' => 'Cayman, Ilhas',
            'KZ' => 'Cazaquistão',
            'CF' => 'Centro-africana, República',
            'TD' => 'Chade',
            'CZ' => 'Checa, República',
            'CL' => 'Chile',
            'CN' => 'China',
            'CY' => 'Chipre',
            'CX' => 'Christmas, Ilha',
            'CC' => 'Cocos, Ilhas',
            'CO' => 'Colômbia',
            'KM' => 'Comores',
            'CG' => 'Congo, República do',
            'CD' => 'Congo',
            'CK' => 'Cook, Ilhas',
            'KR' => 'Coreia do Sul',
            'KP' => 'Coreia do Norte',
            'CI' => 'Costa do Marfim',
            'CR' => 'Costa Rica',
            'HR' => 'Croácia',
            'CU' => 'Cuba',
            'DK' => 'Dinamarca',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominicana, República',
            'EG' => 'Egipto',
            'SV' => 'El Salvador',
            'AE' => 'Emiratos Árabes Unidos',
            'EC' => 'Equador',
            'ER' => 'Eritreia',
            'SK' => 'Eslováquia',
            'SI' => 'Eslovénia',
            'ES' => 'Espanha',
            'US' => 'Estados Unidos',
            'EE' => 'Estónia',
            'ET' => 'Etiópia',
            'FO' => 'Faroe, Ilhas',
            'FJ' => 'Fiji',
            'PH' => 'Filipinas',
            'FI' => 'Finlândia',
            'FR' => 'França',
            'GA' => 'Gabão',
            'GM' => 'Gâmbia',
            'GH' => 'Gana',
            'GE' => 'Geórgia',
            'GS' => 'Geórgia do Sul e Sandwich do Sul, Ilhas',
            'GI' => 'Gibraltar',
            'GR' => 'Grécia',
            'GD' => 'Grenada',
            'GL' => 'Gronelândia',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GY' => 'Guiana',
            'GF' => 'Guiana Francesa',
            'GW' => 'Guiné-Bissau',
            'GN' => 'Guiné-Conacri',
            'GQ' => 'Guiné Equatorial',
            'HT' => 'Haiti',
            'HM' => 'Heard e Ilhas McDonald, Ilha',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungria',
            'YE' => 'Iémen',
            'IN' => 'Índia',
            'ID' => 'Indonésia',
            'IQ' => 'Iraque',
            'IR' => 'Irão',
            'IE' => 'Irlanda',
            'IS' => 'Islândia',
            'IL' => 'Israel',
            'IT' => 'Itália',
            'JM' => 'Jamaica',
            'JP' => 'Japão',
            'JE' => 'Jersey',
            'JO' => 'Jordânia',
            'KI' => 'Kiribati',
            'KW' => 'Kuwait',
            'LA' => 'Laos',
            'LS' => 'Lesoto',
            'LV' => 'Letónia',
            'LB' => 'Líbano',
            'LR' => 'Libéria',
            'LY' => 'Líbia',
            'LI' => 'Liechtenstein',
            'LT' => 'Lituânia',
            'LU' => 'Luxemburgo',
            'MO' => 'Macau',
            'MK' => 'Macedónia, República da',
            'MG' => 'Madagáscar',
            'MY' => 'Malásia',
            'MW' => 'Malawi',
            'MV' => 'Maldivas',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'FK' => 'Malvinas, Ilhas (Falkland)',
            'IM' => 'Man, Ilha de',
            'MP' => 'Marianas Setentrionais',
            'MA' => 'Marrocos',
            'MH' => 'Marshall, Ilhas',
            'MQ' => 'Martinica',
            'MU' => 'Maurícia',
            'MR' => 'Mauritânia',
            'YT' => 'Mayotte',
            'UM' => 'Menores Distantes dos Estados Unidos, Ilhas',
            'MX' => 'México',
            'MM' => 'Myanmar (antiga Birmânia)',
            'FM' => 'Micronésia, Estados Federados da',
            'MZ' => 'Moçambique',
            'MD' => 'Moldávia',
            'MC' => 'Mónaco',
            'MN' => 'Mongólia',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'NA' => 'Namíbia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NI' => 'Nicarágua',
            'NE' => 'Níger',
            'NG' => 'Nigéria',
            'NU' => 'Niue',
            'NF' => 'Norfolk, Ilha',
            'NO' => 'Noruega',
            'NC' => 'Nova Caledónia',
            'NZ' => 'Nova Zelândia',
            'OM' => 'Oman',
            'NL' => 'Países Baixos (Holanda)',
            'PW' => 'Palau',
            'PS' => 'Palestina',
            'PA' => 'Panamá',
            'PG' => 'Papua-Nova Guiné',
            'PK' => 'Paquistão',
            'PY' => 'Paraguai',
            'PE' => 'Peru',
            'PN' => 'Pitcairn',
            'PF' => 'Polinésia Francesa',
            'PL' => 'Polónia',
            'PR' => 'Porto Rico',
            'PT' => 'Portugal',
            'QA' => 'Qatar',
            'KE' => 'Quénia',
            'KG' => 'Quirguistão',
            'GB' => 'Reino Unido',
            'RE' => 'Reunião',
            'RO' => 'Roménia',
            'RW' => 'Ruanda',
            'RU' => 'Rússia',
            'EH' => 'Saara Ocidental',
            'AS' => 'Samoa Americana',
            'WS' => 'Samoa (Samoa Ocidental)',
            'PM' => 'Saint Pierre et Miquelon',
            'SB' => 'Salomão, Ilhas',
            'KN' => 'São Cristóvão e Névis',
            'SM' => 'San Marino',
            'ST' => 'São Tomé e Príncipe',
            'VC' => 'São Vicente e Granadinas',
            'SH' => 'Santa Helena',	 
            'LC' => 'Santa Lúcia',
            'SN' => 'Senegal',
            'SL' => 'Serra Leoa',
            'RS' => 'Sérvia',
            'SC' => 'Seychelles',
            'SG' => 'Singapura',
            'SY' => 'Síria',
            'SO' => 'Somália',
            'LK' => 'Sri Lanka',
            'SZ' => 'Suazilândia',
            'SD' => 'Sudão',
            'SE' => 'Suécia',
            'CH' => 'Suíça',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard e Jan Mayen',
            'TH' => 'Tailândia',
            'TW' => 'Taiwan',
            'TJ' => 'Tajiquistão',
            'TZ' => 'Tanzânia',
            'TF' => 'Terras Austrais e Antárticas Francesas (TAAF)',
            'IO' => 'Território Britânico do Oceano Índico',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TK' => 'Toquelau',
            'TO' => 'Tonga',
            'TT' => 'Trindade e Tobago',
            'TN' => 'Tunísia',
            'TC' => 'Turks e Caicos',
            'TM' => 'Turquemenistão',
            'TR' => 'Turquia',
            'TV' => 'Tuvalu',
            'UA' => 'Ucrânia',
            'UG' => 'Uganda',
            'UY' => 'Uruguai',
            'UZ' => 'Usbequistão',
            'VU' => 'Vanuatu',
            'VA' => 'Vaticano',
            'VE' => 'Venezuela',
            'VN' => 'Vietname',
            'VI' => 'Virgens Americanas, Ilhas',
            'VG' => 'Virgens Britânicas, Ilhas',
            'WF' => 'Wallis e Futuna',
            'ZM' => 'Zâmbia',
            'ZW' => 'Zimbabwe'
        ];


if (isset ($_GET['code']))
   $code =  $_GET['code']; 
/*else
	$code  =8278;*/

if (isset ($_GET['tipo']))
   $tipo =  $_GET['tipo']; 

if (isset ($_GET['periodoI'])) 
   $periodoI= $_GET['periodoI'];

if (isset ($_GET['periodoF'])) 
   $periodoF= $_GET['periodoF'];

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

$data = array();

$sql = "SELECT \"countryC\" as code, country, count(*) as total FROM visitas_downloads
WHERE tipo ='".$tipo."' AND ( \"countryC\" is not Null OR \"countryC\" <> ' ')
AND code = '".$code."' 
AND data BETWEEN '".$periodoI."' AND '".$periodoF."'
GROUP BY \"countryC\", country
ORDER BY total desc";
		 
		 
//print $sql;		 
		 
		 
 $q = pg_query($db_conn2, $sql);		 

 
/* echo "<table>";
 echo "<tr><th>Países</th>";
 echo "<th></th>";
 echo "<th>Total de visitas</th></tr>";*/
 
 /*Uso da Tag  crossorigin='anonymous' para corregir o erro ERR_BLOCKED_BY_RESPONSE.NotSameOrigin*/
 
 $i =0;
  /*https://flagsapi.com/BE/flat/64.png"*/
  while($row = pg_fetch_array($q)){
	/*echo "<tr>";  */
	if ($row['code'] <> '') {
		$flag = $paises[$row['code']];
		//$flag = "<img crossorigin='anonymous' src='https://flagsapi.com/".$row['code']."/flat/64.png' width='30px' height='20px' alt='Bandeira:".$paises[$row['code']]."' > ". $paises[$row['code']]; 
		$total = number_format($row['total'], 0, ',', '.	');
		if ($tipo == 'v')
	    	$data[$i] = ['Países'=>$flag, 'Total de Visitas'=>$total];
		else 
			$data[$i] = ['Países'=>$flag, 'Total de Downloads'=>$total];
		$i++;
	}	
	/*echo "</tr>";*/
}


//echo "</table>";
//now print the data
print json_encode($data);


?>