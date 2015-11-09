<?php
	require "src/scripts/conecta.php";
require "src/scripts/functions.php";
include_once "src/classes/Users.class.php";
include_once "src/classes/Notas.class.php";
include_once "src/classes/UnidadeCons.class.php";

/* * ************************************************
  ARQUIVO DE SCRIPTS DE AÇÃO RECEBIDOS POR S_POST
 * ************************************************ */

if (getenv("REQUEST_METHOD") == "POST") {

    if (isset($_POST['action'])) {

        switch ($_POST['action']) {

            case "verificaNota":

                $uc = new UnidadeConsumidora($_POST['ucid']);
                echo $uc->isNotaRegistered($_POST['data']);

                break;
            case "marcarPago":

                $data = getTransformDate($_POST['data']);
                $nota = new Nota($_POST['nid']);
                $nota->marcarPago($data);
                echo "<font color='red'>" . $_POST['data'] . "</font>";

                break;
            case "getProvs":

                $autos = (int) $_POST['autos'];
                $sql = "SELECT DISTINCT(provisoria) FROM daee_notas WHERE contrato = $autos ORDER BY provisoria DESC";
                $query = mysql_query($sql);
                if (mysql_num_rows($query) > 0) {

                    while ($prov = mysql_fetch_array($query)) {

                        $num = $prov['provisoria'];
                        if ($num == 0) {

                            echo "<option value='$num'>Desconhecida</option>";
                        } else {

                            echo "<option value='$num'>$num</option>";
                        }
                    }
                } else {

                    echo "<option value='$num'>" . mysql_error() . "</option>";
                }

                break;
                
            case "salvar-leitura": // Recebido de painel_uc.php/medicao.php
                
                //var_dump($_POST);
                if(isset($_POST['leitura'],$_POST['user'],$_POST['ucid'],$_POST['flutuante'],$_POST['permanencia'],$_POST['observacao'])){
                    
                    $ucid = (int) $_POST['ucid'];
                    $leitura = floatval($_POST['leitura']);
                    $flutuante = (int) $_POST['flutuante'];
                    $permanencia = (int) $_POST['permanencia'];
                    $observacao = mysql_real_escape_string($_POST['observacao']);
                    $user       = mysql_real_escape_string($_POST['user']);
                    
                    $sql = "SELECT leitura FROM sys_medicao WHERE uc = $ucid AND leitura >= $leitura";
                    $query = mysql_query($sql);
                    $grava = false;
                    
                    if(mysql_num_rows($query) < 1){
                        
                        $sql = "INSERT INTO sys_medicao(id,uc,user,data_leitura,criado_em,leitura,pop_flut,permanencia,obs) VALUES ('', $ucid, '$user', CURDATE(), NOW(), $leitura, $flutuante, $permanencia, '$observacao')";
                        mysql_query($sql);
                        echo 1;
                        
                    }else{
                        
                        echo "ERRO: Medição inválida";
                        
                    }
                    
                }else{
                    
                    echo "ERRO: Faltam argumentos";
                    
                }
                break;
        }
    }
}
?>