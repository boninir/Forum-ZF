<?php

class TVille extends Zend_Db_Table_Abstract
{
	protected $_name = 'ville'; // le nom de la table, s'il n'est pas spécifié, le $_name par defaut est le nom de la classe
	protected $_primary = 'id_ville'; // la ou les colonnes faisant office de clé primaire

	// fonction qui récupère la liste des villes pour l'autocompletion
	public function recupListeVille()
	{
		$db = Zend_Registry::get('db');

		$requete = $db->select()->from('ville');

        $listVille= $db->fetchAll($requete);

        return $listVille;
	}
}

?>