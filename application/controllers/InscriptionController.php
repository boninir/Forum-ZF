<?php
class InscriptionController extends Zend_Controller_Action
{
	public function inscriptionAction()
	{
		//////////////////////////////////////////////////
		// CHARGEMENT DES PARAMÈTRES GÉNÉRAUX DE LA VUE //
		//////////////////////////////////////////////////

		// on charge les différents éléments qui vont constituer notre page

		$this->_helper->actionStack('header','index','default',array());
		$this->_helper->actionStack('footer','index','default',array());

		// on charge et passe à la vue les formulaires

		$this->view->loginForm = $loginForm = new loginForm;
		$this->view->form = $form = new inscriptionForm;

		// on initialise la session à utiliser pour la redirection
		$sessionProvenance = new Zend_Session_Namespace();

		/////////////////
		// INSCRIPTION //
		/////////////////

		// on procède a l'enregistrement de l'utilisateur
		if($post = $this->_request->isPost()){

			$formData = $this->getRequest()->getPost();

			if($form->isValid($formData)){

				$db = Zend_Registry::get('db');
				$db->beginTransaction();

				try{
					$tableUsers = new TUsers();
					$token = $tableUsers->addUser($formData);
					
					$db->commit();
					
					///////////////////
					// ENVOI DE MAIL //
					///////////////////

					$transport = new Zend_Mail_Transport_Smtp('mailx.u-picardie.fr');
					Zend_Mail::setDefaultTransport($transport);

					// on parametre le mail a envoyer
					$mail = new Zend_Mail();
					$mail->setFrom('no-reply@etud.u-picardie.fr', 'No Reply'); // expéditeur
					$mail->addTo($formData['mail'], $formData['nom']. ' '. $formData['prenom']); // destinataire
					$mail->setSubject('Social\'Insset - confirmation d\'inscription'); // sujet du mail

					// on défini le contenu du mail
					$contenu = 'Bonjour ' . $formData['pseudo'] .'<br /><br />';
					$contenu .= 'Pour activer votre compte, veuillez cliquer sur le lien ci dessous
								ou le copier/coller dans votre navigateur internet. <br /><br />';

					$contenu .= '<a href="next.insset.u-picardie.fr/' . $this->view->baseUrl('inscription/activation/token/' .$token) . '">
									next.insset.u-picardie.fr' . $this->view->baseUrl('inscription/activation/token/' .$token) . '</a>';

					$contenu .='<br /><br />---------------<br />
								Ceci est un mail automatique, Merci de ne pas y r&eacute;pondre.';

					$mail->setBodyHtml($contenu);

					// on envoi le mail généré
					try{
						$mail->send($transport);
					}catch(exception $e)
					{
						echo 'Exception : ' . get_class($e) . '<br />';
						echo 'Message : ' . $e->getMessage() . '<br />';
					}

					// l'inscription s'est bien déroulée, on redirige l'utilisateur vers la page précédement visitée
					$this->_redirect($sessionProvenance->provenance);

				}catch (Zend_Exception $e)
				{
					echo 'Exception : ' . get_class($e) . '<br />';
					echo 'Message : ' . $e->getMessage() . '<br />';
					$db->rollBack();
				}

			}else{
				$form->populate($formData);
			}

		}

		////////////////////////////////////////
		// AFFICHAGE DU BANDEAU DE NAVIGATION //
		////////////////////////////////////////

		$auth = Zend_Auth::getInstance();

		if ($auth->hasIdentity()) {
			// on passe le pseudo à la vue pour l'afficher
			$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();

			$pseudo = "<div class=\"inscription texte\">" . $identity->pseudo . "</div>";
			$disconnect = "<a href=" . $this->view->baseUrl('login/logout') ." class=\"deconnexion texte menu_nav\">D&eacute;connexion</a>";

			$this->view->connexion = $pseudo;
			$this->view->deconnexion = $disconnect;
		} else {

			$connect = "<a href=" . $this->view->baseUrl('index/index') . " class=\"inscription texte menu_nav\">Retour à l'accueil</a>";
			$connect .= "<div class=\"texte menu_nav right\" onclick=\"document.getElementById('conteneur_login_connexion').style.display='block';document.getElementById('overlayer').style.display='block';\">Connexion</div>";

			$this->view->connexion = $connect;
		}
		
	}

