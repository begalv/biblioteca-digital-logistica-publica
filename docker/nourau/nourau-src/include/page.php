<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

/*-------------- includes --------------*/

require_once BASE . 'include/control.php';
require_once BASE . 'include/format_d.php';
require_once BASE . 'include/html.php';
require_once BASE . 'include/util.php';
require_once BASE . 'include/util_d.php';
require_once BASE . 'include/util_u.php';
require_once BASE . 'include/start.php';
/*-------------- local variables --------------*/

$page_has_menu = false;
$width=300;

/*-------------- page functions --------------*/
function page_begin_aux ($title = '', $head = '', $tag = false, $result='')
{
  global $cfg_site, $cfg_site_title, $cfg_reg_mode, $cfg_version,
    $cfg_banner, $cfg_banner_url, $cfg_banner_background, $cfg_banner_color;
  global $print, $session, $user, $lg;

   
   if (!isset($_SESSION['slevel'])) {
 
     $link =$cfg_site."user/login.php";
     redirect($link);
   } 


  if (empty($title))
    $title = $cfg_site_title;

  if (!html_header($title, $head, $body="", $tag, $result))
    return;

}

function page_menu_begin ($module = '')
{
 global $cfg_site, $cfg_logo_url ;

echo "<div class=\"sidebar\">\n";
	echo "<div class=\"logo-details\">\n";
        	echo "<img class=\"image_logo\" src=\"{$cfg_logo_url}\" alt=\"logo\" />\n";
	echo "</div>\n";

	
	echo "<ul class=\"nav-links\">\n";
	
      echo "<li>\n";
       echo "<a href=\"{$cfg_site}\">\n";
       echo "<i class='bx bx-home'></i>\n";
       echo "<span class=\"links_name\">Início</span>\n";
       echo "</a>\n";
       echo "</li>\n";
  
       echo "<button class='submenu-btn'><li>";	
      	 echo "<i class='bx bx-box' ></i>\n";
       	 echo "<span class=\"links_name\">Coleções</span>\n";
       	 echo "<i class='bx bx-chevron-down'></i></li>\n";
         echo "</li>\n";
       echo "</button>\n";
       

       $sql = "SELECT topic.id, topic.name, description From topic INNER JOIN topic_users ON topic.id = topic_id  WHERE  parent_id=0 and users_id ={$_SESSION['suid']} ORDER BY topic.name";	
		
       $q = db_query($sql);
       echo "<div class='submenu-items'  style='display: block;'>";
       while ($a = db_fetch_array($q)) {
			
		echo "<li>\n";
		echo "<a href=\"{$cfg_site}document/list.php?tid={$a['id']}\" title='::{$a['description']}'>\n";
		echo "<i></i>\n";
		echo "<span class=\"links_name\">{$a['name']}</span>\n";
        	echo "</a>\n";
        	echo "</li>\n";
	}
	echo "</div>";	
		
     if (is_maintainer () || is_administrator() ) { 	
	
       echo "<li>\n";
       echo "<a href=\"{$cfg_site}document/manage.php\">\n";
       echo "<i class='bx bx-cog'></i>\n";
       echo "<span class=\"links_name\">Curadoria</span>\n";
       echo "</a>\n";
        echo "</li>\n";
	 }


 
	 
	if ( is_administrator()) { 		
	
     echo "<button class='submenu-btn'><li><i class='bx bx-wrench'></i>\n";
	   echo "<span class='links_name'>Adminsitração</span>\n"; 
	    echo "<i class='bx bx-chevron-down'></i></li>\n";
	   echo "</button>\n";

    echo "<div class='submenu-items'>";
		echo "<li><a href=\"{$cfg_site}document/list.php?tid=0\">\n";
		  echo "<i class='bx bx-box'></i>\n";
		  echo "<span class='links_name'>Coleção</span>";
		echo "</a></li>";
		echo "<li>\n";
			echo "<a href=\"{$cfg_site}user/list.php\">\n";
			echo "<i class='bx bx-user'></i>\n";
			echo "<span class=\"links_name\">Usuários</span>\n";
		  echo "</a>\n";
    echo "</li>\n";
           echo "<li>\n";
		echo "<a href=\"{$cfg_site}information\">\n";
		echo "<i class='bx bx-list-plus'></i></i>\n";
		echo "<span class=\"links_name\">Tipo de informações</span>\n";
		echo "</a>\n";
	        echo "</li>\n";;

            echo "<li>\n";
		  echo "<a href=\"{$cfg_site}ods\">\n";
		  echo "<i class='bx bx-list-plus'></i></i>\n";
		  echo "<span class=\"links_name\">ODS</span>\n";
		  echo "</a>\n";
	  echo "</li>\n";

           echo "</div>\n";


	 }

       echo "<li class=\"log_out\">\n";
       echo "<a id='logOut' href=\"{$cfg_site}user/logout.php\">\n";
       echo "<i class='bx bx-log-out'></i>\n";
       echo "<span class=\"links_name\">Sair</span>\n";
       echo "</a>\n";
       echo "</li>\n";
       echo "</ul>\n";
       echo "</div>\n";

  echo "\n\n";


 echo "</div><!-- end #sidebar--> \n";
 echo "\n\n";

?>
<script>
	var dropdown = document.getElementsByClassName("submenu-btn")
	var i ;

	for (i = 0; i < dropdown.length; i++) {
  		dropdown[i].addEventListener("click", function() {
    		this.classList.toggle("active");
    		var dropdownContent = this.nextElementSibling;
    		if (dropdownContent.style.display === "block") {
      			dropdownContent.style.display = "none";
    		} else {
      			dropdownContent.style.display = "block";
   		 }
 	     });
	}	
	
</script>

<?php

}


