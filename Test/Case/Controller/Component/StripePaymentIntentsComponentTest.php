<?php

App::uses('StripePaymentIntentsComponent', 'StripePaymentIntents.Controller/Component');
App::uses('ComponentCollection', 'Controller');
App::import('StripePaymentIntents.Test', 'Config');

class StripePaymentIntentsComponentTest extends CakeTestCase {
    
    /** @var StripePaymentIntentsComponent */
    private $Component;

    private $originalConfig;
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();        
        $this->originalConfig = Configure::read('StripePaymentIntents');
        $copy = $this->originalConfig;
        $copy['keys']['test']['public'] = 'pk_test';        
        $copy['keys']['test']['secret'] = 'sk_test_4eC39HqLyjWDarjtT1zdp7dc';
        Configure::write('StripePaymentIntents', $copy);    
        date_default_timezone_set("UTC");
        $Collection = new ComponentCollection();
        $mockedController = $this->getMock('Controller', ['stripeWebhookCallback']);
        $this->Controller = $mockedController;
        $this->Component = new StripePaymentIntentsComponent($Collection);
        /** @noinspection PhpParamsInspection */
        $this->Component->startup($mockedController);
        Cache::clear();        
    } 

    public function tearDown()
    {
        Configure::write('StripePaymentIntents', $this->originalConfig);
    }

    public function testGetMode()
    {
        $this->assertEquals('test', $this->Component->GetMode());
    }

    public function testGetPublicKey()
    {
        $this->assertEquals('pk_test', $this->Component->GetPublicKey());
    }

    public function testCreate()
    {
        $actual = $this->Component->Create(123);    
        $this->assertEquals($actual->amount, 123);
        $this->assertEquals($actual->currency, 'eur');
        $this->assertStringStartsWith('pi_', $actual->id);   
    }

    public function testRetrieve()
    {
        $expected = $this->Component->Create(123);
        $actual = $this->Component->Retrieve($expected->id);
        $expected = $expected->toArray();
        
        $actual = $actual->toArray();
        $this->assertEquals($expected, $actual);
    }

    public function testCreateOverridesDefault()
    {
        $actual = $this->Component->Create(123, ['amount' => 456, 'description' => 'IntegrationTest']);  
        $this->assertEquals($actual->amount, 456);
        $this->assertEquals($actual->description, 'IntegrationTest');
    }    

    public function testHandleEventThrowsExceptionOnInvalidData()
    {
        $dataPath = self::GetDataPath('invalid.json');
        $this->expectException(\UnexpectedValueException::class, 'Event has no type');        
        $this->Component->HandleWebhook($dataPath);
    }

    public function testHandleEventThrowsExceptionOnNoData()
    {
        $dataPath = self::GetDataPath('nodata.json');
        $this->expectException(\UnexpectedValueException::class, 'Event has no type');        
        $this->Component->HandleWebhook($dataPath);
    }

    public function testHandleEventThrowsExceptionOnEmptyData()
    {
        $dataPath = self::GetDataPath('empty.json');
        $this->expectException(\UnexpectedValueException::class, 'Event has no type');        
        $this->Component->HandleWebhook($dataPath);
    }

    public function testHandleEvent()
    {
        $dataPath = self::GetDataPath('event.json');
        $this->Controller->expects($this->once())
            ->method('stripeWebhookCallback')
            ->with($this->anything());
        $this->Component->HandleWebhook($dataPath);
    }

    private static function GetDataPath($filename)
    {
        return join(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..', 'Data', $filename]);
    }
}