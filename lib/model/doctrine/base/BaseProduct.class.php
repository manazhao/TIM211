<?php

/**
 * BaseProduct
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $producer
 * @property integer $consumer
 * @property integer $holder
 * @property float $cost
 * @property float $utility
 * 
 * @method integer getId()       Returns the current record's "id" value
 * @method integer getProducer() Returns the current record's "producer" value
 * @method integer getConsumer() Returns the current record's "consumer" value
 * @method integer getHolder()   Returns the current record's "holder" value
 * @method float   getCost()     Returns the current record's "cost" value
 * @method float   getUtility()  Returns the current record's "utility" value
 * @method Product setId()       Sets the current record's "id" value
 * @method Product setProducer() Sets the current record's "producer" value
 * @method Product setConsumer() Sets the current record's "consumer" value
 * @method Product setHolder()   Sets the current record's "holder" value
 * @method Product setCost()     Sets the current record's "cost" value
 * @method Product setUtility()  Sets the current record's "utility" value
 * 
 * @package    ProductTrading
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseProduct extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('product');
        $this->hasColumn('id', 'integer', 2, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('producer', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 2,
             ));
        $this->hasColumn('consumer', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 2,
             ));
        $this->hasColumn('holder', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 2,
             ));
        $this->hasColumn('cost', 'float', null, array(
             'type' => 'float',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('utility', 'float', null, array(
             'type' => 'float',
             'notnull' => true,
             'default' => 0,
             ));

        $this->option('type', 'INNODB');
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}