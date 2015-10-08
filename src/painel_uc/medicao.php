<?php
    
    require "../scripts/conecta.php";
    //include "../classes/UnidadeCons.class.php";
    include "../scripts/functions.php";
    include "../classes/Relatorio.class.php";
    require "../classes/Template.class.php";
    require "../scripts/restrito.php";
    
    
    redirectByPermission(0);
    $tpl = new Template("../../html_libs/painel_uc_conteudo_container.html");    
    $tpl->addFile('CONTEUDO_1', '../../html_libs/painel_uc_medicao_1.html');
    //$tpl->addFile('CONTEUDO_2', '../../html_libs/painel_uc_home_2.html');
    $relatorio = new Relatorio();
    $ucTipos = $relatorio->ucTipos;
    
    
    if(isset($_SESSION['uc'])){
        
        $ucid = (int) $_SESSION['uc'];
        
        /*
        * Pegando Datas do banco e Calculando quantidade de dias 
        * Caso não haja conta com data de medição, não mostrar nada
        */
        $getAno = (isset($_GET['ano']) && $_GET['ano']>=2012 && $_GET['ano']<=Date('Y')) ? $_GET['ano'] : Date('Y');
        $getMes = (isset($_GET['mes']) && $_GET['mes']>=1 && $_GET['mes']<=12) ? $_GET['mes'] : Date('n');
        $mesAnt     = ($getMes == 1) ? 12 : $getMes-1 ;
        $anoAnt     = ($getMes == 1) ? $getAno-1 : $getAno ;
        $sqlDatas   = "SELECT data_medicao, medicao, consumo, mes_ref FROM daee_notas WHERE uc=$ucid AND data_medicao IS NOT NULL AND ((mes_ref=$mesAnt AND ano_ref=$anoAnt) OR (mes_ref=$getMes AND ano_ref=$getAno)) ORDER BY data_medicao";
        $queryDatas = mysql_query($sqlDatas);
        $data_inicial= "";
        $data_final = "";
        $med_ini    = 0;
        $med_fin    = 0;
        $consumo_ini= 0;
        $continue   = false;
        if(mysql_num_rows($queryDatas) == 1){
            $linha       = mysql_fetch_array($queryDatas);
            if($linha['mes_ref'] == $mesAnt){
                $data_inicial= $linha['data_medicao'];
                $data_final  = Date('Y-m-d');
                $med_ini = $linha['medicao'];
                $consumo_ini = $linha['consumo'];
                $continue = true;
            }else
                $continue = false;
        } elseif(mysql_num_rows($queryDatas) == 2){
            $ret;
            while($linha = mysql_fetch_row($queryDatas)){
                $ret[] = $linha;
            }
            $data_inicial = $ret[0][0];
            $data_final   = $ret[1][0];
            $med_ini      = $ret[0][1];
            $med_fin      = $ret[1][1];
            $consumo_ini  = $ret[0][2];
            $continue = true;
        }else{ 
            $continue = false;
        }
        $time_inicial   = strtotime($data_inicial);
        $time_final     = strtotime($data_final);
        $diferenca      = $time_final - $time_inicial; // Calcula a diferença de segundos entre as duas datas:
        $quantDias      = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
        
        if($continue){
            
            /*
             * Mostrando Variáveis
             * 
             */
            $tpl->PERIODO   = $quantDias;
            $tpl->REFERENCIA= $mes_ref = getMesNome($getMes) . "/" . $getAno;
            $tpl->DATA_INI  = setDateDiaMesAno($data_inicial);
            $tpl->DATA_FIN  = setDateDiaMesAno($data_final);
            $tpl->DATA_INIC = str_replace("-", ",", $data_inicial);
            //$tpl->DATA_FINC = str_replace("-", ",", $data_final);
            $tpl->MES_ANT   = $mesAnt;
            $tpl->ANO_ANT   = $anoAnt;
            $tpl->MES_POS   = ($getMes == 12) ? 1 : $getMes+1;
            $tpl->ANO_POS   = ($getMes == 12 && $getAno < Date('Y')) ? $getAno+1 : $getAno;

            $sqlPontos = "SELECT m.* FROM sys_medicao m WHERE m.uc = $ucid AND m.data_leitura BETWEEN '$data_inicial' AND '$data_final' ORDER BY m.data_leitura";
            $queryPontos = mysql_query($sqlPontos);
            $pontos;
            $chart_data = "[" . convertDateToGoogle($data_inicial) . ", " . $med_ini . ", '". getMesNome($mesAnt) . "/" . $anoAnt ."', '<b>Nota Lançada em</b> " . setDateDiaMesAno($data_inicial) . "<br />Inicial: $med_ini {TIPOMEDIDA}<br />Consumido: $consumo_ini {TIPOMEDIDA}'],";
            if(mysql_num_rows($queryPontos) > 0){

                $med_ant    = 0;
                while($linha = mysql_fetch_array($queryPontos)){

                    if($med_ant == 0) $med_ini = $linha['leitura'];
                    $variacao   = ($med_ant == 0) ?  "" : ($linha['leitura'] * 100 / $med_ant) - 100;
                    $diferenca  = $linha['leitura'] - $med_ant;
                    $tooltip    = "<b>" . setDateDiaMesAno($linha['data_leitura']) . "</b><br />Leitura " . $linha['leitura'] . " {TIPOMEDIDA}<br />" . " Consumido ".  tratarValor($diferenca). " {TIPOMEDIDA}";
                    $chart_data.= "[" . convertDateToGoogle($linha['data_leitura']) . ", " . $linha['leitura'] . ", '" . getPorcentagem($variacao) . "%', '$tooltip'],";
                    $med_ant    = $linha['leitura'];
                    $pontos[] = $linha;

                }

                $tpl->CHARTDATA = substr_replace($chart_data, '', -1);
                $tpl->CON_PERIODO = $med_ant - $med_ini;
                $tpl->VAR_PERIODO = getPorcentagem(($med_ant * 100 / $med_ini) - 100);

            }elseif($med_fin > 0){
                
                $tpl->CHARTDATA = $chart_data . "[" . convertDateToGoogle($data_final) . ", " . $med_fin . ", '$mes_ref', '<b>Nota Lançada em</b> " . setDateDiaMesAno($data_final) . "<br />Inicial: $med_fin {TIPOMEDIDA}<br />Consumido: ". tratarValor($med_fin - $med_ini) ."{TIPOMEDIDA}']";
                $tpl->CON_PERIODO = $med_fin - $med_ini;
                $tpl->VAR_PERIODO = getPorcentagem((($med_fin - $med_ini) * 100 / $consumo_ini) - 100);                
                
            }else{

                $tpl->CHARTDATA = $chart_data;
                
            }

            /*
             * Buscando dados da Consumidora no BD
             * 
             */
            $ucSql = "SELECT * FROM daee_uddc WHERE id = $ucid";
            $ucQuery = mysql_query($ucSql);
            $uc = mysql_fetch_array($ucQuery);
            $tpl->TIPOMEDIDA = $relatorio->getTipoMedida($uc['tipo']);
            $tpl->UCNOME = $uc['rgi'] . " - " . $uc['compl'];
            
            $tpl->block('RESULTS');

        }else{
        
            $tpl->block('NORESULTS');
        }
        
    }else{
        
        $tpl->block('NORESULTS');
        
    }
    
    $tpl->show();
       
    function convertDateToGoogle($date){
        
        $ret = explode("-", $date);
        $ano = $ret[0];
        $mes = ((int) $ret[1]) - 1;
        $dia = (int) $ret[2];
        return "new Date(" .$ano . "," . $mes . "," . $dia . ")" ;
        
    }
?>