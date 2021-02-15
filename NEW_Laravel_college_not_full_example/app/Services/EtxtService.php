<?php

namespace App\Services;

use Ixudra\Curl\Facades\Curl;
use File;

class EtxtService
{
    public static $workTypes = [
        'course_work' => 'Курсовая работа',
        'practice_report' => 'Отчет по практике',
        'nirm_report' => 'Отчет НИРМ',
        'research_article' => 'Научная статья',
        'graduate_work' => 'Дипломная работа',
        'dissertation_master' => 'Магистерская диссертация',
        'another_work' => 'Прочие письменные работы',
    ];

    // путь до сервера
    private $serverUrl;

    // путь до веб части проверки
    private $localServer;

    // путь для получения результата
    private $callbackUrl;

    //Путь до файла
    private $filePath;

    /**
     * EtxtService constructor.
     * @param null $callbackUrl
     */
    function __construct($callbackUrl = null)
    {
        $this->localServer = env('ETXT_XML_SERVER_PATH');
        $this->serverUrl = env('ETXT_SERVER');
        $this->callbackUrl = $callbackUrl ?? env('ETXT_ANSWER_URL');
        $this->filePath = env('ETXT_XML_PATH');
    }

    /**
     * @param $ItemsToCheck
     * @param null $ignore_urls
     * @param null $ignore_domains
     * @return bool|string
     * функция построения xml файла заданий
     */
    public function createXml($ItemsToCheck, $ignore_urls = null, $ignore_domains = null)
    {
        $string = '<?xml version="1.0" encoding="UTF-8" ?'.'><root>';
        $string .= '<serverType>server</serverType>';

        $string_ignore_urls = '';

        if ($ignore_urls != null){
            if (is_array($ignore_urls)){
                foreach ($ignore_urls as $ignore_url){
                    $string_ignore_urls .= '<url>'.base64_encode(@iconv('WINDOWS-1251', 'UTF-8//IGNORE', $ignore_url)).'</url>';
                }
            } else {
                $string_ignore_urls .= '<url>'.base64_encode(@iconv('WINDOWS-1251', 'UTF-8//IGNORE', $ignore_urls)).'</url>';
            }
        }
        $string_ignore_domains = '';

        if ($ignore_domains != null){
            if (is_array($ignore_domains)){
                foreach ($ignore_domains as $ignore_domain){
                    $string_ignore_domains .= '<domain>'.base64_encode(@iconv('WINDOWS-1251', 'UTF-8//IGNORE', $ignore_domain)).'</domain>';
                }
            } else {
                $string_ignore_domains .= '<domain>'.base64_encode(@iconv('WINDOWS-1251', 'UTF-8//IGNORE', $ignore_domains)).'</domain>';
            }
        }
        if ($string_ignore_domains !== '' or $string_ignore_urls !== ''){
            $string .= '<exceptions>'.
                $string_ignore_domains.
                $string_ignore_urls
                .'</exceptions>';
        }

        foreach ($ItemsToCheck as $item) {
            $codeText = mb_detect_encoding($item['text']);
            $codeName = mb_detect_encoding($item['name']);

            $text = base64_encode(@iconv($codeText, 'UTF-8//IGNORE', $item['text']));
            $name = base64_encode(@iconv($codeName, 'UTF-8//IGNORE', $item['name']));

            $string .= '<entry>'.
                    '<id>'.$item['id'].'</id>'.
                    '<type>'.$item['type'].'</type>';

            if (isset($item['uservars']) && is_array($item['uservars'])) {
                $string .= '<uservars>';
                foreach ($item['uservars'] as $key => $uservar)
                    $string .= '<'.$key.'>'.$uservar.'</'.$key.'>';
                $string .= '</uservars>';
            }
            $string .='<name>'.$name.'</name>'.
                    '<text>'.$text.'</text>';
            $string .= "<settings>";

            $string .= !empty($item['num_samples']) ?  "<NumSamples>{$item['num_samples']}</NumSamples>" : '';
            $string .= !empty($item['num_ref_per_sample']) ?  "<NumRefPerSample>{$item['num_ref_per_sample']}</NumRefPerSample>" : '';
            $string .= !empty($item['num_samples_per_document']) ?  "<NumSamplesPerDocument>{$item['num_samples_per_document']}</NumSamplesPerDocument>" : '';
            $string .= !empty($item['compare_method']) ?  "<CompareMethod>{$item['compare_method']}</CompareMethod>" : '';
            $string .= !empty($item['num_words_i_shingle']) ?  "<NumWordsInShingle>{$item['num_words_i_shingle']}</NumWordsInShingle>" : '';
            $string .= !empty($item['ignore_citation']) ?  "<IgnoreCitation>{$item['ignore_citation']}</IgnoreCitation>" : '';
            $string .= !empty($item['uniqueness_threshold']) ?  "<UniquenessThreshold>{$item['uniqueness_threshold']}</UniquenessThreshold>" : '';
            $string .= !empty($item['self_uniq']) ?  "<SelfUniq>{$item['self_uniq']}</SelfUniq>" : '';

            $string .= '</settings></entry>';
        }

        $string .= '</root>';

        $str_length = strlen($string);
        $pad_length = ($str_length % 16 == 0) ? $str_length : ($str_length +  (16 - ($str_length % 16)));
        $string = str_pad($string, $pad_length, "\0", STR_PAD_RIGHT);

        $string = openssl_encrypt ($string, "AES-128-ECB", getenv('ETXT_KEY'), (OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING));

        $file_name = time().str_random(10).'.xml';
        File::put($this->filePath.$file_name, $string);

        if (!File::exists($this->filePath.$file_name)){
            return false;
        }
        return $file_name;
    }

