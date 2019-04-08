<?php

namespace UnitTest;

use PHPUnit\Framework\TestCase;


class ElSearchTest extends TestCase
{
    /** @test */
    public function countDocuments()
    {
        $el = new \Elastic\ElSearch();
        $this->assertEquals(3,count(($el->searchMain(false,'анса',false,false)['hits']['hits'])));
    }
    /** @test */
    public function checkNotEmptyDocuments()
    {
        $el = new \Elastic\ElSearch();
        $this->assertNotFalse($el->getAllDocuments());
    }
    /** @test */
    public function checkbyCostExact()
    {
        $el = new \Elastic\ElSearch();
        $this->assertEquals(3,count(($el->searchMain(false,false,'21421','21421')['hits']['hits'])));
    }
    /** @test */
    public function checkbyCostGte()
    {
        $el = new \Elastic\ElSearch();
        $this->assertEquals(4,count(($el->searchMain(false,false,'21421',false)['hits']['hits'])));
    }
    /** @test */
    public function checkbyCostLte()
    {
        $el = new \Elastic\ElSearch();
        $this->assertEquals(3,count(($el->searchMain(false,false,false,'21421')['hits']['hits'])));
    }
    /** @test */
    public function checkbyCostNotInclude()
    {
        $el = new \Elastic\ElSearch();
        $this->assertEquals(0,count(($el->searchMain(false,false,'5','70')['hits']['hits'])));
    }
    /** @test */
    public function checkUpdateField()
    {
        $el = new \Elastic\ElSearch();
        $el->updateById('Jpns4WkBltUe-1WrT0ff','rooms','3');
        $this->assertEquals(3,$el->getDocumentById('Jpns4WkBltUe-1WrT0ff')['_source']['rooms']);
    }
    /** @test */
    public function checkDeleteById()
    {
        xdebug_disable();
        $el = new \Elastic\ElSearch();
        $el->deleteById('LJkj4mkBltUe-1Wrt0ek');
        $this->assertFalse($el->getDocumentById('LJkj4mkBltUe-1Wrt0ek'));
    }
    /** @test */
    public function create()
    {
        $el = new \Elastic\ElSearch();
        $addStruct = [
            'id' => '555',
            'transactionType' => 'sale',
            'typeBulding' => 'stalin',
            'cost' => '21421',
            'square' => '14241',
            'rooms' => '5',
            'finish' => '4',
            'trim' => 'yes',
            'fund' => 'residential',
            'accomodationFormat' => 'flat',
            'mandatoryConditions' => 'parking',
            'name' => 'БК курица БК...',
            'description' => '3'
        ];
        $el->create($addStruct);
        $this->assertNotFalse($el->getAllDocuments());
    }
    /**
     * @depends create
     */
    public function checkCreate()
    {
        $el = new \Elastic\ElSearch();
        $this->assertEquals(1,count(($el->searchMain(false,'курица',false,false)['hits']['hits'])));
    }
}