function page_head() {
	global $cfg_site;
	
	echo "<section class=\"home-section\">\n";
	echo "<nav>\n";
	echo "<div class=\"sidebar-button\">\n";
	echo "<i class=\"bx bx-menu sidebarBtn\"></i>\n";
	echo "<span class=\"dashboard\"></span>\n";
	echo "</div>\n";
?>	
	<script type="text/javascript"> 

  	const siteUrl = "<?php echo $cfg_site; ?>";
	
		let urlSite = siteUrl;
    console.log (urlSite);


	$(document).ready(function() {
		
		$("#inputSearch").keypress(function(e) {
            if (e.which == 13) {
				       var words = $("#inputSearch").val();
                 var url = urlSite+"/document/results.php?words="+words;
			          location.replace(url);
           return false;
        }
     });
		
		$ ("#btnSearch").click(function()  {
			   var words = $("#inputSearch").val();
			  // alert(words);
			   var url = urlSite+"document/results.php?words="+words;
			   location.replace(url);
			                   
			});

      
	  
			
	});
	
	
	</script> 
	
<?php	
	echo "<div class=\"search-box\">\n";
    echo "<input type=\"text\" id=\"inputSearch\" placeholder=\"Procurar...\">";
    echo "<i id=\"btnSearch\" class='bx bx-search' ></i>";
    echo "</div>";
	
	echo "<div class=\"profile-details\">\n";

   if (is_user()){
	$username = $_SESSION['susername'];

	echo "<span class=\"admin_name\">\n";
    echo "<div class=\"dropdown\">";
    echo "<button class=\"dropbtn\">$username";
    echo " <i class='bx bx-chevron-down' ></i>";
    echo " </button>";
    echo "<div class=\"dropdown-content\">";
    echo "<a href=\"{$cfg_site}user/edit.php?uid={$_SESSION['suid']}\">Editar</a>\n";
    echo "<a href=\"{$cfg_site}user/change.php?uid={$_SESSION['suid']}\">Trocar Senha</a>\n";
    echo "</div>";
    echo "</div> ";
    echo "</div></span> ";
   
   }
   

	echo "</div>\n";
	
	echo "</nav>\n";
	

}




