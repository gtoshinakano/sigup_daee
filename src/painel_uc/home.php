<?php
    //include "../classes/Users.class.php";
    //include "../classes/UnidadeCons.class.php";
    //include "../classes/Notas.class.php";
    require "../scripts/conecta.php";
    include "../scripts/functions.php";
    include "../classes/Relatorio.class.php";
    require "../classes/Template.class.php";
    require "../scripts/restrito.php";
    
    
    redirectByPermission(0);
    $tpl = new Template("../../html_libs/painel_uc_home.html");    
    $relatorio = new Relatorio();
    $ucTipos = $relatorio->ucTipos;
    
    
    
    $getAno = (isset($_GET['ano']) && $_GET['ano']>=2012 && $_GET['ano']<=Date('Y')) ? $_GET['ano'] : Date('Y');
    //$_SESSION['uc'] = 619;
    
    //$tpl->UCRGI = "RGI 1203920-23 : PET - Vestiário 1 e 2";
    $tpl->ANO_ATUAL = $getAno;
    
    if(isset($_SESSION['uc'])){
                
        $ucid = (int) $_SESSION['uc'];
        $tpl->GETANO = $getAno;
        $sql = "SELECT * FROM ( SELECT uo.unidade,uo.nome AS dirnome,uc.rgi,uc.compl,uc.tipo,n.mes_ref,n.ano_ref,SUM(n.valor) AS valor,SUM(n.consumo) AS consumo, uc.rua, e.nome ";
        $sql.= "FROM daee_udds uo,daee_uddc uc,daee_notas n, sys_empresas e ";
        $sql.= "WHERE uc.uo = uo.id AND n.uc=uc.id AND uc.empresa=e.id AND uc.id=$ucid AND n.ano_ref = $getAno ";
        $sql.= "GROUP BY n.mes_ref,uc.id ORDER BY valor DESC,uo.unidade,n.mes_ref ASC ) AS res ";
        $sql.= "GROUP BY res.rgi,res.mes_ref ";
        $query = mysql_query($sql);

        if (mysql_num_rows($query) > 0) {

            $info;
            $info['chartval'] = "";
            $info['chartcon'] = "";
            while ($linha = mysql_fetch_array($query, MYSQL_ASSOC)) {

                $tpl->MESNOME = getMesNome($linha['mes_ref']);
                $tpl->block('MESLABEL');

                $tpl->VAL = tratarValor($linha['valor'], true);
                $tpl->block('VALBLOCK');

                $tpl->CONS = tratarValor($linha['consumo']);
                $tpl->block('CONSBLOCK');


                $info['nome'] = $linha['rgi'] . " - " . $linha['compl'];
                $info['tipo'] = $linha['tipo'];
                $info['unidade'] = $linha['unidade'];
                $info['valor'][] = $linha['valor'];
                $info['consumo'][] = $linha['consumo'];
                $info['chartval'].= ",['" . getMesNome($linha['mes_ref']) . "'," . $linha['valor'] . "]";
                $info['chartcon'].= ",['" . getMesNome($linha['mes_ref']) . "', 200," . $linha['consumo'] . "]";// COLOCAR META ONDE 400 
            }

            $tpl->MEDIAVAL = tratarValor($relatorio->average($info['valor']), true);
            $tpl->MEDIACONS = tratarValor($relatorio->average($info['consumo']));
            $tpl->CHARTVAL = $info['chartval'];
            $tpl->CHARTCON = ($relatorio->temConsumo($info['tipo'])) ? $info['chartcon'] : "";

            $tpl->TIPOMEDIDA = $relatorio->getTipoMedida($info['tipo']);

            $tpl->block('TABLEROWVAL');
            if ($relatorio->temConsumo($info['tipo']))
                $tpl->block('TABLEROWCONS');
            $tpl->UCNOME = $info['nome'];


            $tpl->block('RESULTS');
        }else {

            $tpl->block('NORESULTS');
        }
    }else{
        
        $tpl->block('NORESULTS');
        
    }
    
    $tpl->show();
        

    //$tpl->CONTEUDO = "<h2>RGI 1203920-23 : PET - Vestiário 1 e 2</h2>";
    /*$relatorio = new Relatorio();
    $geral = $relatorio->getGerais();

    $tpl->LANCDESDE = ExplodeDateTime2($geral['first']);
    $tpl->DADOSDESDE = getMesNome($geral['dados_desde']['mes_ref'], true) . "/" . $geral['dados_desde']['ano_ref'];
    
    $default = array();
    $default['notas'] = 0;
    foreach ($geral['total_geral'] as $ucTipo) {

        $tpl->UCNOTAQTD = tratarValor($ucTipo['cnt']);
        $tpl->VALTOTAL  = tratarValor($ucTipo['val'],true);
        $medida = $relatorio->getTipoMedida($ucTipo['tipo']);
        if($medida != "Min" && $medida != "Mb" && $medida != "NDA"){
            
            $tpl->UCMEDIDA = $medida;
            $tpl->CONSTOTAL = tratarValor($ucTipo['cons']);
            $tpl->CONSMEDIA = tratarValor($ucTipo['cons']/$ucTipo['cnt']);
            $tpl->block('SHOWCONS');
            $tpl->block('SHOWCONS2');
            
        }
        $tpl->VALMEDIA = tratarValor($ucTipo['val'] / $ucTipo['cnt'],true);
        $tpl->UCTIPONOME= $relatorio->getTipoNome($ucTipo['tipo']);
        $tpl->block('EACHUCTIPO');
        $default['notas'] += $ucTipo['cnt'];
        
    }
    
    $tpl->NUMNOTAS = tratarValor($default['notas']);
    $tpl->UOQTD = $geral['unidades'][0];
    $tpl->UCQTD = $geral['unidades'][1];*/
    
?>