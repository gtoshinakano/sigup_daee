<?php
    $tpl->addFile('CONTEUDO', 'html_libs/painel_ucRanking.html');

    
    if(isset($_GET['ano'],$_GET['tipo'])){
        
        if(array_key_exists($_GET['tipo'], $ucTipos) && $_GET['ano']>=2012 && $_GET['ano']<=Date('Y')){
            
            $getTipo = $_GET['tipo'];
            $ano = $_GET['ano'];
            $sql = "SELECT uo.unidade, uc.id, uc.rgi, uc.compl, uc.rua, n.tipo, SUM(n.valor) AS soma, e.nome,SUM(n.consumo) consumo ";
            $sql.= "FROM daee_udds uo, daee_uddc uc, daee_notas n,sys_empresas e ";
            $sql.= "WHERE uc.uo=uo.id AND n.uc=uc.id AND uc.empresa=e.id AND n.ano_ref=".$ano." AND uc.tipo=".$getTipo." ";
            $sql.= "GROUP BY uc.id ORDER BY n.ano_ref DESC, soma DESC LIMIT 50";
            $query = mysql_query($sql);
            if(mysql_num_rows($query) > 0){
                
                $tpl->TIPONOME  = $ucTipos[$getTipo];
                $tpl->GETANO    = $ano;
                
                $medida     = $relatorio->getTipoMedida($getTipo);
                $x = 1;
                while($linha = mysql_fetch_array($query)){
                    
                    $tpl->NO = $x;
                    $tpl->UCNOME    = $linha['rgi'] . " - " . $linha['compl'];
                    $tpl->TOOLTIP   = $linha['rua'] . " - " . $linha['nome'];
                    //$tpl->EMPRESA   = $linha['nome'];
                    $tpl->UC        = $linha['unidade'];
                    $tpl->UCID      = $linha['id'];
                    $tpl->TOTAL     = tratarValor($linha['soma'],true);
                    $tpl->TOTAL    .= ($relatorio->temConsumo($getTipo)) ? "<br /><span class='blue'>$medida " . tratarValor($linha['consumo']) . "</span>" : "";
                    
                    
                    $tpl->block('EACHUC');
                    $x++;
                    
                }
                
                
                $tpl->block('RESULTS');
                
            }else{
                
                $tpl->block('NORESULTS');
                
            }
            
        }
        
    }
 
    /*
     * MONTAR FORM
     * 
     */
    for($i = 0; $i < count($ucTipos); $i++ ){
            
        $tpl->TPINDEX = $i;
        $tpl->TPNOME = $ucTipos[$i];
        $tpl->block('EACHTIPO');
            
    }
    
    for($a=Date('Y'); $a>=2012; $a--){
        
        $tpl->ANOFORM = $a;
        $tpl->block('ANOS');
        
    }
    
?>