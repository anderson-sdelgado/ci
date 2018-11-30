var host = window.location.host;
var caminho = "/sistema/org";
if(host == "usina.commit.inf.br"){
    caminho = "";
}

//------------------------------------------------------------------------------
//inicia o jquery
$(document).ready(function(){
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
});