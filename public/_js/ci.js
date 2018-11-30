//var host = window.location.host;
//var caminho = "/sistema/org";
//if(host == "usina.commit.inf.br"){
//    caminho = "";
//}
var url = window.location.pathname;
var caminho = url.substring(0, url.indexOf("/admin/", 0));

$(document).ready(function(){
    var url = window.location; 
    var url2 = url.toString();
    //------------------------------------------------------------------------------
    //C.I.
    
   $(".add_anexo").live('click',function(){
       $('#tabela_anexar').append('<tr><td><input type="text" name="desc_arquivo[]" value="" /></td><td><input type="file" name="arquivo[]" value="arquivo[]" >&nbsp;<input type="button" value="-" class="delete_linha_anexo"/></td></tr>');                         
    });
//   $(".delete_linha_anexo").live('click',function(){
//       $(this).closest('tr').remove();
//    });
   
   $("#btn_anexo").click(function(){
           $("#form_anexo").submit();
//           $("#limpar").click();
        $('#msg-upload').dialog({
                    title: "Enviando Anexos",
                    width: 400,
                    height: 200,
                    resizable: false,
                    position: 'center',
                    modal:true,
                    buttons: {
                        "Fechar": function() {
                            $(this).dialog("close");
                        }
                    }
                }
                ).html('<br/><br/><h1>Enviando anexos, Aguarde!</h1>');
    });
   
    $('.delete-anexo').live('click',(function(){
            var anexo = $(this).attr("id");
            var ci = $('#id').val();
            if(window.confirm('Deseja realmente excluir esse registro?')){
                $.post(caminho+'/admin/ci/deleteanexo',{
                        anexo: anexo
                    },
                    function(resposta){
                        if(resposta=="OK"){
                            $.post(caminho+'/admin/ci/anexos',{
                                    id: ci
                                },
                                function(resposta){
                                    $('#div-anexos').html(resposta);
                                }
                            );
                        }                    
                    }
                );
            }
        return false;
    }));
    $('#div-anexos').load(
                caminho+'/admin/ci/anexos',
                {id: $('#id').val()},
                function (responseText, textStatus, XMLHttpRequest) {
                    aprovadores();
                    detalhesfinalidade();
                    despesasCI();
                });
                
    $('.download-anexo').live('click',(function(){
            var anexo = $(this).attr("id");            
            $("#anexo").val(anexo);
            $("#form_download").submit();
        return false;
    }));
   
    
    $('#form-ci #submeter').live('click',function(){
        $('<div class="ui-widget-overlay" style="position: fixed;"></div>').appendTo('body');
        $('<div id="msg-carregando"></div>').appendTo('body');
//        alert("teste");
    });
    $('.add_aprovador').click(function(){
        var div     = $('<div style="display:none;"></div>').appendTo('body');
        var ccusto  = ($(this).attr('id'));
        var tr_pai  = ($(this).closest("tr").attr('id'));
        var url_pai = url2.substr(0, url2.search("#"));
        var url     = caminho+'/admin/ajax/aprovadores';
        var retorno = "nada";
        div.load(
                url,
                {ccusto: ccusto},
                function (responseText, textStatus, XMLHttpRequest) {
                    div.dialog({
                        width: 600,
                        height: 200,
                        modal: true,
                        closeOnEscape: true,
                        close: function(event, ui) {div.remove();},
                        title: 'Aprovadores',
                        buttons: {"Adicionar": function() {
//                                alert("data");
//                                var cliente = $('#aprovador').val();
                                var action  = $('#form-aprovadores').attr('action');
                                $('#form-aprovadores').attr('action','javascript:void(0);');
                                
                                    $.post(action ,$("#form-aprovadores").serialize(),  function(data){
                                        retorno = data;
                                        if(retorno==""){
                                            div.remove();
                                            window.location = url_pai+"#"+tr_pai;
                                            window.location.reload();
//                                          alert("null");
                                        }
                                    });
                                    $('#form-aprovadores').attr('action',action);
                                    
                                    return null;
                                    
                            },
                                  "Fechar": function() {div.remove();}}
                    });
                }
            );
    });
    
    $('.add_aprovadorgeral').click(function(){
        var div     = $('<div style="display:none;"></div>').appendTo('body');
        var id_valor= ($(this).attr('id'));
        var url_pai = url2.substr(0, url2.search("#"));
        var url     = caminho+'/admin/ajax/aprovadores';
        var retorno = "nada";
        div.load(
                url,
                {id_valor: id_valor},
                function (responseText, textStatus, XMLHttpRequest) {
                    div.dialog({
                        width: 600,
                        height: 200,
                        modal: true,
                        closeOnEscape: true,
                        close: function(event, ui) {div.remove();},
                        title: 'Aprovadores',
                        buttons: {"Adicionar": function() {
                                var action  = $('#form-aprovadores').attr('action');
                                $('#form-aprovadores').attr('action','javascript:void(0);');
                                
                                    $.post(action ,$("#form-aprovadores").serialize(),  function(data){
                                        retorno = data;
                                        if(retorno==""){
                                            div.remove();
                                            window.location = url_pai;
                                            window.location.reload();
                                        }
                                    });
                                    $('#form-aprovadores').attr('action',action);
                                    
                                    return null;
                                    
                            },
                                  "Fechar": function() {div.remove();}}
                    });
                }
            );
    });
    
    $('.add_aprovadorfinalidadevalor').click(function(){
        var div         = $('<div style="display:none;"></div>').appendTo('body');
        var fin_valor   = ($(this).attr('id'));
        var url_pai     = url2.substr(0, url2.search("#"));
        var url         = caminho+'/admin/ajax/aprovadores';
        var retorno     = "nada";
        div.load(
                url,
                {fin_valor: fin_valor},
                function (responseText, textStatus, XMLHttpRequest) {
                    div.dialog({
                        width: 600,
                        height: 200,
                        modal: true,
                        closeOnEscape: true,
                        close: function(event, ui) {div.remove();},
                        title: 'Aprovadores',
                        buttons: {"Adicionar": function() {
                                var action  = $('#form-aprovadores').attr('action');
                                $('#form-aprovadores').attr('action','javascript:void(0);');
                                
                                    $.post(action ,$("#form-aprovadores").serialize(),  function(data){
                                        retorno = data;
                                        if(retorno==""){
                                            div.remove();
                                            window.location = url_pai;
                                            window.location.reload();
                                        }
                                    });
                                    $('#form-aprovadores').attr('action',action);
                                    
                                    return null;
                                    
                            },
                                  "Fechar": function() {div.remove();}}
                    });
                }
            );
    });
    
    $('.add_despesafinalidade').click(function(){
        var div         = $('<div style="display:none;"></div>').appendTo('body');
        var finalidade  = ($(this).attr('id'));
        var url_pai     = url2.substr(0, url2.search("#"));
        var url         = caminho+'/admin/ajax/novadespesa';
        var retorno     = "nada";
        div.load(
                url,
                $.param({finalidade: finalidade}),
                function (responseText, textStatus, XMLHttpRequest) {
                    div.dialog({
                        width: 600,
                        height: 280,
                        modal: true,
                        closeOnEscape: true,
                        close: function(event, ui) {div.remove();},
                        title: 'Adicionar tipo de despesa/adiantamento',
                        buttons: {"Adicionar": function() {
                                var action  = $('#form-despesa').attr('action');
                                $('#form-despesa').attr('action','javascript:void(0);');
                                
                                    $.post(action ,$("#form-despesa").serialize(),  function(data){
                                        retorno = data;
                                        if(retorno==""){
                                            div.remove();
//                                            window.location = url_pai;
                                            window.location.reload();
                                        }
                                    });
                                    $('#form-despesa').attr('action',action);
                                    
                                    return null;
                                    
                            },
                                  "Fechar": function() {div.remove();}}
                    });
                }
            );
    });
    
    $('.editarDespesaFinalidade').click(function(e){
        var div         = $('<div style="display:none;"></div>').appendTo('body');
        var url         = $(this).attr('href');
        var retorno     = "";
        div.load(
                url,
                function (responseText, textStatus, XMLHttpRequest) {
                    div.dialog({
                        width: 600,
                        height: 280,
                        modal: true,
                        closeOnEscape: true,
                        close: function(event, ui) {div.remove();},
                        title: 'Adicionar tipo de despesa/adiantamento',
                        buttons: {"Salvar": function() {
                                var action  = $('#form-despesa').attr('action');
                                $('#form-despesa').attr('action','javascript:void(0);');
                                
                                    $.post(action ,$("#form-despesa").serialize(),  function(data){
                                        retorno = data;
                                        if(retorno==""){
                                            div.remove();
//                                            window.location = url_pai;
                                            window.location.reload();
                                        }
                                    });
                                    $('#form-despesa').attr('action',action);
                                    
                                    return null;
                                    
                            },
                                  "Fechar": function() {div.remove();}}
                    });
                }
            );
        e.preventDefault();
    });
    $('.delete_despesafinalidade').click(function(){
        var div     = $('<div style="display:none;">Deseja relamente Remover a despesa desta finalidade?</div>').appendTo('body');
        var id      = ($(this).attr('id'));
        var url     = caminho+'/admin/ci/deletedespesafinalidade';
//        var url_pai = url2.substr(0, url2.search("#"));
        
        div.dialog({
            width: 400,
            height: 200,
            modal: true,
            closeOnEscape: true,
            close: function(event, ui) {div.remove();},
            title: 'Remover despesa/adiantamento da finalidade',
            buttons: {"Sim": function() {
                        $.post(url,{
                                id: id
                            },
                            function(resposta){
                                if(resposta==""){
                                            div.remove();
                                            window.location.reload();
                                }else{
                                    if(resposta.search('integrity constraint') >= 0){
                                        alert("impossível excluir registro vinculado a outra tabela");
                                    }else{
                                        alert(resposta);
                                    }
                                }
                            }
                        );
                        return null;

                },
                      "Não": function() {div.remove();}}
        });
    });
    
    
    $('.add_visualizadorfinalidade,.add_criadorfinalidade').click(function(){
        var div         = $('<div style="display:none;"></div>').appendTo('body');
        var finalidade  = ($(this).attr('id'));
        var url_pai     = url2.substr(0, url2.search("#"));
        var url         = caminho+'/admin/ajax/listausuarios';
        var retorno     = "nada";
        var action      = $(this).hasClass("add_criadorfinalidade") ? 'admin/ci/addcriador' : 'admin/ci/addvisualizador';
        var tipo        = $(this).hasClass("add_visualizadorfinalidade") ? 1 : 2;
        div.load(
                url,
                {finalidade: finalidade, tipo: tipo},
                function (responseText, textStatus, XMLHttpRequest) {
                    div.dialog({
                        width: 600,
                        height: 200,
                        modal: true,
                        closeOnEscape: true,
                        close: function(event, ui) {div.remove();},
                        title: 'Adicionar visualizador para as CIs da finalidade',
                        buttons: {"Adicionar": function() {
//                                var action  = $('#form-visualizadores').attr('action');
                                $('#form-visualizadores').attr('action','javascript:void(0);');
                                
                                    $.post(caminho+"/"+action ,$("#form-visualizadores").serialize(),  function(data){
                                        retorno = data;
                                        if(retorno==""){
                                            div.remove();
                                            window.location = url_pai;
                                            window.location.reload();
                                        }
                                    });
                                    $('#form-visualizadores').attr('action',action);
                                    
                                    return null;
                                    
                            },
                                  "Fechar": function() {div.remove();}}
                    });
                }
            );
    });
    
    $('.delete_aprovador').click(function(){
        var div     = $('<div style="display:none;">Deseja relamente Remover o aprovador do Centro de Custo?</div>').appendTo('body');
        var id      = ($(this).attr('id'));
        var url     = caminho+'/admin/ci/deleteaprovador';
        var tr_pai  = ($(this).closest("tr").attr('id'));
        var url_pai = url2.substr(0, url2.search("#"));
        
        div.dialog({
            width: 400,
            height: 200,
            modal: true,
            closeOnEscape: true,
            close: function(event, ui) {div.remove();},
            title: 'Remover Aprovador',
            buttons: {"Sim": function() {
                        $.post(url,{
                                id: id
                            },
                            function(resposta){
                                if(resposta==""){
                                            div.remove();
                                            window.location = url_pai+"#"+tr_pai;
                                            window.location.reload();
                                }else{
                                    alert(resposta);
                                }
                            }
                        );
                        return null;

                },
                      "Não": function() {div.remove();}}
        });
    });
    
    $('.delete_aprovadorgeral').click(function(){
        var div     = $('<div style="display:none;">Deseja relamente Remover o aprovador desta faixa de valor?</div>').appendTo('body');
        var id      = ($(this).attr('id'));
        var url     = caminho+'/admin/ci/deleteaprovadorgeral';
        var url_pai = url2.substr(0, url2.search("#"));
        
        div.dialog({
            width: 400,
            height: 200,
            modal: true,
            closeOnEscape: true,
            close: function(event, ui) {div.remove();},
            title: 'Remover Aprovador',
            buttons: {"Sim": function() {
                        $.post(url,{
                                id: id
                            },
                            function(resposta){
                                if(resposta==""){
                                            div.remove();
                                            window.location = url_pai;
                                            window.location.reload();
                                }else{
                                    alert(resposta);
                                }
                            }
                        );
                        return null;

                },
                      "Não": function() {div.remove();}}
        });
    });
    
    
    
    $('.delete_aprovadorfinalidadevalor').click(function(){
        var div     = $('<div style="display:none;">Deseja relamente Remover o aprovador desta faixa de valor?</div>').appendTo('body');
        var id      = ($(this).attr('id'));
        var url     = caminho+'/admin/ci/deleteaprovadorfinalidadevalor';
        var url_pai = url2.substr(0, url2.search("#"));
        
        div.dialog({
            width: 400,
            height: 200,
            modal: true,
            closeOnEscape: true,
            close: function(event, ui) {div.remove();},
            title: 'Remover Aprovador',
            buttons: {"Sim": function() {
                        $.post(url,{
                                id: id
                            },
                            function(resposta){
                                if(resposta==""){
                                            div.remove();
                                            window.location = url_pai;
                                            window.location.reload();
                                }else{
                                    alert(resposta);
                                }
                            }
                        );
                        return null;

                },
                      "Não": function() {div.remove();}}
        });
    });
    
    
    $('.delete_visualizadorfinalidade, .delete_criadorfinalidade').click(function(){
        var div     = $('<div style="display:none;">Deseja relamente Remover o usuário desta finalidade?</div>').appendTo('body');
        var id      = ($(this).attr('id'));
        var url     = $(this).hasClass("delete_criadorfinalidade") ? caminho+'/admin/ci/deletecriadorfinalidade' : caminho+'/admin/ci/deletevisualizadorfinalidade';
        var url_pai = url2.substr(0, url2.search("#"));
        
        div.dialog({
            width: 400,
            height: 200,
            modal: true,
            closeOnEscape: true,
            close: function(event, ui) {div.remove();},
            title: 'Remover usuário da finalidade',
            buttons: {"Sim": function() {
                        $.post(url,{
                                id: id
                            },
                            function(resposta){
                                if(resposta==""){
                                            div.remove();
                                            window.location = url_pai;
                                            window.location.reload();
                                }else{
                                    alert(resposta);
                                }
                            }
                        );
                        return null;

                },
                      "Não": function() {div.remove();}}
        });
    });
    
    $('.linha_ci td').click(function(){
        var div = $('<div style="display:none;"></div>').appendTo('body');
        var id  = ($(this).parent().attr('id'));
        var url = caminho+'/admin/ci/detalhes';
        var classe = $(this).attr('class');
        var achou = classe.search("linha-menu");
        var altura = $(window).height()*0.9;
        if(achou == -1){
            div.load(
                url,
                {id: id},
                function (responseText, textStatus, XMLHttpRequest) {
                    div.dialog({
                        width: 900,
                        height: altura,
//                        position: { my: "top center", at: "center", of: window  },
                        modal: true,
                        closeOnEscape: true,
                        close: function(event, ui) {div.remove();},
                        title: 'Detalhes CI',
                        buttons: {"Fechar": function() {div.remove();}}
                    });
                }
            );
        }        
    });
    
    $('.btn-avaliar').click(function(){
        var div = $('<div style="display:none;"></div>').appendTo('body');
        var id  = ($(this).parent().parent().attr('id'));
        var url = caminho+'/admin/ci/avaliarci/id/'+id;
        div.load(
        url,
        function (responseText, textStatus, XMLHttpRequest) {
            div.dialog({
                width: '950',
                height: $(window).height()-20,
                modal: true,
                closeOnEscape: true,
                close: function(event, ui) {
                    div.remove();
                },
                title: 'Avaliar CI',
                buttons: [
                {
                    text: 'Aprovar',
                    click: function() {
                        if(parseInt($('#form-avaliar').find("#status_atual").val()) === 6){
                            $('#form-avaliar').find("#status").val('7');
                        }else{
                            $('#form-avaliar').find("#status").val('3');
                        }
                        $('<div id="fundo-carregando" class="ui-widget-overlay" style="position: fixed;"></div>').appendTo('body');
                        $('<div id="msg-carregando"></div>').appendTo('body');
                        var form = $('#form-avaliar');
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
                    text: 'Reprovar',
                    click: function() {
                        if(parseInt($('#form-avaliar').find("#status_atual").val()) === 6){
                            $('#form-avaliar').find("#status").val('8');
                        }else{
                            $('#form-avaliar').find("#status").val('4');
                        }
                        $('<div id="fundo-carregando" class="ui-widget-overlay" style="position: fixed;"></div>').appendTo('body');
                        $('<div id="msg-carregando"></div>').appendTo('body');
                        var form = $('#form-avaliar');
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
                    text: 'Fechar',
                    click: function() {
                        div.remove();
                    }
                }
                ]
                              
            });
        }
        );
    });
    
    $("#valor").blur(function(){
        aprovadores();
    });
    $('input[name^="despesa["]').live('blur', function(){
        calculaDespesas();
    });
    
    $('#ccusto_para').change(function(){
        aprovadores();
    });
    
    function aprovadores(){
        var valor       = $("#valor").val();
        var ccusto      = $('#ccusto_para').val();
        var finalidade  = $('#finalidade').val();
        $('.listagemAprovadores tbody').remove();
        $('#div-aprovadores').append('<div id="loading-image" ><img src="'+caminho+'/public/_img/icones/carregando.gif" alt="Carregando..." /></div>');
        var url = caminho+'/admin/ajax/aprovadoresci';
        $("#div-aprovadores").load(
                url,
                {ccusto: ccusto, valor: valor, finalidade: finalidade},
                function (responseText, textStatus, XMLHttpRequest) {});
    }
    $('#finalidade').change(function(){
        detalhesfinalidade();
        var ccusto      = $('#ccusto_para').val();
        if(ccusto){
            aprovadores();
        }
        despesasCI();
    });
    
    function detalhesfinalidade(){
        var finalidade  = $('#finalidade').val();
        $('#div-detalhes_finalidade').html('');
        $('#div-detalhes_finalidade').append('<div id="loading-image" ><img src="'+caminho+'/public/_img/icones/carregando.gif" alt="Carregando..." /></div>');
        var url = caminho+'/admin/ci/detalhesfinalidade';
        $("#div-detalhes_finalidade").load(
                url,
                {finalidade: finalidade},
                function (responseText, textStatus, XMLHttpRequest) {});
    }
    
    function despesasCI(){
        var finalidade  = $('#finalidade').val();
        if(finalidade > 0 ){
            var id  = $('#id').val();

            $('#td-despesasCI').html('');
            $('#td-despesasCI').append('<div id="loading-image" ><img src="'+caminho+'/public/_img/icones/carregando.gif" alt="Carregando..." /></div>');
            var url = caminho+'/admin/ci/civalordespesa';
            $("#td-despesasCI").load(
                    url,
                    {finalidade: finalidade, id: id},
                    function (responseText, textStatus, XMLHttpRequest) {
                        $('input[name^="despesa["]').priceFormat({prefix: '',centsSeparator: ',',thousandsSeparator: '.',centsLimit:2});
                    });
        }
    }
    function calculaDespesas(){
        var valor_anterior = $("#valor").val();
        var valor_total = 0;
        var valor;
        var operador;
        $('input[name^="despesa["]').each(function(  ) {
            valor = formaMoeda2($(this).val());
//            if(valor){
//                alert(valor);
//            }
            operador = $(this).attr("operador");
            if(operador === "+"){
                valor_total = valor_total + valor;
            }
            if(operador === "-"){
                valor_total = valor_total - valor;
            }
            
        });
        if(valor_anterior != formaMoeda(valor_total)){
            $("#valor").val(formaMoeda(valor_total));
            aprovadores();
        }
    }
    
    $('#lista-titulo-menu-gerar_xls').click(function(){
        $("#gerar_xls").val("true");
        $("#form-pesquisa").submit();
        $("#gerar_xls").val(null);
       
    });

});