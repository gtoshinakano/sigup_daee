<?php

include("src/mpdf/mpdf.php");
$mpdf = new mPDF();
$stylesheet = file_get_contents('cssjs_libs/pdf_style.css');

$mpdf->SetHTMLHeader('<div class="cabecalho" >
                    <div class="brasao"><img src="images/relatorio_brasao.png" alt=""  ></div>
                    <div id="texto"> 
                        <span id="titulo">SECRETARIA DE  SANEAMENTO E RECURSOS HÍDRICOS</span><br/>
                        <span id="subtitulo">DEPARTAMENTO DE ÁGUAS E ENERGIA ELÉTRICA</span><br/>
                        <span id="endereco">Rua: Boa Vista,170/175 - telefone 011-3293-8200/8201 - CEP 01014-000 – Centro – São Paulo-SP</span><br/>
                        <span id="site"> www.daee.sp.gov.br</span>
                    </div>
                </div>');
$mpdf->WriteHTML($stylesheet, 1);
$mpdf->WriteHTML(' <div class="texto ">'
                    . '<p><strong>AUTOS: [Nº] - PROV. [Nº]</strong></p>'
        . '<p><strong>INTERESSADO: ADA</strong></p>
                        <br/>
                        <p><strong><u>INFORMAÇÃO ADA/[Nº]</u></strong></p>
                        <p class="paragrafo"><strong>Senhor Responsável pela ADA:</strong></p>
                        <p class=""><strong>Senhor Responsável pela ADA:</strong></p>
                        <div id="paragrafo"></div>
                        <p class="text-center">Assim sendo, proponho o envio da presente à DOF, para o devido pagamento</p>
                        <p class="text-center"><strong> ADA</strong>, [Nº].</p> '
                . '</div>');
$filename = "teste.pdf";
$mpdf->Output($filename, "I");
exit;
?>