//var host = window.location.host;
//var caminho = "/sistema/org";
//if(host == "usina.commit.inf.br"){
//    caminho = "";
//}
var url = window.location.pathname;
var caminho = url.substring(0, url.indexOf("/admin/", 0));

//    window.onbeforeunload = function (my_event) {
//    var message = "Your logout page has been opened in a new window, Check that out. Make sure that you have enabled pop up in your browser to see that?";
//    if (typeof my_event == 'undefined') {
//    my_event = window.event;
//    }
//    if (my_event) {
//    newWindow=window.open('test.html','','width=450,height=350')
//    newWindow.document.write("<p>This is 'newWindow'</p>")
//    newWindow.focus();
//    my_event.returnValue = message;
//    }
//    return message;
//    }


//------------------------------------------------------------------------------
//inicia o jquery
//$(window).unload(function() {
//    
//    if(window.screenLeft >screen.width){
//        alert("Window Closed");
//    }
//    else{
//        alert("Window NOT closed");
//    }
//    
//    
//});

$(document).ready(function(){

    var url = window.location; 
    var url2 = url.toString();
    var achou_link1 = url2.search("processos/update");
    var achou_link2 = url2.search("informativos/updateitem");   
    var achou_link3 = url2.search("processos/insert");
    var achou_link4 = url2.search("informativos/additem");   
    var url_add     = "";
    var url_delete  = "";
    var url_links   = "";    
    
    if(achou_link1 >= 0 || achou_link3 >= 0){
        url_links   = "/admin/processos/links";
        url_add     = "/admin/processos/addlink";
        url_delete  = "/admin/processos/deletelink";
    }
    if(achou_link2 >= 0 || achou_link4 >= 0){
        url_links   = "/admin/informativos/links";
        url_add     = "/admin/informativos/addlink";
        url_delete  = "/admin/informativos/deletelink";
    }
    if(achou_link1 >= 0 || achou_link2 >= 0){
        var id_processo = $('#id').val();
        $.post(caminho+url_links,{
                id: id_processo
            },
            function(resposta){
                $('#div-links').html(resposta);
            }
        );
    }
    if(url2.search("#") > 0 ){
        document.location.href = document.location.href;
    }

//
//$(window).unload(function(event) {
//                var output = '';
//                for (property in event) {
//                  output += property + ': ' + event[property]+'; ';
//                }
//                alert(output);
//                if(event.clientY < 0) {
//                    //do whatever you want when closing the window..
//                }
//            });


    //--------------------------------------------------------------------------
    //controle de borda
    $('input[tabindex=1]').focus();

    //--------------------------------------------------------------------------
    //controle de borda
    $('#layout-default').corner('15px');
    $('.btn').corner('5px');
    $('#lista-titulo-calendario').corner('5px');
    $('.btn-menu').corner('cc:#E8EDF1 5px'); 
    $('.btn-menu-sub').corner('cc:#025C7F 5px'); 
    $('#painel-calendario').corner('left');
    $('#painel-calendario-titulo').corner('top 5px');
    $('#lista-borda-calendario').corner('top 5px');
    $('.div-titulo').corner('top 5px');
    $('#conteudo').corner('5px');
    $('.div-menu').corner('top 5px');
    $('#lista-titulo, .div-menu-titulo').corner('5px');
    $('#lista-borda').corner('5px');
    $('.botao').corner('5px');
    $('.botao, .botao-rel').corner('5px');
    $('#corner').corner('15px');

    //--------------------------------------------------------------------------
    //controle de mascara
    $("#periodo_inicio").mask("99/99/9999");
    $("#periodo_fim").mask("99/99/9999");
    $("#data_inicial").mask("99/99/9999 99:99");
    $("#data_fim").mask("99/99/9999 99:99");
    $("#celular").mask("99 9999-9999");
    $("#mes_ref").mask('99/9999');
    $('#valor_inicio, #valor_fim, #valor_inicial, #valor_final, #valor').priceFormat({prefix: '', centsSeparator: ',', thousandsSeparator: '.', centsLimit:2, allowNegative: true});
    
    //--------------------------------------------------------------------------
    //datepicker
//    $(function(){
        $("#data").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior'
        });
        $("#data").mask("99/99/9999");
