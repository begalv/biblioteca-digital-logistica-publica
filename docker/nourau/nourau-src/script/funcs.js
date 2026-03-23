/*
*   Arquivo: funcs.js      
*
*  Funções em JavaScript
*
*  
*/

 
// verifica se o navegador é compativel com AJAX
   function GetXmlHttpObject() {
   	var xmlHttp=null;
   	try {
   		// Firefox, Opera 8.0+, Safari, IE7, Chome
   		xmlHttp=new XMLHttpRequest();
   	}
   	catch (e) {
   		// Internet Explorer
   		try {
   			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP.6.0");
   		}
   		catch (e) {
   			try {
   				xmlHttp=new ActiveXObject("Msxml2.XMLHTTP.5.0");
   			}
   			catch (e) {
   				try {
   					xmlHttp=new ActiveXObject("Msxml2.XMLHTTP.4.0");
   				}
   				catch (e) {
   					try {
   						xmlHttp=new ActiveXObject("Msxml2.XMLHTTP.3.0");
   					}
   					catch (e) {
   						try {
   							xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
   						}
   						catch (e) {
   							xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
   						}
   					}
   				}
   			}
   		}
   	}
   	return xmlHttp;
   }


/* Funções : Módulo Tópico*/

  // (bloco)funcao Carrega o URL 
   // verifica o estado do servidor AJAX

   function stateChangedMudaURL() {

   	if (xmlHttp.readyState==4) {
   		document.getElementById("remoten").innerHTML=xmlHttp.responseText;
   	 }
   }


  function mostraURL(remote,url) {
      var checkbox = document.getElementById("remoteURL");
	  
  
   	xmlHttp=GetXmlHttpObject()
   	if (xmlHttp==null) {
   		alert ("Seu navegador nao suporta AJAX!");
   		return;
   	}

       if (checkbox.checked == true)  {
          remote ='s';
       } else {
           remote ='n';
          }
  

    xmlHttp.onreadystatechange=stateChangedMudaURL;
   	xmlHttp.open("GET","/manager/topic/mostraTopic.php?remote="+remote+'&url='+url+'&tipo=rmt',true);
   	xmlHttp.send(null);

   }
    
	
	 function stateChangedTipoDoc() {

   	if (xmlHttp.readyState==4) {
   		document.getElementById("tipoDoc").innerHTML=xmlHttp.responseText;
   	 }
   }
    
   function mostraTipoDoc(archieve,url) {
      var checkbox = document.getElementById("archieve");
	  
  
   	xmlHttp=GetXmlHttpObject()
   	if (xmlHttp==null) {
   		alert ("Seu navegador nao suporta AJAX!");
   		return;
   	}

       if (checkbox.checked == true)  {
           archieve ='s';
       } else {
           archieve ='n';
          }
  

    xmlHttp.onreadystatechange=stateChangedTipoDoc;
   	xmlHttp.open("GET","/manager/topic/mostraTopic.php?archieve="+archieve+'&url='+url+'&tipo=achv',true);
   	xmlHttp.send(null);

   }


