<?php
    $tpl->addFile('CONTEUDO', 'html_libs/painel_uoGeoChart.html');
    include "src/classes/Unidade.class.php";
    //include "src/classes/UnidadeCons.class.php";
    
    /*
     * MONTAR FORM
     */
    for($a=Date('Y'); $a>=2012; $a--){
        
        $tpl->ANOFORM = $a;
        $tpl->block('ANOS');
        
    }    
    
    /*
     * Verifica se o tipo tem consumo
     * 
     */
    if(isset($_GET['tipo'],$_GET['ano']) && array_key_exists($_GET['tipo'], $relatorio->ucTipos) && $relatorio->temConsumo($_GET['tipo']) && ($_GET['ano'] >= 2012 && $_GET['ano'] <= Date('Y')) ){
        
        $tipo = $_GET['tipo'];
        $ano = $_GET['ano'];
        $tipos = $relatorio->ucTipos;
        $tpl->ANO = $ano;
        
        $sql = "SELECT uo.nome, uo.unidade, uo.cidade, c.nome AS cidade, uo.latitude, uo.longitude,";
        $sql.= "SUM(n.consumo) AS consumo, SUM(n.valor) AS valor, COUNT(DISTINCT(n.uc)) AS ucqtd, COUNT(n.id) AS nqtd ";
        $sql.= "FROM daee_notas n, daee_uddc uc, daee_udds uo, sys_cidade c ";
        $sql.= "WHERE uc.uo=uo.id AND n.uc=uc.id AND uo.cidade=c.id AND uc.tipo=$tipo AND n.ano_ref=$ano ";
        $sql.= "GROUP BY uo.id ORDER BY consumo DESC";
        $query = mysql_query($sql);
        
        if(mysql_num_rows($query) > 0){
            
            /*
             * Mostrar dados e fazer InfoWindows
             * 
             */
            $indice = 0;
            while($marker = mysql_fetch_array($query, MYSQL_ASSOC)){
                
                /*
                 * MOSTRAR DADOS PARA GRÁFICO
                 */
                $tpl->SEQUENCIA = $indice;
                $tpl->LATLONG   = $marker['latitude'] . ", " . $marker['longitude'];
                $tpl->GETCOLOR  = getColor($marker['consumo']);
                $tpl->GETRADIUS = getRadiusValor($marker['valor']);
                
                
                
                
                        
                /*
                 * MOSTRAR DADOS PARA INFOWINDOWS 
                 */
                $tpl->RANK = $indice+1;
                $tpl->UOSIGLA   = $marker['unidade'];
                $tpl->UONOME    = $marker['nome'];  
                $tpl->TOTALCONS = tratarValor($marker['consumo']);
                $tpl->TOTALPAGO = tratarValor($marker['valor'], true);
                $tpl->TIPOMEDIDA= $relatorio->getTipoMedida($tipo);
                $tpl->UOCIDADE  = $marker['cidade'];
                $tpl->UCQTD     = $marker['ucqtd'];
                $tpl->NTQTD     = $marker['nqtd'];
                
                $tpl->block('MAPMARKERS');
                $tpl->block('INFOWINDOWS');
                
                
                $indice++;
                
            }
            
        }
        
        
        $tpl->TIPONOME = $tipos[$tipo];
        
        $tpl->block('MAPCONTAINER');
        /*
         * Fazer form com 1 item a mais
         */
        $formTipo = '<select name="tipo">';
        $formTipo .= "<option value='$tipo'>$tipos[$tipo]</option>";
        for($i=0; $i<= sizeof($tipos);$i++){
            
            if($relatorio->temConsumo($i)){
                
                $formTipo .= "<option value='$i'>$tipos[$i]</option>";
                
            }
            
        }
        $formTipo.= '</select>';
        $tpl->FORMTIPO = $formTipo;        
        
        
        
        
    }else{
        /*
         * Fazer form e não mostrar nada
         */
        $formTipo = '<select name="tipo">';
        $tipos = $relatorio->ucTipos;
        for($i=0; $i<= sizeof($tipos);$i++){
            
            if($relatorio->temConsumo($i)){
                
                $formTipo .= "<option value='$i'>$tipos[$i]</option>";
                
            }
            
        }
        $formTipo.= '</select>';
        $tpl->FORMTIPO = $formTipo;
        
        
    }
    
        
function getRadius($consumo, $tipo){
    
    if($consumo <= 0){
        
        return 1000;
        
    }else if($tipo == 0){
        
        return 1000 + $consumo * 10;
        
    }else if($tipo == 1){
        
        return 1000 + $consumo;
        
    }else{
        
        return 1000 + $consumo /10;
        
    }
    
}     

function getRadiusValor($valor){
    
    if($valor <= 0){
        
        return 1000;
        
    }else{
        
        return 1000 + $valor / 10;
        
    }
    
}  
    
function getColor($consumo){
    
    if($consumo == 0){
        
        return '#0066cc';
        
    }else{
        
        return '#f00';
        
    }
    
}

    
?>