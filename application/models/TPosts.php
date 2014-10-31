<?php

class TPosts extends Zend_Db_Table_Abstract
{
	protected $_name = 'posts'; // le nom de la table, s'il n'est pas spécifié, le $_name par defaut est le nom de la classe
	protected $_primary = 'id_post'; // la ou les colonnes faisant office de clé primaire

	protected $_referenceMap = array(
		'theme' => array(
			'columns' => 'theme_id',
			'refTableClass' => 'TTheme'
		),
		'discution' => array(
			'columns' => 'discution_id',
			'refTableClass' => 'TDiscution'
		),
		'users' => array(
			'columns' => 'user_id',
			'refTableClass' => 'TUsers'
		)
	);

	// fonction d'ajout d'un post en base de données
	public function addPost($message, $id_theme, $id_discution, $id_user)
	{
		$postData = array(
				'id_post' => '',
				'message' => substr($message,0,5000),
				'date_creation' => date('Y-m-d H:i:s'),
				'theme_id' => $id_theme,
				'discution_id' => $id_discution,
				'user_id' => $id_user
		);

		$this->insert($postData);
	}

	// fonction d'update d'un post en base de données
	public function majPost($id_post, $message)
	{

		$postData = array('message' => substr($message,0,5000));

		$condition = $this->getAdapter()
						  ->quoteInto('id_post = ?',$id_post);

		$this->update($postData, $condition);
	}

	// fonction de suppression d'un post
	public function delPost($id_post)
	{
		$post= $this->find($id_post)->current();

		$post->delete();
	}

	// fonction qui récupère le dernier post enregistré d'un theme
	public function dateDernierPostsParTheme($theme_id)
	{
		$db = Zend_Registry::get('db');

		$requete = $db->select()
					  ->from('posts',array('max(date_creation) as derniere_date', 'theme_id'))
					  ->where('theme_id = ?', $theme_id);

        $resultat = $db->fetchRow($requete);

		return $resultat['derniere_date'];
	}

	// fonction qui récupère la liste des posts d'une discution
	public function recupListePosts($id_discution)
	{
		$db = Zend_Registry::get('db');

		$requete = $db->select()
					  ->from('posts')
					  ->where('discution_id = ?', $id_discution);

        $listPosts = $db->fetchAll($requete);

        return $listPosts;
	}
}

?>