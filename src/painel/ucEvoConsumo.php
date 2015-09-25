<?php
    $tpl->addFile('CONTEUDO', 'html_libs/painel_ucEvoConsumo.html');
    
    if(isset($_GET['tipo']))$getTipo = $_GET['tipo'];
    else $getTipo = 0;
    
    $tpl->TIPONOME = $ucTipos[$getTipo];
    
    /*
     * DADOS DE FORMULÁRIO
     */
    for($m = 2;$m<=12;$m++){
        
        $tpl->MESNUM = $m;
        $tpl->MESNOME= getMesNome($m,false);
        $tpl->block('MESES');
        $tpl->block('MESES1');
        
    }
    for($a=Date('Y'); $a >= 2012; $a--){
        
        $tpl->ANOFORM = $a;
        $tpl->block('ANOS');
        
    }

    /*****************************************************
    RECEBER DADOS DO FORMULÁRIO E CADASTRAR
    *****************************************************/	
    if (getenv("REQUEST_METHOD") == "POST") {
        
        if(isset($_POST['mes_post'],$_POST['ano_form'])){
            
            $mes = $_POST['mes_post'];
            $mes_ant = $mes-1;
            $tpl->MESANT = getMesNome($mes_ant). "/" . $_POST['ano_form'];
            $tpl->MESPOS = getMesNome($mes). "/" . $_POST['ano_form'];
            $sql = "SELECT * FROM ( SELECT uo.unidade,uc.id,uc.rgi,uc.compl,uc.tipo,n.mes_ref,n.ano_ref,";
            $sql.= "SUM(n.valor) AS valor,SUM(n.consumo) AS consumo, uc.rua, e.nome ";
            $sql.= "FROM daee_udds uo,daee_uddc uc,daee_notas n, sys_empresas e ";
            $sql.= "WHERE uc.uo = uo.id AND n.uc=uc.id AND uc.empresa=e.id AND uc.tipo=" . $getTipo . " ";
            $sql.= "AND n.mes_ref IN (" . $mes . "," . $mes_ant . ") AND n.ano_ref = " . $_POST['ano_form'] . " ";
            $sql.= "GROUP BY n.mes_ref,uc.id ORDER BY valor DESC,uo.unidade,n.mes_ref ASC ) AS res ";
            $sql.= "GROUP BY res.rgi,res.mes_ref ";
            $query = mysql_query($sql);
            
            $undAtual = "";
            $index = 0;
            $result= array();
            //echo $sql;
            
            if(mysql_num_rows($query) > 0){
            
                while($linha = mysql_fetch_array($query, MYSQLI_ASSOC)){

                    if($undAtual == $linha['rgi']){


                        $result[$index][$linha['mes_ref']]['valor']     = $linha['valor'];
                        $result[$index][$linha['mes_ref']]['consumo']   = $linha['consumo'];
                        $result[$index]['varValor']                     = ($result[$index][$mes_ant]['valor'] > 0) ? ($linha['valor'] / $result[$index][$mes_ant]['valor'] - 1) * 100 : 0;
                        $result[$index]['varConsumo']                   = ($result[$index][$mes_ant]['consumo']>0) ? ($linha['consumo'] / $result[$index][$mes_ant]['consumo'] - 1) * 100 : 0;

                        $index++;
                        //echo $linha['mes_ref'];//segundo dado

                    }else{

                        $result[$index]['unidade']  = $linha['rgi'] . " - " . $linha['compl'];
                        $result[$index]['diretoria']= $linha['unidade'];
                        $result[$index]['endereco'] = $linha['rua'];
                        $result[$index]['empresa']  = $linha['nome'];
                        $result[$index]['ucid']  = $linha['id'];
                        $undAtual = $linha['rgi'];
                        $result[$index][$linha['mes_ref']]['valor']     = $linha['valor'];
                        $result[$index][$linha['mes_ref']]['consumo']   = $linha['consumo'];
                        $result[$index]['varValor']                     = 0;
                        $result[$index]['varConsumo']                   = 0;

                    }

                }


                /*
                 * MOSTRAR DADOS NA TABELA
                 * 1° Ordenar
                 * 2° Mostrar
                 */
                foreach($result as $key=>$row){

                    $filtro[$key] = $row['varValor'];

                }
                array_multisort($filtro, SORT_DESC, $result);

                //MOSTRAR
                $no = 1;
                $temConsumo = $relatorio->temConsumo($getTipo);
                $medida     = $relatorio->getTipoMedida($getTipo);
                foreach($result as $res){

                    $tpl->UONOME= $res['diretoria'];
                    $tpl->UCNOME= $res['unidade'];
                    $tpl->UCID= $res['ucid'];
                    $tpl->ANT   = (isset($res[$mes_ant])) ? tratarValor($res[$mes_ant]['valor'],true) : "0";
                    $tpl->ANT  .= (isset($res[$mes_ant]) && $temConsumo) ? "<br /><span class='blue'>$medida " . tratarValor($res[$mes_ant]['consumo']) . "</span> ": "";
                    $tpl->POST  = (isset($res[$mes])) ? tratarValor($res[$mes]['valor'],true) : "0";
                    $tpl->POST .= (isset($res[$mes]) && $temConsumo) ? "<br /><span class='blue'>$medida " . tratarValor($res[$mes]['consumo']) . "</span> " : "";
                    $tpl->VAR   = ($res['varValor'] > 0) ? "↑ " . getPorcentagem($res['varValor'], true) : "↓ " . getPorcentagem($res['varValor'], true);
                    $tpl->VAR  .= ($temConsumo) ? ($res['varConsumo'] > 0) ? "<br />↑ " . getPorcentagem($res['varConsumo'], true) : "<br />↓ " . getPorcentagem($res['varConsumo'], true) : "";
                    $tpl->UCTOOLTIP= $res['endereco'] . " - " . $res['empresa'];
                    $tpl->ANOCOR= $_POST['ano_form'];
                    $tpl->NO    = $no;
                    $no++;
                    $tpl->block('EACHUO');

                }
            
                $tpl->block('RESULTS');
            
            }else{
                
                $tpl->block('NORESULTS');
                
            }

        }

    }

    function ordenaVariacao($a, $b){
        
        
        
    }
?>