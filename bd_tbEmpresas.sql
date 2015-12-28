/*
SQLyog Ultimate v9.02 
MySQL - 5.6.15-log : Database - ada
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`ada` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `ada`;

/*Table structure for table `sys_empresas` */

DROP TABLE IF EXISTS `sys_empresas`;

CREATE TABLE `sys_empresas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cnpj` varchar(20) NOT NULL,
  `nome` varchar(80) NOT NULL,
  `razao_social` varchar(350) NOT NULL,
  `contato` varchar(250) DEFAULT NULL,
  `servico` varchar(250) DEFAULT NULL,
  `representante` varchar(150) DEFAULT NULL,
  `criado` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`cnpj`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

/*Data for the table `sys_empresas` */

insert  into `sys_empresas`(`id`,`cnpj`,`nome`,`razao_social`,`contato`,`servico`,`representante`,`criado`) values (1,'43776517000180','SABESP','CIA SANEAMENTO BASICO EST S PAULO-SABESP','08000550195','Companhia de Saneamento Básico do Estado de São Paulo','','2013-08-07 14:26:33'),(2,'02302100000106','BANDEIRANTE','BANDEIRANTE ENERGIA S/A','','BANDEIRANTE ENERGIA S.A. - Distribuidora de Energia Elétrica','','2013-08-26 10:45:22'),(3,'02328280000197','ELEKTRO','ELEKTRO ELETRICIDADE E SERVICOS S/A','','ELEKTRO - ELETRICIDADE E SERVIÇOS S.A. - Distribuidora de Energia Elétrica','','2013-08-30 16:03:52'),(4,'61695227000193','ELETROPAULO','ELETROPAULO METROPOL ELETRIC DE S P S/A','','ELETROPAULO METROPOLITANA -ELETRECIDADE SÃO PAULO S/A - Distribuidora de Energia Elétrica','','2013-08-30 16:04:15'),(5,'33050196000188','CPFL S.J.R.P.','COMPANHIA PAULISTA DE FORCA E LUZ','08000101010','Companhia Paulista de Força e Luz','','2013-09-10 09:20:08'),(6,'04691691000178','SEMAE','SERVIÇO MUNICIPAL AUTÔNOMO DE ÁGUA E ESGOTO','08007706666','Serviço Municipal Autônomo de Água e Esgoto','','2013-09-10 09:26:22'),(7,'00000000000000','SERVIÇOS DAS PREFEITURAS','',NULL,'Serviços de fornecimento de Água',NULL,NULL),(8,'02449992000164','VIVO S.A.','VIVO S.A.','','Empresa de Telecomunicações','','2013-09-17 13:50:22'),(9,'00000000000000','CPFL','COMPANHIA PAULISTA DE FORCA E LUZ',NULL,'Companhia Paulista de Força e Luz',NULL,'2013-09-23 00:00:00'),(10,'00000000000000','DAAE RIO CLARO','',NULL,'Departamento Autônomo de Água e Esgoto de Rio Claro',NULL,'2013-09-10 00:00:00'),(11,'44239770000167','DEPARTAMENTO AUTôNOMO DE ÁGUA E ESGOTOS DE ARARAQUARA','DEPARTAMENTO AUTONOMO DE AGUA E ESGOTO','08007701595','Serviço de Distribuição e Tratamento de Água','','2014-05-21 10:22:07'),(12,'52061181000160','DEPARTAMENTO DE ÁGUA E ESGOTO DE MARíLIA','DEPARTAMENTO DE AGUA E ESGOTO DE MARILIA','','Serviço de Distribuição e Tratamento de Água','','2014-05-21 10:26:28'),(13,'50062751000100','SERVIçO AUTôNOMO DE ÁGUA E ESGOTO DE CAPIVARI','SERVIÇO AUTONOMO DE AGUA E ESGOTO DE CAPIVARI','019-3492-9800','Serviço de Distribuição e Tratamento de Água','','2014-05-21 10:28:02'),(14,'45289329000152','SERVIçO AUTôNOMO DE ÁGUA E ESGOTO DE BARRETOS','SERVIÇO AUTONOMO DE AGUA E ESGOTO DE BARRETOS','017-3321-5300','Serviço de Distribuição e Tratamento de Água','','2014-05-21 10:30:00'),(15,'46255196000166','SAMAE - SERVIçO AUTôNOMO MUNICIPAL DE ÁGUA E ESGOTO DE MOGI-GUAçU','SERV AUTONOMO MUNICIPAL DE AGUA E ESGOTO DE MOGI GUACU','019-3831-9888','Serviço de Distribuição e Tratamento de Água','','2014-05-21 10:34:58'),(16,'07282377000120','CAIUÁ DISTRIBUIÇÃO DE ENERGIA','CAIUÁ - DISTRIBUIÇÃO DE ENERGIA S.A.','','Serviço de Distribuição de Energia','','2014-05-21 11:33:45');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
