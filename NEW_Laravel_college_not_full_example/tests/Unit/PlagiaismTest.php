<?php

namespace Tests\Unit;

use App\EtxtAnswer;
use App\Services\EtxtService;
use factory;
use Faker\Provider\Lorem;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Tests\TestCase;
use File;
use SimpleXMLElement;

class PlagiaismTest extends TestCase
{
    /**
     *
     */
    public function checkEnvVariables()
    {
        $issetEtxtXlmPath = !empty(env('ETXT_XML_PATH')) and env('ETXT_XML_PATH') !== '';
        $this->assertTrue($issetEtxtXlmPath, 'env variable ETXT_XML_PATH can not be null!');

        $issetEtxtKey = !empty(env('ETXT_KEY')) and env('ETXT_KEY') !== '';
        $this->assertTrue($issetEtxtKey, 'env variable ETXT_KEY can not be null!');
    }
    /**
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function testParseDOC()
    {
        $text = Lorem::text(200);
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(14);

        $section = $phpWord->addSection();
        $section->addText(htmlspecialchars($text));

        $docName = time().'__unit_test_doc.docx';
        $objWriter = IOFactory::createWriter($phpWord,'Word2007');
        $objWriter->save(public_path($docName));

        $parseString = EtxtService::documentParseToString(public_path($docName));
        $isDeleted = File::delete(public_path($docName));

        $this->assertTrue($isDeleted, 'Can not be delete doc: '.public_path($docName));
        $this->assertTrue($parseString === $text, 'Document parse error');
    }
    /**
     *
     */
    public function testDecodeXML()
    {
        $this->checkEnvVariables();

        $xml =  '<?xml version="1.0" ?><root>'.
                '<serverType>server</serverType>'.
                '<enrty><ftext uniq="25">'.base64_encode('Test text').
                '</ftext></enrty></root>';

        $string = openssl_encrypt ($xml, "AES-128-ECB", getenv('ETXT_KEY'),  1);
        $string = base64_encode($string);
        $string = str_replace('+', ' ', $string);

        $parseXml = EtxtService::decodeAnswer($string);
        $testXml = new SimpleXMLElement($xml);

        $this->assertTrue($parseXml->asXML() === $testXml->asXML(), 'Create xml error');
    }
    /**
     *
     */
    public function testCreateXML()
    {
        $this->checkEnvVariables();

        $textName = 'Unit Test name';
        $text = Lorem::text(200);

        $etxt = new EtxtService();

        $data = [[
            'type' => 'text',
            'name' => $textName,
            'text' => $text,
            'id'   => 1,
        ]];
        $xmlName = $etxt->createXml($data);

        $fileExist = File::exists(env('ETXT_XML_PATH').$xmlName);
        $this->assertTrue($fileExist, 'Can not create xml!');

        $isDeleted = File::delete(env('ETXT_XML_PATH').$xmlName);
        $this->assertTrue($isDeleted, 'Can not be delete xml: '.env('ETXT_XML_PATH').$xmlName);
    }
    /**
     *
     */
    public function testSaveAndDeleteAnswer()
    {
        $newAnswer = factory(EtxtAnswer::class)->create();

        $this->assertTrue($newAnswer->save(), 'Can not be create EtxtAnswer');
        $this->assertTrue($newAnswer->delete(), 'Can not be delete EtxtAnswer');
    }
}
