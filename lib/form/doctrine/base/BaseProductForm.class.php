<?php

/**
 * Product form base class.
 *
 * @method Product getObject() Returns the current form's model object
 *
 * @package    ProductTrading
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'       => new sfWidgetFormInputHidden(),
      'producer' => new sfWidgetFormInputText(),
      'consumer' => new sfWidgetFormInputText(),
      'cost'     => new sfWidgetFormInputText(),
      'utility'  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'producer' => new sfValidatorInteger(array('required' => false)),
      'consumer' => new sfValidatorInteger(array('required' => false)),
      'cost'     => new sfValidatorNumber(array('required' => false)),
      'utility'  => new sfValidatorNumber(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Product';
  }

}
