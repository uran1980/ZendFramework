<?php
/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */

/**
 * Zend_Translate_Adapter_Tmx
 */
require_once 'Zend/Translate/Adapter/Tmx.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */
class Zend_Translate_Adapter_TmxTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Translate_Adapter_TmxTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Tmx);

        try {
            $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/nofile.tmx', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('is not readable', $e->getMessage());
        }

        try {
            $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/failed.tmx', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('Mismatched tag at line', $e->getMessage());
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
        $this->assertEquals('Tmx', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
        $this->assertTrue($adapter->isTranslated('Message 1'));
        $this->assertFalse($adapter->isTranslated('Message 6'));
        $this->assertTrue($adapter->isTranslated('Message 1', true));
        $this->assertTrue($adapter->isTranslated('Message 1', true, 'en'));
        $this->assertFalse($adapter->isTranslated('Message 1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message 4'));
        $this->assertEquals('Message 2', $adapter->translate('Message 2', 'ru'));
        $this->assertEquals('Message 1', $adapter->translate('Message 1', 'xx'));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1', 'en_US'));

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en.tmx', 'xx');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }

        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.tmx', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals(array('testoption' => 'testkey', 'clear' => false, 'scan' => null, 'locale' => 'en'), $adapter->getOptions());
        $this->assertEquals('testkey', $adapter->getOptions('testoption'));
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testClearing()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 5 (en)', $adapter->translate('Message 5'));
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.tmx', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4', $adapter->translate('Message 4'));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
        $this->assertEquals('en', $adapter->getLocale());
        $locale = new Zend_Locale('en');
        $adapter->setLocale($locale);
        $this->assertEquals('en', $adapter->getLocale());

        try {
            $adapter->setLocale('nolocale');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }
        try {
            $adapter->setLocale('it');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('has to be added before it can be used', $e->getMessage());
        }
    }

    public function testList()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
        $this->assertEquals(array('en' => 'en', 'fr' => 'fr'), $adapter->getList());
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.tmx', 'fr');
        $this->assertEquals(array('en' => 'en', 'de' => 'de', 'fr' => 'fr'), $adapter->getList());
        $this->assertTrue($adapter->isAvailable('fr'));
        $locale = new Zend_Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }

    public function testOptionLocaleDirectory()
    {
        require_once 'Zend/Translate.php';
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/testtmx', 'de', array('scan' => Zend_Translate::LOCALE_DIRECTORY));
        $this->assertEquals(array('de' => 'de', 'en' => 'en', 'fr' => 'fr'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptionLocaleFilename()
    {
        require_once 'Zend/Translate.php';
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/testtmx', 'de', array('scan' => Zend_Translate::LOCALE_FILENAME));
        $this->assertEquals(array('de' => 'de', 'en' => 'en', 'fr' => 'fr'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testZF3937()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en.tmx', 'en');
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_empty.tmx', 'de');

        $this->assertEquals('en', $adapter->getLocale());
        try {
            $adapter->setLocale('de');
            $this->fail('Empty translations should not be settable');
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('No translation for the language', $e->getMessage());
        }
    }

    public function testIsoEncoding()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_en3.tmx', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (it)', $adapter->_('Message 1', 'it'));
        $this->assertEquals(iconv('UTF-8', 'ISO-8859-1', 'Küchen Möbel (en)'), $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate(iconv('UTF-8', 'ISO-8859-1', 'Küchen Möbel')));
    }

    public function testWithoutEncoding()
    {
        $adapter = new Zend_Translate_Adapter_Tmx(dirname(__FILE__) . '/_files/translation_withoutencoding.tmx', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
    }
}

// Call Zend_Translate_Adapter_TmxTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Translate_Adapter_TmxTest::main") {
    Zend_Translate_Adapter_TmxTest::main();
}
