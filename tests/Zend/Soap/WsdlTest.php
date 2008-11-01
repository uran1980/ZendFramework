<?php
/**
 * @package Zend_Soap
 * @subpackage UnitTests
 */

require_once dirname(__FILE__)."/../../TestHelper.php";

/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Soap_Wsdl */
require_once 'Zend/Soap/Wsdl.php';


/**
 * Test cases for Zend_Soap_Wsdl
 *
 * @package Zend_Soap
 * @subpackage UnitTests
 */
class Zend_Soap_WsdlTest extends PHPUnit_Framework_TestCase
{
    function testConstructor()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        $this->assertEquals($wsdl->toXml(),
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                                 . 'xmlns:tns="http://localhost/MyService.php" '
                                 . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                                 . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                                 . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                                 . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' . PHP_EOL);
    }

    function testSetUriChangesDomDocumentWsdlStructureTnsAndTargetNamespaceAttributes()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        $wsdl->setUri('http://localhost/MyNewService.php');

        $this->assertEquals($wsdl->toXml(),
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                                 . 'xmlns:tns="http://localhost/MyNewService.php" '
                                 . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                                 . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                                 . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                                 . 'name="MyService" targetNamespace="http://localhost/MyNewService.php"/>' . PHP_EOL);
    }

    function testAddMessage()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        $messageParts = array();
        $messageParts['parameter1'] = $wsdl->getType('int');
        $messageParts['parameter2'] = $wsdl->getType('string');
        $messageParts['parameter3'] = $wsdl->getType('mixed');

        $wsdl->addMessage('myMessage', $messageParts);

        $this->assertEquals($wsdl->toXml(),
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<message name="myMessage">'
                               .   '<part name="parameter1" type="xsd:int"/>'
                               .   '<part name="parameter2" type="xsd:string"/>'
                               .   '<part name="parameter3" type="xsd:anyType"/>'
                               . '</message>'
                          . '</definitions>' . PHP_EOL);
    }

    function testAddPortType()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addPortType('myPortType');

        $this->assertEquals($wsdl->toXml(),
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                          . '</definitions>' . PHP_EOL);
    }

    function testAddPortOperation()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        $portType = $wsdl->addPortType('myPortType');

        $wsdl->addPortOperation($portType, 'operation1');
        $wsdl->addPortOperation($portType, 'operation2', 'tns:operation2Request', 'tns:operation2Response');
        $wsdl->addPortOperation($portType, 'operation3', 'tns:operation3Request', 'tns:operation3Response', 'tns:operation3Fault');

        $this->assertEquals($wsdl->toXml(),
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType">'
                               .   '<operation name="operation1"/>'
                               .   '<operation name="operation2">'
                               .     '<input message="tns:operation2Request"/>'
                               .     '<output message="tns:operation2Response"/>'
                               .   '</operation>'
                               .   '<operation name="operation3">'
                               .     '<input message="tns:operation3Request"/>'
                               .     '<output message="tns:operation3Response"/>'
                               .     '<fault message="tns:operation3Fault"/>'
                               .   '</operation>'
                               . '</portType>'
                          . '</definitions>' . PHP_EOL);
    }

    function testAddBinding()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addPortType('myPortType');
        $wsdl->addBinding('MyServiceBinding', 'myPortType');

        $this->assertEquals($wsdl->toXml(),
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType"/>'
                          . '</definitions>' . PHP_EOL);
    }

    function testAddBindingOperation()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addPortType('myPortType');
        $binding = $wsdl->addBinding('MyServiceBinding', 'myPortType');

        $wsdl->addBindingOperation($binding, 'operation1');
        $wsdl->addBindingOperation($binding,
                                   'operation2',
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                  );
        $wsdl->addBindingOperation($binding,
                                   'operation3',
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                   );

        $this->assertEquals($wsdl->toXml(),
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType">'
                               .   '<operation name="operation1"/>'
                               .   '<operation name="operation2">'
                               .     '<input>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</input>'
                               .     '<output>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</output>'
                               .   '</operation>'
                               .   '<operation name="operation3">'
                               .     '<input>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</input>'
                               .     '<output>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</output>'
                               .     '<fault>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</fault>'
                               .   '</operation>'
                               . '</binding>'
                          . '</definitions>' . PHP_EOL);
    }

    function testAddSoapBinding()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addPortType('myPortType');
        $binding = $wsdl->addBinding('MyServiceBinding', 'myPortType');

        $wsdl->addSoapBinding($binding);

        $wsdl->addBindingOperation($binding, 'operation1');
        $wsdl->addBindingOperation($binding,
                                   'operation2',
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                  );

        $this->assertEquals($wsdl->toXml(),
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType">'
                               .   '<soap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http"/>'
                               .   '<operation name="operation1"/>'
                               .   '<operation name="operation2">'
                               .     '<input>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</input>'
                               .     '<output>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</output>'
                               .   '</operation>'
                               . '</binding>'
                          . '</definitions>' . PHP_EOL);

        $wsdl1 = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl1->addPortType('myPortType');
        $binding = $wsdl1->addBinding('MyServiceBinding', 'myPortType');

        $wsdl1->addSoapBinding($binding, 'rpc');

        $wsdl1->addBindingOperation($binding, 'operation1');
        $wsdl1->addBindingOperation($binding,
                                   'operation2',
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                  );

        $this->assertEquals($wsdl1->toXml(),
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType">'
                               .   '<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>'
                               .   '<operation name="operation1"/>'
                               .   '<operation name="operation2">'
                               .     '<input>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</input>'
                               .     '<output>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</output>'
                               .   '</operation>'
                               . '</binding>'
                          . '</definitions>' . PHP_EOL);
    }


    function testAddSoapOperation()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addPortType('myPortType');
        $binding = $wsdl->addBinding('MyServiceBinding', 'myPortType');

        $wsdl->addSoapOperation($binding, 'http://localhost/MyService.php#myOperation');

        $wsdl->addBindingOperation($binding, 'operation1');
        $wsdl->addBindingOperation($binding,
                                   'operation2',
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"),
                                   array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/")
                                  );

        $this->assertEquals($wsdl->toXml(),
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType">'
                               .   '<soap:operation soapAction="http://localhost/MyService.php#myOperation"/>'
                               .   '<operation name="operation1"/>'
                               .   '<operation name="operation2">'
                               .     '<input>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</input>'
                               .     '<output>'
                               .       '<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'
                               .     '</output>'
                               .   '</operation>'
                               . '</binding>'
                          . '</definitions>' . PHP_EOL);
    }

    function testAddService()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addPortType('myPortType');
        $wsdl->addBinding('MyServiceBinding', 'myPortType');

        $wsdl->addService('Service1', 'myPortType', 'MyServiceBinding', 'http://localhost/MyService.php');

        $this->assertEquals($wsdl->toXml(),
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType"/>'
                               . '<binding name="MyServiceBinding" type="myPortType"/>'
                               . '<service name="Service1">'
                               .   '<port name="myPortType" binding="MyServiceBinding">'
                               .     '<soap:address location="http://localhost/MyService.php"/>'
                               .   '</port>'
                               . '</service>'
                          . '</definitions>' . PHP_EOL);
    }

    function testAddDocumentation()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        $portType = $wsdl->addPortType('myPortType');

        $wsdl->addDocumentation($portType, 'This is a description for Port Type node.');

        $this->assertEquals($wsdl->toXml(),
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<portType name="myPortType">'
                               .   '<documentation>This is a description for Port Type node.</documentation>'
                               . '</portType>'
                          . '</definitions>' . PHP_EOL);
    }

    function testToXml()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        $this->assertEquals($wsdl->toXml(),
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' . PHP_EOL);
    }

    function testToDomDocument()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        $dom = $wsdl->toDomDocument();

        $this->assertTrue($dom instanceOf DOMDocument);

        $this->assertEquals($dom->saveXML(),
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' . PHP_EOL);
    }

    function testDump()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        ob_start();
        $wsdl->dump();
        $wsdlDump = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($wsdlDump,
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' . PHP_EOL);

        $wsdl->dump(dirname(__FILE__) . '/_files/dumped.wsdl');
        $dumpedContent = file_get_contents(dirname(__FILE__) . '/_files/dumped.wsdl');

        $this->assertEquals($dumpedContent,
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php"/>' . PHP_EOL);

        unlink(dirname(__FILE__) . '/_files/dumped.wsdl');
    }

    function testGetType()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php', true);

        $this->assertEquals('xsd:string',       $wsdl->getType('string'),  'xsd:string detection failed.');
        $this->assertEquals('xsd:string',       $wsdl->getType('str'),     'xsd:string detection failed.');
        $this->assertEquals('xsd:int',          $wsdl->getType('int'),     'xsd:int detection failed.');
        $this->assertEquals('xsd:int',          $wsdl->getType('integer'), 'xsd:int detection failed.');
        $this->assertEquals('xsd:float',        $wsdl->getType('float'),   'xsd:float detection failed.');
        $this->assertEquals('xsd:float',        $wsdl->getType('double'),  'xsd:float detection failed.');
        $this->assertEquals('xsd:boolean',      $wsdl->getType('boolean'), 'xsd:boolean detection failed.');
        $this->assertEquals('xsd:boolean',      $wsdl->getType('bool'),    'xsd:boolean detection failed.');
        $this->assertEquals('soap-enc:Array',   $wsdl->getType('array'),   'soap-enc:Array detection failed.');
        $this->assertEquals('xsd:struct',       $wsdl->getType('object'),  'xsd:struct detection failed.');
        $this->assertEquals('xsd:anyType',      $wsdl->getType('mixed'),   'xsd:anyType detection failed.');
        $this->assertEquals('',                 $wsdl->getType('void'),    'void  detection failed.');
    }

    function testGetComplexTypeBasedOnStrategiesBackwardsCompabilityBoolean()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php', true);
        $this->assertEquals('tns:Zend_Soap_Wsdl_Test', $wsdl->getType('Zend_Soap_Wsdl_Test'));
        $this->assertTrue($wsdl->getComplexTypeStrategy() instanceof Zend_Soap_Wsdl_Strategy_DefaultComplexType);

        $wsdl2 = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php', false);
        $this->assertEquals('xsd:anyType',             $wsdl2->getType('Zend_Soap_Wsdl_Test'));
        $this->assertTrue($wsdl2->getComplexTypeStrategy() instanceof Zend_Soap_Wsdl_Strategy_AnyType);
    }

    function testGetComplexTypeBasedOnStrategiesStringNames()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php', 'Zend_Soap_Wsdl_Strategy_DefaultComplexType');
        $this->assertEquals('tns:Zend_Soap_Wsdl_Test', $wsdl->getType('Zend_Soap_Wsdl_Test'));
        $this->assertTrue($wsdl->getComplexTypeStrategy() instanceof Zend_Soap_Wsdl_Strategy_DefaultComplexType);

        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php', 'Zend_Soap_Wsdl_Strategy_AnyType');
        $this->assertEquals('xsd:anyType',             $wsdl->getType('Zend_Soap_Wsdl_Test'));
        $this->assertTrue($wsdl2->getComplexTypeStrategy() instanceof Zend_Soap_Wsdl_Strategy_AnyType);
    }

    function testSettingUnknownStrategyThrowsException()
    {
        try {
            $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php', 'Zend_Soap_Wsdl_Strategy_UnknownStrategyType');
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {
            
        }
    }

    function testSettingInvalidStrategyObjectThrowsException()
    {
        try {
            $strategy = new Zend_Soap_Wsdl_Test();
            $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php', $strategy);
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    function testAddingSameComplexTypeMoreThanOnceThrowsException()
    {
        try {
            $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
            $wsdl->addType('Zend_Soap_Wsdl_Test');
            $wsdl->addType('Zend_Soap_Wsdl_Test');
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    function testUsingSameComplexTypeTwiceLeadsToReuseOfDefinition()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');
        $wsdl->addComplexType('Zend_Soap_Wsdl_Test');
        $this->assertEquals(array('Zend_Soap_Wsdl_Test'), $wsdl->getTypes());

        $wsdl->addComplexType('Zend_Soap_Wsdl_Test');
        $this->assertEquals(array('Zend_Soap_Wsdl_Test'), $wsdl->getTypes());
    }

    function testAddComplexType()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        $wsdl->addComplexType('Zend_Soap_Wsdl_Test');

        $this->assertEquals($wsdl->toXml(),
                            '<?xml version="1.0"?>' . PHP_EOL .
                            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" '
                               . 'xmlns:tns="http://localhost/MyService.php" '
                               . 'xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" '
                               . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                               . 'xmlns:soap-enc="http://schemas.xmlsoap.org/soap/encoding/" '
                               . 'name="MyService" targetNamespace="http://localhost/MyService.php">'
                               . '<types>'
                               .   '<xsd:schema targetNamespace="http://localhost/MyService.php">'
                               .     '<xsd:complexType name="Zend_Soap_Wsdl_Test">'
                               .       '<xsd:all>'
                               .         '<xsd:element name="var1" type="xsd:int"/>'
                               .         '<xsd:element name="var2" type="xsd:string"/>'
                               .       '</xsd:all>'
                               .     '</xsd:complexType>'
                               .   '</xsd:schema>'
                               . '</types>'
                          . '</definitions>' . PHP_EOL);
    }

    /**
     * @group ZF-3910
     */
    function testCaseOfDocBlockParamsDosNotMatterForSoapTypeDetectionZf3910()
    {
        $wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php');

        $this->assertEquals("xsd:string", $wsdl->getType("StrIng"));
        $this->assertEquals("xsd:string", $wsdl->getType("sTr"));
        $this->assertEquals("xsd:int", $wsdl->getType("iNt"));
        $this->assertEquals("xsd:int", $wsdl->getType("INTEGER"));
        $this->assertEquals("xsd:float", $wsdl->getType("FLOAT"));
        $this->assertEquals("xsd:float", $wsdl->getType("douBLE"));
    }
}



/**
 * Test Class
 */
class Zend_Soap_Wsdl_Test {
    /**
     * @var integer
     */
    public $var1;

    /**
     * @var string
     */
    public $var2;
}