function page_end ($url = '')
{	
   
  echo "</div> <!--end class recent-sales-boxes-->";
  echo "</div> <!--end class sales-boxes-->";
echo "</div><!--end class home-content-->\n";

 $anoatual = date('Y');
 
/*  echo "<div class=\"footer\">";
 echo "<div>"; 
 echo "<p class=\"copyright\">@copyright 2002-$anoatual. Sistema de Bibliotecas da Unicamp. Nou-Rau v.3 desenvolvido por DTI/SBU";
 echo "</div>";
 echo "</div><!--End#footer -->";*/
 
   
 echo "</section>";
 

 
 
/*	<p div="copyright">@copyright 2002-$anoatual. Sistema de Bibliotecas da Unicamp. Nou-Rau v.2 desenvolvido por DTI/SBU<br>$copyright1.<br><a href="https://creativecommons.org/licenses/by-nc/3.0/br/deed.pt_BR" target="_blank"><img src="{$cfg_site}images/by-nc.png" border="0" style="width:90px;height:20px;" alt='Link para o Site do SBU' title='Link para o Creative Commons'  ></a> </p>
	<p div="copyrightl">Sistema Nou-rau</p>
	</div><!--End#footer -->*/

echo <<<HTML

	<script>
	   let sidebar = document.querySelector(".sidebar");
	   let sidebarBtn = document.querySelector(".sidebarBtn");
	   sidebarBtn.onclick = function() {
		   sidebar.classList.toggle("active");
		  if(sidebar.classList.contains("active")){
			sidebarBtn.classList.replace("bx-menu" ,"bx-menu-alt-right");
		  }else
			sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
	}
	 </script>
HTML;
	 
  html_footer();
}

/*-------------- feedback functions --------------*/

function message($url,$msg,$type){
global $cfg_site;	

  if (empty($url))
    $url = $cfg_site;

  if (empty($msg)) 
	$msg = "Um erro ineseperado ocorreu.";

  if (empty($type))
	$msg = "warning";
  
  echo "<script type='text/javascript'> ";
  echo "sessionStorage.setItem('my_report',".json_encode("<strong>$msg</strong>").");";
  echo " sessionStorage.setItem('type',". json_encode($type).");";		
  echo "location.href='".$url."';";
  echo "</script>";
exit();	
}



function error ($url = '',$msg, $type)
{
 global $cfg_site;

  if (empty($url))
    $url = $cfg_site;


  echo "<script type='text/javascript'> ";
  echo "sessionStorage.setItem('my_report',".json_encode("<strong>$msg</strong>").");";
  echo " sessionStorage.setItem('type',". json_encode($type).");";		
  echo "location.href='".$url."';";
  echo "</script>";

exit();	
}

function error_teses ($msg, $url = '')
{
  global $cfg_site;
  $error = _('Error');
  if (empty($url))
    $url = $cfg_site;
/*  page_begin_aux($msg,"<meta http-equiv=\"refresh\" content=\"5; URL=$url\">");
  page_begin('b');
  echo "<div id='mainContent'>";*/
  echo html_p(html_error(html_big("$error: $msg")));
  if (!empty($url))
    echo html_p(html_a(_('Click here to continue'), $url));
  /*echo "</div><!--End#maninContent1-->";
  page_end();*/
  exit();
}

function confirm ($msg, $url, $back = '')
{

 page_begin_aux($msg);
 page_begin();
  echo "<div id='mainContent1'>";
  $yes = 'Sim';
  $no = 'Não';

echo <<<HTML
<form method="post" action="$url">
<table align="center" border="0" cellpadding="8" cellspacing="0">
<tr><td align="center" colspan="2"><b>$msg</b></td></tr>
<tr><td align="center"><input type="submit" name="conf" value="$yes"></td>
<td align="center"><input type="submit" name="conf" value="$no"></td></tr>
</table>
</form>
HTML;

 echo "</div><!--#mainContent1-->\n";
	page_end();
  exit();
}

function remove ($msg, $url, $notify, $back = '')
{

  $yes = "Sim";
  $no = "Não";

echo <<<HTML
  <form method="post" action="$url">
<table align="center" border="0" cellpadding="8" cellspacing="0">
</td></tr>
<tr><td align="center" colspan="2"><b>$msg</b></td></tr>
<tr><td align="center"><input type="submit" name="conf" value="$yes"></td>
<td align="center"><input type="submit" name="conf" value="$no"></td></tr>
</table>
</form>
HTML;

  exit();
}


?>
