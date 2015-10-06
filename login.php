<?php
	require "src/classes/Template.class.php";
	require "src/scripts/conecta.php";
	include_once "src/classes/Users.class.php";
	//require "src/scripts/restrito.php";
	
	$tpl = new Template('html_libs/template_livre.html');
	
	
	$tpl->addFile("CONTEUDO","html_libs/sys_login.html");
	
	/* 
	 * Recebendo dados por S_POST
	 * para fazer login
	*/
	
	if (getenv("REQUEST_METHOD") == "POST") {
	
		if(isset($_POST['user'], $_POST['pass'])){
		
			$user = $_POST['user'];
			$pass = $_POST['pass'];
			if(strlen($user) >= 6 && strlen($pass) >=4){
			
				$user = new User($user,$pass);
                                
                                $sql = "SELECT uc.id,us.nivel,us.login,us.bacia FROM daee_uddc uc, (SELECT nivel, bacia, login FROM sys_users WHERE pront = $pass AND ativo=1) us ";
                                $sql.= "WHERE uc.uo = us.bacia AND uc.rgi = '". $_POST['user'] ."' AND uc.tipo = 0";
                                $query = mysql_query($sql);
                                
				if($user->isValidUser()){
				
                                    $user->startSession();
                                    $user->gotoRightPage();
                                    //$tpl->ALERTA = "Você está logado como ".$_SESSION['usuario'];
				
                                }elseif(mysql_num_rows($query) == 1){
                                    
                                    session_start();
                                    $uc = mysql_fetch_array($query);
                                    $_SESSION['uc'] = $uc['id'];//fetch;
                                    $_SESSION['nivel'] = $uc['nivel'];
                                    $_SESSION['lastAccess'] = date("Y-n-j H:i:s");
                                    $_SESSION['bacia'] = $uc['bacia'];
                                    $_SESSION['usuario'] = $uc['login'];
                                    header("Location: painel_uc.php");
                                    //var_dump($_SESSION);
                                    
                                }else{
				
					$tpl->ALERTA = "Os dados fornecidos estão incorretos!";
				
				}
			
			}else{
			
				$tpl->ALERTA = "O usuario deve ter no mínimo 6 caracteres e a senha 4.";
			
			}
		
		}
	
	}
	
	/*
	 * Mostrar Página Restrita
	 *
	 */
	
	if(isset($_GET['x'])){
	
		$tpl->ALERTA = "Página Restrita: Faça o login";
	
	}elseif(isset($_GET['y'])){
	
		$tpl->ALERTA = "Permissão incorreta: Faça o login novamente";
	
	}elseif(isset($_GET['z'])){
	
		$tpl->ALERTA = "Sessão expirada por inatividade: Faça o login";
	
	}


	
	$tpl->show();
	
?>