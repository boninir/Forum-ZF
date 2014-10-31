<?php 

Class loginForm extends Zend_Form
{
	public function init()
	{
		// // parametrage du formulaire
		$this->setMethod('post');

		try {
			$url = new Zend_View_Helper_Url();
	    	$this->setAction($url->url(array('controller'=> 'login','action'=>'login'),'default'));
			
		} catch (Exception $e) {
			echo 'Exception : ' . get_class($e) . '<br />';
			echo 'Message : ' . $e->getMessage() . '<br />';
		}

		// $this->setAction('/login/login');

		$decoratorSubmit = array(
			array('ViewHelper'),
			array('Errors'),
			array(array('data'=>'HtmlTag'), array('class'=>'btn_submit_login')),
			array('HtmlTag')
		);

		$decoratorLabel = array(
			array('ViewHelper'),
			array('Errors'),
			array('Label', array(
				'class' => 'label_form_login')
			),
			array('HtmlTag'),
			array(
				array('DivTag' => 'HtmlTag'),
				array('tag' => 'div', 'class' => 'conteneur_element_login')
			)
		);

		// création des éléments du formulaire

		$eLogin = new Zend_Form_Element_Text('login');
		$eLogin->setLabel('Login (adresse mail) :')
				->setRequired(true)
			    ->addFilter('StripTags')
			    ->addFilter('StringTrim')
			    ->addValidator('NotEmpty')
			    ->setDecorators($decoratorLabel);

		$ePass = new Zend_Form_Element_Password('pass');
		$ePass->setLabel('Password :')
				  ->setRequired(true)
				  ->addFilter('StringTrim')
				  ->addValidator('NotEmpty')
				  ->setDecorators($decoratorLabel);

		$eSubmit = new Zend_Form_Element_Submit('Connexion');
		$eSubmit->setDecorators($decoratorSubmit)
				->setLabel('Connexion');

		$this->addElement($eLogin)
			 ->addElement($ePass)
			 ->addElement($eSubmit);
	}
}

// 03P141255UBP

?>