<?php

class TDiscution extends Zend_Db_Table_Abstract
{
	protected $_name = 'discution'; // le nom de la table, s'il n'est pas spécifié, le $_name par defaut est le nom de la classe
	protected $_primary = 'id_discution'; // la ou les colonnes faisant office de clé primaire

	protected $_referenceMap = array(
		'theme' => array(
			'columns' => 'theme_id',
			'refTableClass' => 'TTheme'
		),
		'users' => array(
			'columns' => 'user_id',
			'refTableClass' => 'TUsers'
		)
	);

	// fonction d'ajout d'une discution en base de données
	public function addDiscution($libelle, $id_theme, $id_user)
	{
		$discutionData = array(
				'id_discution' => '',
				'libelle' => $libelle,
				'date_creation' => date("Y-m-d H:i:s"),
				'theme_id' => $id_theme,
				'user_id' => $id_user
		);
		
		$this->insert($discutionData);
	}

	// fonction qui récupère la liste des dsicussions
	public function nbDiscutions()
	{
		$requete = $this->select()->from($this->_name);

        $listDiscutions = $this->fetchAll($requete);

        return $listDiscutions;
	}

	// fonction qui récupère le nombre de discution totale d'un theme
	public function nbDiscutionsParTheme($theme_id)
	{
		$requete = $this->select()
					  ->from($this->_name)
					  ->where('theme_id = ?', $theme_id);

        $resultat = $this->fetchAll($requete);

		return count($resultat);
	}

	// fonction qui récupère la date de la dernière discussion enregistrée d'un theme
	public function dateDerniereDiscusionParTheme($theme_id)
	{
		$derniereDate = $this->select()
					  ->from($this->_name,array('max(date_creation) as derniere_date', 'theme_id'))
					  ->where('theme_id = ?', $theme_id);

        $resultat = $this->fetchRow($derniereDate);

		return $resultat['derniere_date'];
	}

	// fonction qui récupère la liste des discutions d'un theme
	public function recupDiscutions($id_theme)
	{
		$requete = $this->select()
					  ->from($this->_name)
					  ->where('theme_id = ?', $id_theme)
					  ->order('date_creation desc');

        $discutions = $this->fetchAll($requete);

        return $discutions;
	}

	// fonction qui récupère les informations d'une discution en fonction de l'id
	public function recupDiscution($id_discution)
	{
		$requete = $this->select()
					  ->from($this->_name)
					  ->where('id_discution = ?', $id_discution);;

        $discution = $this->fetchRow($requete);

		return $discution;
	}
}

?>