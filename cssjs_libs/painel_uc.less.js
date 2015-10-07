var url = window.location.href;
var baseName = url.split("/");
var simpleBaseName = baseName[baseName.length-1];

$(document).ready(function(){

    //alert("aioo");
    $('#conteudo').load("src/painel_uc/home.php", function(){
        drawChart();
        drawChart2();     
    });
    
    $('.menu-uc a').click(function(e){
        
        //alert("sad");
        e.preventDefault();
        $('.menu-uc a').removeClass('menu-active');
        $(this).addClass('menu-active');
        var loc = $(this).attr("href");
        $('#conteudo').load(loc, function(){
            drawChart();
            drawChart2();
        });
        
        
    });

    $(window).resize(function(){
        drawChart();
        drawChart2();
    });
    
    


});