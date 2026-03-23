<?php


// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.
// Listar usuários: /user/list.php

require_once '../include/start.php';
require_once BASE . 'include/format.php';
require_once BASE . 'include/page_u.php';
require_once BASE . 'include/util.php';
require_once BASE . 'include/control.php';

$desc = isset($_GET['desc']) ? $_GET['desc'] : 'n';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'a';

check_administrator_rights();


page_begin();

?>

<script type="text/javascript"> 



 var msg = sessionStorage.getItem("my_report");
  var type = sessionStorage.getItem("type");
   if( msg ){
	   mostraDialogo(msg, type, 4500); 	
       sessionStorage.removeItem("my_report");
	   sessionStorage.removeItem("type");
}

$(document).ready(function($) {
	
	//Esconde os botões
	$(document).find('.btn_save').hide();
	$(document).find('.btn_cancel').hide(); 
	 

	$( "#btn_add" ).click(function() {
			 
			if (!$('#ods_name').val()) {
				alert('Enter your name!');
			}
			else {
				var dados =$("#frm_add").serialize();
				console.log(dados);
				$.ajax({
				  type: "POST",  
				  url: "action.php?op=i",  
				  data: dados,
				  dataType: "json",       
				  success: function(response)  
				  {
					$('#msg').html('');
					console.log(response.id);
					if(response.status) {
						//$('#'+action+'_model').modal('hide')
						//console.log('id= '+id )
						location.reload(true);
					     	sessionStorage.setItem("my_report", <?php echo json_encode( "<strong>O ODS foi adicionado!</strong>" ); ?> );
					     	sessionStorage.setItem("type", <?php echo json_encode("success"); ?> );
					} else {
						 sessionStorage.setItem("my_report", <?php echo json_encode( "<strong> Erro ao adicionar ODS</strong>" ); ?> );
					     	 sessionStorage.setItem("type", <?php echo json_encode("failure"); ?> );
						 console.log(response.op);
					}
			  
					},
				 error: function(jqXHR, textStatus, errorThrown) {
					sessionStorage.setItem("my_report", <?php echo json_encode( "Error'+textStatus+'!'+errorThrown" ); ?> );
				     	sessionStorage.setItem("type", <?php echo json_encode("failure"); ?> );
				     
				}  
				});
			}		  
				 
	});		
	
		
	$( ".btn_delete" ).click(function() {  
	  var id = $(this).attr("id");
	  var item = $(this).closest("tr")   // Finds the closest row <tr> 
                       .find(".data-name")     // Gets a descendent with class="nr"
                      .text();    

      var msg = "O ODS " + item + " será removido permanetemente. Você tem certeza que deseja apagar ?"
      $( "p" ).text( msg );
      $("#dialog-confirm").data('id', id).dialog("open");   	  
		
	});
	
	
		
	$( ".btn_edit" ).click(function() {
		
	  	event.preventDefault();
		var tbl_row = $(this).closest('tr');

		var row_id = tbl_row.attr('row_id');
		tbl_row.find('.btn_edit').hide(); 
		tbl_row.find('.btn_delete').hide(); 
		
		//make the whole row editable
		tbl_row.find('.data-name')
		.attr('contenteditable', 'true')
		.attr('edit_type', 'button')
		.addClass('bg-warning')
		.css('padding','3px')

        //--->add the original entry > start
		tbl_row.find('.row_data').each(function(index, val) 
		{  
			//this will help in case user decided to click on cancel button
			$(this).attr('original_entry', $(this).html());
 	}); 		


		//make the whole row editable
		tbl_row.find('.numero')
		.attr('contenteditable', 'true')
		.attr('edit_type', 'button')
		.addClass('bg-warning')
		.css('padding','3px')

        //--->add the original entry > start
		tbl_row.find('.row_data').each(function(index, val) 
		{  
			//this will help in case user decided to click on cancel button
			$(this).attr('original_entry', $(this).html());
 	}); 		


		tbl_row.find('.btn_save').show();
		tbl_row.find('.btn_cancel').show();


	});		
		
	$( ".btn_save" ).click(function() {
		event.preventDefault();
		
		var tbl_row = $(this).closest('tr');
		
		var id = $(this).attr("id");
		var item = $(this).closest("tr")   // Finds the closest row <tr> 
			.find(".data-name")     // Gets a descendent with class="nr"
            .text();     
	   //	console.log(item);			  
	   var numero = $(this).closest("tr")   // Finds the closest row <tr> 
			.find(".numero")     // Gets a descendent with class="nr"
            .text();     


		//hide save and cacel buttons
		tbl_row.find('.btn_save').hide();
		tbl_row.find('.btn_cancel').hide();

		//show edit button
		tbl_row.find('.btn_edit').show();
        tbl_row.find('.btn_delete').show();
	
			
             			
		if (item.length==0){
		   alert('O ODS deve ser preenchido!');
		}
	    	else {
				$.ajax({
				  type: "POST",  
				  url: "action.php?op=u",  
				  data:  {id:id, ods_name:item, ods_ordem:numero},
				  dataType: "json",       
				  success: function(response)  
				  {
					$('#msg').html('');
					if(response.status) {
						console.log(response.resp);
						location.reload(true);
						sessionStorage.setItem("my_report", <?php echo json_encode( "<strong>O ODS foi atualizado!</strong>" ); ?> );
						sessionStorage.setItem("type", <?php echo json_encode("success"); ?> );						
					} else {
						$('#msg').html('<div class="alert alert-danger ">Error! to insert record</div>');  
						//console.log(response.op);
					}
			  
					},
						error: function(jqXHR, textStatus, errorThrown) {
						$('#msg').html('<div class="alert alert-danger ">Error'+textStatus+'!'+errorThrown);
					}  
				});
			}
				 
	});			

	
	
   $( "#dialog-confirm" ).dialog({
		        autoOpen: false, 
				resizable: false,
				height: "auto",
				width: 400,
				modal: true,
				buttons: {
					  "Apagar": function() {       
					   var id = $(this).data('id');		
					   $( this ).dialog( "close" );
					  apagar(id);
				},
					"Cancelar": function() {
					$( this ).dialog( "close" );

				}
			}
		});


	$( "#dialog-action" ).dialog({
		        autoOpen: false, 
				resizable: false,
				height: "auto",
				width: 800,
				modal: true,
				buttons: {
					  "Atribuir": function() {       
					   var id =$("#id").text();	
					   var tid = $("#colecao").val();						   
					   console.log ('tid : '+ tid +' idtf: '+ id)
					   $( this ).dialog( "close" );
					   atribuir(tid,id);
				},
					"Cancelar": function() {
					$( this ).dialog( "close" );
					location.reload(true);
				    sessionStorage.setItem("my_report", <?php echo json_encode( "<strong>O ODS foi adicionado!</strong>" ); ?> );
				    sessionStorage.setItem("type", <?php echo json_encode("success"); ?> );
				}
			}
	});
		  
}); 
	
 function apagar(id){

   $.ajax({
		  type: "POST",  
		  url: "action.php?op=d&id="+id,  
		  dataType: "json",       
		  success: function(response)  
		  {
			$('#msg').html('');
			if(response.status) {
				//$('#'+action+'_model').modal('hide');
				 console.log(response.resp);
				 location.reload(true);
				 sessionStorage.setItem("my_report", <?php echo json_encode( "<strong>O ODS foi Apagado!</strong>" ); ?> );
				 sessionStorage.setItem("type", <?php echo json_encode("success"); ?> );
			} else {
				 var msg = "<strong>Erro: O ODS não foi apagado!<strong>";    
				 mostraDialogo(msg, 'failure', 4500); 	 
			}
      
			},
				error: function(jqXHR, textStatus, errorThrown) {
				$('#msg').html('<div class="alert alert-danger ">Erro: '+textStatus+'!'+errorThrown);
			}  
		});
 }		

			
