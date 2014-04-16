<?php

/**
 * Transaction filter form base class.
 *
 * @package    ProductTrading
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTransactionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'from_id' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'to_id'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'round'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'status'  => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'from_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'to_id'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'round'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'status'  => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('transaction_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Transaction';
  }

  public function getFields()
  {
    return array(
      'id'      => 'Number',
      'from_id' => 'Number',
      'to_id'   => 'Number',
      'round'   => 'Number',
      'status'  => 'Boolean',
    );
  }
}
