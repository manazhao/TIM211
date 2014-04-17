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
      'from_id'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'to_id'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'product'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'price'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'start_time' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'end_time'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'status'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'from_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'to_id'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'product'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'price'      => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'start_time' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'end_time'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'status'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
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
      'id'         => 'Number',
      'from_id'    => 'Number',
      'to_id'      => 'Number',
      'product'    => 'Number',
      'price'      => 'Number',
      'start_time' => 'Date',
      'end_time'   => 'Date',
      'status'     => 'Number',
    );
  }
}
