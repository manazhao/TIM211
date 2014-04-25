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
      'type'           => new sfWidgetFormFilterInput(),
      'from_id'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'to_id'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'refer_id'       => new sfWidgetFormFilterInput(),
      'product'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'price'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'first_ref_fee'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'second_ref_fee' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'ref_degree'     => new sfWidgetFormFilterInput(),
      'rnd'            => new sfWidgetFormFilterInput(),
      'expire'         => new sfWidgetFormFilterInput(),
      'status'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'type'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'from_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'to_id'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'refer_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'product'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'price'          => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'first_ref_fee'  => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'second_ref_fee' => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'ref_degree'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'rnd'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'expire'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'status'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
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
      'id'             => 'Number',
      'type'           => 'Number',
      'from_id'        => 'Number',
      'to_id'          => 'Number',
      'refer_id'       => 'Number',
      'product'        => 'Number',
      'price'          => 'Number',
      'first_ref_fee'  => 'Number',
      'second_ref_fee' => 'Number',
      'ref_degree'     => 'Number',
      'rnd'            => 'Number',
      'expire'         => 'Number',
      'status'         => 'Number',
    );
  }
}
