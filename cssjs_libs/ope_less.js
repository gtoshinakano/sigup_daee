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
      //  $('.info').show();
        var soma = 0;
        $("input[name='notas[]']:checked").each(function () {
            soma += parseFloat($(this).attr("title"));
        });
        $(".div-total").html(tratarValor(soma.toFixed(2), true));
    //   $(".div-extenso").html(ValorPorExtenso(soma));
    });
    
    $("input[name=marcar-todos]").change(function () {
        if (this.checked) {
            $("input[name='notas[]']").each(function () {
                this.checked = true;
              
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



function ValorPorExtenso(valor) {

if (!valor) return 'Zero';

var singular = ["centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão"];
var plural = ["centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões"];

var c = ["", "cento", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"];
var d = ["", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"];
var d10 = ["dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove"];
var u = ["", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];

var z = 0;

valor = valor.toString();
valor = number_format(valor, 2, '.', '.');

var inteiro = valor.split(/\./);

for (var i = 0; i < inteiro.length; i++) {
inteiro[i] = inteiro[i].toString();
for (var ii = inteiro[i].length; ii < 3; ii++) {
inteiro[i] = '0' + inteiro[i];
}
}

var fim = inteiro.length -( inteiro[inteiro.length-1] > 0 ? 1 : 2 );

var rc, rd, ru;
var r, t;
var rt = "";
var valor_split;
for (var i = 0; i < inteiro.length; i++) {

valor = inteiro[i];
valor_split = valor.match(/./g);

rc = ((valor > 100) && (valor < 200)) ? "cento" : c[valor_split[0]];
rd = (valor_split[1] < 2) ? "" : d[valor_split[1]];
ru = (valor > 0) ? ((valor_split[1] == 1) ? d10[valor_split[2]] : u[valor_split[2]]) : "";

r = rc + ((rc && (rd || ru)) ? " e " : "") + rd + ((rd && ru) ? " e " : "") + ru;
t = inteiro.length - 1 - i;

r = r + (r ? " " + (valor > 1 ? plural[t] : singular[t]) : "");
if (valor == "000") z++;
else if (z > 0) z--;

if ((t==1) && (z>0) && (inteiro[0] > 0)) {
r = r + ((z>1) ? " de " : "") + plural[t];
}
if (r) {
rt = rt + (((i > 0) && (i <= fim) && (inteiro[0] > 0) && (z < 1)) ? ( (i < fim) ? ", " : " e ") : " ") + r;
}

}

return (rt ? rt : "zero");

}


