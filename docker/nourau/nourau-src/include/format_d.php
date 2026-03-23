<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

/*-------------- includes --------------*/

require_once BASE . 'include/html.php';

/*-------------- formating functions --------------*/

function format_bar ($left, $right = '')
{
 echo "<div class='totalResultado'>";
   echo "<p class='left'>".$left."</p>";
   echo "<p class='right'>".$right."</p>";
 echo "</div>";
}

function format_page_list ($pages, $current)
{
  global $cfg_site;

  $previous = 'Anterior';
  $next = 'Próxima';
  $last = 'Última';
  $first ='Primeira'; 	

  $menos = $current - 1;
  $mais = $current + 1;

  if ($pages == 1)
    return;
  echo "<div class='pagination'>";
  echo "<ul>";
  
  $url = "{$_SERVER['PHP_SELF']}?" . preg_replace('/&page=\d+/', '', $_SERVER['QUERY_STRING']);

 if ($current == 1) {
	echo "<li><a href='#' class='prevnext disablelink'>".$first."</a></li>";
	echo "<li><a href='#' class='prevnext disablelink'>".$previous."</a></li>";
  }
 else {
    $i = $menos;
	 $u = ($i == 1) ? $url : "$url&page=$i";
	echo "<li><a href='".$u."&page=1' class='prevnext'>".$first."</a></li>";
	echo "<li><a href='".$u."' class='prevnext'>".$previous."</a></li>";
 }

 if (($current-9)<1)
      $anterior = 1;
 else 
 	$anterior = $current-9;

   if (($current+9)>$pages)
     $posterior = $pages;
   else
     $posterior = $current+9;


   for ($i = $anterior; $i <= $posterior; $i++){
      if ($i == $current)
	      echo "<li><a href='#' class='currentpage'> ".$i."</a></li>"; 
	  else{
		  
		  $u = ($i == 1) ? $url : "$url&page=$i";
	   echo "<li><a href='".$u."'> ".$i."</a></li>"; 
	   }
    }

  if ($current == $pages){
    /*echo $next . '&nbsp;';
    echo "&Uacute;ltimo";*/
	echo "<li><a href='#' class='prevnext disablelink'>".$next."</a></li>";
	echo "<li><a href='#' class='prevnext disablelink'>".$last."</a></li>";
   }
  else {
     $i = $mais;
     /*echo html_a($next, "$url&page=$i") . '&nbsp;';
     echo html_a('&uacute;ltimo',"$url&page=$pages");*/
	 echo "<li><a href='".$url."&page=".$i."' class='prevnext'>".$next."</a></li>";
	echo "<li><a href='".$url."&page=".$pages."' class='prevnext'>".$last."</a></li>";
  }
  echo "</ul></div>\n";
}


//usado pelo htdig
/*function format_search_box ($center = false)
{
  global $cfg_htdig_conf, $cfg_site;
  global $adv, $matchesperpage, $method, $sort, $words;

  html_form_begin("{$cfg_site}document/results.php", false);

 if ($center)
    echo "<table align=\"center\" border=\"0\"><tr><td>\n";


   if ($adv == 'y') {
    $method_op = array('and'     => _('all words'),
                       'or'      => _('any words'),
                       'boolean' => _('logical expression'));
    $sort_op = array('score'    => _('score'),
                     'title'    => _('title'),
                     'time'     => _('time'),
                     'revscore' => _('reverse score'),
                     'revtitle' => _('reverse title'),
                     'revtime'  => _('reverse time'));
    $matches_op = array('10'  => '10',
                        '20'  => '20',
                        '50'  => '50',
                        '999' => _('all'));
    html_form_select(_('Selection'), 'method', $method_op, $method, false);
    html_form_select(_('Ordering'), 'sort', $sort_op, $sort, false);
    html_form_select(_('Matches per page'), 'matchesperpage', $matches_op,
                     $matchesperpage, false);
    if ($center)
      echo "</td></tr><tr><td>\n";
    else
      echo "<p>\n";
  }
  else {
    if (!empty($method))
      html_form_hidden('method', $method);
    if (!empty($sort))
      html_form_hidden('sort', $sort);
    if (!empty($matchesperpage))
      html_form_hidden('matchesperpage', $matchesperpage);
  }

  if (!empty($adv))
    html_form_hidden('adv', $adv);

  html_form_text(_('Search for'), 'words',100, $words, 200, false);
  html_form_submit(_('Search'));
  echo "&nbsp;&nbsp;";

  if ($adv == 'y')
    echo html_a(html_small(_('Normal search')),
                "{$cfg_site}document/search.php?words=" .
                rawurlencode(stripslashes($words)));
  else
    echo html_a(html_small(_('Advanced search')),
                "{$cfg_site}document/search.php?adv=y&words=" .
                rawurlencode(stripslashes($words)));

  if ($center)
    echo "</td></tr></table>\n";
  html_form_end();
}*/

function format_search_box_menu ()
{
  global $cfg_site, $session, $user;

  html_form_begin("{$cfg_site}cgi-bin/search.cgi", false);

  if (is_user())
    html_form_hidden('us',$_SESSION['suid']);

  if (!empty($user))
    html_form_hidden('us',$user);

  html_form_hidden('fl','p');

 html_form_hidden('ps','50');

  html_table_begin(true);
  html_table_item(html_b(_('Search for') . ':'), 'title');
  html_table_item('<input type="text" name="q" value="" maxlength="100" size="10">', 'odd');
  html_table_item('<input type="submit" value="' . _('Search') . '">', 'odd');
  html_table_item(html_a(html_small('Pesquisa Avançada'),
                         "{$cfg_site}document/search.php?adv=y"), 'odd');
  html_table_end();
  html_form_end();
}

?>