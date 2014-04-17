<?php

/**
 * GroupProduct filter form base class.
 *
 * @package    ProductTrading
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseGroupProductFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'group_id'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'product_id' => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'group_id'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'product_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('group_product_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'GroupProduct';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'group_id'   => 'Number',
      'product_id' => 'Number',
    );
  }
}
