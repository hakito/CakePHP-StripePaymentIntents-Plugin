<?php

App::uses('StripePaymentIntentsComponent', 'StripePaymentIntents.Controller/Component');
App::uses('ComponentCollection', 'Controller');
App::import('StripePaymentIntents.Test', 'Config');

class StripePaymentIntentsComponentTest extends CakeTestCase {
    
    /** @var StripePaymentIntentsComponent */
    private $Component;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();        

        date_default_timezone_set("UTC");
        $Collection = new ComponentCollection();
        $mockedController = $this->getMock('Controller', ['stripeWebhookCallback']);
        $this->Controller = $mockedController;
        $this->Component = new StripePaymentIntentsComponent($Collection);
        /** @noinspection PhpParamsInspection */
        $this->Component->startup($mockedController);
        Cache::clear();        
    } 

    public function testCreate()
    {
        $actual = $this->Component->Create(123);    
        $this->assertEquals($actual->amount, 123);
        $this->assertEquals($actual->currency, 'eur');
        $this->assertStringStartsWith('pi_', $actual->id);   
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