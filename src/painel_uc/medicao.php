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
    $tpl->addFile('CONTEUDO_2', '../../html_libs/painel_uc_medicao_2.html');
    $tpl->addFile('CONTEUDO_3', '../../html_libs/painel_uc_medicao_3.html');
    $relatorio = new Relatorio();
    $ucTipos = $relatorio->ucTipos;
    
    
    if(isset($_SESSION['uc'])){
        
        $ucid           = (int) $_SESSION['uc'];
        $tpl->USERNAME  = $_SESSION['usuario'];
        $tpl->UCID      = $ucid;
        /*
         * Buscando dados da Consumidora no BD
         */
        $ucSql = "SELECT * FROM daee_uddc WHERE id = $ucid";
        $ucQuery = mysql_query($ucSql);
        $uc = mysql_fetch_array($ucQuery);

        /*
        * Pegando Datas do banco e Calculando quantidade de dias 
        * Caso não haja conta com data de medição, não mostrar nada
        */
        $getAno = (isset($_GET['ano']) && $_GET['ano']>=2012 && $_GET['ano']<=Date('Y')) ? $_GET['ano'] : Date('Y');
        $getMes = (isset($_GET['mes']) && $_GET['mes']>=1 && $_GET['mes']<=12) ? $_GET['mes'] : Date('n');
        $mesAnt     = ($getMes == 1) ? 12 : $getMes-1 ;
        $anoAnt     = ($getMes == 1) ? $getAno-1 : $getAno ;
        $sqlDatas   = "SELECT data_medicao, medicao, consumo, mes_ref, id FROM daee_notas WHERE uc=$ucid AND data_medicao IS NOT NULL AND ((mes_ref=$mesAnt AND ano_ref=$anoAnt) OR (mes_ref=$getMes AND ano_ref=$getAno)) ORDER BY data_medicao";
        $queryDatas = mysql_query($sqlDatas);
        $data_inicial= "";
        $data_final = "";
        $ultima_data;
        $med_ini    = 0;
        $med_fin    = 0;
        $consumo_ini= 0;
        $consumo_fin= 0;
        $nota_id    = 0;
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
            $data_inicial = $ret[0][0]; //Data do mês anterior
            $data_final   = $ret[1][0]; //Data do mês atual
            $med_ini      = $ret[0][1];
            $med_fin      = $ret[1][1];
            $consumo_ini  = $ret[0][2];
            $consumo_fin  = $ret[1][2];
            $nota_id      = $ret[1][4];
            $continue = true;
        }else{
            $sql_ultimo = "SELECT mes_ref, ano_ref FROM daee_notas WHERE uc=$ucid AND data_medicao IS NOT NULL ORDER BY data_medicao DESC LIMIT 1";
            $queryUltimo = mysql_query($sql_ultimo);
            if(mysql_num_rows($queryUltimo) == 1){
                $linha      = mysql_fetch_array($queryUltimo);
                $mes_ref    = (intval($linha['mes_ref']) == 12) ? 1 : $linha['mes_ref'] + 1;
                $ano_ref    = (intval($linha['mes_ref']) == 12) ? $linha['ano_ref'] + 1 : $linha['ano_ref'];
                //$continue = true;
                header("Location: medicao.php?mes=$mes_ref&ano=$ano_ref");
            }else
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
            $tpl->REFERENCIAC= getMesNome($getMes,false) . " de " . $getAno; //Forma completa
            $tpl->DATA_INI  = setDateDiaMesAno($data_inicial);
            $tpl->DATA_FIN  = setDateDiaMesAno($data_final);
            $tpl->DATA_INIC = str_replace("-", ",", $data_inicial);
            //$tpl->DATA_FINC = str_replace("-", ",", $data_final);
            $tpl->MES_ANT   = $mesAnt;
            $tpl->MES_ANTERIOR= getMesNome($mesAnt);
            $tpl->ANO_ANT   = $anoAnt;
            $tpl->MES_POSTERIOR= ($getMes == 12) ? getMesNome(1) : getMesNome($getMes+1);
            $tpl->MES_POS   = $mes_pos = ($getMes == 12) ? 1 : $getMes+1;
            $tpl->ANO_POS   = ($getMes == 12 && $getAno < Date('Y')) ? $getAno+1 : $getAno;
            
            $ultima_data    = ($data_final == Date('Y-m-d')) ? date('Y-m-d', strtotime("+1 day")) : $data_final;
            $sqlPontos = "SELECT m.* FROM sys_medicao m WHERE m.uc = $ucid AND m.data_leitura > '$data_inicial' AND m.data_leitura <= '$ultima_data' ORDER BY m.data_leitura";
            $queryPontos = mysql_query($sqlPontos);
            /*
             * Mostrar TABLEROW_FIXO e Primeiro ponto do gráfico
             */
            $tpl->TR_CLASS      = 'warning';
            $tpl->TR_MES_REF    = getMesNome($mesAnt) . "/" . $anoAnt;
            $tpl->TR_DATA       = setDateDiaMesAno($data_inicial);
            $tpl->TR_MEDICAO    = $med_ini;
            $tpl->TR_CONSUMO    = "--";
            $tpl->TR_DIAS_UT    = "--";
            $tpl->TR_DIA_SEM    = getDiaSemana($data_inicial);
            //$tpl->TR_POP_FLU    = "--";
            $tpl->block('TABLEROW_FIXO');
            $chart_data = "[" . convertDateToGoogle($data_inicial) . ", " . $med_ini . ", '". getMesNome($mesAnt) . "/" . $anoAnt ."', '<b>Leitura feita pela empresa em:</b> " . setDateDiaMesAno($data_inicial) . "<br />Inicial: <b>$med_ini {TIPOMEDIDA}</b><br />Consumo do período: $consumo_ini {TIPOMEDIDA}'],";
            if(mysql_num_rows($queryPontos) > 0){

                $med_ant = $med_ini;
                $con_periodo = 0;
                $con_litros  = 0;
                $pop_flut_tot= 0;
                $dias_tot    = 0;
                $data_ant    = strtotime($data_inicial);
                $i = 1;
                /*
                 * Mostrar pontos no Gráfico
                 */
                while($linha = mysql_fetch_array($queryPontos)){

                    $diferenca  = round(($linha['leitura'] - $med_ant) * 100) / 100; // Diferença entre leituras
                    $con_periodo += $diferenca;
                    $litros     = $diferenca * 1000;
                    $con_litros += $litros;
                    /*
                     * Calcular média de litros por dia
                     */
                    $diff_dias  = (int)floor( (strtotime($linha['data_leitura']) - $data_ant) / (60 * 60 * 24)); // Dias decorridos entre pontos
                    $med_Litro_dia= ($diff_dias > 0) ? round($litros / $diff_dias * 100) / 100 : round($litros / 1 * 100) / 100;
                    $data_ant   = strtotime($linha['data_leitura']);
                    $ultima_data= $linha['data_leitura'];
                    
                    $tooltip    = "<b>" . setDateDiaMesAno($linha['data_leitura']) . "</b> Leitura <b>" . $linha['leitura'] . " {TIPOMEDIDA}</b><br />" . " Consumo desde a última medição<br />$diff_dias dias.<br /><b>" . $diferenca. "{TIPOMEDIDA} = " . $litros . " L ( $med_Litro_dia L/dia )</b><br /><b>". $linha['user'] .":</b> " . $linha['obs'];
                    $chart_data.= "[" . convertDateToGoogle($linha['data_leitura']) . ", " . $linha['leitura'] . ", '" . $con_periodo . " {TIPOMEDIDA}', '$tooltip'],";
                    $med_ant    = $linha['leitura'];
                    $med_fin    = ($linha['leitura'] > $med_fin) ? $linha['leitura'] : $med_fin;
                    
                    $pop_flut_tot+= $linha['pop_flut'];
                    $dias_tot    += $diff_dias;
                    
                    $med_pes    = ($diff_dias > 0) ? $litros / ($uc['pop_fixa'] * $diff_dias + $linha['pop_flut'] * $diff_dias) : $litros / ($uc['pop_fixa'] * $diff_dias + $linha['pop_flut'] * $linha['permanencia']);
                    
                    /* 
                     * Mostrar Medições a Tabela
                     */
                    $tpl->TR_INDEX      = $i;
                    $tpl->TR_DATA       = ExplodeDateTime($linha['criado_em']);
                    $tpl->TR_BDATA      = date("Y", strtotime($linha['data_leitura'])) . ', ' . date("n", strtotime($linha['data_leitura'])) . ', ' . date("j", strtotime($linha['data_leitura']));
                    $tpl->TR_USERNAME   = $linha['user'];
                    $tpl->TR_MEDICAO    = $linha['leitura'];
                    $tpl->TR_CONSUMO    = tratarValor($litros);
                    $tpl->TR_CONSUMO_T  = $litros;
                    $tpl->TR_DIAS_UT    = $diff_dias;
                    $tpl->TR_DIA_SEM    = getDiaSemana($linha['data_leitura']);
                    $tpl->TR_POP_FLU    = $linha['pop_flut'];
                    $tpl->TR_MED_DIA    = $med_Litro_dia;
                    $tpl->TR_PERM       = ($linha['permanencia'] > $diff_dias) ? $diff_dias : $linha['permanencia'];
                    $tpl->TR_MED_PES    = round($med_pes * 100) / 100;
                    $tpl->TR_OBS        = $linha['obs'];
                    $i++;
                    $tpl->block('TABLEROW_INPUT');                    

                }
                $tpl->CHARTDATA = substr($chart_data, 0, -1) ;//Sem vírgula no final
                $tpl->CHARTDATA.= ($consumo_fin > 0 && $data_final != $ultima_data) ? ",[" . convertDateToGoogle($data_final) . ", " . $med_fin . ", '". tratarValor($med_fin - $med_ini) ."{TIPOMEDIDA}', '<b>$mes_ref</b><br />Leitura constante na nota " . setDateDiaMesAno($data_final) . "<br />Final: $med_fin {TIPOMEDIDA}<br />Consumido: ". tratarValor($med_fin - $med_ini) ." {TIPOMEDIDA} ']" : "";
                 
                $diff = $med_fin - $med_ini;
                $tpl->CON_PERIODO = round($diff * 100) / 100;
                $tpl->VAR_PERIODO = getPorcentagem(($diff * 100 / $consumo_ini) - 100);
                
                /* 
                 * Mostrar Ultima nota na Tabela
                 */
                if($consumo_fin > 0 && $data_final != $ultima_data){
                    
                    $tpl->TR_INDEX      = $i;
                    $tpl->TR_DATA       = setDateDiaMesAno($data_final) . " " . $mes_ref;
                    $tpl->TR_BDATA      = date("Y", strtotime($data_final)) . ', ' . date("n", strtotime($data_final)) . ', ' . date("j", strtotime($data_final));
                    $tpl->TR_USERNAME   = $mes_ref;
                    $tpl->TR_MEDICAO    = $med_fin;
                    $tpl->TR_CONSUMO    = tratarValor(($med_fin - $med_ant) * 1000);
                    $tpl->TR_CONSUMO_T  = $l = ($med_fin - $med_ant) * 1000;
                    $tpl->TR_DIAS_UT    = $d = (int)floor( (strtotime($data_final) - $data_ant) / (60 * 60 * 24));
                    $tpl->TR_DIA_SEM    = getDiaSemana($data_final);
                    $tpl->TR_POP_FLU    = 0;
                    $tpl->TR_MED_DIA    = $m = ($d > 0) ?  round(($med_fin - $med_ant) * 1000 / $d * 100) / 100 : round(($med_fin - $med_ant) * 1000 / 1 * 100) / 100;
                    $tpl->TR_PERM       = 0;
                    $med_pes            = $l / ($uc['pop_fixa'] * $d);
                    $tpl->TR_MED_PES    = round($med_pes * 100) / 100;
                    $tpl->TR_OBS        = "Data de leitura constante na Nota : " . setDateDiaMesAno($data_final) ;
                    $con_litros        += ($med_fin - $med_ant) * 1000;
                    $dias_tot          += $d;
                    $i++;
                    $tpl->block('TABLEROW_INPUT');
                    
                }
                /*
                 * Mostrar Totais na tabela
                 */
                $tpl->TF_TOTAL      = tratarValor($con_litros);
                $tpl->POP_FLUT_SUM  = $pop_flut_tot;
                $tpl->TOT_DIAS  = $dias_tot;
                
            }elseif($med_fin > 0){
                
                $tpl->CHARTDATA = $chart_data . "[" . convertDateToGoogle($data_final) . ", " . $med_fin . ", '$mes_ref', '<b>$mes_ref</b><br />Leitura constante na nota " . setDateDiaMesAno($data_final) . "<br />Final: <b>$med_fin {TIPOMEDIDA}</b><br />Consumido: <b>". tratarValor($med_fin - $med_ini) ."</b>{TIPOMEDIDA}']";
                $tpl->CON_PERIODO = $med_fin - $med_ini;
                $tpl->VAR_PERIODO = getPorcentagem((($med_fin - $med_ini) * 100 / $consumo_ini) - 100); 
                $tpl->TR_INDEX      = 1;
                $tpl->TR_DATA       = setDateDiaMesAno($data_final) . " " . $mes_ref;
                $tpl->TR_BDATA      = date("Y", strtotime($data_final)) . ', ' . date("n", strtotime($data_final)) . ', ' . date("j", strtotime($data_final));
                $tpl->TR_USERNAME   = $mes_ref;
                $tpl->TR_MEDICAO    = $med_fin;
                $tpl->TR_CONSUMO    = tratarValor(($med_fin - $med_ini) * 1000);
                $tpl->TR_CONSUMO_T  = ($med_fin - $med_ini) * 1000;
                $tpl->TR_DIAS_UT    = $d = (int)floor( (strtotime($data_final) - strtotime($data_inicial)) / (60 * 60 * 24));
                $tpl->TR_DIA_SEM    = getDiaSemana($data_final);
                $tpl->TR_POP_FLU    = 0;
                $tpl->TR_MED_DIA    = $m = round(($med_fin - $med_ini) * 1000 / $d * 100) / 100;
                $tpl->TR_PERM       = $d;
                $tpl->TR_MED_PES    = round($m / $uc['pop_fixa'] * 100) / 100;
                $tpl->TR_OBS        = "Data de leitura constante na Nota : " . setDateDiaMesAno($data_final) ;
                $tpl->block('TABLEROW_INPUT');
                $tpl->TF_TOTAL      = tratarValor(($med_fin - $med_ini) * 1000);
                $tpl->POP_FLUT_SUM  = 0;
                
            }else{

                $med_fin = $med_ini;
                $tpl->CHARTDATA = $chart_data;
                
            }

            /*
             * Dados UC
             */
            $tpl->TIPOMEDIDA = $relatorio->getTipoMedida($uc['tipo']);
            $tpl->UCNOME = $uc['rgi'] . " - " . $uc['compl'];
            $tpl->CON_ANTERIOR= $consumo_ini;
            $meta = $uc['meta'];
            $tpl->META = $meta;
            $tpl->DATA_HOJE = date('d/m/Y');
            $tpl->POP_FIXA = $uc['pop_fixa'];
            
            $tpl->block('HISTORICO_MED');
            $tpl->block('COMPARACAO_BLOCK');
            /*
             * Buscando nota
             */
            $notaSql = "SELECT n.*, c.* FROM daee_uddc c, daee_notas n WHERE n.uc = c.id AND n.id = " .$nota_id;
            $notaQuery= mysql_query($notaSql);
            if(mysql_num_rows($notaQuery) == 1){
                
                $nota = mysql_fetch_array($notaQuery);
                $tpl->NDATA_LANCTO  = ExplodeDateTime($nota['criado']) . " <abbr>($nota_id)</abbr>";
                $tpl->NDATA_LEITURA = setDateDiaMesAno($nota['data_medicao']);
                $tpl->NLEITURA      = $nota['medicao'];
                $tpl->NCONSUMO      = $nota['consumo'];
                $tpl->NVALOR        = tratarValor($nota['valor'],true);
                
                $tpl->block('NOTA_BLOCK');
                
            }else
                $tpl->block('SNOTA_BLOCK');
            
            
            $tpl->block('RESULTS');
            $tpl->block('OUTROS_DADOS');
            
            /*
             * Mostrar formulário
             */
            if( $nota_id == 0 && $ultima_data != Date('Y-m-d') ){
                
                $tpl->MED_FIN = $med_fin;
                $tpl->block('FORM_BLOCK');
            
            }

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