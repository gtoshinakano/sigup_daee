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
    $tpl->addFile('CONTEUDO_1', '../../html_libs/painel_uc_home.html');
    $tpl->addFile('CONTEUDO_2', '../../html_libs/painel_uc_home_2.html');
    $relatorio = new Relatorio();
    $ucTipos = $relatorio->ucTipos;
    
    
    $getAno = (isset($_GET['ano']) && $_GET['ano']>=2012 && $_GET['ano']<=Date('Y')) ? $_GET['ano'] : Date('Y');
    //$_SESSION['uc'] = 619;
    
    //$tpl->UCRGI = "RGI 1203920-23 : PET - Vestiário 1 e 2";
    $tpl->ANO_ATUAL = $getAno;
    $tpl->ANO_ATUAL_MENOS   = $getAno - 1;
    $tpl->ANO_ATUAL_MAIS    = $getAno + 1;
    
    if(isset($_SESSION['uc'])){
                
        $ucid = (int) $_SESSION['uc'];
        
        $ultSql = "SELECT mes_ref, ano_ref FROM daee_notas WHERE uc= $ucid ORDER BY criado DESC LIMIT 1";
        $ultQuery = mysql_query($ultSql);
        $ult = mysql_fetch_array($ultQuery);
        $tpl->ULTIMANOTA = getMesNome($ult['mes_ref'], true) . "/" . $ult['ano_ref'];
        
        $tpl->GETANO = $getAno;
        $sql = "SELECT * FROM ( SELECT uo.unidade,uo.nome AS dirnome,uc.rgi,uc.compl,uc.tipo,n.mes_ref,n.ano_ref,SUM(n.valor) AS valor,SUM(n.consumo) AS consumo, uc.rua, e.nome, uc.meta ";
        $sql.= "FROM daee_udds uo,daee_uddc uc,daee_notas n, sys_empresas e ";
        $sql.= "WHERE uc.uo = uo.id AND n.uc=uc.id AND uc.empresa=e.id AND uc.id=$ucid AND n.ano_ref = $getAno ";
        $sql.= "GROUP BY n.mes_ref,uc.id ORDER BY valor DESC,uo.unidade,n.mes_ref ASC ) AS res ";
        $sql.= "GROUP BY res.rgi,res.mes_ref ";
        $query = mysql_query($sql);

        if (mysql_num_rows($query) > 0) {

            $info;
            $info['chartval'] = "";
            $info['chartcon'] = "";
            $consAtu          = 0;
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
                $info['meta'] = $linha['meta'];
                
                $variacao = ($consAtu == 0) ? "" : ($linha['consumo'] * 100 / $consAtu) - 100 ;
                $consAtu = $linha['consumo'];
                $info['chartval'].= ",['" . getMesNome($linha['mes_ref']) . "'," . $linha['valor'] . ", @@MEDIAVAL@@]";
                $info['chartcon'].= ",['" . getMesNome($linha['mes_ref']) . "'," . $linha['meta'] . ", @@MEDIACONS@@, " . $linha['consumo'] . ", '". getPorcentagem($variacao) ."%']";
                
            }

            $tpl->MEDIAVAL = tratarValor($relatorio->average($info['valor']), true);
            $tpl->MEDIACONS = tratarValor($relatorio->average($info['consumo']));
            $info['chartval'] = str_replace('@@MEDIAVAL@@', $relatorio->average($info['valor']), $info['chartval']);
            $tpl->CHARTVAL = $info['chartval'];
            $info['chartcon'] = str_replace('@@MEDIACONS@@', $relatorio->average($info['consumo']), $info['chartcon']);
            $tpl->CHARTCON = substr_replace($info['chartcon'], "", 0,1);

            $tpl->TIPOMEDIDA = $relatorio->getTipoMedida($info['tipo']);
            $tpl->META = tratarValor($info['meta']);

            $tpl->block('TABLEROWVAL');
            if ($relatorio->temConsumo($info['tipo']))
                $tpl->block('TABLEROWCONS');
            $tpl->UCNOME = $info['nome'];


        }else {

            $tpl->block('NORESULTS');
            
        }
    }else{
        
        $tpl->block('NORESULTS');
        
    }
    
    $tpl->show();
       
?>