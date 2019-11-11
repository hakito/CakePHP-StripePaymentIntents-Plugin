<?php
/**
 * All  plugin tests
 */
class AllStripePaymentIntentsTest extends CakeTestCase {

	/**
	 * Suite define the tests for this plugin
	 *
	 * @return CakeTestSuite
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All test');

		$path = CakePlugin::path('StripePaymentIntents') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);

		return $suite;
	}

}
