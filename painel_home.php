<?php
	
	require "src/scripts/conecta.php";
	require "src/scripts/restrito.php";
        require "src/scripts/functions.php";
	require "src/classes/Template.class.php";
	include_once "src/classes/Users.class.php";
	include "src/classes/Relatorio.class.php";
	
	
	redirectByPermission(1); // SETAR PERMISSÃO DA PÁGINA
	
	$tpl = new Template('html_libs/template_painel.html');
	$inicio = execucao();
        $relatorio = new Relatorio();
        $ucTipos = $relatorio->ucTipos;
        
	if(!isset($_GET['mode'])){
            
            include "src/painel/home.php";
            
        }else{
            
            switch($_GET['mode']){
                
                case "uoRanking":
                    include "src/painel/uoRanking.php";
                break;
                case "uoEvoConsumo":
                    include "src/painel/uoEvoConsumo.php";
                break;
                case "ucEvoConsumo":
                    include "src/painel/ucEvoConsumo.php";
                break;
                case "ucRanking":
                    include "src/painel/ucRanking.php";
                break;
                case "ucHistory":
                    include "src/painel/ucHistory.php";
                break;            
                case "uoHistory":
                    include "src/painel/uoHistory.php";
                break;
                case "uoGeoChart":
                    include "src/painel/uoGeoChart.php";
                break;
                case "ucGeoChart":
                    include "src/painel/ucGeoChart.php";
                break;            
            
            }
            
            
        }

        /*
         * MONTAR MENU DINAMICAMENTE 
         * BASE EM INFORMAÇÕES DE RELATÓRIO.CLASS
         * 
         */
	for($i = 0; $i < count($ucTipos); $i++ ){
            
            $tpl->TPINDEX = $i;
            $tpl->TPNOME = $ucTipos[$i];
            $tpl->block('MENUTP1');
            $tpl->block('MENUTP2');
            
        }
        
	$fim = execucao();
	$tempo = number_format(($fim-$inicio),6);
	$tpl->EXECTIME = "Exec. <b>".$tempo."</b> segundos";        
	$tpl->show();
	
?>