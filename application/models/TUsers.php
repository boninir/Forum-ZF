<?php

class TUsers extends Zend_Db_Table_Abstract
{
	protected $_name = 'users'; // le nom de la table, s'il n'est pas spécifié, le $_name par defaut est le nom de la classe
	protected $_primary = 'id_user'; // la ou les colonnes faisant office de clé primaire

	// fonction qui ajoute un utilisateur en base de données
	public function addUser($data)
	{
		$userData = array(
				'id_user' => '',
				'pseudo' => $data['pseudo'],
				'password' => md5($data['password']),
				'nom' => $data['nom'],
				'prenom' => $data['prenom'],
				'age' => $data['age'],
				'telephone' => $data['telephone'],
				'ville' => $data['ville'],
				'mail' => $data['mail'],
				'token' => md5($data['mail'].$data['pseudo']),
				'moderateur' => 0,
				'actif' => 0, // Doit valider son compte
				'date_inscription' 	=> date('Y-m-d H:i:s')
		);

		// on détruit les paramètres temporaires
		unset($data['recaptcha_challenge_field'],
			  $data['recaptcha_response_field'],
			  $data['password_confirm'],
			  $data['password']);

		$this->insert($userData);

		// on conserve le token pour l'envoi de la confirmation par mail
		return $userData['token'];
	}

	// fonction qui récupère un utilisateur en fonction de son id
	public function recupUser($id)
	{
		$requete = $this->select()
					  ->from('users')
					  ->where('id_user = ?', $id);

        $user = $this->fetchRow($requete);

        return $user;
	}

	// fonction d'update d'un post en base de données
	public function majUser($id_user, $data)
	{
		$userData = array(
				'pseudo' => $data['pseudo'],
				'password' => md5($data['password']),
				'nom' => $data['nom'],
				'prenom' => $data['prenom'],
				'age' => $data['age'],
				'telephone' => $data['telephone'],
				'ville' => $data['ville'],
				'mail' => $data['mail'],
				'token' => md5($data['mail'].$data['pseudo'])
		);

		// on détruit les paramètres temporaires
		unset($data['recaptcha_challenge_field'],
			  $data['recaptcha_response_field'],
			  $data['password_confirm'],
			  $data['password']);

		$condition = $this->getAdapter()
						  ->quoteInto('id_user = ?',$id_user);

		$this->update($userData, $condition);
	}

	// fonction qui récupère la liste des utilisateurs
	public function recupListeUsers()
	{
		$requete = $this->select()->from('users');

        $listUsers = $this->fetchAll($requete);

        return $listUsers;
	}

	// fonction de validation d'un compte utilisateur
	public function validationCompte($token)
	{
		$majActif = array('actif' => 1);

		$condition = $this->getAdapter()
						  ->quoteInto('token = ?',$token);
		
		$this->update($majActif, $condition);
	}

	// fonction qui récupère le statut d'un utilisateur (actif)
	public function verifActif($mail)
	{
		$requete = $this->select()
					  ->from('users','actif')
					  ->where('mail = ?', $mail);
					  // echo $requete;

        $actif = $this->fetchRow($requete);

        return $actif['actif'];
	}

}

?>