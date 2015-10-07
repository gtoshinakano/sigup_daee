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
        $sqlDatas   = "SELECT data_medicao, medicao FROM daee_notas WHERE uc=$ucid AND ((mes_ref=$mesAnt AND ano_ref=$anoAnt) OR (mes_ref=$getMes AND ano_ref=$getAno)) ORDER BY data_medicao";
        $queryDatas = mysql_query($sqlDatas);
        $data_inicial= "";
        $data_final = "";
        $medicao;
        if(mysql_num_rows($queryDatas) == 1 && $getMes == Date('n')){
            $linha       = mysql_fetch_array($queryDatas);
            $data_inicial= $linha['data_medicao'];
            $data_final  = Date('Y-m-d');
            $medicao[] = $linha;
        } elseif(mysql_num_rows($queryDatas) == 2){
            $ret;
            while($linha = mysql_fetch_array($queryDatas)){
                $ret[] = $linha;
            }
            $data_inicial = $ret[0][0];
            $data_final   = $ret[1][0];
            $medicao      = $ret;
        }else{ 
            $data_inicial = null;
            $data_final   = null;
        }
        $time_inicial   = strtotime($data_inicial);
        $time_final     = strtotime($data_final);
        $diferenca      = $time_final - $time_inicial; // Calcula a diferença de segundos entre as duas datas:
        $quantDias      = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
        
        /*
         * Mostrando Variáveis
         * 
         */
        
        $tpl->PERIODO = $quantDias;
        $tpl->REFERENCIA = getMesNome($getMes) . "/" . $getAno;
        $tpl->DATA_INI = setDateDiaMesAno($data_inicial);
        $tpl->DATA_FIN = setDateDiaMesAno($data_final);
        $tpl->DATA_INIC= str_replace("-", ",", $data_inicial);
        $tpl->DATA_FINC= str_replace("-", ",", $data_final);
        //$tpl->TESTE = $data_inicial . $data_final;
        
        /*
         * Buscando pontos de medição no BD
         * 
         */
        $sqlPontos = "SELECT m.* FROM sys_medicao m WHERE m.uc = $ucid AND m.data_leitura BETWEEN '$data_inicial' AND '$data_final' ORDER BY m.data_leitura";
        $queryPontos = mysql_query($sqlPontos);
        $pontos;
        if(mysql_num_rows($queryPontos) > 0){
            
            $chart_data = "['date', 'Consumo'],";
            while($linha = mysql_fetch_array($queryPontos)){
                
                $chart_data.= "[" . convertDateToGoogle($linha['data_leitura']) . ", " . $linha['leitura'] . "],";
                $ponos[] = $linha;
                
            }
            
            $tpl->CHARTDATA = substr_replace($chart_data, '', -1);
            
        }else 
            $tpl->CHARTDATA = "['date', 'Consumo'],[new Date('$data_inicial'), 0]";
        
        /*
         * Buscando dados da Consumidora no BD
         * 
         */
        $ucSql = "";
        
        
        
        //$tpl->UCNOME = $dados[0]['rgi'] . " - " . $dados[0]['compl'];
        
        
       //$data_inicial   = () ? : ;
       //$data_final     = () ? : ;            
        
        /*
         * Desenhar Gráfico
         *            ['date', 'Consumo'],
            [new Date(2014,11,25),  1000],
            [new Date(2014,11,27),  1170],
            [new Date(2014,11,31),  660],
            [new Date(2015,0,05),  1030],
            [new Date(2015,0,15),  660],
         */
        
        //$tpl->GETANO = $getAno;
        
        
        
        /*$sql = "SELECT * FROM ( SELECT uo.unidade,uo.nome AS dirnome,uc.rgi,uc.compl,uc.tipo,n.mes_ref,n.ano_ref,SUM(n.valor) AS valor,SUM(n.consumo) AS consumo, uc.rua, e.nome ";
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
                $info['chartval'].= ",['" . getMesNome($linha['mes_ref']) . "'," . $linha['valor'] . ", @@MEDIAVAL@@]";
                $info['chartcon'].= ",['" . getMesNome($linha['mes_ref']) . "', 200, @@MEDIACONS@@, " . $linha['consumo'] . "]";// COLOCAR META ONDE 400 
            }

            $tpl->MEDIAVAL = tratarValor($relatorio->average($info['valor']), true);
            $tpl->MEDIACONS = tratarValor($relatorio->average($info['consumo']));
            $info['chartval'] = str_replace('@@MEDIAVAL@@', $relatorio->average($info['valor']), $info['chartval']);
            $tpl->CHARTVAL = $info['chartval'];
            $info['chartcon'] = str_replace('@@MEDIACONS@@', $relatorio->average($info['consumo']), $info['chartcon']);
            $tpl->CHARTCON = ($relatorio->temConsumo($info['tipo'])) ? $info['chartcon'] : "";

            $tpl->TIPOMEDIDA = $relatorio->getTipoMedida($info['tipo']);

            $tpl->block('TABLEROWVAL');
            if ($relatorio->temConsumo($info['tipo']))
                $tpl->block('TABLEROWCONS');
            $tpl->UCNOME = $info['nome'];

        }else {

            $tpl->block('NORESULTS');
        }*/
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