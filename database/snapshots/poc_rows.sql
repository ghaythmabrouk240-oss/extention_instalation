-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: gestion_installation
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES (1,'Biomedical Demo','biomedical@example.com','2026-06-10 07:37:36','$2y$12$TBY9361q7Xil1ZSoS2vnrO6UTfi8CxAXzjhAuUbzgPcFBshxSlcry','biomedical','TnvSbfShBo','2026-06-10 07:37:37','2026-06-10 07:37:37');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` (`id`, `nom`, `adresse`, `email`, `telephone`, `fax`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,'Clinique Demo Tunis','Avenue de la sante, Tunis','demo-clinic@example.com','+216 70 000 000',NULL,'2026-06-10 07:37:37','2026-06-10 07:37:37',NULL);
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `equipements`
--

LOCK TABLES `equipements` WRITE;
/*!40000 ALTER TABLE `equipements` DISABLE KEYS */;
INSERT INTO `equipements` (`id`, `code`, `numero_equipement`, `modele`, `marque`, `designation`, `numero_serie`, `modalite_id`, `client_id`, `software`, `date_installation`, `date_debut_garantie`, `plan_prev`, `garantie`, `created_at`, `updated_at`) VALUES (1,'EQ-CATH-00100','NE-CATH-001O9','Azurion 7','Philips','Systeme angiographie principal','SN-CATH-001',1,1,'R2.1','2026-06-10','2026-06-10',12,'24 mois','2026-06-10 07:37:37','2026-06-11 17:29:30'),(2,'EQ-INJ-0017test','NE-INJ-001','Mark 7','Medrad','Injecteur de contraste','SN-INJ-001',1,1,'1.0','2026-06-10','2026-06-10',12,'12 mois','2026-06-10 07:37:37','2026-06-11 18:08:46'),(6,'EQ-CATH-0010027','NE-CATH-001','Azurion 7','Philips','Systeme angiographie principal','SN-CATH-001',3,1,'R2.1','2026-07-03','2026-06-17',3,'12 mois','2026-06-11 18:08:23','2026-06-12 07:13:58'),(7,'EQ-CATH-0010023','NE-CATH-001','Azurion 7','airliquide','Systeme angiographie principal','SN-CATH-001',3,1,'R2.1','2026-07-03','2026-06-17',4,'12 mois','2026-06-17 07:06:53','2026-06-17 07:06:53');
/*!40000 ALTER TABLE `equipements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `installations`
--

