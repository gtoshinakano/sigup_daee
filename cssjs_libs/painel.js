
$(document).ready(function(){

    $( "#menu-container" ).menu();
    $('#conteudo .inner-tabs').tabs();
    $('#conteudo .inner-accordion').accordion({
        heightStyle: "content"
    });
    
    $('tbody tr:odd').css("background-color", "#f1f1f1");
    
    $( ".show-option" ).tooltip({
        show: {
            effect: "slideDown",
            delay: 1
        }
    });
   

    
});