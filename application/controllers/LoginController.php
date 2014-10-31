<?php
class LoginController extends Zend_Controller_Action
{
	public function loginAction()
	{
		//////////////////////////////////////////////////
		// CHARGEMENT DES PARAMÈTRES GÉNÉRAUX DE LA VUE //
		//////////////////////////////////////////////////

		// on charge les différents éléments qui vont constituer notre page

		$this->_helper->actionStack('header','index','default',array());
		$this->_helper->actionStack('footer','index','default',array());
		
		$connect = "<a href=" . $this->view->baseUrl('index/index') . " class=\"inscription texte menu_nav\">Retour à l'accueil</a>";
		$this->view->connexion = $connect;

		//////////////////////
		// AUTHENTIFICATION //
		//////////////////////

		// recupération des informations pour la connexion
		$this->view->form = $loginForm = new loginForm; // on passe le formulaire à la vue
		$db = Zend_Registry::get('db');	// on instancie le schéma
		$auth = Zend_Auth::getInstance();	// on instancie Zend_Auth pour la connexion
		$dbAdapter = new Zend_Auth_Adapter_DbTable($db,'users','mail','password','?');	// on parametre l'adapteur pour la connxeion

		// on intercepte la requete POST envoyée
		if($post = $this->_request->isPost()){

			// on récupère et stoke celle-ci
			$loginFormData = $this->getRequest()->getPost();

			// on vérifie que le champ 'login' n'est pas vide pour pallier aux éventuelles erreurs
			if ($loginFormData['login'] == '') {
				$loginForm->populate($loginFormData);
				$loginForm->getElement('login')->addError('Erreur : aucune adresse mail renseignée');
			} else {

				// récupération des valeurs entrées par l'utilisateur
				$login =  $loginFormData['login'];
				$pass =  md5($loginFormData['pass']);

				$dbAdapter->setIdentity($login);
				$dbAdapter->setCredential($pass);
				
				// on tente de se connecter
				$result = $auth->authenticate($dbAdapter);

				if ($result->isValid()) {
					
					// on vérifie si l'utilisateur a activé son compte
					$TUsers = new TUsers;
					$actif = $TUsers->verifActif($login);

					if ($actif == 0) {
						$loginForm->populate($loginFormData);
						$loginForm->getElement('pass')->addError('Erreur : votre compte n\'est pas activé');
					} else {
						// on récupère toutes les occurences de la table de l'utilisateur connecté pour les stocker en session
						$data = $dbAdapter->getresultRowObject(NULL,'password'); // excepté le password
						$auth->getStorage()->write($data);

						// enfin, on procède à la redirection
						$url = substr($_SERVER['HTTP_REFERER'],34);

						// echo $url;
						if ($url == 'rbonini/login/login') {
							$retourIndex = substr($this->view->baseUrl('index/index'),9);
							try {
							$this->_redirect($retourIndex);
								
							} catch (Exception $e) {
								echo 'Exception : ' . get_class($e) . '<br />';
					echo 'Message : ' . $e->getMessage() . '<br />';
							}
						} else {
							$this->_redirect($_SERVER['HTTP_REFERER']);
						}						
					}					
					
				} else {

					$loginForm->populate($loginFormData);
					$loginForm->getElement('pass')->addError('Erreur : login ou mot de passe incorrect');
				}
			}
		}
		
	}

	public function logoutAction()
	{
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
		$this->_redirect('index/index');

	}

	// fonction appellé a la fin du constructeur qui permet de charger des variable a l'initialisation
	public function init()
	{
		
	}
}