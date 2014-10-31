<?php
class IndexController extends Zend_Controller_Action
{
	public function indexAction()
	{

		//////////////////////////////////////////////////
		// CHARGEMENT DES PARAMÈTRES GÉNÉRAUX DE LA VUE //
		//////////////////////////////////////////////////

		// on charge les différents éléments qui vont constituer notre page

		$this->_helper->actionStack('header','index','default',array());
		$this->_helper->actionStack('footer','index','default',array());
		// $this->_helper->actionStack('connecte','index','default',array());

		// on récupère et parse l'url afin de récupérer uniquement la partie voulue
		$url = substr(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(),9);

		// on place l'url en session afin de rediriger correctement l'utilisateur s'il s'inscrit
		$sessionProvenance = new Zend_Session_Namespace();
		$sessionProvenance->provenance = $url;
		
		// on charge les models necessaires aux différentes actions de la vue
		$TTheme = new TTheme;

		// on charge et passe à la vue les formulaires et les instances de table
		$this->view->loginForm = $loginForm = new loginForm;
		$this->view->themeForm = $themeForm = new themeForm;
		$this->view->nbthemes = $nbthemes = $TTheme->nbThemes();
		// $this->view->themes = $themes = $TTheme->recupListeThemes();

		////////////////////////////////////////
		// AFFICHAGE DU BANDEAU DE NAVIGATION //
		////////////////////////////////////////

		$auth = Zend_Auth::getInstance();

		// on vérifie si l'utilisateur en cours est authentifié, si c'est le cas, on modifie la barre d'options
		if ($auth->hasIdentity()) {
			// on récupère les logs
			$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();

			// on récupère l'id de l'utilisateur en cours dans le cas ou il souhaiterait ajouter une discution
			$id_user = $identity->id_user;

			// on affiche les options particulières si le connecté est un modérateur
			if ($identity->moderateur === '1') {
				$listeUsers = "<a href=" . $this->view->baseUrl('index/listusers') . " class=\"inscription liste_users texte menu_nav\">Liste des utilisateurs</a>";
				$ajout_theme = "<div class=\"texte menu_nav left ajout_discution\" onclick=\"document.getElementById('conteneur_disussion_connexion').style.display='block';document.getElementById('overlayer').style.display='block';\">Ajouter un thème</div>";

				$this->view->ajout_theme = $ajout_theme;
				$this->view->listeUsers = $listeUsers;
			}

			// l'utilisateur est authentifié, on affiche donc son pseudo et la possibilité de se déconnecter
			$pseudo = "<div class=\"inscription texte menu_nav\" onclick=\"document.getElementById('conteneur_controle').style.display='block';\">" . $identity->pseudo ;
			$pseudo .= "<span class='fleche right'></span></div>";
			$edit_profil = "<a href=" . $this->view->baseUrl('inscription/edition') . " class=\"inscription texte menu_nav\">&Eacute;diter votre profil</a>";
			$disconnect = "<a href=" . $this->view->baseUrl('login/logout') ." class=\"deconnexion texte menu_nav\">D&eacute;connexion</a>";

			$this->view->connexion = $pseudo;
			$this->view->edit_profil = $edit_profil;
			$this->view->deconnexion = $disconnect;
		} else {

			// l'utilisateur n'est pas authentifié, il peut donc se connecter ou se créer un compte
			$connect = "<a href=" . $this->view->baseUrl('inscription/inscription') . " class=\"inscription texte menu_nav\">Cr&eacute;er un compte</a>";
			$connect .= "<div class=\"texte menu_nav right\" onclick=\"document.getElementById('conteneur_login_connexion').style.display='block';document.getElementById('overlayer').style.display='block';\">Connexion</div>";

			$this->view->connexion = $connect;	
		}

		//////////////////////
		// AJOUT D'UN THEME //
		//////////////////////

		// on définit les différents paramètres necessaire à la création d'un nouveau theme

		try {
			if($post = $this->_request->isPost()){

			$themeFormData = $this->getRequest()->getPost();

			if($themeForm->isValid($themeFormData)){

				$nom = $themeFormData['nom'];

				$db = Zend_Registry::get('db');
				$db->beginTransaction();

				$tableTheme = new TTheme();
				$tableTheme->addTheme($nom, $id_user);

				$db->commit();

				$this->_redirect($_SERVER['HTTP_REFERER']);
			}
		}
		}catch (Zend_Exception $e)
				{
					echo 'Exception : ' . get_class($e) . '<br />';
					echo 'Message : ' . $e->getMessage() . '<br />';
				}	
		

		///////////////////////////
		// PAGINATION DES THEMES //
		///////////////////////////

		// on récupère les données en base en passant par les models

		$page = $this->_getParam('page',1);

		// on paramètre la pagination et on la passe a la vue
		$this->view->pagination = $pagination = Zend_Paginator::factory($nbthemes);

		$pagination->SetItemCountPerPage(5)
				   ->SetCurrentPageNumber($page);
	}

