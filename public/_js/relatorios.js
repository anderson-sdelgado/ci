//var host = window.location.host;
//var caminho = "/sistema/org";
//if(host == "usina.commit.inf.br"){
//    caminho = "";
//}
var url = window.location.pathname;
var caminho = url.substring(0, url.indexOf("/admin/", 0));
var DELAY = 500;
var clicks = 0;
var timer = null;

//------------------------------------------------------------------------------
//inicia o jquery
$(document).ready(function(){
    //--------------------------------------------------------------------------
    //Visualizar log de erro
        $(".modal")
    .live("click", function(e){
        var modal = $(this);
        clicks++;  //count clicks

        if(clicks === 1) {

            timer = setTimeout(function() {
            
            var id = modal.attr('itemid');
            var title = modal.attr('title');
            var data_organograma = '';
            data_organograma = $("#data_organograma").val();
            data_organograma = data_organograma.replace(/\//gi,"-");
            $('#dialog').dialog({
                title: title,
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
                buttons: {"Sair": function() {$(this).dialog("destroy");}}
            }
            )
            .load(
                    caminho+'/admin/relatorios/info/id_cargo/'+id+'/data/'+data_organograma, 
                    {}, // omit this param object to issue a GET request instead a POST request, otherwise you may provide post parameters within the object
                    function (responseText, textStatus, XMLHttpRequest) {
                        // remove the loading class
                        //dialog.removeClass('loading');

                    }
            );
            
            
//                alert("Single Click");  //perform single-click action    
                clicks = 0;             //after action performed, reset counter

            }, DELAY);

        } else {

            clearTimeout(timer);    //prevent single-click action
//            alert("Double Click");  //perform double-click action
            clicks = 0;             //after action performed, reset counter
        }

    })
    .live("dblclick", function(e){
        e.preventDefault();  //cancel system double-click event
    });
//    $('.modal').live('dblclick',(function(){
//        var id = $(this).attr('itemid');
//        var title = $(this).attr('title');
//        var data_organograma = '';
//        data_organograma = $("#data_organograma").val();
//        data_organograma = data_organograma.replace(/\//gi,"-");
//        $('#dialog').dialog({
//            title: title,
//            width: 800,
//            height: 650,
//            resizable: false,
//            position: 'top',
//            open: function ()
//            {
//                $('#dialog').html('');
//                $(this).append('<div class="loading" style="text-align:center;color:#FF0000;font-weight:bold">Carregando...</div>');
//            },         
//            modal:true,
//            buttons: {"Sair": function() {$(this).dialog("destroy");}}
//        }
//        )
//        .load(
//                caminho+'/admin/relatorios/info/id_cargo/'+id+'/data/'+data_organograma, 
//                {}, // omit this param object to issue a GET request instead a POST request, otherwise you may provide post parameters within the object
//                function (responseText, textStatus, XMLHttpRequest) {
//                    // remove the loading class
//                    //dialog.removeClass('loading');
//    
//                }
//        );
//    }));


    $('.ajaxProc').live('click',function(){
        var id_processo = $(this).attr('id');
        var title = $(this).attr('title');
        $('#detProcesso').dialog({
            title: title,
            width: 400,
            height: 400,
            resizable: false,
            open: function ()
            {
                $('#detProcesso').html('');
                $(this).append('<div class="loading" style="text-align:center;color:#FF0000;font-weight:bold">Carregando...</div>');
            },         
            modal:true,
            buttons: {"Sair": function() {$(this).dialog("destroy");}}
        }
        )
        .load(
                caminho+'/admin/ajax/detprocessos/id_processo/'+id_processo, 
                {}, // omit this param object to issue a GET request instead a POST request, otherwise you may provide post parameters within the object
                function (responseText, textStatus, XMLHttpRequest) {
                    // remove the loading class
                    //dialog.removeClass('loading');
                    
                }
        );
        return false;
    });
    
    $('.links-processo').live('click',function(){
        var div     = $('<div style="display:none;"></div>').appendTo('body');
        var id_processo = ($(this).attr('id'));
        var url     = caminho+'/admin/ajax/processoslinks';
        div.load(
                url, 
                {id: id_processo,visualizar: true},
                function (responseText, textStatus, XMLHttpRequest) {
                    div.dialog({
                        width: 640,
                        height: 400,
                        modal: true,
                        closeOnEscape: true,
                        close: function(event, ui) {div.remove();},
                        title: 'Links para o processo',
                        buttons: {"Sair": function() {div.remove();}}
                    });
                }
            );
    });
    
    $('.links-informativos').live('click',function(){
        var div     = $('<div style="display:none;"></div>').appendTo('body');
        var id_processo = ($(this).attr('id'));
        var url     = caminho+'/admin/ajax/informativoslinks';
        div.load(
                url, 
                {id: id_processo,visualizar: true},
                function (responseText, textStatus, XMLHttpRequest) {
                    div.dialog({
                        width: 640,
                        height: 400,
                        modal: true,
                        closeOnEscape: true,
                        close: function(event, ui) {div.remove();},
                        title: 'Links',
                        buttons: {"Sair": function() {div.remove();}}
                    });
                }
            );
    });
    
    /*
     * pop up com descricao dos cargos nas informacoes adicionais do relatorio organograma
     */
    $('.linkCargo').live('click',function(){
        var id_cargo = $(this).attr('id');
        var title = $(this).attr('title');
        
        $('#detCargo').dialog({
            title: title,
            width: 700,
            height: 500,
            position: 'middle',
            resizable: false,
            open: function ()
            {
                $('#detCargo').html('');
                $(this).append('<div class="loading" style="text-align:center;color:#FF0000;font-weight:bold">Carregando...</div>');
            },         
            modal:true,
            buttons: {"Sair": function() {$(this).dialog("destroy");}}
        }
        )
        .load(
                caminho+'/admin/ajax/detcargo/id_cargo/'+id_cargo, 
                {}, // omit this param object to issue a GET request instead a POST request, otherwise you may provide post parameters within the object
                function (responseText, textStatus, XMLHttpRequest) {
                    // remove the loading class
                    //dialog.removeClass('loading');
                }
        );
        return false;
    });
    
    /*
     * botao gerar pdf
     */
    $('#pdf').live('click',function(){
        $('#printCargo').submit();
        return false;
    });
    
    $('#frmRel').submit(function(){
        
        var rel = $('#selectRel').val();
        //valida se o usuario colocou o tipo do relatorio
        if(rel != ""){
            var data = $('#mes_ref').val();
            if(data != ""){
                if(rel == 1)
                        rel = 'horafunc';
                    else
                        rel = 'horacargo';

                $.post(caminho+'/admin/ajax/'+rel,{
                        data: data
                    },
                    function(resposta){
                        $('#targetAjax').html(resposta);
                        $('#targetAjax').show();
                    }
                );
            }else{
                alert('Informe a data');
            }
        }else{
            alert('Selecione o tipo de relatório');
        }
        return false;
    })
});