	public function activationAction()
	{

		//////////////////////////////////////////////////
		// CHARGEMENT DES PARAMÈTRES GÉNÉRAUX DE LA VUE //
		//////////////////////////////////////////////////

		// on charge les différents éléments qui vont constituer notre page

		$this->_helper->actionStack('header','index','default',array());
		$this->_helper->actionStack('footer','index','default',array());

		// on charge et passe à la vue les formulaires et les instances de table
		$this->view->loginForm = $loginForm = new loginForm;

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
			$connect = "<a href=" . $this->view->baseUrl('index/index') . " class=\"inscription texte menu_nav\">Retour à l'accueil</a>";
			$connect .= "<div class=\"texte menu_nav right\" onclick=\"document.getElementById('conteneur_login_connexion').style.display='block';document.getElementById('overlayer').style.display='block';\">Connexion</div>";

			$this->view->connexion = $connect;	
		}


		//////////////////////////
		// ACTIVATION DU COMPTE //
		//////////////////////////


		try{
			// on récupère le token du mail placé en get dans l'url
			$token = $this->getParam('token');
			
			if($token){
				$Tusers = new TUsers;
				$Tusers->validationCompte($token);
			}
		}catch(exception $e)
		{
			echo 'Exception : ' . get_class($e) . '<br />';
			echo 'Message : ' . $e->getMessage() . '<br />';
		}
		
	}

	public function editionAction()
	{
		$this->_helper->actionStack('header','index','default',array());
		$this->_helper->actionStack('footer','index','default',array());

		$auth = Zend_Auth::getInstance();
		$TUsers = new TUsers;

		// on initialise la session à utiliser pour la redirection
		$sessionProvenance = new Zend_Session_Namespace();

		// on passe le pseudo à la vue pour l'afficher
		$auth = Zend_Auth::getInstance();
		$this->view->identity = $identity = $auth->getIdentity();

		$user = $TUsers->recupUser($identity->id_user);
		$dataUser = $user->toArray();
		// on instancie le formulaire avec les valeurs du connecté et on le passe à la vue
		$editForm = new editionForm;
		$this->view->editForm = $editForm->populate($dataUser);


		$pseudo = "<div class=\"inscription texte\">" . $identity->pseudo . "</div>";
		$disconnect = "<a href=" . $this->view->baseUrl('login/logout') ." class=\"deconnexion texte menu_nav\">D&eacute;connexion</a>";

		$this->view->connexion = $pseudo;
		$this->view->deconnexion = $disconnect;


		/////////////
		// EDITION //
		/////////////

		// on procède a l'enregistrement de l'utilisateur
		if($post = $this->_request->isPost()){

			$formDataEdit = $this->getRequest()->getPost();

			if($editForm->isValid($formDataEdit)){

				$db = Zend_Registry::get('db');
				$db->beginTransaction();

				try{
					$tableUsers = new TUsers();
					$tableUsers->majUser($identity->id_user, $formDataEdit);
					
					$db->commit();

					$this->_redirect($sessionProvenance->provenance);

				}catch (Zend_Exception $e)
				{
					echo 'Exception : ' . get_class($e) . '<br />';
					echo 'Message : ' . $e->getMessage() . '<br />';
					$db->rollBack();
				}

			}else{
				$editForm->populate($formDataEdit);
			}

		}
		
	}

	// fonction appellé a la fin du constructeur qui permet de charger des variable a l'initialisation
	public function init()
	{
		// Ajout du helper d'action JQuery autoComplete
      	Zend_Controller_Action_HelperBroker::addHelper(
        new ZendX_JQuery_Controller_Action_Helper_AutoComplete());
	}
}