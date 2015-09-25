
<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
		<meta charset="utf-8">
		
		<title>Sistema DAEE - ADA</title>
		
		<script language="javascript" src="jquery.js"></script>
		
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        
        $(document).ready(function(){
           
           $('#go').click(function(){
               
               var nid = $('#inputNotaid').val();
               $.get("index.php", {nota:nid}, function(data){
                   
                   $('#show').html(data);
                   //alert('nid');
                   
               });
               
           });
            
        });
    
    
    
    </script>
    <style>
        body{
            font-family: verdana;
        }
        /** Scaffold View **/
        dl {
                line-height: 2em;
                margin: 0em 0em;
                width: 60%;
        }
        dl dd:nth-child(4n+2),
        dl dt:nth-child(4n+1) {
                background: #f4f4f4;
        }

        dt {
                font-weight: bold;
                padding-left: 4px;
                vertical-align: top;
                width: 10em;
        }
        dd {
                margin-left: 10em;
                margin-top: -2em;
                vertical-align: top;
        }        
    </style>
	</head>
	<body>
<?php

    require "../src/scripts/conecta.php";
    require "../src/scripts/functions.php";
    if (getenv("REQUEST_METHOD") == "POST") {
        
        $id = (int) $_POST['delid'];
        $deleteSql = "DELETE FROM daee_notas WHERE id = $id";
        $query = mysql_query($deleteSql);
        echo "Apagado com Sucesso!";
        
    }
    if (isset($_GET['nota'])){
    
        $notaid = (int) $_GET['nota'];
        $sql = "SELECT n.*, c.* FROM daee_notas n, daee_uddc c WHERE n.id=$notaid AND n.uc = c.id";
        $query = mysql_query($sql);
        if(mysql_num_rows($query) > 0){
            
            $infos = mysql_fetch_array($query);
            ?>
                <dl>
                    <dt>ID</dt>
                    <dd><?php echo $_GET['nota']; ?></dd>            
                    <dt>UND. CONS.</dt>
                    <dd><?php echo $infos['rgi'] . ' - ' . $infos['compl']; ?></dd>
                    <dt>MÊS REF.</dt>
                    <dd><?php echo getMesNome($infos['mes_ref']) . "/" . $infos['ano_ref']; ?></dd>                    
                    <dt>VALOR</dt>
                    <dd><?php echo tratarValor($infos['valor'], true); ?></dd>
                    <dt>CONSUMO</dt>
                    <dd><?php echo tratarValor($infos['consumo']); ?></dd>
                </dl>
            <?php
        
        }else{
            
            echo "Não foi possível encontrar nota com ID $notaid.";
            
        }
        
    }else{

    ?>        
            <h1>DELETAR</h1>
            Insira o ID da nota:
            <form method="post" >
                <input type="text" name="delid" id="inputNotaid" />
                <input type="button" name="go" id="go" value="Pesquisar" />
                <div id="show">
                </div>
                <input type="submit" name="Deletar" id="go" value="Deletar" />
            </form>
        </body>
    <?php 
    
    }
    
    ?>        
</html>
