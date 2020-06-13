<?php

namespace StripePaymentIntents\TestCase\Controller\Component;

use Cake\Cache\Cache;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\TestSuite\TestCase;

use StripePaymentIntents\Controller\Component\StripePaymentIntentsComponent;

class StripePaymentIntentsComponentTest extends TestCase {

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
        $this->Controller = $this->getMockBuilder(\Cake\Controller\Controller::class)
            ->setMethods(['dummy'])
            ->getMock();
        $this->Controller->getEventManager()->setEventList(new \Cake\Event\EventList());

        $registry = new ComponentRegistry();
        $this->Component = new StripePaymentIntentsComponent($registry);

        $event = new Event('Controller.startup', $this->Controller);
        $this->Component->startup($event);

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
        \Cake\Event\EventManager::instance()->on('StripePaymentIntents.Event',
        function (\Cake\Event\Event $event, \Stripe\Event $stripeEvent)
        {
            return ['handled' => true]; 
        });
        $this->Component->HandleWebhook($dataPath);
        $this->assertEventFired('StripePaymentIntents.Event', $this->Controller->getEventManager());
    }

    private static function GetDataPath($filename)
    {
        return join(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..', 'Data', $filename]);
    }
}