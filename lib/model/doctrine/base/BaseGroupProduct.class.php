<?php

/**
 * BaseGroupProduct
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $group_id
 * @property integer $product_id
 * 
 * @method integer      getId()         Returns the current record's "id" value
 * @method integer      getGroupId()    Returns the current record's "group_id" value
 * @method integer      getProductId()  Returns the current record's "product_id" value
 * @method GroupProduct setId()         Sets the current record's "id" value
 * @method GroupProduct setGroupId()    Sets the current record's "group_id" value
 * @method GroupProduct setProductId()  Sets the current record's "product_id" value
 * 
 * @package    ProductTrading
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseGroupProduct extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('group_product');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('group_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 2,
             ));
        $this->hasColumn('product_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 2,
             ));

        $this->option('type', 'INNODB');
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}