//    });
//    $("#data").datepicker();
    //--------------------------------------------------------------------------
    //controla o fechamento da mensagem
    $('#PluginFlashMessenger.success').slideDown(500).delay(3800).slideUp(500);
    $('#PluginFlashMessenger.warning').slideDown(500).delay(8000).slideUp(500);
    $('#PluginFlashMessenger.error').slideDown(500).delay(8000).slideUp(500);
    $('#PluginFlashMessenger.notice').slideDown(500).delay(8000).slideUp(500);

    //--------------------------------------------------------------------------
    //controla o click do menu
    $('div[id*="MainMenuTexto"]').click(function(){
        var key = $(this).attr("accesskey");
        
        $('div[id*="MainSubMenu"]').hide();
        $('div[id*="MainMenuTexto"]').css({
            'background':'',
            'border':'0px solid #787878',
            'color':'#000'
        });
        $(this).css({
            'background':'#1f5a7c',
            'color':'#fff',
            'border':'0px solid #FFF'
        });
        $('#MainSubMenu'+key).show();
    });

    //--------------------------------------------------------------------------
    //esconde as divs no caso de menu
    $('.div-menu-titulo').click(function(){
        $('#'+$(this).attr("itemref")).slideToggle("slow");
    });

    //--------------------------------------------------------------------------
    //opcao para o botao pesquisa
    $('#lista-titulo-menu-pesquisa').click(function(){
        $('#'+$(this).attr("itemref")).slideToggle("slow");
    });

    //--------------------------------------------------------------------------
    //Seleciona toda as opcoes na permissao do sistema
    $("input#todas").click(function(){
        var valor = $(this).val();
        var checked_status = this.checked;
        $("input[itemid="+valor+"]").each(function(){this.checked = checked_status;});
    });
    
    //--------------------------------------------------------------------------
    //Seleciona toda as opcoes para gerar relatório
    $("#selecionar_todos").click(function(){
//        var valor = $(this).val();
        var checked_status = this.checked;
        $(".check_grafico").each(function(){this.checked = checked_status;});
    });
    
    //--------------------------------------------------------------------------
    //muda a cor da linha quando clicada
    $('tr[itemref="clique"]').click(function(){
        var ativo = $(this).attr('itemid')
        var cor   = $(this).attr('class')
        if(ativo == 0){
            $(this).css({'background':'#ffff66'});
            $(this).attr('itemid','1');
        }else{
            if(cor == 'linha2'){
                $(this).css({'background':'#edf2ff'});
            }else{
                $(this).css({'background':'#fff'});
            }
            $(this).attr('itemid','0');
        }
    });
    
    $('#selectCargos').change(function(){
        var ele = $(this);
        var valor = ele.val();
        //busca processos atrelados aos cargos
        $.post(caminho+'/admin/ajax/processos',{ 	  
                cargo: valor
            },
            function(resposta){
                $('#targetAjaxProc').html(resposta);
                $('#targetAjaxArq').show();
            }
        );
        $.post(caminho+'/admin/ajax/mo',{ 	  
                cargo: valor
            },
            function(resposta){
                $('#MO').val(resposta);
                $('#targetMO, #MO').show();
            }
        );
    });
    $('#selectCargosInformativos').change(function(){
        var ele = $(this);
        var valor = ele.val();
        //busca processos atrelados aos cargos
        $.post(caminho+'/admin/ajax/informativos',{ 	  
                cargo: valor
            },
            function(resposta){
                $('#targetAjaxProc').html(resposta);
            }
        );
    });    
    
    $('.arqExclui').live('click',(function(){
        if(window.confirm('Deseja realmente excluir esse arquivo?')){
            var ele = $(this);
            var arquivo = ele.attr('id');
            var id_cargo = $('#selectCargos').val();
            //busca processos atrelados aos cargos
            $.post(caminho+'/admin/ajax/arquivos',{ 	  
                    caminho: caminho,
                    arquivo: arquivo,
                    id_cargo: id_cargo
                },
                function(resposta){
                    $('#mensagem').html(resposta);
                    $('#PluginFlashMessenger').slideDown(500).delay(3800).slideUp(500);
                    ele.parent().parent().remove();
                }
            );
        }
        return false;
    }));
   
    
    $('#add-link').live('click',(function(){
            var link = $('#link').val();
            var descricao = $('#descricao').val();
            var id_processo = $('#id').val();                       
            if(link){
                $.post(caminho+url_add,{
                        link:      link,
                        descricao: descricao
                    },
                    function(resposta){
                        $.post(caminho+url_links,{
                                id: id_processo
                            },
                            function(resposta){
                                $('#div-links').html(resposta);
                            }
                        );
                    }
                );
            }
            
        return false;
    }));
    $('.delete-link').live('click',(function(){
            var link = $(this).attr("id");
            var id_processo = $('#id').val();
            if(window.confirm('Deseja realmente excluir esse registro?')){
                $.post(caminho+url_delete,{
                        link: link
                    },
                    function(resposta){
                        if(resposta=="OK"){
                            $.post(caminho+url_links,{
                                    id: id_processo
                                },
                                function(resposta){
                                    $('#div-links').html(resposta);
                                }
                            );
                        }                    
                    }
                );
            }
        return false;
    }));   
   
    //--------------------------------------------------------------------------
    //Visualizar log de erro
    $('.btnViewLog').click(function(){
        var div = '#visualizacao_log_'+$(this).attr('id');

        $(div).dialog({
            title: 'Visualizar log de erro',
            width: 600,
            height: 300,
            resizable: false,
            modal:true,
            buttons: {"Sair": function() {$(this).dialog("destroy");}}
        });
    });
    /*
    //adiciona linha na tela de cadastro de horas extras por digitacao
    $('.addImg').live('click',function(){
        var div = $('#live_1').html();
        var num = $('#num');
        var novo = parseInt(num.val())+1;
        num.val(novo);
        div = div.replace('remove_1','remove_'+novo);
        $('#digitacao').append('<div class="live" id="live_'+novo+'">'+div+'</div>');
    });
    //remove linha na tela de cadastro de horas extras por digitacao
    $('.removeImg').live('click',function(){
        var total = $('.live').size();
        if(total > 1){        
            var btn = $(this);
            var id = btn.attr('id');
            var quebra = id.split('_');
            id = quebra[1];            
            $('#live_'+id).remove();
        }else{
            var num = $('#num');
            num.val(1);
            var div = $('#live_1').html();
            div = div.replace('remove_1','remove_'+novo);
            $('#digitacao').append('<div class="live" id="live_'+novo+'">'+div+'</div>');
        }
    });
    */

    $('#btnLayout').click(function(){
        var btn = $(this);
        var mostra = btn.attr('alt');
        if(mostra == 1){
            btn.attr('alt','2');
            $('#importacaoLayout').show();
            btn.val('Clique aqui para fechar o layout de importação');
        }else{
            btn.attr('alt','1');
            $('#importacaoLayout').hide();
            btn.val('Clique aqui para abrir o layout de importação');
        }
        
    });

});
//------------------------------------------------------------------------------
//controla os links
    function OpenSelf(url){
	javascript:window.self.location.href = url;
    }

