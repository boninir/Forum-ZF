<?php 

Class themeForm extends Zend_Form
{
	public function init()
	{
		// parametrage du formulaire
		$this->setMethod('post');
		$url = new Zend_View_Helper_Url();
    	$this->setAction($url->url(array('controller'=> 'index','action'=>'index'),'default'));
		// $this->setAction('/index/index');

		$this->clearDecorators();

		$decoratorForm = array(
			'FormElements',
			array('HtmlTag'),
			array(
				array('DivTag' => 'HtmlTag'),
				array('tag' => 'div', 'class' => 'conteneur_form_discussion')
			),
			'Form'
		);

		$decoratorSubmit = array(
			array('ViewHelper'),
			array('Errors'),
			array(array('data'=>'HtmlTag'), array('class'=>'btn_submit_discussion')),
			array('HtmlTag')
		);

		$decoratorLabel = array(
			array('ViewHelper'),
			array('Errors'),
			array('Label', array(
				'class' => 'label_form_discussion')
			),
			array('HtmlTag'),
			array(
				array('DivTag' => 'HtmlTag'),
				array('tag' => 'div', 'class' => 'conteneur_element_discussion_label lbl_discussion')
			)
		);

		// création des éléments du formulaire

		$eNom = new Zend_Form_Element_Text('nom');
		$eNom->setLabel('Intitulé du thème :')
			 	 ->setDecorators($decoratorLabel);

		$eSubmit = new Zend_Form_Element_Submit('Ajouter le thème');
		$eSubmit->setDecorators($decoratorSubmit)
				->setLabel('Ajouter le thème');

		$this->addElement($eNom)
			 ->addElement($eSubmit)
			 ->setDecorators($decoratorForm);
	}
}

?>