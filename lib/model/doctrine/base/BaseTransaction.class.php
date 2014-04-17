<?php

/**
 * BaseTransaction
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $from_id
 * @property integer $to_id
 * @property integer $product
 * @property float $price
 * @property datetime $start_time
 * @property datetime $end_time
 * @property integer $status
 * 
 * @method integer     getId()         Returns the current record's "id" value
 * @method integer     getFromId()     Returns the current record's "from_id" value
 * @method integer     getToId()       Returns the current record's "to_id" value
 * @method integer     getProduct()    Returns the current record's "product" value
 * @method float       getPrice()      Returns the current record's "price" value
 * @method datetime    getStartTime()  Returns the current record's "start_time" value
 * @method datetime    getEndTime()    Returns the current record's "end_time" value
 * @method integer     getStatus()     Returns the current record's "status" value
 * @method Transaction setId()         Sets the current record's "id" value
 * @method Transaction setFromId()     Sets the current record's "from_id" value
 * @method Transaction setToId()       Sets the current record's "to_id" value
 * @method Transaction setProduct()    Sets the current record's "product" value
 * @method Transaction setPrice()      Sets the current record's "price" value
 * @method Transaction setStartTime()  Sets the current record's "start_time" value
 * @method Transaction setEndTime()    Sets the current record's "end_time" value
 * @method Transaction setStatus()     Sets the current record's "status" value
 * 
 * @package    ProductTrading
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseTransaction extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('transaction');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('from_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 2,
             ));
        $this->hasColumn('to_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 2,
             ));
        $this->hasColumn('product', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 2,
             ));
        $this->hasColumn('price', 'float', null, array(
             'type' => 'float',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('start_time', 'datetime', null, array(
             'type' => 'datetime',
             ));
        $this->hasColumn('end_time', 'datetime', null, array(
             'type' => 'datetime',
             ));
        $this->hasColumn('status', 'integer', 1, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 1,
             ));

        $this->option('type', 'INNODB');
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}