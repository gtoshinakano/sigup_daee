<?php
    $tpl->addFile('CONTEUDO', 'html_libs/painel_uoHistory.html');
    include "src/classes/Unidade.class.php";
    include "src/classes/UnidadeCons.class.php";
    /*
     * MONTAR FORM
     */
    
    for($a=Date('Y'); $a>=2012; $a--){
        
        $tpl->ANOFORM = $a;
        $tpl->block('ANOS');
        
    }    
    
    if(isset($_GET['uo'],$_GET['ano'])){
        
        if($_GET['ano']>=2012 && $_GET['ano']<=Date('Y')){
            
            $uoid = (int) $_GET['uo'];
            $getAno = $_GET['ano'];
            //$tpl->UCID  = $ucid;
            $tpl->GETANO= $getAno;
            $uo = new Unidade($uoid);
            $consChart = Array();
                        
            if($uo->getSigla() != ""){
                
                $ucs = $uo->getAllUcs();
                $tpl->UONOME = $uo->getSigla() . " - " . $uo->getNome();
                $tpl->UOFORMNOME= $uo->getSigla() . " | " . $uo->getNome();
                $tpl->UOFORMID  = $uoid;
                $tpl->block('EACHUOFORM');                
                
                $arrayUcs;
                foreach($ucs as $ucid){
                    
                    $uc = new UnidadeConsumidora($ucid);
                    $arrayUcs[$uc->get('tipo')][$uc->get('id')]['rgi'] = $uc->get('rgi');
                    $arrayUcs[$uc->get('tipo')][$uc->get('id')]['nome'] = $uc->getNome();
                    $arrayUcs[$uc->get('tipo')][$uc->get('id')]['empresa'] = $uc->get('empresa')->getNome();
                    $arrayUcs[$uc->get('tipo')][$uc->get('id')]['mensal'] = $uc->getExercicioMensal($getAno);
                    
                }

                $totalGeral[1] = 0;$totalGeral[2] = 0;$totalGeral[3] = 0;$totalGeral[4] = 0;$totalGeral[5] = 0;$totalGeral[6] = 0;
                $totalGeral[7] = 0;$totalGeral[8] = 0;$totalGeral[9] = 0;$totalGeral[10] = 0;$totalGeral[11] = 0;$totalGeral[12] = 0;
                
                
                foreach($arrayUcs as $tipos=>$ucs){
                    
                    $temConsumo = $relatorio->temConsumo($tipos);
                    $tipoMedida = $relatorio->getTipoMedida($tipos);
                    
                    $totalMensal[1]['pago'] = 0;$totalMensal[2]['pago'] = 0;$totalMensal[3]['pago'] = 0;$totalMensal[4]['pago']  = 0;$totalMensal[5]['pago']  = 0;$totalMensal[6]['pago']  = 0;
                    $totalMensal[7]['pago'] = 0;$totalMensal[8]['pago'] = 0;$totalMensal[9]['pago'] = 0;$totalMensal[10]['pago'] = 0;$totalMensal[11]['pago'] = 0;$totalMensal[12]['pago'] = 0;
                    
                    $totalMensal[1]['cons'] = 0;$totalMensal[2]['cons'] = 0;$totalMensal[3]['cons'] = 0;$totalMensal[4]['cons']  = 0;$totalMensal[5]['cons']  = 0;$totalMensal[6]['cons']  = 0;
                    $totalMensal[7]['cons'] = 0;$totalMensal[8]['cons'] = 0;$totalMensal[9]['cons'] = 0;$totalMensal[10]['cons'] = 0;$totalMensal[11]['cons'] = 0;$totalMensal[12]['cons'] = 0;
                    
                    foreach($ucs as $uc){
                        
                        $tpl->RGI = $uc['rgi'];
                        $tpl->SHOWINFO = $uc['rgi'] . " (".$uc['empresa'] .") / " . $uc['nome'];
                        $index = 1;
                        unset($tot);
                        $tot;
                        for($i=1;$i<=12; $i++){

                            if(array_key_exists($index,$uc['mensal']) && $uc['mensal'][$index]['mes_ref'] == $i){
						
                                $val = $uc['mensal'][$index]['valor'];
                                $cons= $uc['mensal'][$index]['consumo'];
				$tpl->UCMESVAL = tratarValor($val, true);
                                $tpl->UCMESVAL.= ($temConsumo) ? "<br/><span class='blue'><small>". $tipoMedida ."</small> " . $uc['mensal'][$index]['consumo'] ."</span>": "";
				$index++;
						
                            }else{
							
                                $val = 0;
                                $cons= 0;
                                $tpl->UCMESVAL = "";
				
                            }
                            
                            $tot['valor'][$i] = $val;
                            $tot['consumo'][$i] = $cons;
                            $totalGeral[$i] += $val;
                            $totalMensal[$i]['pago']+= $val;
                            $totalMensal[$i]['cons']+= $cons;
                            $tpl->block('EACHMESVAL');
                            
                        }
                        $tpl->TOTVAL = tratarValor(array_sum($tot['valor']), true);
                        $tpl->TOTVAL.= ($temConsumo) ? "<br/><span class='blue'><small>". $tipoMedida ."</small> " . tratarValor(array_sum($tot['consumo'])) . "</span>": "";
                        $tpl->block('EACHUCMENSAL');
                        
                    }
                    
                    $tpl->TIPONOME = $relatorio->getTipoNome($tipos);
                    $tpl->CHART_INDEX= $tipos;
                    $tpl->TIPOMEDIDA = $tipoMedida;
                    
                    /****************************************
                     * MOSTRAR TOTAL MENSAL CONSUMO E VALOR *
                     ****************************************/
                    foreach($totalMensal as $key=>$val){
                        
                        $tpl->MESVAL = tratarValor($val['pago'], true);
                        $tpl->MESVAL.= ($temConsumo) ? "<br/><span class='blue'><small><b>". $tipoMedida ."</b></small> " . $val['cons'] ."</span>": "";
                        $tpl->block('EACH_TOT_MENSAL');
                        if($temConsumo){

                            $consChart[$tipos][$key] = $val['cons'];

                        }
                        
                    }
                    
                    $consChartVals = "";
                    if($temConsumo){
                        
                        foreach($consChart[$tipos] as $key=>$vals){
                            
                            $consChartVals.= ",['" . getMesNome($key) . "', " . $vals . "]";
                            
                        }
                        $tpl->CONSCHART = $consChartVals;
                        $tpl->block('TIPO_CONS_CHART');
                        
                    }
                    $tpl->block('EACHTIPO');
                    
                }
                
                /*
                 * Mostrar dados em tabela Geral e Montar Gráfico
                 * 
                 */
                $mediaUo = $relatorio->average($totalGeral);
                $chart_val = "";
                foreach($totalGeral as $key=>$totalmes){
                    
                    $tpl->TOTMESVAL = tratarValor($totalmes, true);
                    $chart_val.= ",['".getMesNome($key)."', $totalmes, $mediaUo]";
                    $tpl->block('TOTMESBLOCK');
                    
                }
                $tpl->CHARTVAL = $chart_val;
                $tpl->TOTMEDIA = tratarValor($mediaUo,true);
                
                $tpl->block('RESULTS');
                
            }else{
                
                $tpl->block('NORESULTS');
                
            }
            
        }
        
    }
        
    $sql = "SELECT id,nome,unidade FROM daee_udds ORDER BY unidade";
    $query = mysql_query($sql);
        
    while($uo = mysql_fetch_array($query)){
          
        $uoNome = $uo['unidade'] . " | " . $uo['nome'];
        $tpl->UOFORMNOME= $uoNome;
        $tpl->UOFORMID  = $uo['id'];
        $tpl->block('EACHUOFORM');
            
    }
        
        
    
 

    
?>