LOCK TABLES `installations` WRITE;
/*!40000 ALTER TABLE `installations` DISABLE KEYS */;
INSERT INTO `installations` (`id`, `code_installation`, `nom`, `type_profil`, `statut`, `criticite`, `proprietaire_interne_id`, `client_id`, `equipement_principal_id`, `planned_start_date`, `planned_end_date`, `actual_start_date`, `actual_end_date`, `calendar_note`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,'INST-CATH-0017689','Salle Catheterisme 1','CATHETERISME','Brouillon','Haute',1,1,2,'2026-06-09','2026-06-11',NULL,NULL,'Reception salle, controle radioprotection et tests qualite.','2026-06-10 07:37:37','2026-06-12 08:48:29',NULL),(3,'TEST-001','Test Installation','IRM','Brouillon','Basse',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-12 08:21:57','2026-06-12 08:44:26',NULL),(4,'424234m','SALLE A','IRM','Installe','Moyenne',1,1,6,'2026-06-18','2026-07-01','2026-07-03','2026-07-10','JKH','2026-06-12 08:31:04','2026-06-12 08:31:04',NULL),(5,'424234mLK','SALLE A','CATHETERISME','En validation','Haute',1,1,6,'2026-06-18','2026-07-01','2026-07-03','2026-07-10',NULL,'2026-06-12 08:34:50','2026-06-12 08:34:50',NULL),(6,'424234testtest','SALLE A','IRM','Archive','Critique',1,1,6,'2026-06-18','2026-07-01','2026-07-03','2026-07-10',NULL,'2026-06-12 08:45:38','2026-06-12 08:46:41',NULL),(7,'424234ABNEW','SALLE A','CATHETERISME','Installe','Moyenne',1,1,6,'2026-06-18','2026-07-01','2026-07-03','2026-07-10',NULL,'2026-06-17 12:52:55','2026-06-17 12:52:55',NULL),(8,'75434','2A','IRM','Operationnel','Moyenne',1,1,2,'2026-06-18','2026-06-24','2026-06-10','2026-07-01',NULL,'2026-06-19 09:34:56','2026-06-19 09:34:56',NULL);
/*!40000 ALTER TABLE `installations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `profil_irms`
--

LOCK TABLES `profil_irms` WRITE;
/*!40000 ALTER TABLE `profil_irms` DISABLE KEYS */;
INSERT INTO `profil_irms` (`id`, `installation_id`, `champ_magnetique`, `zone_controlee`, `blindage`, `atelier`, `confinement_ferromagnetique`, `arret_urgence`, `batiment`, `etage`, `zone`, `created_at`, `updated_at`) VALUES (3,3,'test',0,'test',NULL,0,0,'test','test','test','2026-06-12 08:24:35','2026-06-12 08:24:35'),(4,4,'1.5T',1,'RF','JJK',0,0,'JHK','8','ZONE CONTROLEE','2026-06-12 08:31:04','2026-06-12 08:31:04'),(5,6,'1.5T',0,'RF','JJK',0,0,'JHK','8','ZONE CONTROLEE','2026-06-12 08:45:38','2026-06-12 08:45:38'),(6,8,'1.2',1,'RF conforme','N/A',1,1,'2NA','4','Zone controlée','2026-06-19 09:34:56','2026-06-19 09:34:56');
/*!40000 ALTER TABLE `profil_irms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `profil_cat_labs`
--

LOCK TABLES `profil_cat_labs` WRITE;
/*!40000 ALTER TABLE `profil_cat_labs` DISABLE KEYS */;
INSERT INTO `profil_cat_labs` (`id`, `installation_id`, `departement`, `batiment`, `etage`, `systeme_angiographie`, `station_controle`, `radioprotection`, `alimentation`, `reseau`, `ventilation`, `protection_murale`, `stockage_consommables`, `injecteur`, `moniteurs`, `controle_acces`, `signalisation_rayonnement`, `conformite_salle_interventionnelle`, `dispositifs_securite`, `table_patient`, `created_at`, `updated_at`, `angio_manufacturer`, `angio_model`, `angio_serial`, `radiation_shielding_status`, `lead_glass_status`, `ceiling_support_status`, `emergency_equipment_status`, `access_control_status`, `dose_monitoring_available`, `hvac_info`, `acceptance_test_status`, `installation_date`, `warranty_end_date`) VALUES (1,1,'AAA','AAA','34','Azurion 7 C20','AZERTI','Controle plombage conforme','4','6','9','FGH','87','Injecteur Medrad Mark 7','2 moniteurs salle + 1 console',1,'HK','KL','JH','Table flottante motorisee','2026-06-10 07:37:37','2026-06-11 17:32:53',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL),(2,5,'AAA','AAA','34','Azurion 7 C20','AZERTI','Controle plombage conforme','4','6','9','FGH','87','Injecteur Medrad Mark 7','2 moniteurs salle + 1 console',1,'HK','KL','JH','Table flottante motorisee','2026-06-12 08:34:50','2026-06-12 08:34:50',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL),(3,7,'AAA','AAA','34','Azurion 7 C20','AZERTI','Controle plombage conforme','4','6','9','FGH','87','Injecteur Medrad Mark 7','2 moniteurs salle + 1 console',1,'HK','KL','JH','Table flottante motorisee','2026-06-17 12:52:55','2026-06-17 12:52:55',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `profil_cat_labs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `sous_equipements`
--

LOCK TABLES `sous_equipements` WRITE;
/*!40000 ALTER TABLE `sous_equipements` DISABLE KEYS */;
INSERT INTO `sous_equipements` (`id`, `identifiant`, `designation`, `marque`, `modele`, `description`, `equipement_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,'SE-MON-001','Moniteur salle','Philips','FlexVision','Moniteur rattache au systeme principal',1,'2026-06-10 07:37:37','2026-06-10 07:37:37',NULL),(2,'AJKJ23','Systeme angiographie principal','Philips','Azurion 7','SJDJ',2,'2026-06-12 07:14:38','2026-06-12 07:14:38',NULL);
/*!40000 ALTER TABLE `sous_equipements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `lien_equipement_installation`
--

LOCK TABLES `lien_equipement_installation` WRITE;
/*!40000 ALTER TABLE `lien_equipement_installation` DISABLE KEYS */;
INSERT INTO `lien_equipement_installation` (`installation_id`, `equipement_id`, `role`, `created_at`, `updated_at`) VALUES (4,2,'secondaire','2026-06-12 08:31:04','2026-06-12 08:31:04'),(5,1,'secondaire','2026-06-12 08:34:50','2026-06-12 08:34:50'),(7,7,'secondaire','2026-06-17 12:52:55','2026-06-17 12:52:55');
/*!40000 ALTER TABLE `lien_equipement_installation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `document_installations`
--

LOCK TABLES `document_installations` WRITE;
/*!40000 ALTER TABLE `document_installations` DISABLE KEYS */;
INSERT INTO `document_installations` (`id`, `installation_id`, `categorie`, `type_rapport`, `version`, `statut`, `description`, `est_bloquant`, `reference_dms`, `reference_fichier`, `fichier_path`, `fichier_original_name`, `fichier_mime_type`, `profil_concerne`, `est_version_active`, `created_at`, `updated_at`) VALUES (1,1,'Rapport installation generale','installation_generale','1.9','Valide','Document demo pour PV installation',1,'DMS-CATH-PV-INSTALLATION',NULL,'installation-reports/nD6lHQA6mS6ve1qBkv2uV9xcUlWlFV0j6R5dqbte.pdf','projet1_Restoration_using_Image_Fi.pdf','application/pdf','IRM',0,'2026-06-10 07:37:37','2026-06-12 07:15:40'),(2,1,'Rapport installation generale','document_requis','1.8','Valide','Document demo pour Validation securite',0,'DMS-CATH-VALIDATION-SECURITE',NULL,'installation-reports/xfRm4bMoLXF2UjO8WqGUvHjmOTqFSM4WIWgm3VSN.png','raw.png','image/png','IRM',1,'2026-06-10 07:37:37','2026-06-12 07:15:40'),(3,1,'Radioprotection','document_requis','1.0','Valide','Document demo pour Radioprotection',1,'DMS-CATH-RADIOPROTECTION',NULL,NULL,NULL,NULL,'CATHETERISME',1,'2026-06-10 07:37:37','2026-06-10 07:37:37'),(4,1,'Plan salle CathLab','document_requis','1.0','Valide','Document demo pour Plan salle CathLab',0,'DMS-CATH-PLAN-SALLE-CATHLAB',NULL,NULL,NULL,NULL,'CATHETERISME',1,'2026-06-10 07:37:37','2026-06-10 07:37:37'),(5,1,'Rapport des tests','rapport_tests','1.0','Valide','Tests qualite, securite interventionnelle et validation technique de la salle.',1,'DMS-CATH-RAPPORT-TESTS',NULL,NULL,NULL,NULL,'CATHETERISME',1,'2026-06-10 07:37:37','2026-06-10 07:37:37'),(6,1,'Rapport installation generale',NULL,'1.9','Valide',NULL,0,NULL,NULL,'installation-reports/YmND88HRqdq8d09wMlFNnbWP8T9tz0rTAcHP1eih.pdf','Les Prothèses.pdf','application/pdf','COMMUN',0,'2026-06-11 15:49:14','2026-06-12 12:12:19'),(7,1,'Rapport installation generale',NULL,'1.0','Valide',NULL,0,NULL,NULL,'installation-reports/bM5gK4JXecdbZSoHh3zETcC2fuKv8vrQqWBUncpD.png','main_prothese-bionique-deus-ex-a-bras-raccourci-_060723.PNG','image/png','COMMUN',1,'2026-06-12 12:12:19','2026-06-12 12:12:19'),(8,6,'Rapport installation generale','installation_generale','1.0','Valide',NULL,0,NULL,NULL,'installation-reports/A2yl49pV8YuQF2nhrI5zAlZpc25kX0K4a5nzYjTj.png','raw.png','image/png','COMMUN',1,'2026-06-12 12:14:57','2026-06-12 12:14:57'),(9,6,'Rapport installation generale','rapport_tests','1.8','Valide',NULL,1,NULL,NULL,NULL,NULL,NULL,'COMMUN',0,'2026-06-12 12:18:50','2026-06-12 12:18:50'),(10,6,'Rapport des tests','rapport_tests','1.0','Valide',NULL,0,NULL,NULL,'installation-reports/DcEO9pEaM2xaNn1lnsplFnM2Dzl9mq9PD4eNvB2v.jpg','ampli-d-instrumentation-3-amplis-l.jpg','image/jpeg','IRM',1,'2026-06-12 12:31:40','2026-06-12 12:31:40'),(11,6,'Documents radioprotection','rapport_technique','1.7','Valide',NULL,1,NULL,NULL,'installation-reports/guglZMVYIN0r93NSJyhbY5hytSCNNwuzSGfMeaEd.pdf','Les Prothèses.pdf','application/pdf','IRM',1,'2026-06-12 12:46:07','2026-06-12 12:46:07'),(12,6,'Controle qualite',NULL,'1.0','Valide',NULL,0,NULL,NULL,NULL,NULL,NULL,'CATHETERISME',0,'2026-06-12 12:53:12','2026-06-12 12:54:01'),(13,6,'Controle qualite',NULL,'1.0','Valide',NULL,0,NULL,NULL,NULL,NULL,NULL,'CATHETERISME',1,'2026-06-12 12:54:01','2026-06-12 12:54:01'),(14,6,'Plan de prevention',NULL,'1.0','Valide',NULL,0,NULL,NULL,NULL,NULL,NULL,'IRM',0,'2026-06-12 13:05:24','2026-06-12 13:06:11'),(15,6,'Rapports techniques',NULL,'1.0','Valide',NULL,0,NULL,NULL,NULL,NULL,NULL,'IRM',1,'2026-06-12 13:05:47','2026-06-12 13:05:47'),(16,6,'Plan de prevention',NULL,'1.0','Valide',NULL,0,NULL,NULL,NULL,NULL,NULL,'IRM',1,'2026-06-12 13:06:11','2026-06-12 13:06:11'),(17,6,'Rapport de reception',NULL,'1.0','Valide',NULL,0,NULL,NULL,NULL,NULL,NULL,'IRM',1,'2026-06-12 13:06:38','2026-06-12 13:06:38');
/*!40000 ALTER TABLE `document_installations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `historique_statut_installations`
--

LOCK TABLES `historique_statut_installations` WRITE;
/*!40000 ALTER TABLE `historique_statut_installations` DISABLE KEYS */;
INSERT INTO `historique_statut_installations` (`id`, `installation_id`, `user_id`, `ancien_statut`, `nouveau_statut`, `commentaire`, `created_at`, `updated_at`) VALUES (1,4,1,'','Installe','Creation de l installation','2026-06-12 08:31:04','2026-06-12 08:31:04'),(2,5,1,'','En validation','Creation de l installation','2026-06-12 08:34:50','2026-06-12 08:34:50'),(3,6,1,'','Installe','Creation de l installation','2026-06-12 08:45:38','2026-06-12 08:45:38'),(4,6,1,'Installe','Archive','Changement de statut via modification','2026-06-12 08:46:41','2026-06-12 08:46:41'),(5,7,1,'','Installe','Creation de l installation','2026-06-17 12:52:55','2026-06-17 12:52:55'),(6,8,1,'','Operationnel','Creation de l installation','2026-06-19 09:34:56','2026-06-19 09:34:56');
/*!40000 ALTER TABLE `historique_statut_installations` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-19 11:39:56
