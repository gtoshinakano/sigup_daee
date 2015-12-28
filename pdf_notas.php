<?php

require "src/scripts/conecta.php";
require "src/scripts/restrito.php";
require "src/classes/Template.class.php";
include_once "src/classes/Users.class.php";
include_once "src/classes/Notas.class.php";
include_once "src/classes/UnidadeCons.class.php";


include("src/mpdf/mpdf.php");
include("src/scripts/functions.php");

$mpdf = new mPDF();
$stylesheet = file_get_contents('cssjs_libs/pdf_style.css');

$selec = $_GET['selec'];
$autos = $_GET['autos'];
$prov = $_GET['prov'];
$info = $_GET['ninfo'];
$info_ano = $_GET['info_ano'];
$nfls = $_GET['nfls'];
$natestado = $_GET['natestado'];
$atest_ano = $_GET['atest_ano'];
$text_atest = stripslashes($_GET['texto_atest']);
$texto_info = stripslashes($_GET['texto_info']);
$ids = implode(',', $_GET['notas']); //trasforma o array dos ids selecionados numa string separada por , por exemplo 1, 5,8
$nivel = $_SESSION['nivel'];
$mes_ref = $_GET['mes'];
$ano_ref = $_GET['ano'];
$emp_id = $_GET['emp_id'];


if ($selec == 0) {
    $aux = "PROCESSO: $autos";
    $aux2 = "PROCESSO: $autos";
} else {
    $aux = "AUTOS: $autos - PROV. $prov";
    $aux2 = "AUTOS: $autos<br/>PROV. $prov";
}
date_default_timezone_set('America/Sao_Paulo');
$date = date('d') . " de " . getMesNome(date('m'), false) . ' de ' . date('Y');
$date2 = " de " . getMesNome(date('m'), false) . ' de ' . date('Y');
$date3 = date('Y');
$user = $_SESSION['usuario'];

$sql_cont = "SELECT * FROM sys_users WHERE login='$user'";
$query_cont = mysql_query($sql_cont);
$qtd = 0;
if (mysql_num_rows($query_cont) > 0) {

    while ($res = mysql_fetch_array($query_cont)) {
        $nome = $res['nome'];
        $cargo = $res['cargo'];
        $pront = $res['pront'];
    }
}

