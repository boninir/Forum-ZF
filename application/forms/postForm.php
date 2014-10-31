<?php 

Class postForm extends Zend_Form
{
	public function init()
	{
		// // parametrage du formulaire
		$this->setMethod('post');
		$this->setAction($this->id_discution);
		$this->setAttrib('id', 'frm_ajout');

		$this->clearDecorators();

		$decoratorSubmit = array(
			array('ViewHelper'),
			array('Errors'),
			array(array('data'=>'HtmlTag'), array('class'=>'btn_submit_post')),
			array('HtmlTag')
		);

		// création des éléments du formulaire

		$eIdPost = new Zend_Form_Element_Hidden('id_post');
		$eIdPost->removeDecorator('label');

		$eProvenance = new Zend_Form_Element_Hidden('provenance');
		$eProvenance->removeDecorator('label')
					->setValue('frm_ajout');

		$eMessage = new Zend_Form_Element_Hidden('message');
		$eMessage->removeDecorator('label');

		$eSubmit = new Zend_Form_Element_Submit('Ajouter');
		$eSubmit->setDecorators($decoratorSubmit)
				->setAttrib('onClick', 'document.getElementById(\'message\').value = document.getElementById(\'content_post\').innerHTML;')
				->setAttrib('id', 'btn_envoi_post')
				->setLabel('Ajouter');

		$this->addElement($eIdPost)
			 ->addElement($eProvenance)
			 ->addElement($eMessage)
			 ->addElement($eSubmit);
	}
}

?>