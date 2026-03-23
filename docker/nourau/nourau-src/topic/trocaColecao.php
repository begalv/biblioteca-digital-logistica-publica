<?php

// NOU-RAU - Copyright (C) 2002 Instituto Vale do Futuro
// This program is free software; see COPYING for details.

require_once '../include/start.php';
require_once BASE . 'include/defs_d.php';
require_once BASE . 'include/format.php';
require_once BASE . 'include/format_d.php';
require_once BASE . 'include/format_t.php';
require_once BASE . 'include/page.php';
require_once BASE . 'include/page_d.php';
require_once BASE . 'include/util.php';
require_once BASE . 'include/util_d.php';

format_document();


/*-------------- functions --------------*/
?>

<script type="text/javascript">
	 var msg = sessionStorage.getItem("my_report");
	  var type = sessionStorage.getItem("type");
	   if( msg ){
		   mostraDialogo(msg, type, 4500); 	
		   sessionStorage.removeItem("my_report");
		   sessionStorage.removeItem("type");
	}
		
	$(document).ready(function() {

		$("#troca_colecao").change(function(){
			let valor = $(this).val();
			let nome_select = "#colecao_origem";
           
		   //limpar o conteudo             
            $('#colecao_origem').find('option').not(':first').remove();
			$('#colecao_origem').find('optgroup').remove();
			$('#tipoInf').find('option').not(':first').remove();  
			//console.log("Valor Escolhido foi: "+valor);
			//habilitar o select
			$('#colecao_origem').removeAttr('disabled');
			carrega_colecao(valor, nome_select)
		});
		
	
		$("#colecao_origem").change(function(){
			let valor = $(this).val();  
			type_information(valor)
		});
		
		$("#troca_colecao_destino").change(function(){
			let valor = $(this).val();
			let nome_select = "#colecao_destino";
            //limpar o conteudo   		
     		$("#colecao_destino").find('option').not(':first').remove();
			$("#colecao_destino").find('optgroup').remove();
		    $("#colecao_destino").find('option').not(':first').remove();  
			console.log("Valor Escolhido foi: "+valor);
	        //habilitar o select	
    		$("#colecao_destino").removeAttr('disabled');
			
			carrega_colecao(valor, nome_select)
		});
		
		function carrega_colecao (topic_id, nome){
			var valor = topic_id; 
			var nome = nome;
			console.log("Valor Escolhido foi: "+valor +" : Nome do select " +nome);
			$.ajax({
				  type: "POST",  
				  url: "action.php?op=l",  
				  data: {tid:valor},
				  dataType: "json",       
				  success: function(response)  {
					 var len = response.length;
                     var optgroup='';
					 var group = 'n';
                    					
                    if (len == 0) {
                       type_information(valor);
					    $(nome).attr('disabled', 'disabled');
					}			
					else{
						// Add data to state dropdown
						for( var i = 0; i<len; i++){
						 
							   var colecao_id = response[i]['id'];
							   var colecao_name = response[i]['name'];
							   var archieve = response[i]['archieve'];
							   var parent_id = response[i]['parent_id'];
								
								if (archieve=='n'){
									
								  var pid_atual = parent_id;
								  console.log(pid_atual);
								  
								 if (group == 's')
									 optgroup +=  "</optgroup>";  		
								 
								  optgroup +=  "<optgroup label ='"+ colecao_name +"'>";
									
								}	
								else{ 		   
								
								   if (pid_atual != parent_id) 
									  optgroup +=  "</optgroup>";  
								
									optgroup +=  "<option value='"+ colecao_id +"' >"+ colecao_name +"</option>";
								}
								
								group = archieve;
								
						}  
						
						$(nome).append(optgroup);		
					}	
				  }
			});
			
		}
		
		
		
		function type_information (topic_id){
			var valor = topic_id; 
			console.log("Valor Escolhido foi: "+valor);
			 $.ajax({
				  type: "POST",  
				  url: "action.php?op=TI",  
				  data: {tid:valor},
				  dataType: "json",       
				  success: function(response)  {
					  var len = response.length;
					 // console.log(response);
				 /*  $("#tipoInf").html(response);
				   console.log(response);*/
				   for( var i = 0; i<len; i++){
						var id = response[i]['id'];
						var name = response[i]['name'];
						var qtde = response[i]['quantidade'];
						$("#tipoInf").append("<option value='"+ id +"' >"+ name +"("+qtde+")</option>");		
						//console.log(id + ' ' + name);
					}
				  }
			});
		}
		
		
		 $("#trocar").button().click(function() {
			
			var colecao_origem =  $("#troca_colecao").find("option:selected").text()+  ' ->' + $("#colecao_origem").find("option:selected").text();
			var colecao_destino = $("#troca_colecao_destino").find("option:selected").text() + ' ->' + $("#colecao_destino").find("option:selected").text();
	  		var msg = "O(s) documento(s) da coleção " + colecao_origem + " vão ser movidos para a coleção "+ colecao_destino +". Você tem certeza que deseja mover os documentos ?"
			
			if (!$('#troca_colecao').val() || !$('#colecao_origem').val() || !$('#tipoInf').val() || !$('#troca_colecao_destino').val() || !$('#colecao_destino').val() ) {
				//alert('Enter your name!');
			}
			else {
		
				$( "#msg_dialog" ).text( msg );			
				$("#dialog-trocar").dialog("open");
				return false;
			}	
	  });	
			
		
		
		$( "#dialog-trocar" ).dialog({
          autoOpen: false, 
		  resizable: false,
		  height: "auto",
		  width: 400,
		  modal: true,
		  buttons: {
			Trocar: function() {
				  $( this ).dialog( "close" );
				   var colecao_origem =  $("#colecao_origem").val();
				   var colecao_destino = $("#colecao_destino").val();
				   var tipoInf= $("#tipoInf").val();
				   var op = 'tc'; 
				   var conf = 'Sim'
				   $.post("action.php",{op:op,did:did,conf:conf},
				  function(resposta){
						if (resposta == 'sucesso') {    					 
						    var url = "https://www.bibliotecadigital.unicamp.br/manager/document/list.php?tid="+tid;
						    sessionStorage.setItem("my_report", <?php echo json_encode( "<strong>O Documento foi Apagado!</strong>" ); ?> );
						    sessionStorage.setItem("type", <?php echo json_encode("success"); ?> );
						    location.replace(url);
						}
				 });	         
		  	},
			Cancelar: function() {
			  $( this ).dialog( "close" );
			}
		  }
     });
		
		
	});
	
	 


    function redirect_page(){
	  //window.location = location.href;
	  window.location.reload();
	}
	
	
	

