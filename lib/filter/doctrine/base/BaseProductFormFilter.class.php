<?php

/**
 * Product filter form base class.
 *
 * @package    ProductTrading
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseProductFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'producer' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'consumer' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'cost'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'utility'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'producer' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'consumer' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'cost'     => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'utility'  => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('product_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Product';
  }

  public function getFields()
  {
    return array(
      'id'       => 'Number',
      'producer' => 'Number',
      'consumer' => 'Number',
      'cost'     => 'Number',
      'utility'  => 'Number',
    );
  }
}