	public function themeAction()
	{

		//////////////////////////////////////////////////
		// CHARGEMENT DES PARAMÈTRES GÉNÉRAUX DE LA VUE //
		//////////////////////////////////////////////////

		// on charge les différents éléments qui vont constituer notre page
		$this->_helper->actionStack('header','index','default',array());
		$this->_helper->actionStack('footer','index','default',array());

		// on récupère et parse l'url afin de récupérer uniquement la partie voulue
		$url = substr(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(),9);

		// on place l'url en session afin de rediriger correctement l'utilisateur s'il s'inscrit
		$sessionProvenance = new Zend_Session_Namespace();
		$sessionProvenance->provenance = $url;

		// on charge les models necessaires aux différentes actions de la vue
		$TTheme = new TTheme;
		$TDiscution = new TDiscution;
		$TUsers = new TUsers;

		// on charge et passe à la vue les formulaires et les instances de table
		$this->view->loginForm = $loginForm = new loginForm;
		$this->view->discussionForm = $discussionForm = new discussionForm;
		
		$this->view->theme = $theme = $TTheme->recupTheme($this->getParam('id'));		
		$this->view->discutions = $discutions = $TDiscution->recupDiscutions($this->getParam('id'));
		$this->view->nbDiscussions = count($discutions);

		////////////////////////////////////////
		// AFFICHAGE DU BANDEAU DE NAVIGATION //
		////////////////////////////////////////

		$auth = Zend_Auth::getInstance();

		// on vérifie si l'utilisateur en cours est authentifié, si c'est le cas, on modifie la barre d'options
		if ($auth->hasIdentity()) {

			// on récupère les logs
			$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();
			
			// on récupère l'id de l'utilisateur en cours dans le cas ou il souhaiterait ajouter une discution
			$id_user = $identity->id_user;

			// l'utilisateur est authentifié, on affiche donc son pseudo et la possibilité de se déconnecter
			$pseudo = "<a href=" . $this->view->baseUrl('inscription/edition') . " class=\"inscription texte menu_nav\">" . $identity->pseudo . "</a>";
			$ajout_discussion = "<div class=\"texte menu_nav left ajout_discution\" onclick=\"document.getElementById('conteneur_disussion_connexion').style.display='block';document.getElementById('overlayer').style.display='block';\">Ajouter une discussion</div>";
			$disconnect = "<a href=" . $this->view->baseUrl('login/logout') ." class=\"deconnexion texte menu_nav\">D&eacute;connexion</a>";

			$this->view->connexion = $pseudo;
			$this->view->ajout_discussion = $ajout_discussion;
			$this->view->deconnexion = $disconnect;
		} else {

			// l'utilisateur n'est pas authentifié, il peut donc se connecter ou se créer un compte
			$connect = "<a href=" . $this->view->baseUrl('inscription/inscription') . " class=\"inscription texte menu_nav\">Cr&eacute;er un compte</a>";
			$connect .= "<div class=\"texte menu_nav right\" onclick=\"document.getElementById('conteneur_login_connexion').style.display='block';document.getElementById('overlayer').style.display='block';\">Connexion</div>";

			$this->view->connexion = $connect;	
		}

		////////////////////////////
		// AJOUT D'UNE DISCUSSION //
		////////////////////////////

		// on définit les différents paramètres necessaire à la création d'une nouvelle discution
		
		$id_theme = (int)$this->_request->getParam("id");

		if($post = $this->_request->isPost()){

			$discussionFormData = $this->getRequest()->getPost();

			if($discussionForm->isValid($discussionFormData)){

				$libelle = $discussionFormData['libelle'];

				$db = Zend_Registry::get('db');
				$db->beginTransaction();

				$tableDiscution = new TDiscution();
				$tableDiscution->addDiscution($libelle, $id_theme, $id_user);
			// echo 'coucou';exit();

				$db->commit();

				$this->_redirect($_SERVER['HTTP_REFERER']);
			}
		}

		///////////////////////////
		// PAGINATION DES THEMES //
		///////////////////////////

		// on récupère le nombre d'éléments à paginer

		$page = $this->_getParam('page',1);

		// on paramètre la pagination et on la passe a la vue
		$this->view->pagination = $pagination = Zend_Paginator::factory($discutions);

		$pagination->SetItemCountPerPage(10)
				   ->SetCurrentPageNumber($page);
	}

