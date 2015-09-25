<?php
$dbname="ada";
$usuario="root";
$password="daeesti";

if(!($id = mysql_connect("localhost",$usuario,$password))) {
echo "<p align=\"center\"><big><strong>Não foi possível estabelecer uma conexão com o gerenciador MySQL. Favor Contactar o Administrador.</strong></big></p>";
exit;
}else{
mysql_set_charset('utf8',$id);
mysql_query("SET NAMES 'UTF8'");
mysql_query("set CHARACTER set 'utf-8'");

}
if(!($con=mysql_select_db($dbname,$id))) {
echo "<p align\"center\"><big><strong>Não foi possível estabelecer uma conexão com o Banco de Dados. Favor Contactar o Administrador.</strong></big></p>";
exit;
}
?>