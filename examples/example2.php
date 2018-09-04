<?php

/**
 * you cannot execute this script within Eclipse PHP
 * because of the limited output buffer. Try to run it
 * directly within a shell.
 */

namespace PHPSQLParser;
require_once dirname(__FILE__) . '/../vendor/autoload.php';

$sql = 'SELECT c.rowid, c.ref, c.datec as date_creation, c.tms as date_update, c.date_contrat, c.statut, c.ref_customer, c.ref_supplier, c.note_private, c.note_public, s.rowid as socid, s.nom as name, s.email, s.town, s.zip, s.fk_pays, s.client, s.code_client, typent.code as typent_code, state.code_departement as state_code, state.nom as state_name, MIN(IF(cd.statut=4,cd.date_fin_validite,null)) as lower_planned_end_date, SUM(IF(cd.statut=0,1,0)) as nb_initial, SUM(IF(cd.statut=4 AND (cd.date_fin_validite IS NULL OR cd.date_fin_validite >= \'2018-09-03 15:26:31\'),1,0)) as nb_running, SUM(IF(cd.statut=4 AND (cd.date_fin_validite IS NOT NULL AND cd.date_fin_validite < \'2018-09-03 15:26:31\'),1,0)) as nb_expired, SUM(IF(cd.statut=4 AND (cd.date_fin_validite IS NOT NULL AND cd.date_fin_validite < \'2018-09-03 15:26:31\'),1,0)) as nb_late, SUM(IF(cd.statut=5,1,0)) as nb_closed FROM llx_societe as s LEFT JOIN llx_c_country as country on (country.rowid = s.fk_pays) LEFT JOIN llx_c_typent as typent on (typent.id = s.fk_typent) LEFT JOIN llx_c_departements as state on (state.rowid = s.fk_departement), llx_contrat as c LEFT JOIN llx_contratdet as cd ON c.rowid = cd.fk_contrat WHERE c.fk_soc = s.rowid AND c.entity IN (1) GROUP BY c.rowid, c.ref, c.datec, c.tms, c.date_contrat, c.statut, c.ref_customer, c.ref_supplier, c.note_private, c.note_public, s.rowid, s.nom, s.email, s.town, s.zip, s.fk_pays, s.client, s.code_client, typent.code, state.code_departement, state.nom ORDER BY c.ref DESC';
echo $sql . "\n";
$start = microtime(true);
$parser = new PHPSQLParser($sql, true);
$stop = microtime(true);
//print_r($parser->parsed);
echo "parse time simplest query:" . ($stop - $start) . "\n";

$parsed = $parser->parsed;

unset($parsed['SELECT'][1],$parsed['SELECT'][6],$parsed['SELECT'][7]);

$builder = new PHPSQLCreator($parsed);
print_r($builder->created);