	public function discutionAction()
	{
		// on charge les différents éléments qui vont constituer notre page
		$this->_helper->actionStack('header','index','default',array());
		$this->_helper->actionStack('footer','index','default',array());

		// on récupère et parse l'url afin de récupérer uniquement la partie voulue
		$url = substr(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(),9);

		// on place l'url en session afin de rediriger correctement l'utilisateur s'il s'inscrit
		$sessionProvenance = new Zend_Session_Namespace();
		$sessionProvenance->provenance = $url;

		// on charge les models necessaires aux différentes actions de la vue
		$TDiscution = new TDiscution;
		$TPosts = new TPosts;

		// on charge et passe à la vue les formulaires et les instances de table
		$this->view->loginForm = $loginForm = new loginForm;
		$this->view->postForm = $postForm = new postForm;
		$this->view->delPostForm = $delPostForm = new delPostForm;

		$this->view->discution = $TDiscution->recupDiscution($this->getParam('id'));
		$this->view->posts = $posts = $TPosts->recupListePosts($this->getParam('id'));

		////////////////////////////////////////
		// AFFICHAGE DU BANDEAU DE NAVIGATION //
		////////////////////////////////////////

		$auth = Zend_Auth::getInstance();

		// on vérifie si l'utilisateur en cours est authentifié, si c'est le cas, on modifie la barre de navigation
		if ($auth->hasIdentity()) {

			// on récupère les logs
			$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();

			// on récupère l'id de l'utilisateur en cours dans le cas ou il souhaiterait ajouter un post
			$id_user = $identity->id_user;
			$this->view->moderateur = $identity->moderateur;

			// l'utilisateur est authentifié, on affiche donc son pseudo et la possibilité de se déconnecter
			$pseudo = "<div class=\"inscription texte menu_nav\">" . $identity->pseudo . "</div>";
			$edition_profil = "<a href=" . $this->view->baseUrl('inscription/edition') . " class=\"inscription texte menu_nav\">&Eacute;diter votre profil</a>";
			$disconnect = "<a href=" . $this->view->baseUrl('login/logout') ." class=\"deconnexion texte menu_nav\">D&eacute;connexion</a>";

			$this->view->connexion = $pseudo;
			$this->view->deconnexion = $disconnect;
		} else {

			// l'utilisateur n'est pas authentifié, il peut donc se connecter ou se créer un compte
			$connect = "<a href=" . $this->view->baseUrl('inscription/inscription') . " class=\"inscription texte menu_nav\">Cr&eacute;er un compte</a>";
			$connect .= "<div class=\"texte menu_nav right\" onclick=\"document.getElementById('conteneur_login_connexion').style.display='block';document.getElementById('overlayer').style.display='block';\">Connexion</div>";

			$this->view->connexion = $connect;	
		}


		////////////////////////////////////////////
		// AJOUT / SUPPRESSION /EDITION D'UN POST //
		////////////////////////////////////////////

		if($post = $this->_request->isPost()){

			///////////////////////////////
			// AJOUT / EDITION D'UN POST //
			///////////////////////////////

			// on récupère les résultat de la requete passée en post
			$postFormData = $this->getRequest()->getPost();

			if($postForm->isValid($postFormData) && $postFormData['provenance'] == 'frm_ajout'){

				// si les informations ne sont pas éronées, on récupère les informations et effectue l'action désirée
				$id_post = $postFormData['id_post'];
				$message = strip_tags($postFormData['message']);

				$id_discution = (int)$this->_request->getParam("id");

				$theme = $TDiscution->recupDiscution($id_discution);
				$id_theme = $theme['theme_id'];

				$db = Zend_Registry::get('db');
				$db->beginTransaction();

				$tablePost = new TPosts();

				// on appelle le script adequat à l'action voulue
				if ($id_post != '') {
					$tablePost->majPost($id_post, $message);
				} else {
					$tablePost->addPost($message, $id_theme, $id_discution, $id_user);
				}

				$db->commit();

				// on procède à la redirection
				$this->_redirect($_SERVER['HTTP_REFERER']);
			} else
			{
				///////////////////////////
				// SUPPRESSION D'UN POST //
				///////////////////////////
				
				if($postForm->isValid($postFormData) && $postFormData['provenance'] == 'frm_del_ajout'){

					// si les informations ne sont pas éronées, on récupère les informations et effectue l'action désirée
					$id_post = $postFormData['id_post_del'];

					$db = Zend_Registry::get('db');
					$db->beginTransaction();

					$tablePost = new TPosts();
					
					$tablePost->delPost($id_post);

					$db->commit();

					// on procède à la redirection
					$this->_redirect($_SERVER['HTTP_REFERER']);
				}
			}
		}

		///////////////////////////
		// PAGINATION DES POSTS //
		///////////////////////////

		// on récupère les données en base en passant par les models

		$this->view->nbPosts = $nbPosts = count($posts);

		$page = $this->_getParam('page',1);

		// on paramètre la pagination et on la passe a la vue
		$this->view->pagination = $pagination = Zend_Paginator::factory($nbPosts);

		$pagination->SetItemCountPerPage(10)
				   ->SetCurrentPageNumber($page);
	}

