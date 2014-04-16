<?php

/**
 * Transaction form base class.
 *
 * @method Transaction getObject() Returns the current form's model object
 *
 * @package    ProductTrading
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTransactionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'      => new sfWidgetFormInputHidden(),
      'from_id' => new sfWidgetFormInputText(),
      'to_id'   => new sfWidgetFormInputText(),
      'round'   => new sfWidgetFormInputText(),
      'status'  => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'from_id' => new sfValidatorInteger(array('required' => false)),
      'to_id'   => new sfValidatorInteger(array('required' => false)),
      'round'   => new sfValidatorInteger(array('required' => false)),
      'status'  => new sfValidatorBoolean(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('transaction[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Transaction';
  }

}
