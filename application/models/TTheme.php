<?php

class TTheme extends Zend_Db_Table_Abstract
{
	protected $_name = 'theme'; // le nom de la table, s'il n'est pas spécifié, le $_name par defaut est le nom de la classe
	protected $_primary = 'id_theme'; // la ou les colonnes faisant office de clé primaire

	// on définit les clés étrangères de la table
	protected $_referenceMap = array(
		'users' => array(
			'columns' => 'user_id',
			'refTableClass' => 'TUsers'
		)
	);

	// fonction d'ajout d'un theme en base de données
	public function addTheme($nom, $id_user)
	{
		$themeData = array(
				'id_theme' => '',
				'nom' => $nom,
				'user_id' => $id_user,
		);

		$this->insert($themeData);
	}

	// fonction qui récupère la liste des themes
	public function nbThemes()
	{
		$requete = $this->select()->from('theme');

        $listThemes = $this->fetchAll($requete);

        return $listThemes;
	}

	// fonction qui récupère la liste des themes
	public function recupListeThemes()
	{
		$requete = $this->select()->from($this->_name);

        $listThemes = $this->fetchAll($requete);

        return $listThemes;
	}

	// fonction qui récupère les informations d'un theme
	public function recupTheme($id_theme)
	{
		$requete = $this->select()
					  ->from($this->_name)
					  ->where('id_theme	 = ?', $id_theme);;

        $listThemes = $this->fetchRow($requete);

        return $listThemes;
	}
}

?>