	public function listusersAction()
	{
		try {

		}catch (Zend_Exception $e)
		{
			echo 'Exception : ' . get_class($e) . '<br />';
			echo 'Message : ' . $e->getMessage() . '<br />';
		}			
		
		//////////////////////////////////////////////////
		// CHARGEMENT DES PARAMÈTRES GÉNÉRAUX DE LA VUE //
		//////////////////////////////////////////////////

		// on charge les différents éléments qui vont constituer notre page
		$this->_helper->actionStack('header','index','default',array());
		$this->_helper->actionStack('footer','index','default',array());

		// on charge les models necessaires aux différentes actions de la vue
		$TUsers = new TUsers;

		// on charge et passe à la vue les formulaires et les instances de table

		$this->view->users = $users = $TUsers->recupListeUsers();
		$this->view->nbUsers = count($users);

		////////////////////////////////////////
		// AFFICHAGE DU BANDEAU DE NAVIGATION //
		////////////////////////////////////////

		$auth = Zend_Auth::getInstance();

		// on vérifie si l'utilisateur en cours est authentifié, si c'est le cas, on modifie la barre d'options
		if ($auth->hasIdentity()) {
			// on récupère les logs
			$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();

			// on récupère l'id de l'utilisateur en cours dans le cas ou il souhaiterait ajouter une discution
			$id_user = $identity->id_user;

			// on affiche les options particulières si le connecté est un modérateur
			if ($identity->moderateur === '1') {
				$listeUsers = "<a href=" . $this->view->baseUrl('index/listUsers') . " class=\"inscription liste_users texte menu_nav\">Liste des utilisateurs</a>";
				$ajout_theme = "<div class=\"texte menu_nav left ajout_discution\" onclick=\"document.getElementById('conteneur_disussion_connexion').style.display='block';document.getElementById('overlayer').style.display='block';\">Ajouter un thème</div>";

				$this->view->ajout_theme = $ajout_theme;
				$this->view->listeUsers = $listeUsers;
			}

			// l'utilisateur est authentifié, on affiche donc son pseudo et la possibilité de se déconnecter
			$pseudo = "<div class=\"inscription texte menu_nav\" onclick=\"document.getElementById('conteneur_controle').style.display='block';\">" . $identity->pseudo ;
			$pseudo .= "<span class='img_option right'></span></div>";
			$edit_profil = "<a href=" . $this->view->baseUrl('inscription/edition') . " class=\"inscription texte menu_nav\">&Eacute;diter votre profil</a>";
			$disconnect = "<a href=" . $this->view->baseUrl('login/logout') ." class=\"deconnexion texte menu_nav\">D&eacute;connexion</a>";

			$this->view->connexion = $pseudo;
			$this->view->edit_profil = $edit_profil;
			$this->view->deconnexion = $disconnect;
		} else {

			// l'utilisateur n'est pas authentifié, il peut donc se connecter ou se créer un compte
			$connect = "<a href=" . $this->view->baseUrl('inscription/inscription') . " class=\"inscription texte menu_nav\">Cr&eacute;er un compte</a>";
			$connect .= "<div class=\"texte menu_nav right\" onclick=\"document.getElementById('conteneur_login_connexion').style.display='block';document.getElementById('overlayer').style.display='block';\">Connexion</div>";

			$this->view->connexion = $connect;	
		}

		///////////////////////////
		// PAGINATION DES POSTS //
		///////////////////////////

		// on récupère les données en base en passant par les models

		$page = $this->_getParam('page',1);

		// on paramètre la pagination et on la passe a la vue
		$this->view->pagination = $pagination = Zend_Paginator::factory($users);

		$pagination->SetItemCountPerPage(15)
				   ->SetCurrentPageNumber($page);
	}

