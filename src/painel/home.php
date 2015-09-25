<?php
    $tpl->addFile('CONTEUDO', 'html_libs/painel_home.html');
    $relatorio = new Relatorio();
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
    $tpl->UCQTD = $geral['unidades'][1];
    
?>