/**/

   // (bloco)funcao Carrega o sub-topico 
   // verifica o estado do servidor AJAX

   function stateChanged() {

   	if (xmlHttp.readyState==4) {
   		document.getElementById("subtopico").innerHTML=xmlHttp.responseText;
   	}
   }


   function CarregaSubTopico(idtopico) {

    	xmlHttp=GetXmlHttpObject()
   	if (xmlHttp==null) {
   		alert ("Seu navegador nao suporta AJAX!");
   		return;
   	}

   	xmlHttp.onreadystatechange=stateChanged;
   	xmlHttp.open("GET","/document/CarregaSubTopico.php?idtopico="+idtopico,true);
   	xmlHttp.send(null);

   }
    
    
    
  // (bloco)funcao Carrega o sub-topico indice 
     // verifica o estado do servidor AJAX
  
     function stateChangedIndice() {
  
     	if (xmlHttp.readyState==4) {
     		document.getElementById("subTopicoIndice").innerHTML= xmlHttp.responseText;
     	}
     }
  
  
     function CarregaSubTopicoIndice(idtopico) {
  
      	xmlHttp=GetXmlHttpObject()
     	if (xmlHttp==null) {
     		alert ("Seu navegador nao suporta AJAX!");
     		return;
     	}
  
     	xmlHttp.onreadystatechange=stateChangedIndice;
     	xmlHttp.open("GET","/document/CarregaSubTopicoIndice.php?idtopico="+idtopico,true);
     	xmlHttp.send(null);
  
     }
    
  // verifica o estado do servidor AJAX
  // (bloco)funcao Mostra arquivo ou remoto 
   function stateChangedremote() {
   
      	if (xmlHttp.readyState==4) {
      		document.getElementById("remoten").innerHTML=xmlHttp.responseText;
      	}
   }
   
   
   function mostra(remote,nomearq,docstatus) {
   
       	xmlHttp=GetXmlHttpObject()
      	if (xmlHttp==null) {
      		alert ("Seu navegador nao suporta AJAX!");
      		return;
      	}
    
      	xmlHttp.onreadystatechange=stateChangedremote;
      	xmlHttp.open("GET",'mostra.php?remote='+remote+'&nomearq='+nomearq+'&docstatus='+docstatus ,true);
      	xmlHttp.send(null);
   
      }
      
  
     // (bloco)funcao Mostra arquivo ou remoto para os arquivos suplementares
     function stateChangedsf() {
     
        	if (xmlHttp.readyState==4) {
        	        
        	        opener.document.getElementById("arquivo_suplementar").innerHTML=xmlHttp.responseText;
        	}
     }
     
     
     function arquivosuplementar(did, tid) {
                //alert('oi');
         	xmlHttp=GetXmlHttpObject()
        	if (xmlHttp==null) {
        		alert ("Seu navegador nao suporta AJAX!");
        		return;
        	}
        	xmlHttp.onreadystatechange=stateChangedsf;
        	xmlHttp.open("GET",'mostrasf.php?did='+did+'&tid='+tid,true);
        	xmlHttp.send(null);     
      }  
            
                     
  /*Funçõe utilizada no script var/www/docuemnt/edit.php 
     Utilizados para carregar o nome do arquivo ou url 
   */                  
   
   function nome_arquivo(valor) {
      document.getElementById('nomearq').value = valor;
	  document.getElementById('nomearq').focus();
   }

/*Funçõe utilizada no script var/www/docuemnt/edit.php 
     Utilizados para carregar o nome do capa 
   */     
   function nome_capa(valor) {
      document.getElementById('nomecapa').value = valor;
	  document.getElementById('nomecapa').focus();
     document.getElementById('capa').setAttribute('src', valor);
   }


 /*Funções utilizadas no script var/www/document/search.php 
      Utilizados na pesquisa
   */
 
 function valorwf(valor) {
     	document.getElementById('vwf').value = 'p';
     	if (valor != '11111111111111110')
  	   document.getElementById('vwf').value = 'm';	   	
  }
   
 function selecionarTodosP(selObj) {
	 
   if (document.getElementById("wf").options[document.getElementById("wf").selectedIndex].value == '11111111111111110' ){
	 /* if (document.getElementById("t").options[document.getElementById("t").selectedIndex].value == '34')
		  document.getElementById('vwf').value = 'p1';	
		else 	 */
		  document.getElementById('vwf').value = 'p';
  }
   else 
	 document.getElementById('vwf').value = 'm';	 
	 
	 
	if (document.getElementById("topico").options[document.getElementById("topico").selectedIndex].value != '-1'){	
   		if(document.getElementById("t").options[document.getElementById("t").selectedIndex].value == '0'){
       		for (var i=0; i<document.getElementById("t").options.length; i++) {
      			document.getElementById("t").options[i].selected = true;
       		} 
    	}
	}
 }

  
  /* function limpaselecionartodos(nameid) {
 	document.getElementById(nameid).checked=false;      
  } */
  
