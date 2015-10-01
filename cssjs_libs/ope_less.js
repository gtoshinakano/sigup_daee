var url = window.location.href;
var baseName = url.split("/");
var simpleBaseName = baseName[baseName.length - 1];

$(document).ready(function () {

    $(".mantem-text").change(function () {
        //ope_assistente lancamento
        var inputClass = $(this).attr('name').replace('mantem-', '');
        if ($(this).is(':checked')) {

            $('.' + inputClass).attr('readonly', true);

        } else {

            $('.' + inputClass).attr('readonly', false);

        }

    });

    $(".mantem-select").change(function () {

        var inputClass = $(this).attr('name').replace('mantem-', '');
        if ($(this).is(':checked')) {

            $('.' + inputClass).attr('disabled', true);

        } else {

            $('.' + inputClass).attr('disabled', false);

        }

    });
 

    $(".simple-table tbody tr:odd").css("background-color", "#C6D3EE");

    //adicionar classe que muda a cor da linha ao selecionar o checkbox
    $('table > tbody > tr > td > :checkbox').bind('click change', function () {
        var tr = $(this).parent().parent();
        if ($(this).is(':checked'))
            $(tr).addClass('selected');
        else
            $(tr).removeClass('selected');
    });

    //somar valores
    $("input[name='notas[]']").change(function () {
        var soma = 0;
        $("input[name='notas[]']:checked").each(function () {
            soma += parseFloat($(this).attr("title"));
        });
        $(".div-total").html(tratarValor(soma.toFixed(2), true));
    });

    $("input[name=marcar-todos]").change(function () {
        if (this.checked) {
            $("input[name='notas[]']").each(function () {
                this.checked = true;
               // alert(this.id);
            });
            $("input[name='notas[]']").change();
        }
        else {
            $("input[name='notas[]']").each(function () {
                this.checked = false;
                $("input[name='notas[]']").change();
            });
        }
    });
    
     $('.info').hide();
     $("input[name=nextButton]").click(function(){
        $('.simple-table').hide();
        $('.info').show();
    });
    $("input[name=prevButton]").click(function(){
        $('.simple-table').show();
        $('.info').hide();
    });
    
    
     tinymce.init({
            selector: "#mytextarea"
        });
    
    
    
    
    
    
    
    
    
    
    
    
});


function tratarValor(val, dinheiro) {
    if (!dinheiro) {
        return number_format(val, 0, ",", ".");
    } else {
        return number_format(val, 2, ",", ".");
    }
}

function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '')
            .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                        .toFixed(prec);
            };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
            .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
                .join('0');
    }
    return s.join(dec);
}