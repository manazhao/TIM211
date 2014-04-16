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
      'id'         => new sfWidgetFormInputHidden(),
      'from_id'    => new sfWidgetFormInputText(),
      'to_id'      => new sfWidgetFormInputText(),
      'product'    => new sfWidgetFormInputText(),
      'price'      => new sfWidgetFormInputText(),
      'start_time' => new sfWidgetFormInputText(),
      'end_time'   => new sfWidgetFormInputText(),
      'status'     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'from_id'    => new sfValidatorInteger(array('required' => false)),
      'to_id'      => new sfValidatorInteger(array('required' => false)),
      'product'    => new sfValidatorInteger(array('required' => false)),
      'price'      => new sfValidatorNumber(array('required' => false)),
      'start_time' => new sfValidatorPass(array('required' => false)),
      'end_time'   => new sfValidatorPass(array('required' => false)),
      'status'     => new sfValidatorInteger(array('required' => false)),
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
