<?php

/**
 * GroupProduct form base class.
 *
 * @method GroupProduct getObject() Returns the current form's model object
 *
 * @package    ProductTrading
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseGroupProductForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'group_id'   => new sfWidgetFormInputText(),
      'product_id' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'group_id'   => new sfValidatorInteger(array('required' => false)),
      'product_id' => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('group_product[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'GroupProduct';
  }

}