/* this function shows the pop-up when
     user moves the mouse over the link */
    function Show(texto,e){   	
		var x=0;
		var y=0;
		var IE = document.all?true:false;
		if (IE) { // IE
			 /* get the mouse left position */
			x = event.clientX + document.body.scrollLeft;
			/* get the mouse top position  */
			y = event.clientY + document.body.scrollTop + 5;
		}
		else { // Netscape, Firefox, Opera */
			x = e.pageX;
			y = e.pageY + 5;
		}    	
			
      /* display the pop-up */
      document.getElementById('Popup').style.display='block';
      /* set the pop-up's left */
      document.getElementById('Popup').style.left = x;
      /* set the pop-up's top */
      document.getElementById('Popup').style.top = y;
      document.getElementById('Popup').innerHTML = trocacaracteres(texto);
    }
    
    /* this function hides the pop-up when
     user moves the mouse out of the link */
    function Hide(){
        /* hide the pop-up */
        document.getElementById('Popup').style.display='none';
    } 
    
   
   /*Funções utilizadas no script var/www/user/edit.php 
     Utilizados para adicionar e remover tópicos 
   */
   
   var NS4 = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) < 5);
   
   //Preenche o campo hidden
   
   function carregahidden(nomecampo, valor)
   {
     if (document.getElementById(nomecampo).value == null || document.getElementById(nomecampo).value == "") 
       virgula= '';
     else 
       virgula = ',';  
    
     document.getElementById(nomecampo).value += virgula + valor;    
   
   }
   
   function encontraTopico(theSel, valor)
   {
      var retorno = 0;
     
       if(theSel.length >= 0){ 
      
     	  for(i=theSel.length-1; i>=0; i--){
              if (theSel.options[i].value == valor)   
    	        retorno = 1; 	
          }
      
        }
        return retorno;
   }
      
   // Remove tópicos 
   function removeTopico(theSel)
   { 
   	var i;
   	
   	for(i=theSel.length-1; i>=0; i--)
   	   {
   	      if(theSel.options[i].selected)
   	      {
   	         //preenche a lista para a exclusão de topicos
   	         carregahidden('cbexcluir', theSel.options[i].value); 
                 //Remove a opção do combo-box
                 theSel.remove(i);
              } 	       	 
           }      
           document.getElementById("allremover").checked=false;
            
   }
   
   //Adicionando um nova opção
   function adicionaOpcao (theSel,texto,valor){
       var elOptNew = document.createElement('option');
       elOptNew.text = texto; 
       elOptNew.value = valor;
       try {
     	    theSel.add(elOptNew, null); // standards compliant; doesn't work in IE
       }
         catch(ex){
		theSel.add(elOptNew); // IE only
       }
   }
   
   //Adiciona Tópicos que o usuario terá acesso
   function adicionaTopico(theSelFrom,theSelTo)
   {
      var selLength = theSelFrom.length;
      var selectedText = new Array();
      var selectedValues = new Array();
      var selectedCount=0;
      var i;   

      for (i=selLength-1; i>=0; i--) {
	  if (theSelFrom.options[i].selected) { 
	    if (encontraTopico(theSelTo, theSelFrom.options[i].value) == 0) {
	        carregahidden('cbincluir', theSelFrom.options[i].value);  
  	        selectedText[selectedCount] = theSelFrom.options[i].text;
		selectedValues[selectedValues] = theSelFrom.options[i].value;
		selectedCount++;
                theSelFrom.options[i].selected = false;
	    }   
  	 }     
      }
      
      for(i=selectedCount-1; i>=0; i--){
       	adicionaOpcao(theSelTo, selectedText[i], selectedValues[i]);
      }
         
      document.getElementById("alladicionar").checked=false; 
       
   }
   
   
function selecionartodos(nameid) {
     var checkboxes = document.getElementsByName(nameid);
	 var button = document.getElementById('alladicionar');
		if(button.value == 'Selecionar todos'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'Desmarcar todos'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'Selecionar todos';
        }
   }  
     


function limpaselecionartodos(nameid) {
 	document.getElementById(nameid).checked=false;      
  } 

function herdar(nameid) {
     var checkboxes = document.getElementsByName(nameid);
	 var button = document.getElementById('alladicionar');
		if(button.value == 'Selecionar todos'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'Desmarcar todos'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'Selecionar todos';
        }
   }  



function topicos(valor){
       	var incluir = document.getElementById('cbincluir').value;
       		
       	for(var i=0; i < document.formuser.tid.length; i++){
       		if(document.formuser.tid[i].checked)
       		document.getElementById('cbincluir').value +=document.formuser.tid[i].value;
       	}
       	
   }
   

