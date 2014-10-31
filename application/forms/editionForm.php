<?php 

Class editionForm extends Zend_Form
{
	public function init()
	{
		// // parametrage du formulaire
		$this->setMethod('post');
		$url = new Zend_View_Helper_Url();
    	$this->setAction($url->url(array('controller'=> 'inscription','action'=>'edition'),'default'));

		$this->clearDecorators();

		$decoratorForm = array(
			array('UiWidgetElement', array('ViewScript', array('class' => 'RegElement'))),
			array('HtmlTag'),
			array(
				array('DivTag' => 'HtmlTag'),
				array('tag' => 'div', 'class' => 'conteneur_form')
			),
			'Form'
		);

		$formJQueryElements = array(
	        array('UiWidgetElement', array('tag' => '')), // it necessary to include for jquery elements
	        array('Errors'),
			array('Label', array(
				'class' => 'label_form texte')
			),
			array('HtmlTag'),
			array(
				array('DivTag' => 'HtmlTag'),
				array('tag' => 'div', 'class' => 'conteneur_element')
			)
		);

		$decoratorSubmit = array(
			array('ViewHelper'),
			array('Errors'),
			array(array('data'=>'HtmlTag'), array('class'=>'btn_submit')),
			array('HtmlTag')
		);

		$decoratorCaptcha = array(
			array(array('data'=>'HtmlTag'), array('class'=>'conteneur_captcha'))
		);

		$decoratorLabel = array(
			array('ViewHelper'),
			array('Errors'),
			array('Label', array(
				'class' => 'label_form texte')
			),
			array('HtmlTag'),
			array(
				array('DivTag' => 'HtmlTag'),
				array('tag' => 'div', 'class' => 'conteneur_element')
			)
		);

		$eIdUser = new Zend_Form_Element_Hidden('id_user');
		$eIdUser->removeDecorator('label');

		// création des éléments du formulaire
		$ePseudo = new Zend_Form_Element_Text('pseudo');
		$ePseudoNotExist = new Zend_Validate_Db_NoRecordExists('users', 'pseudo', array('id_user',$eIdUser));
		$ePseudo->setLabel('Pseudo* :')
				->setRequired(true)
			    ->addFilter('StripTags')
			    ->addFilter('StringTrim')
			    ->addValidator('NotEmpty')
			    ->addValidator($ePseudoNotExist)
				->setDecorators($decoratorLabel);

		$eNom = new Zend_Form_Element_Text('nom');
		$eNom->setLabel('Nom :')
			 ->setDecorators($decoratorLabel);

		$ePrenom = new Zend_Form_Element_Text('prenom');
		$ePrenom->setLabel('Prénom :')
				->setDecorators($decoratorLabel);

		$eAge = new Zend_Form_Element_Text('age');
		$eAge->setLabel('âge :')
			 ->addFilter('Digits')
			 ->setDecorators($decoratorLabel);

		$eTel = new Zend_Form_Element_Text('telephone');
		$eTel->setLabel('Téléphone :')
			 ->addFilter('Digits')
			 ->setDecorators($decoratorLabel);

		$eVille = new Zend_Form_Element_Text('ville');
		$eVille->setLabel('Ville :')
			   ->setDecorators($decoratorLabel);

		$eMail = new Zend_Form_Element_Text('mail');
		$eMailNotExist = new Zend_Validate_Db_NoRecordExists('users', 'mail', array('id_user',$eIdUser));
		$eMail->setLabel('Mail* :')
			  ->setRequired(true)
			  ->addFilter('StripTags')
			  ->addFilter('StringTrim')
			  ->addValidator('EmailAddress')
			  ->addValidator($eMailNotExist)
			  ->setDecorators($decoratorLabel);

		$ePassword = new Zend_Form_Element_Password('password');
		$ePassword->setLabel('Password* :')
				  ->setRequired(true)
				  ->addFilter('StringTrim')
				  ->addValidator('NotEmpty')
				  ->setDecorators($decoratorLabel);

		$ePassword_confirm = new Zend_Form_Element_Password('password_confirm');
		$ePassword_confirm->setLabel('Confirmer le password* :')
						  ->setRequired(true)
						  ->addFilter('StringTrim')
						  ->addValidator('NotEmpty')
						  ->setDecorators($decoratorLabel);


		// création du captcha local
		// $pubKey = '6LewItoSAAAAAKOLFpbrvPeh5wmzyx2ToMO1se3o';
		// $privKey = '6LewItoSAAAAAFaYC_hBOvT6oG86agjjrWM5GYSS';

		// création du captcha sur le serveur de l'insset
		$pubKey = '6Lc09NoSAAAAAMnN_zAlbiIyIVIL9gJd5OK2-aGi';
		$privKey = '6Lc09NoSAAAAACUKR2f7mG4l3xOwksQuQCRaASPl';

		$recaptcha = new Zend_Service_ReCaptcha($pubKey, $privKey);

		$adapter = new Zend_Captcha_ReCaptcha();
		$adapter->setService($recaptcha);

		$captcha = new Zend_Form_Element_Captcha('recaptcha', array( 'label' => "Captcha", 'captcha' => $adapter));
		$captcha->removeDecorator('label')
				->removeDecorator('errors')
				->setRequired(true)
				->setDecorators($decoratorCaptcha);

		$eSubmit = new Zend_Form_Element_Submit('envoyer');
		$eSubmit->setDecorators($decoratorSubmit)
				->setLabel('Edition du compte');


		$this->addElement($eIdUser)
			 ->addElement($ePseudo)
			 ->addElement($eNom)
			 ->addElement($ePrenom)
			 ->addElement($eAge)
			 ->addElement($eTel)
			 ->addElement($eVille)
			 ->addElement($eMail)
			 ->addElement($ePassword)
			 ->addElement($ePassword_confirm)
			 ->addElement($captcha)
			 ->addElement($eSubmit);
	}

	// fonction de vérification de la correspondance des passwords
	public function isValid($data)
	{
		$password = $this->getElement('password');
		$password->addValidator(new App_Validate_PasswordMatch($data['password_confirm']));

		return parent::isValid($data);;
	}
}

// 03P141255UBP

?>