    /**
     * @return mixed
     * Пинг сервера
     */
    private  function pingServer()
    {
        $ch = Curl::to($this->serverUrl)
                    ->withData(['try' => '1'])
                    ->post();
        return $ch;
    }

    /**
     * @param $ItemsToCheck
     * @param null $ignore_url
     * @param null $ignore_domain
     * @return array|bool|string
     */
    public function sendRequst($ItemsToCheck, $ignore_url = null, $ignore_domain = null)
    {
        $serverState = $this->pingServer();
        if ($serverState == false ){
            return ['error' => 'Server unsuccessfull ping'];
        }
        $filePath = $this->createXml($ItemsToCheck, $ignore_url, $ignore_domain);
        if ($filePath == false){
            return ['error' => 'File unsuccessfull create'];
        }
        $data = [
            'xmlUrl' => $this->localServer.$filePath,
            'xmlAnswerUrl' => $this->callbackUrl
        ];
        $ch = Curl::to($this->serverUrl)
            ->withData($data)
            ->post();
        if ($ch == false){
            return ['error' => 'Fail request'];
        }
        return $filePath;
    }

    /**
     * @param $answer
     * @return \SimpleXMLElement
     */
    public static function decodeAnswer($answer)
    {
        $req = str_replace(' ', '+', $answer);;
        $req = base64_decode($req);
        $req = openssl_decrypt($req, "AES-128-ECB", getenv('ETXT_KEY'), (OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING));
        $res = new \SimpleXMLElement($req);
        return $res;
    }

    /**
     * @param $xmlName
     * @return mixed
     */
    public static function pingServerAndStateFile($xmlName)
    {
        $name = base64_encode(@iconv('WINDOWS-1251', 'UTF-8//IGNORE', $xmlName ));
        $ch = Curl::to(getenv('ETXT_SERVER'))
            ->withData([
                'try' => '1',
                'xmlName ' => $name
            ])
            ->post();
        return $ch;
    }

    /**
     * @param $documentPath
     * @return bool|string|string[]|null
     */
    public static function documentParseToString($documentPath)
    {
        $docParser = new DocxHelper($documentPath);

        return $docParser->convertToText();
    }
}