	public function connecteAction()
	{
	
		$this->_helper->viewRenderer->setResponseSegment('connecte');	

		// Connexion à la base de données
		$db = Zend_Registry::get('db');
		$TConnectes = new TConnectes;

		// on vérifie si l'IP se trouve déjà dans la table.
		$this->view->nbre_entrees = $nbre_entrees = $TConnectes->verifIp();

		if ($nbre_entrees == 0) {
			$TConnectes->addIp();
		} else {
			$TConnectes->majIp();
		}

		// on supprime toutes les entrées dont le timestamp est plus vieux que 15 sec.

		$timestamp = time() - 15;
		try {
		// $TConnectes->delIp($timestamp);

		$this->view->nbConnect = $nbConnect = $TConnectes->recupIp();

		// var_dump($nbConnect);

		}catch (Zend_Exception $e)
	{
		echo 'Exception : ' . get_class($e) . '<br />';
		echo 'Message : ' . $e->getMessage() . '<br />';
	}	
	}

	public function headerAction()
	{
		$this->_helper->viewRenderer->setResponseSegment('header');	
	}

	public function footerAction()
	{
		$this->_helper->viewRenderer->setResponseSegment('footer');		
	}

	// fonction appellé a la fin du constructeur qui permet de charger des variable à l'initialisation des vues
	public function init()
	{
		// on passe les instances de table aux vues
		$this->view->TTheme = new TTheme;
		$this->view->TDiscution = new TDiscution;
		$this->view->TPosts = new TPosts;
		$this->view->TUsers = new TUsers;

		$this->view->auth = $auth = Zend_Auth::getInstance();
		$this->identity = $auth->getIdentity();
	}
}