<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class PHPWord_Template
{

    private $_documentXML;

    private $_XmlFile;

    private $_Zipper;

    private $_StorageFile;

    private $_StoragePath;

    /**
     * Create a new Template Object
     * @param string $strFilename
     */
    public function __construct($strFilename)
    {

        $zipper = new \Chumper\Zipper\Zipper();

        $templateFile = $strFilename;
        $this->_StoragePath = storage_path('docxReplace');
        $dirName = str_random(20);

        mkdir($this->_StoragePath . '/' . $dirName,0777);

        $this->_StoragePath = $this->_StoragePath . '/' . $dirName;
        $this->_StorageFile = $this->_StoragePath . '/' . basename($strFilename);

        File::copy($templateFile, $this->_StorageFile);

        $xmlFile = $this->_StoragePath . '/word/document.xml';
        $zipper->make($this->_StorageFile)->extractTo($this->_StoragePath, ['word/document.xml'],\Chumper\Zipper\Zipper::WHITELIST);
        $this->_XmlFile = $xmlFile;
        $this->_Zipper = $zipper;
        $this->_documentXML = file_get_contents($xmlFile);

    }

    /**
     * Set a Template value
     * @param mixed $search
     * @param mixed $replace
     */
    public function setValue($search, $replace)
    {

        $search = '${'.$search.'}';

        $this->_documentXML = str_replace($search, $replace, $this->_documentXML);

    }

    /**
     * Clone a table row
     * @param mixed $search
     * @param mixed $numberOfClones
     */
    public function cloneRow($search, $numberOfClones)
    {

        $search = '${'.$search.'}';

        $tagPos 	 = strpos($this->_documentXML, $search);
        $rowStartPos = strrpos($this->_documentXML, "<w:tr", ((strlen($this->_documentXML) - $tagPos) * -1));
        $rowEndPos   = strrpos($this->_documentXML, "</w:tr>");

        $xmlStart = substr($this->_documentXML,0,$rowStartPos);
        $xmlEnd   = substr($this->_documentXML,$rowEndPos+7);

        $xmlRow = substr($this->_documentXML,$rowStartPos,($rowEndPos+7 - $rowStartPos));

        for ($i = 1; $i <= $numberOfClones; $i++)
        {
            $xmlStart .= preg_replace('/\$\{(.*?)\}/','\${\\1#'.$i.'}', $xmlRow);
        }
        $xmlStart .= $xmlEnd;

        $this->_documentXML = $xmlStart;

    }

    /**
     * Save Template
     * @param string $strFilename
     */
    public function save()
    {

        file_put_contents($this->_XmlFile, $this->_documentXML);
        $this->_Zipper->folder('word')->add($this->_XmlFile);
        $this->_Zipper->close();

        $fileNewName = storage_path('docxReplace/' . str_random(20) . '.docx');
        File::copy($this->_StorageFile, $fileNewName);

        File::deleteDirectory($this->_StoragePath);

        return $fileNewName;

    }

}