</script> 

<?php

 //breadcrumb(array($cfg_site=>_('Home'), 'user/?uid='.$_SESSION['suid']=>_('Profile'), ''=>_('User System') ));

 echo html_h('Objetivos de Desenvolvimento Sustentável');

 $url = $_SERVER['PHP_SELF'];

// set sort column

$ord = 'ordem';
$ord .= ($desc == 'y') ? ' DESC' : ' ASC';


  echo "<form id=\"frm_add\" class='horizon' method=\"post\" action=\"\">";
      echo "<input class='text1' type='text' id='ods_name' name='ods_name' placeholder='Objetivo de Desenvolvimento Sustentável'>";
	  echo "<input class='text2' type='text' id='ods_ordem' name='ods_ordem' placeholder='Ordem de Exibição'>";
      echo "<button id=\"btn_add\" class=\"button1\" type=\"button\">Adicionar</button>";
  echo "</form>";
  
 

  echo "<div id=\"msg\"></div>";

  $query= db_query("Select id, description, ordem from nr_ods ORDER BY ". $ord);
  echo "<div>";
  echo "<table  id ='tabela_info' class=\"tabela-bases\" cellpadding=\"3\" cellspacing=\"0\" >";
  echo "<tr>";
  echo "<th></th>";
  format_header('Descrição', $url . '?sort=a', $sort == 'a', $desc == 'y');
  format_header('Ordem de Exibição', $url . '?sort=a', $sort == 'a', $desc == 'y');
  echo "</tr>";
 
  $i=0;
 while ($a = pg_fetch_array($query)) {
    $id = $a['id'];
    $name = htmlspecialchars(($id == '1') ? _($a['description']) : $a['description']);
	$ordem = $a['ordem'];
  	
     $class = ($i++ & 1) ? 'odd' : 'even';
     echo "<tr rowid ='".$i."' class=".$class.">"; 
	 //{$cfg_site}user/edit.php?uid=$uid
     echo "<td class='icon_1'><a href='' rel=\"noopener noreferrer\" id=\"$id\" class=\"btn_edit\" alt=\"Alterar o Objetivos de Desenvolvimento Sustentável\" title=\"Alterar o Objetivo de Desenvolvimento Sustentável\"><i class='bx bx-edit-alt'> </i></a>";
	 /* $doc = db_simple_query("SELECT COUNT(ID) FROM nr_document WHERE typeinform_id ={$a['id']} AND status = 'a'");
	  if ($doc == 0)	 */
		echo "<a target=\"_blank\" rel=\"noopener noreferrer\" id=\"$id\" class=\"btn_delete\" alt=\"Remover o Objetivo de Desenvolvimento Sustentável\" title=\"Remover o Tipo de Informação\"> <i class='bx bx-trash' > </i></a>";

	  	echo " <a href='' rel=\"noopener noreferrer\" id=\"$id\" class=\"btn_save\" alt=\"Salvar o  Objetivo de Desenvolvimento Sustentável\" title=\"Salvar o Objetivo de Desenvolvimento Sustentável\"><i class='bx bx-save'></i></a>
	       <a href='' rel=\"noopener noreferrer\" id=\"$id\" class=\"btn_cancel\" alt=\"Cancelar a alteração\" title=\"Cancelar a alteração\"><i class='bx bx-x-circle'></i></a></td>";
	 	echo "<td class='data-name'>$name</td>";
	 	echo "<td class='numero'>$ordem</td>";
    }

	echo "<div id='dialog-confirm' title='Apagar'>";
	echo " <p id='msg'><span class='ui-icon ui-icon-alert' style='float:left; margin:12px 12px 20px 0;'></span></p>";
	echo "</div>";
	echo "<span id='r'><span>";
   
  
 echo "</table></div>";
 
page_end();


/*-------------- functions --------------*/


function format_header ($title, $url, $active, $descending)
{
  global $cfg_site;

  if ($active && !$descending)
    $url .= '&desc=y'; 
  echo "<th class=\"titulo\"><b>" . html_a($title, $url) . "</b>";
  if ($active) {
    if ($descending)
      echo " <img src=\"{$cfg_site}images/desc.gif\"></th>";
    else
      echo " <img src=\"{$cfg_site}images/asc.gif\"></th>";
  }
  else
    echo '</th>';
}

?>
