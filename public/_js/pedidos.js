/**
  *Document   : jmduque
  *Created on : 05/09/2011, 11:33:27
  *Author     : julio
  *Description: javascript para os dados inseridos os relatorios
  */

//var host = window.location.hostname;
//var caminho = "/sistema/org";
//if(host == "usina.commit.inf.br" || (host == "dev.commit.inf.br" && window.location.port)){
//    caminho = "";
//}
var url = window.location.pathname;
var caminho = url.substring(0, url.indexOf("/admin/", 0));
//------------------------------------------------------------------------------
//inicia o jquery
$(document).ready(function(){  
    
    $(".data").mask("99/99/9999");
    
    function Listar(){
        $("#dialog-envio").remove();
        var periodo = $("#periodo").val();
        var data_pedido = $("#data_pedido").val();
        if(data_pedido == undefined){
            data_pedido = $("#data_atual").val();
        }
        if(periodo == undefined){
            periodo = "";
        }
        var comprador = "";
        var de_pedido = "";
        var ate_pedido = "";
        var de_data_pedido = "";
        var ate_data_pedido = "";
        var de_fornecedor = "";
        var ate_fornecedor = "";
        var enviado = "";
        var ativo = "";
        var confirmado = "";
        var observacao = "";
        var ordenacao = "";
        var ordenar_por = "";
        if(periodo == ""){
            comprador = $("#selectCompradores").val();
            de_pedido = $("#de_pedido").val();
            ate_pedido = $("#ate_pedido").val();
            de_data_pedido = $("#de_data_pedido").val();
            ate_data_pedido = $("#ate_data_pedido").val();
            de_fornecedor = $("#de_fornecedor").val();
            ate_fornecedor = $("#ate_fornecedor").val();
            enviado = $("#enviado").val();
            ativo = $("#ativo").val();
            confirmado = $("#confirmado").val();
            observacao = $("#observacao").val();
            ordenacao = $("#ordenacao").val();
            ordenar_por = $("#ordenar_por").val();
        }else{
            comprador = $("#Compradores").val();
            de_data_pedido = data_pedido;
            ate_data_pedido = data_pedido;
            ordenacao = $("#ordenacao").val();
            ordenar_por = $("#ordenar_por").val();
        }
        $('#lista-pedidos').html('<div class="loading" style="text-align:center;color:#FF0000;font-weight:bold;height:400px;vertical-align: middle;width:100%;">Carregando...</div>');
        
        $('#lista-pedidos').load(
        
            caminho+'/admin/pedidos/listar/', 
            {
                comprador       :comprador,
                de_pedido       :de_pedido,
                ate_pedido      :ate_pedido,
                de_data_pedido  :de_data_pedido,
                ate_data_pedido :ate_data_pedido,
                de_fornecedor   :de_fornecedor,
                ate_fornecedor  :ate_fornecedor,
                enviado         :enviado,
                ativo           :ativo,
                confirmado      :confirmado,
                observacao      :observacao,
                periodo         :periodo,
                ordenacao       :ordenacao,
                ordenar_por     :ordenar_por
            }, // omit this param object to issue a GET request instead a POST request, otherwise you may provide post parameters within the object
            function (responseText, textStatus, XMLHttpRequest) {
            // remove the loading class
            //dialog.removeClass('loading');
                    
            }
            );
    }
    
    function ListarTransportadoras(elemento){
        elemento.load(
            caminho+'/admin/pedidos/transportadoras/', 
            {}, // omit this param object to issue a GET request instead a POST request, otherwise you may provide post parameters within the object
            function (responseText, textStatus, XMLHttpRequest) {
                // remove the loading class
                //dialog.removeClass('loading');
                if(textStatus == "error"){
                    elemento.html("Ocorreu um erro ao buscar as transportadoras<br/><br/>Tente Novamente mais tarde!");
                }
            }
        );        
    }
    
    var url = window.location; 
    var url2 = url.toString();
    var achou_diario = url2.search("pedidos/diario");
    var achou_mensal = url2.search("pedidos/mensal");
    if(achou_diario >= 0 || achou_mensal >= 0 ){
        Listar();
    }
    
    //--------------------------------------------------------------------------
    //Visualizar log de erro
    $('.linha_pedido td').live('click',(function(){
        var id = $(this).parent().attr("id");
        var title = $(this).attr('title');
        
        var classe = $(this).attr('class');
        //        var data_organograma = '';
        //        data_organograma = $("#data_organograma").val();
        //        data_organograma = data_organograma.replace(/\//gi,"-");
        if(classe == "observacao"){
            if(title){
                $('#dialog').dialog({
                    title: "Observações do Pedido",
                    width: 600,
                    height: 450,
                    resizable: false,
                    position: 'top',                        
                    modal:true,
                    buttons: {
                        "Fechar": function() {
                            $(this).dialog("destroy");
                        }
                    }
                }
                ).load(
                    caminho+'/admin/ajax/obspedido/id_pedido/'+id, 
                    {}, // omit this param object to issue a GET request instead a POST request, otherwise you may provide post parameters within the object
                    function (responseText, textStatus, XMLHttpRequest) {
                    // remove the loading class
                    //dialog.removeClass('loading');
                        $("#"+id+" .observacao").html("Lida");
                    }
                    ).replace(/\n/g,'<br />');
            }           
        }else{
            if(classe != "link" && classe != "td_selecionar"){
                $('#dialog').dialog({
                    title: "Detalhes do Pedido",
                    width: 800,
                    height: 650,
                    resizable: false,
                    position: 'top',
                    open: function ()
                    {
                        $('#dialog').html('');
                        $(this).append('<div class="loading" style="text-align:center;color:#FF0000;font-weight:bold">Carregando...</div>');
                    },         
                    modal:true,
                    buttons: {
                        "Fechar": function() {
                            $(this).dialog("destroy");
                        }
                    }
                }
                )
                .load(
                    caminho+'/admin/pedidos/detalhe/id_pedido/'+id, 
                    {}, // omit this param object to issue a GET request instead a POST request, otherwise you may provide post parameters within the object
                    function (responseText, textStatus, XMLHttpRequest) {
                    // remove the loading class
                    //dialog.removeClass('loading');
                        
                    }
                    );   
            }           
        }
    }));
    
    //--------------------------------------------------------------------------
    //Adicionar e-mail
    $('.add_email').live('click',(function(){
        var id = $(this).parents('.linha_pedido').attr("id");
        $('#dialog').dialog({
                    title: "Emails em cópia",
                    width: 600,
                    height: 300,
                    resizable: false,
                    position: 'top',                        
                    modal:true,
                    buttons: {
                        "Salvar": function() {
                            var email_copia = $('#email_copia').val();
                            $(this).dialog().load(
                                caminho+'/admin/pedidos/emailcopia/id_pedido/'+id, 
                                {email_copia : email_copia, id_pedido: id}, // omit this param object to issue a GET request instead a POST request, otherwise you may provide post parameters within the object
                                function (responseText, textStatus, XMLHttpRequest) {
                                    var email_atual = $("#"+id+" .add_email").attr("title");
                                    
                                    if(email_copia && responseText == "OK"){
                                        $("#"+id+" .add_email").attr("title", email_copia);
                                        $(this).dialog("destroy");
                                    }else{
                                        $("#"+id+" .add_email").attr("title", email_atual);
                                    }
                                
                                }
                                );
                        },
                        "Fechar": function() {
                            $(this).dialog("destroy");
                        }
                    }
                }
                ).load(
                    caminho+'/admin/pedidos/emailcopia/id_pedido/'+id,
                    function () {
                    }
                    );
    })); 
    
    //--------------------------------------------------------------------------
    //Adicionar e-mail
    $('.add_obs').live('click',(function(){
        var id = $(this).parents('.linha_pedido').attr("id");
        $('#dialog').dialog({
                    title: "Observação do Comprador",
                    width: 600,
                    height: 450,
                    resizable: false,
                    position: 'top',                        
                    modal:true,
                    buttons: {
                        "Salvar": function() {
                            var observacao_comprador = $('#observacao_comprador').val();
                            $(this).dialog().load(
                                caminho+'/admin/pedidos/observacaocomprador/id_pedido/'+id, 
                                {observacao_comprador : observacao_comprador, id_pedido: id}, // omit this param object to issue a GET request instead a POST request, otherwise you may provide post parameters within the object
                                function (responseText, textStatus, XMLHttpRequest) {
                                    var observacao_atual = $("#"+id+" .add_obs").attr("title");
                                    
                                    if(observacao_comprador && responseText == "OK"){
                                        $("#"+id+" .add_obs").attr("title", observacao_comprador);
                                        $(this).dialog("destroy");
                                    }else{
                                        $("#"+id+" .add_obs").attr("title", observacao_atual);
                                    }
                                }
                                );
                        },
                        "Fechar": function() {
                            $(this).dialog("destroy");
                        }
                    }
                }
                ).load(
                    caminho+'/admin/pedidos/observacaocomprador/id_pedido/'+id,
                    function () {
                    }
                    );
    })); 
    
    //--------------------------------------------------------------------------
    //Visualizar log de erro
    $('#Buscar').live('click',(function(){
        Listar();
    })); 
    
    var xhr_enviar = null;
    var xhr_inativar = null;
    
    function Enviar_Pedidos(pedidos,i,transportadora,frete){
        var porcentagem = 0;
        xhr_enviar = $.post(caminho+'/admin/pedidos/enviar',{
            pedido          : pedidos[i],
            transportadora  : transportadora,
            frete          : frete
        },
        function(resposta){
            var total = $("#numero-total").val();
            var enviados = parseInt($("#numero-enviados").val());
            var nao_enviados = ($("#nao-enviados").val());
            var qtde_nao_enviados = parseInt($("#qtde-nao-enviados").val());
//            resposta = resposta.replace(/^\s+|\s+$/g,"");
            if(resposta.length>1){
//                alert(resposta.length);
                qtde_nao_enviados = qtde_nao_enviados+1;
                if(nao_enviados == ""){
                    nao_enviados = pedidos[i];
                }else{
                    nao_enviados = nao_enviados+" , "+pedidos[i];                        
                }
                $("#nao-enviados").val(nao_enviados);
                $("#qtde-nao-enviados").val(qtde_nao_enviados);
            }else{
                enviados = enviados+1;
                $("#"+pedidos[i]+" .enviou").html("Sim");
                porcentagem = ((enviados+qtde_nao_enviados)/total)*100;
                $("#enviados").progressbar({
                    value: porcentagem
                });
                $("#numero-enviados").val(enviados);
            }
            var msg_nao_enviados = "";
            if(nao_enviados != ""){
                msg_nao_enviados = qtde_nao_enviados+" com erros no envio";
            }
            if(enviados == total){
                $('#linhanvio').dialog("destroy");
                Listar();
            }else{
                if( enviados+qtde_nao_enviados == total){
                    $("#msg-envio").html(enviados+" de "+total+" pedidos enviados<br/><br/>Pedidos com erro de envios:"+nao_enviados);
                }else{
                    $("#msg-envio").html(enviados+" de "+total+" pedidos enviados<br/>"+msg_nao_enviados);
                }
            }
            i++;    
            if(pedidos.length > i){
                Enviar_Pedidos(pedidos, i, transportadora, frete);
            }else{
                
            }
            
        }
        );
        
    }
    //--------------------------------------------------------------------------
    //Enviar Pedidos Selecionados
    $('#Enviar_Selecionados').live('click',function() {
//        var action  = $('#form-Enviar').attr('action');
//        var id = $(this).attr('id');
        var qtde = 0;
        var enviados = 0;
        var confirmados = 0;
        var pedidos = new Array();
        $("#numero-enviados").val(0);
//        $("#enviados").html("Enviando Pedidos!");
        $('#form-Enviar').attr('action','javascript:void(0);');        
        $('input:checkbox').each( function() {                
            if(this.checked == true){
                var dados = $(this).attr('id');
                var valores = dados.split("|");
                pedidos[qtde] = valores[1];
                if(valores[2]=="s"){
                    enviados++;
                }
                if(valores[3]=="s"){
                    confirmados++;
                }
                qtde++;
            }
        });        
        if(qtde > 0){
            $("#numero-total").val(qtde);
                $('#dialog-envio').dialog({
                    title: "Enviando Pedidos",
                    width: 400,
                    height: 250,
                    resizable: false,
                    position: 'center',
                    modal:true,
                    buttons: [{
                        id:"btn-continuar",
                        text: "Continuar",
                        click: function() {
                            $(".ui-dialog-buttonset #btn-continuar").hide();
                            $(".ui-dialog-buttonset #btn-confirmar").show();
                            ListarTransportadoras($('#dialog-envio'));
                        }
                    },{
                        id:"btn-confirmar",
                        text: "Enviar",
                        click: function() {
                            var id_transportadora = $("#transportadora").val();
                            var flag_frete = $("#frete").val();
                            if((id_transportadora) && (flag_frete)){
								if((id_transportadora == 90191) && (flag_frete == "N")){
									$("#msgTransportadora").html("<label class='msg-erro'>Se 'Cotar frete' = NÃO, a transportadora não pode ser 'A COMBINAR'. </label>");
								}else{
									$(this).dialog().html('<h1>Enviando Pedidos, Aguarde!<h1><br/><br/><div id="enviados" ></div><br/><div id="msg-envio" ></div>');
									$("#btn-confirmar").attr("disabled","disabled").addClass("ui-state-disabled");
									$("#enviados").progressbar({
										value: 0
									});
									Enviar_Pedidos(pedidos, 0, id_transportadora, flag_frete);
								}
                            }else{
								$("#msgTransportadora").html("<label class='msg-erro'>" + id_transportadora + " - Transportadora ou Cotar frete? não preenchido. Verifique para continuar.</label>");
                                //$("#msgTransportadora").html("<label class='msg-erro'>Transportadora ou Cotar frete? não preenchido. Verifique para continuar.</label>");
                            }                           
                        }
                    },{
                        id:"btn-sair",
                        text: "Fechar",
                        click: function() {
                            $(this).dialog("destroy");
                            if(xhr_enviar!=null){
                                xhr_enviar.abort();
                            }
                            Listar();
                        }
                    }]
                }                    
                );
            if(enviados+confirmados > 0){
                $('#dialog-envio').html('Existem pedidos já enviados, deseja reenviar?');
                $(".ui-dialog-buttonset #btn-confirmar").hide();
            }else{
                $(".ui-dialog-buttonset #btn-continuar").hide();
                ListarTransportadoras($('#dialog-envio'));
            }
            $( 'a.ui-dialog-titlebar-close' ).remove();
            
        }
    });
    
    //--------------------------------------------------------------------------
    //Inativar Pedidos Selecionados
    $('#Inativar_Selecionados').live('click',function() {
        
        var qtde = 0;
        var enviados = 0;
        var confirmados = 0;
        //        $("#enviados").html("Enviando Pedidos!");
        $('#form-Enviar').attr('action','javascript:void(0);');
        
        
        var pedidos = new Array();        
        
        $('input:checkbox').each( function() {                
            if(this.checked == true){
                var dados = $(this).attr('id');
                var valores = dados.split("|");
                pedidos[qtde]= valores[1];
                if(valores[2]=="s"){
                    enviados++;
                }
                if(valores[3]=="s"){
                    confirmados++;
                }
                qtde++;
            }
        });        
        if(qtde > 0){
            $("#numero-total").val(qtde);
//            if(enviados+confirmados > 0){
//            //pedidos ja enviados
//            }else{
                $('#dialog-envio').dialog({
                    title: "Inativando Pedidos",
                    width: 400,
                    height: 250,
                    resizable: false,
                    position: 'center',
                    modal:true,
                    buttons: [{
                        id:"btn-inativar",
                        text: "Inativar",
                        click: function() {
                            $(this).dialog().html('<h1>Enviando Pedidos, Aguarde!<h1><br/><br/><div id="enviados" ></div><br/><div id="msg-envio" ></div>');                            
                            xhr_inativar = $.post(caminho+'/admin/pedidos/inativar',{
                                pedido          : pedidos
                            },
                            function(resposta){
                    //            resposta = resposta.replace(/^\s+|\s+$/g,"");
                                if(resposta.length>1){                                    
                                    $("#msg-envio").html("Os pedidos abaixo não foram enviados:<br/><br/>"+resposta);
                                }else{
                                    $(this).dialog("destroy");                            
                                    Listar();
                                }
                            });
                            $("#btn-inativar").attr("disabled","disabled").addClass("ui-state-disabled");
                            
                            
                        }
                    },{
                        id:"btn-sair",
                        text: "Fechar",
                        click: function() {
                            $(this).dialog("destroy");
                            if(xhr_inativar!=null){
                                xhr_inativar.abort();
                            }
                            Listar();
                        }
                    }]
                }
                ).html('<h1>Os pedidos selecionados serão inativados!<h1><br/><br/><div id="enviados" ></div><br/><div id="msg-envio" ></div>');
//            }
//            $( 'a.ui-dialog-titlebar-close' ).remove();
        }
    });
    
    
    
    //--------------------------------------------------------------------------
    //Alterar Comprador
    $('.alterar-data').live('click',(function(){
        var data = $(this).attr('id');
        $('#data_pedido').val(data); 
        Listar();
    }));
    $('.ordenar').live('click',(function(){
        var data = $(this).attr('id');
        var dados = data.split("-");
        //        alert("Ordenacao:"+dados[1]);
        //        alert("Ordenar por :"+dados[0]);
        
        
        $('#ordenacao').val(dados[1]); 
        $('#ordenar_por').val(dados[0]); 
        Listar();
    }));    
    
    $('#Compradores').change(function(){
        Listar();
    });
    
    
    //--------------------------------------------------------------------------
    //Visualizar log de erro
    $('#ul_listar li').live('click',(function(){
        var id = $(this).attr('id');
        var tipo = null;
        if(id == "li_nao_enviados"){
            tipo = "e";
        }
        if(id == "li_nao_confimados"){
            tipo = "c";
        }
        if(id == "li_desmarcar"){
            tipo = "d";
        }
        $('input:checkbox:not(:disabled)').each( function() {
            if(tipo != null){
                var dados = $(this).attr('id');
                var valores = dados.split("|");
                if(tipo == "e"){
                    if(valores[2]=="n"){
                        this.checked = true;
                    }else{
                        this.checked = false;
                    }
                }
                if(tipo == "c"){
                    if(valores[3]=="n"){
                        this.checked = true;
                    }else{
                        this.checked = false;
                    }
                }
                if(tipo == "d"){
                    this.checked = false;
                }                
            }else{
                this.checked = true;
            }            
        });
    }));
    $('#btn-historico').live('click',(function(){
        $('#tbl-historico').toggle();
        var display = $('#tbl-historico').css("display");
        if(display != "none"){
            $('#dialog').animate({
                scrollTop: $("#dialog").height()
            }, 800);
        }
    }));   
    
    $('#de_data_pedido').mouseout(function() {
        $('#ate_data_pedido').text($(this).text());
    });    
    
    $("#de_pedido").focusout(function() {
        $("#ate_pedido").val($(this).val());
    });
    $("#de_data_pedido").focusout(function() {
        $("#ate_data_pedido").val($(this).val());
    });
    $("#de_fornecedor").focusout(function() {
        $("#ate_fornecedor").val($(this).val());
    });    
});
