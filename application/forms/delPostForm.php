<?php 

Class delPostForm extends Zend_Form
{
	public function init()
	{
		// // parametrage du formulaire
		$this->setMethod('post');
		$this->setAction($this->id_discution);
		$this->setAttrib('id', 'frm_del_post');

		$this->clearDecorators();

		// $decoratorSubmit = array(
		// 	array('ViewHelper'),
		// 	array('Errors'),
		// 	array(array('data'=>'HtmlTag'), array('class'=>'btn_submit_post')),
		// 	array('HtmlTag')
		// );

		// création des éléments du formulaire

		$eIdPost = new Zend_Form_Element_Hidden('id_post_del');
		$eIdPost->removeDecorator('label');

		$eProvenance = new Zend_Form_Element_Hidden('provenance');
		$eProvenance->removeDecorator('label')
					->setValue('frm_del_ajout');

		$eSubmit = new Zend_Form_Element_Submit('Supprimer');
		$eSubmit->setAttrib('id', 'btn_del_post')
				->setLabel('Supprimer');

		$this->addElement($eIdPost)
			 ->addElement($eProvenance)
			 ->addElement($eSubmit);
	}
}

?>