</script> 


<?php


function format_document ()
{
  global $cfg_site;
  global $zeus_auth;
  
  page_begin(); 
  echo html_h("Troca de Coleções");
  
  check_administrator_rights();
  
  html_form_begin($_SERVER['PHP_SELF'], true, 'post' );
   
echo "<fieldset>";
echo "<legend>Coleção de Origem:</legend>";
  
    $q = db_query("SELECT id, name FROM topic WHERE parent_id = 0 ORDER BY NAME");   
	echo "<p><label><b>Coleções Principais:</b></label>";
		echo "<select name='Pcolecao_origem' size='1' id='troca_colecao' required >";
		echo "<option selected='selected'  value=''>-- escolha uma das coleções prinicpais --</option>";
		while ($atp = db_fetch_array($q)) {
			echo "<option value='".$atp['id']."'>{$atp['name']}</option>";   
		}
	echo "</select></p>";
	
	
	echo "<p><label><b>Coleções:</b></label>";
		echo "<select name='colecao_origem' size='1' id='colecao_origem' required >";
		echo "<option selected='selected'  value=''>-- escolha uma das coleções --</option>";
	echo "</select></p>";
	
	echo "<p><label><b>Tipos de Informações:</b></label>";
		echo "<select name='TipoInf' size='1' id='tipoInf' required >";
		echo "<option selected='selected'  value=''>-- escolha o tipo de Informação --</option>";
	echo "</select></p>";
	
echo "</fieldset>";	

echo "<fieldset>";
echo "<legend>Coleção de Destino:</legend>";
  
    $q = db_query("SELECT id, name FROM topic WHERE parent_id = 0 ORDER BY NAME");   
	echo "<p><label><b>Coleções Principais:</b></label>";
		echo "<select name='Pcolecao_destino' size='1' id='troca_colecao_destino' required >";
		echo "<option selected='selected'  value=''>-- escolha uma das coleções prinicpais --</option>";
		while ($atp = db_fetch_array($q)) {
			echo "<option value='".$atp['id']."'>{$atp['name']}</option>";   
		}
	echo "</select></p>";
	
	
	echo "<p><label><b>Coleções:</b></label>";
		echo "<select name='SColecao_destino' size='1' id='colecao_destino' required >";
		echo "<option selected='selected'  value=''>-- escolha uma das coleções --</option>";
	echo "</select></p>";
	
echo "</fieldset>";	

 echo "<br><button class='button_action' id='trocar'><span style='text-decoration:underline'>Trocar de coleção</span></button>"; 
 echo "<div id='dialog-trocar' title='Apagar'>";
 echo " <p id='msg_dialog'><span class='ui-icon ui-icon-alert' style='float:left; margin:12px 12px 20px 0;'></span></p>";
 echo "</div>";


  
}


?>