function janelaArquivoSuplementar (URL,height,width){ 
      window.open(URL,"janela1","height ="+ height+", width ="+ width+" ,top="+((screen.height-height)/2)+",left="+((screen.width-width)/2)+",status=no,location=no,scrollbars=no"); 
} 

function VerificarEnter(e) {
        var gt = new Gettext({ 'domain' : 'nou-rau' });
        var evento = window.event || e;
        var tecla = evento.keyCode || evento.witch;
        if (tecla == 13) {
               alert(gt.gettext('Please press the button: Send my password now'));
			  //alert(gt.gettext('Cancel'));
                return false;
        }

}

function retira_acentos(palavra) {
	com_acento = 'áàãâäéèêëíìîïóòõôöúùûüçÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÖÔÚÙÛÜÇ';
	sem_acento = 'aaaaaeeeeiiiiooooouuuucAAAAAEEEEIIIIOOOOOUUUUC';
	nova='';

	for(i=0;i<palavra.length;i++) {
		if (com_acento.search(palavra.substr(i,1))>=0) {
			nova+=sem_acento.substr(com_acento.search(palavra.substr(i,1)),1);
		}
		else {
			nova+=palavra.substr(i,1);
		}
	}
	return nova;
}




function valida(email) {
	var gt = new Gettext({ 'domain' : 'nou-rau' });
if (document.getElementById("email").value == "") {
		alert(gt.gettext('The field EMAIL should be specified.'));
		document.getElementById("email").focus();
		return false;
	}
	/*else{
		 var er = /^(_)[a-zA-Z0-9][a-zA-Z0-9\._-]+@([a-zA-Z0-9\._-]+\.)[a-zA-Z-0-9]{2}/;
         if(!er.exec(document.getElementById("email").value)){ 
            alert ("O campo E-MAIL deve ser conter um endereço eletrônico válido!");
			document.getElementById("email").focus();
            return false;
        }
		 */
		/*var filtro=/^.+@.+\..{2,3}$/;
			if (filtro.test(document.getElementById("email").value) == false) {
			alert ("O campo E-MAIL deve ser conter um endereço eletrônico válido!");
				document.getElementById("email").focus();
			return false;
			}*/
	//}
  
    if (document.getElementById("password").value == "") {
		alert(gt.gettext('The field PASSWORD should be specified.'));
		document.getElementById("password").focus();
		return false;
	}
}

function mostraDialogo(mensagem, tipo, tempo){
    
    // se houver outro alert desse sendo exibido, cancela essa requisição
    if($("#message").is(":visible")){
        return false;
    }

    // se não setar o tempo, o padrão é 3 segundos
    if(!tempo){
        var tempo = 3000;
    }

    // se não setar o tipo, o padrão é alert-info
    if(!tipo){
        var tipo = "info";
    }

    // monta o css da mensagem para que fique flutuando na frente de todos elementos da página
   /* var cssMessage = "display: block; position: fixed; top: 10%; left: 40%; right: 20%; width: 40%; padding-top: 10px; z-index: 9999; background-color:#fff; color: #0A2558; ";
    var cssInner = "margin: 0 auto; ";*/
	//box-shadow: 1px 1px 5px black;
	 var cssMessage = "display: block; position: fixed; top: 10%; left: 80%; right: 20%; width: 15%; padding-top: 10px; z-index: 9999; ";
	 var cssInner = "margin: 0 auto;";

    // monta o html da mensagem com Bootstrap
    var dialogo = "";
    dialogo += '<div id="message" style="'+cssMessage+'">';
    dialogo += '    <div class="alert-box '+tipo+'" style="'+cssInner+'">';
    dialogo += '    <a href="#"></a> ';
  
    dialogo +=          mensagem;
    dialogo += '    </div>';
    dialogo += '</div>';

    // adiciona ao body a mensagem com o efeito de fade
    $("body").append(dialogo);
    $("#message").hide();
    $("#message").fadeIn(200);

    // contador de tempo para a mensagem sumir
    setTimeout(function() {
        $('#message').fadeOut(300, function(){
            $(this).remove();
        });
    }, tempo); // milliseconds

}