$mpdf->SetHTMLHeader('<div class="cabecalho" >
                    <div class="brasao"><img src="images/relatorio_brasao.png" alt=""  ></div>
                    <div id="texto"> 
                        <span id="titulo">SECRETARIA DE  SANEAMENTO E RECURSOS HÍDRICOS</span><br/>
                        <span id="subtitulo">DEPARTAMENTO DE ÁGUAS E ENERGIA ELÉTRICA</span><br/>
                        <span id="endereco">Rua: Boa Vista,170/175 - telefone 011-3293-8200/8201 - CEP 01014-000 – Centro – São Paulo-SP</span><br/>
                        <span id="site"> www.daee.sp.gov.br</span>
                    </div>
                </div>');
$mpdf->WriteHTML($stylesheet, 1);
$mpdf->WriteHTML(' <div class="texto ">'
        . '<p><strong>' . $aux . '</strong></p>'
        . '<p><strong>INTERESSADO: ADA</strong></p>'
        . '<p><strong><u>INFORMAÇÃO ADA/' . $info . '/' . $info_ano . '</u></strong></p>'
        . '<p class="paragrafo"><strong>Senhor Responsável pela ADA:</strong></p>'
        . '<p class="paragrafo"><strong>' . $texto_info . '</strong></p>'
        . '<div class="center">Assim sendo, proponho o envio da presente à DOF, para o devido pagamento</div>'
        . '<div class="center"><strong> ADA</strong>, ' . $date . '.</div> '
        . '</div><br/><br/>'
        . '<div class="center2"><strong>' . $nome . ' </strong></div> '
        . '<div class="center2"><strong>' . $cargo . '</strong></div>'
        . '<div class="center2"><strong>' . $pront . '</strong></div>'
        . '<br/>'
        . '<p class="paragrafo"><strong>Senhor Diretor da DSD:</strong></p>'
        . '<p class="paragrafo">1 - Ciente e de acordo</p>'
        . '<p>2 - Propondo o envio à DOF, para pagamento.</p>'
        . '<div class="center"><strong> ADA</strong>, ' . $date . '.</div>'
        . '<br/><br/>'
        . '<div class="center2"><strong>RENALDO DO AMARAL </strong></div> '
        . '<div class="center2"><strong>RESPONSÁVEL PELA ADA</strong></div>'
        . '<div class="center2"><strong>PRONT. 9396</strong></div>'
        . '<br/>'
        . '<p class="paragrafo"><strong>À DOF</strong></p>'
        // . '<p class="center"><strong> DSD</strong>,<span class="data"> ___</span> '.$date2.'.</p>'
        . '<p class="center"><strong> DSD</strong>,<span class="data"> ___</span> de <span class="data">______________</span> de ' . $date3 . '.</p>'
        . '<br/><br/>'
        . '<div class="center2"><strong>NELSON GARBELOTTO </strong></div> '
        . '<div class="center2"><strong>DIRETOR DA DSD</strong></div>'
        . '<div class="center2"><strong>PRONT. 3973</strong></div>'
        . '<br/><br/>');

$mpdf->AddPage();
if ($_GET['tem_atestado']) {
    $mpdf->WriteHTML(' <div class="texto ">'
            //  . '<p>' . $test . '</p>'
            //  . '<p><strong>AUTOS: ' . $autos . '</strong></p>'
            //  . '<p><strong>Prov: ' . $prov . '</strong></p>'
            . '<p><strong>' . $aux2 . '</strong></p>'
            . '<br/>'
            . '<div class="center"><strong><u>ATESTADO ADA – ' . $natestado . '/2015</u><strong></div><br/>'
            . ' <div id="paragrafo">' . $text_atest . '</div>'
            . '<br/><br/><br/><br/><br/>'
            . '<div class = "center"><strong> ADA</strong>, ' . $date . ' . </div> '
            . '<br/><br/><br/><br/><br/><br/>'
            . '<div class="left">'
            . '<div class=""><strong>' . $nome . '</strong></div> '
            . '<div class=""><strong>' . $cargo . '</strong></div>'
            . '<div class=""><strong>' . $pront . '</strong></div>'
            . '</div>'
            . '<div class="right">'
            . '<div class=""><strong>RENALDO DO AMARAL </strong></div> '
            . '<div class=""><strong>RESPONSÁVEL PELA ADA</strong></div>'
            . '<div class=""><strong>PRONT. 9396</strong></div>'
            . '</div>'
            . '</div>');
}
$mpdf->AddPage();

$sql = "SELECT  n.*, uo.unidade, uc.compl, emp.nome emp_nome, uc.rgi, emp.razao_social emp_rz
                FROM daee_notas n, daee_uddc uc, daee_udds uo, daee_contratos c, sys_cidade cid, sys_empresas emp 
                WHERE n.uc=uc.id AND uc.contrato=c.id AND uc.cidade=cid.id AND c.empresa=emp.id  AND uc.uo=uo.id 
                AND n.id IN(" . $ids . ")   order by  n.criado DESC";

$temConsumo = false;
$th = '';
switch ($nivel) {
    case 1:$th = "NUMERO";
        $temConsumo = true;
        break;
    case 2:$th = "RGI";
        $temConsumo = true;
        break;
    case 3:$th = "INSTALAÇÃO";
        $temConsumo = true;
        break;
    case 4:$th = "NUMERO";
        $temConsumo = false;
        break;
}

$tabela = ' <table class="borda-simples "  width="100%">
                     <thead>
                    <tr>
                          <th >' . $th . '</th>
                        <th >UNIDADE</th>
                        <th >LOCALIDADE/IDENTIFICADOR</th>
                        <th >VENCIMENTO</th> 
                        <th >LANÇAMENTO</th>
                        <th >EMPRESA</th>';

if ($temConsumo == true) {
    $tabela.='<th >CONSUMO</th>';
}

$tabela.='  <th >VALOR</th>
                    </tr>
                </thead>

                    <tbody>
               ';
$query = mysql_query($sql);
$total = 0;
while ($res = mysql_fetch_array($query)) {
    $tabela.=' <tr>
                              <td align="center">' . $res['rgi'] . ' </td>
                            <td align="center">' . $res['unidade'] . ' </td>
                            <td align="center">' . $res['compl'] . ' </td>
                            <td align="center">' . DATE("d/m/Y", strtotime($res['vencto'])) . ' </td>
                            <td align="center">' . DATE("d/m/Y", strtotime($res['criado'])) . '</td>
                            <td align="center">' . $res['emp_nome'] . '</td>';
    if ($temConsumo == true) {
        $tabela.='<td align="center">' . $res['consumo'] . '</td>';
    }

    $tabela.='<td align="center">R$ ' . tratarValor($res['valor'], true) . '</td></tr>';
    $total = $total + $res['valor'];
}
$tabela.=' </tbody>
                    <tfoot>
                        <tr>
                            <td  align="center"></td>
                            <td align="center"><!--{UCTOTAL}--></td>
                            <td ><span class="div-extenso"></span></td>
                            <td colspan="3" align="center"><h4>TOTAL GERAL SELECIONADO</h4></td>
                            <td colspan="2" align="center"><h4>R$ <span class="div-total">' . tratarValor($total, true) . '</span></h4></td>
                        </tr>
                    </tfoot>
                </table>';

$mpdf->WriteHTML(' <div class="texto2 ">'
        . ' <h3>RELAÇÃO DE CONTAS ' . $mes_ref . ' / ' . $ano_ref . ' </h3>'
        . '<p><strong>' . $tabela . '</strong></p>'
        . '</div>');


$filename = "teste.pdf";
$mpdf->Output($filename, "I");
exit;
?>