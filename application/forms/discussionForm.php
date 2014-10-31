<?php 

Class discussionForm extends Zend_Form
{
	public function init()
	{
		// parametrage du formulaire
		$this->setMethod('post');
		$this->setAction($this->id_theme);

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

		$eLibelle = new Zend_Form_Element_Text('libelle');
		$eLibelle->setLabel('Intitulé de la discution :')
			 	 ->setDecorators($decoratorLabel);

		$eSubmit = new Zend_Form_Element_Submit('Ajouter la discution');
		$eSubmit->setDecorators($decoratorSubmit)
				->setLabel('Ajouter la discution');

		$this->addElement($eLibelle)
			 ->addElement($eSubmit)
			 ->setDecorators($decoratorForm);
	}
}

?>