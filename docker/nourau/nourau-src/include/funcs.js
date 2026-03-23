/*
*   Arquivo: funcs.js      
*
*   funcoes em JavaScript
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
   	xmlHttp.open("GET","CarregaSubTopico.php?idtopico="+idtopico,true);
   	xmlHttp.send(null);

   }
    
      // verifica o estado do servidor AJAX
      // (bloco)funcao Mostra arquivo ou remoto 
   function stateChangedremote() {
   
      	if (xmlHttp.readyState==4) {
      		document.getElementById("remoten").innerHTML=xmlHttp.responseText;
      	}
   }
   
   
   function mostra(remote,nomearq) {
   
       	xmlHttp=GetXmlHttpObject()
      	if (xmlHttp==null) {
      		alert ("Seu navegador nao suporta AJAX!");
      		return;
      	}
   
      	xmlHttp.onreadystatechange=stateChangedremote;
      	xmlHttp.open("GET",'mostra.php?remote='+remote+'&nomearq='+nomearq,true);
      	xmlHttp.send(null);
   
      }
                     
             
   function valorwf(valor) {
   	document.getElementById('vwf').value = 'p';
   	if (valor != '11111111111111111')
	   document.getElementById('vwf').value = 'm';	   	
   }
   
   
   function nome_arquivo(valor) {
      document.getElementById('nomearq').value = valor;
   }
   
function selecionartodos(selObj,nameid) {
     
	if(document.getElementById(nameid).checked==true){
		for (var i=0; i<selObj.options.length; i++) {
	 		selObj.options[i].selected = true;
 		}
 	}
 	else{
		for (var i=0; i<selObj.options.length; i++) {
 	 		selObj.options[i].selected = false;
 	        }
 	}     
  
}
	   
function limpaselecionartodos() {
	document.getElementById("all").checked=false;      
} 
 
 
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
    
    function topicos(valor){
       	var incluir = document.getElementById('cbincluir').value;
       		
       	for(var i=0; i < document.formuser.tid.length; i++){
       		if(document.formuser.tid[i].checked)
       		document.getElementById('cbincluir').value +=document.formuser.tid[i].value;
       	}
       	
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