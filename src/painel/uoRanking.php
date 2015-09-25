<?php
    $tpl->addFile('CONTEUDO', 'html_libs/painel_uoRanking.html');
    $sql = "SELECT uo.id,uo.nome,uo.unidade,SUM(n.valor) AS total, n.ano_ref FROM daee_udds uo, daee_uddc uc, daee_notas n ";
    $sql.= "WHERE uc.uo = uo.id AND n.uc = uc.id GROUP BY uo.id,n.ano_ref ORDER BY n.ano_ref DESC, total DESC";
    $query = mysql_query($sql);
    
    $uos = array();
    $anos = array();
    while($linha = mysql_fetch_array($query, MYSQL_ASSOC)){
        
        $uos[] = $linha;
        $anos[]= $linha['ano_ref'];
        
    }
    $anos = array_unique($anos);
    $anoAtual = $anos[0];
    $chartVals = "['Unidades', 'Valor']";
    $chartIndex = 0;
    $ordem = 1;
    
    foreach($uos as $uo){
        
        if($uo['ano_ref'] != $anoAtual){
            
            $tpl->ANO = $anoAtual;
            $tpl->CHART_VALS = $chartVals;
            $tpl->CHART_INDEX = $chartIndex;
            $tpl->block('EACH_ANO');
            $tpl->block('CHART_JS');
            $tpl->block('CHART_DIVS');
            
            $ordem = 1;
            $chartIndex++;
            $chartVals = "['Unidades', 'Valor']";
        }
        
        $tpl->UOORDEM = $ordem++;
        $tpl->UONOME= $uo['unidade'] . " - " . $uo['nome'];
        $tpl->UOID= $uo['id'];
        $tpl->TOTAL = tratarValor($uo['total'], true);
        $tpl->block('EACHUO');
        
        $chartVals .= ",['" . $uo['unidade'] . "'," . $uo['total'] . "]";


        
        $anoAtual = $uo['ano_ref'];
        
    }

    if($anoAtual == end($anos)){

        $tpl->ANO = $anoAtual;
        $tpl->CHART_VALS = $chartVals;
        $tpl->CHART_INDEX = $chartIndex;
        $tpl->block('EACH_ANO');
        $tpl->block('CHART_JS');
        $tpl->block('CHART_DIVS');

    }
    $tpl->ANOATUAL = Date('Y');
        
?>