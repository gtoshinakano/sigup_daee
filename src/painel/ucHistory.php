<?php
    $tpl->addFile('CONTEUDO', 'html_libs/painel_ucHistory.html');
    
    /*
     * MONTAR FORM
     */
    
    for($a=Date('Y'); $a>=2012; $a--){
        
        $tpl->ANOFORM = $a;
        $tpl->block('ANOS');
        
    }    
    
    if(isset($_GET['uc'],$_GET['ano'])){
        
        if($_GET['ano']>=2012 && $_GET['ano']<=Date('Y')){
            
            $ucid = (int) $_GET['uc'];
            $getAno = $_GET['ano'];
            //$tpl->UCID  = $ucid;
            $tpl->GETANO= $getAno;
            $sql = "SELECT * FROM ( SELECT uo.unidade,uo.nome AS dirnome,uc.rgi,uc.compl,uc.tipo,n.mes_ref,n.ano_ref,SUM(n.valor) AS valor,SUM(n.consumo) AS consumo, uc.rua, e.nome ";
            $sql.= "FROM daee_udds uo,daee_uddc uc,daee_notas n, sys_empresas e ";
            $sql.= "WHERE uc.uo = uo.id AND n.uc=uc.id AND uc.empresa=e.id AND uc.id=$ucid AND n.ano_ref = $getAno ";
            $sql.= "GROUP BY n.mes_ref,uc.id ORDER BY valor DESC,uo.unidade,n.mes_ref ASC ) AS res ";
            $sql.= "GROUP BY res.rgi,res.mes_ref ";
            $query = mysql_query($sql);            
            
            if(mysql_num_rows($query) > 0){
                
                $info;
                $info['chartval'] = "";
                $info['chartcon'] = "";
                while($linha = mysql_fetch_array($query, MYSQL_ASSOC)){
                    
                    $tpl->MESNOME = getMesNome($linha['mes_ref']);
                    $tpl->block('MESLABEL');
                    
                    $tpl->VAL = tratarValor($linha['valor'],true);
                    $tpl->block('VALBLOCK');
                    
                    $tpl->CONS = tratarValor($linha['consumo']);
                    $tpl->block('CONSBLOCK');
                    
                    
                    $info['nome'] = $linha['rgi'] . " - " . $linha['compl'];
                    $info['tipo'] = $linha['tipo'];
                    $info['unidade'] = $linha['unidade'];
                    $info['valor'][] = $linha['valor'];
                    $info['consumo'][] = $linha['consumo'];
                    $info['chartval'].= ",['" . getMesNome($linha['mes_ref']) . "'," . $linha['valor'] .  "]"; 
                    $info['chartcon'].= ",['" . getMesNome($linha['mes_ref']) . "'," . $linha['consumo'] .  "]"; 
                    
                }
                
                $tpl->MEDIAVAL = tratarValor($relatorio->average($info['valor']),true);
                $tpl->MEDIACONS= tratarValor($relatorio->average($info['consumo']));
                $tpl->CHARTVAL = $info['chartval'];
                $tpl->CHARTCON = ($relatorio->temConsumo($info['tipo'])) ? $info['chartcon'] : "";
                
                $tpl->TIPOMEDIDA = $relatorio->getTipoMedida($info['tipo']);
                
                $tpl->block('TABLEROWVAL');
                if($relatorio->temConsumo($info['tipo'])) $tpl->block('TABLEROWCONS');
                $tpl->UCNOME = $info['nome'];
                
                /*
                 * Colocar default em primeiro lugar em SELECT
                 * 
                 */
                $tpl->UCFORMNOME= $relatorio->getTipoNome($info['tipo']) . " | " . $info['nome'] . " | " . $info['unidade'];
                $tpl->UCFORMID  = $ucid;
                $tpl->block('EACHUCFORM');
                
                
                $tpl->block('RESULTS');
                
            }else{
                
                $tpl->block('NORESULTS');
                
            }
            
        }
        
    }
        
    $sql = "SELECT uo.unidade,uc.id,uc.tipo,uc.rgi,uc.compl FROM daee_udds uo, daee_uddc uc ";
    $sql.= "WHERE uc.uo=uo.id ORDER BY uc.tipo";
    $query = mysql_query($sql);
        
    while($uc = mysql_fetch_array($query)){
          
        $ucNome = $relatorio->getTipoNome($uc['tipo']) . " | " . $uc['rgi'] . " - " . $uc['compl'] . " | " . $uc['unidade'];
        $tpl->UCFORMNOME= $ucNome;
        $tpl->UCFORMID  = $uc['id'];
        $tpl->block('EACHUCFORM');
            
    }
        
        
    
 

    
?>