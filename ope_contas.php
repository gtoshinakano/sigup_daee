<?php

require "src/scripts/conecta.php";
require "src/scripts/restrito.php";
include "src/scripts/functions.php";
require "src/classes/Template.class.php";
include_once "src/classes/Users.class.php";
include_once "src/classes/Notas.class.php";
include_once "src/classes/UnidadeCons.class.php";


redirectByPermission(2); // SETAR PERMISSÃO DA PÁGINA

$tpl = new Template('html_libs/template_ope.html');
$tpl->addFile("MENU", 'html_libs/ope_secondMenu.html');
// $tpl->addFile("JSCRIPT", 'cssjs_libs/js_parts/js_ope_assistente.html');//colocar um script 
$tpl->addFile("CONTEUDO", 'html_libs/ope_relacao_contas.html');
//$tpl->block('LIMPAR_BLOCK');

$nivel = $_SESSION['nivel'];
//$nivel = 2;
$user = new User($_SESSION['usuario']);


if (!isset($_GET['ano_ref'], $_GET['mes_ref']) || $_GET['ano_ref'] < 2012 || $_GET['mes_ref'] < 1 || $_GET['mes_ref'] > 12 || $_GET['ano_ref'] > Date('Y')) { //se não tem get
    for ($i = Date('Y'); $i >= 2010; $i--) {
        $tpl->ANO = $i;
        $tpl->block('EACH_ANOREF');
    }

    for ($i = 1; $i <= 12; $i++) {
        $tpl->MES = $i;
        $tpl->MESNOME = getMesNome($i, false);
        $tpl->block('EACH_MESREF');
    }
    $tpl->block('PRIMEIRO_FORM');
} else {
    $ano_ref = (int) $_GET['ano_ref'];
    $mes_ref = (int) $_GET['mes_ref'];

    $tpl->NMES_REF = getMesNome($mes_ref, false);
    $tpl->NANO_REF = $ano_ref;

    if($nivel!=1){
        $sql = "SELECT  n.*, uo.unidade, uc.compl, emp.nome emp_nome, uc.rgi
        FROM daee_notas n, daee_uddc uc, daee_udds uo, daee_contratos c, sys_cidade cid, sys_empresas emp 
        WHERE n.uc=uc.id AND uc.contrato=c.id AND uc.cidade=cid.id AND uc.empresa=emp.id AND uc.uo=uo.id AND c.permissao=$nivel
        AND n.mes_ref=$mes_ref AND n.ano_ref=$ano_ref AND uc.ativo=1 AND n.pagto='0000-00-00' order by  n.criado DESC ";
        
    }else{
    $sql = "SELECT  n.*, uo.unidade, uc.compl, emp.nome emp_nome, uc.rgi
        FROM daee_notas n, daee_uddc uc, daee_udds uo, daee_contratos c, sys_cidade cid, sys_empresas emp 
        WHERE n.uc=uc.id AND uc.contrato=c.id AND uc.cidade=cid.id AND uc.empresa=emp.id AND uc.uo=uo.id 
        AND n.mes_ref=$mes_ref AND n.ano_ref=$ano_ref AND uc.ativo=1 AND n.pagto='0000-00-00' order by  n.criado DESC ";
    }

    $query = mysql_query($sql);
    $count = 0;
    $temConsumo = false;
    switch ($nivel) {
        case 2:$tpl->NTH_RGI = "RGI";
            $temConsumo = true;
            break;
        case 3:$tpl->NTH_RGI = "INSTALAÇÃO";
            $temConsumo = true;
            break;
        case 4:$tpl->NTH_RGI = "NUMERO";
            $temConsumo = false;
            break;
    }

    if ($temConsumo == true) {
        $tpl->block('CONSUMO');
    }

    while ($res = mysql_fetch_array($query)) {
        $count ++;
        $tpl->NID = $res['id'];
        $tpl->NINDICE = $count;
        $tpl->NNUMERO = $res['rgi'];
        $tpl->NUNIDADE = $res['unidade'];
        $tpl->NMUNICIPIO = $res['compl'];
        $tpl->NVENCIMENTO = date("d/m/Y", strtotime($res['vencto']));
        $tpl->NEMPRESA = $res['emp_nome'];
        $tpl->LANCTO = $res['criado'];
         $tpl->LANCTO2 = date("d/m/Y", strtotime($res['criado']));;

        if ($temConsumo == true) {
            $tpl->block('EACH_CONSUMO');
            $tpl->NCONSUMO = $res['consumo'];
        }
        $tpl->NVALOR = $res['valor'];
        $tpl->NVALOR_C = tratarValor($res['valor'],true) ;
        //$tpl->ANO_REF = $res['ano_ref'];
        //$tpl->MES_REF = getMesNome($res['mes_ref'], true);
        $tpl->block('EACH_NO');
    }
    $tpl->UCTOTAL = $count;
    $tpl->block('TABELA');
}

$tpl->show();
?>