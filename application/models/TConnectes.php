<?php

class TConnectes extends Zend_Db_Table_Abstract
{
	protected $_name = 'connectes'; // le nom de la table, s'il n'est pas spécifié, le $_name par defaut est le nom de la classe
	protected $_primary = 'ip'; // la ou les colonnes faisant office de clé primaire	

	// fonction qui verifie si l'ip du connecté est deja presente en base
	public function verifIp()
	{
		$requete = $this->select()
					  ->from($this->_name)
					  ->where('ip = ?', $_SERVER['REMOTE_ADDR']);

        $connectes = $this->fetchAll($requete);

        return count($connectes);
	}

	 // fonction d'ajout d'une ip en base de données
	public function addIp()
	{
		$ipData = array(
				'ip' => $_SERVER['REMOTE_ADDR'],
				'timestamp' => time()
		);

		$this->insert($ipData);
	}

	// fonction d'update d'une connexion en base de données
	public function majIp()
	{
		$ipData = array('timestamp' => time());

		$condition = $this->getAdapter()
						  ->quoteInto('ip = ?',$_SERVER['REMOTE_ADDR']);

		$this->update($ipData, $condition);
	}

	// fonction de suppression des timestamps inférieur a 15 sec
	public function delIp($timestamp)
	{
		$requete = $this->select()
				  ->from($this->_name, array('timestamp'))
				  ->where('timestamp < ?', $timestamp);
				  // echo $requete;

		$this->delete($requete);
	}

	// fonction qui recupère le nombre de connecté
	public function recupIp()
	{
		$connectes = $this->fetchAll();

        return $connectes;
	}

}

?>