function openModal(url,w,h,titulo,btn_enviar,btn_fechar,id_form){
    var div     = $('<div id="div-modal" style="display:none;"></div>').appendTo('body');
    var form    =  null;
    if(!btn_enviar){
        btn_enviar = "Enviar";
    }
    if(!btn_fechar){
        btn_fechar = "Fechar";
    }
    div.load(
        url,
        function (responseText, textStatus, XMLHttpRequest) {
            var achouFormulario = responseText.search('<form');
            div.dialog({
                width: w,
                height: h,
                modal: true,
                closeOnEscape: true,
                close: function(event, ui) {
                    div.remove();
                },
                open: function(event, ui) {
                    if(achouFormulario < 0){
                        $("#dialog-submit").attr("disabled","disabled").addClass("ui-state-disabled");
                    }                                
                },
                title: titulo,
                buttons: [
                {
                    text: btn_enviar,
                    id: 'dialog-submit',
                    click: function() {
                        $('<div id="fundo-carregando" class="ui-widget-overlay" style="position: fixed;"></div>').appendTo('body');
                        $('<div id="msg-carregando"></div>').appendTo('body');
                        if(!id_form){
                            form = $('#div-modal '+id_form);
                        }else{
                            form = $('#div-modal form');
                        }
                        var action  = form.attr('action');
                        form.attr('action','javascript:void(0);');
                        $.post(action ,form.serialize(),  function(data){
                            var achou = data.search('<div id="PluginFlashMessenger" class="success">');
                            if(achou == -1){
                                $('#msg-carregando').remove();
                                $('#fundo-carregando').remove();
                                div.html(data);
                            }else{
                                div.remove();
                                window.location = window.location;
                            }
                        });
                        form.attr('action',action);
                        return null;
                    }
                },
                {
                    text: btn_fechar,
                    click: function() {
                        div.remove();
                    }
                }
                ]
                              
            });
        }
        );
}
    
    function confirmaModal(url,w,h,titulo){
        var div     = $('<div id="div-modal" style="display:none;">'+titulo+'</div>').appendTo('body');
        div.dialog({
            width: w,
            height: h,
            modal: true,
            closeOnEscape: true,
            close: function(event, ui) {div.remove();},
            title: "",
            buttons: {"Sim": function() {
                                $('<div id="fundo-carregando" class="ui-widget-overlay" style="position: fixed;"></div>').appendTo('body');
                                $('<div id="msg-carregando"></div>').appendTo('body');
                                $.post(url , function(data){
                                    if(data ==""){
                                        window.location = window.location;
                                    }else{                                        
                                        $('#msg-carregando').remove();
                                        $('#fundo-carregando').remove();
                                        var fk_constraint = data.search('ORA-02292');
                                        if(fk_constraint > 0){
                                            alert("Registro não pode ser excluido pois está sendo utilizado!");
                                        }else{
                                            alert("Ocorreu um erro ao relaizar a operação!");
                                        }
                                    }
                                });
                                
                             },
                      "Fechar": function() {div.remove();}}
        });
    }

    function openPop(url,width,height,scrollbars){
        javascript:window.open(url, "location=1,status=0,scrollbars="+scrollbars+", width="+width+",height="+height+",resizable=0,menubar=0");
    }

    function AtualizaGridFatura(cliente, unidade){
        $(document).ready(function(){
            $("#gride-fatura").load(caminho+'/admin/fatura/lista/cliente/'+cliente+'/unidade/'+unidade);
        });
    }
    
    
    function formaMoeda(valor) {
    var num = valor;
    var x = 0;

    if (num < 0) {
        num = Math.abs(num);
        x = 1;
    }

    if (isNaN(num))
        num = "0";
    var cents = Math.floor((num * 100 + 0.5) % 100);

    num = Math.floor((num * 100 + 0.5) / 100).toString();

    if (cents < 10)
        cents = "0" + cents;
    for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
        num = num.substring(0, num.length - (4 * i + 3)) + '.'
                + num.substring(num.length - (4 * i + 3));

    var ret = num + ',' + cents;

    if (x == 1)
        ret = '-' + ret;

    return ret;
}

    function formaMoeda2(valor) {
        var ret = valor;
        if(valor){
            var achou = valor.indexOf(",");
            if(achou > 0 ){
                ret = valor.replace(".","");
                ret = ret.replace(",",".");
            }
        }else{
            ret = 0;
        }        
        return parseFloat(ret);
    }
    
