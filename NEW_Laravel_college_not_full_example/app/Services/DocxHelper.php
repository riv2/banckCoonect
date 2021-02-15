<?php


namespace App\Services;
use Illuminate\Support\Facades\File;

class DocxHelper
{
    private $filename;

    /**
     * DocxHelper constructor.
     * @param $filePath
     */
    public function __construct($filePath) {
        $this->filename = $filePath;
    }

    /**
     * @return string|string[]|null
     */
    private function read_doc()	{
        $fileHandle = fopen($this->filename, "r");
        $line = @fread($fileHandle, filesize($this->filename));
        $lines = explode(chr(0x0D),$line);
        $outtext = "";
        foreach($lines as $thisline)
        {
            $pos = strpos($thisline, chr(0x00));
            if (($pos !== FALSE)||(strlen($thisline)==0))
            {
            } else {
                $outtext .= $thisline." ";
            }
        }
        $outtext = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","",$outtext);
        return $outtext;
    }

    /**
     * @return bool|string
     */
    private function read_docx(){

        $striped_content = '';
        $content = '';

        $zip = zip_open($this->filename);

        if (!$zip || is_numeric($zip)) return false;

        while ($zip_entry = zip_read($zip)) {

            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

            if (zip_entry_name($zip_entry) != "word/document.xml") continue;

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            zip_entry_close($zip_entry);
        }// end while

        zip_close($zip);

        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);

        return $striped_content;
    }

    /**
     * @return bool|string|string[]|null
     */
    public function convertToText() {

        if(isset($this->filename) && !file_exists($this->filename)) {
            return new Exception("File Not exists");
        }

        $fileArray = pathinfo($this->filename);
        $file_ext  = $fileArray['extension'];
        if($file_ext == "doc" || $file_ext == "docx")
        {
            if($file_ext == "doc") {
                return $this->read_doc();
            } else {
                return $this->read_docx();
            }
        } else {
            return new Exception("Invalid File Type");
        }
    }
    /**
     * @param $fileName
     * @param $replaceArray
     * @return string
     * @throws \Exception
     */
    static function replace($fileName, $replaceArray, $format = 'docx')
    {
        $zipper = new \Chumper\Zipper\Zipper();

        $templateFile = $fileName;
        $storagePatch = storage_path('docxReplace');
        $dirName = str_random(20);

        mkdir($storagePatch . '/' . $dirName,0777);

        $storagePatch = $storagePatch . '/' . $dirName;
        $storageFile = $storagePatch . '/' . basename($fileName);

        File::copy($templateFile, $storageFile);

        $xmlFile = $storagePatch . '/word/document.xml';
        $zipper->make($storageFile)->extractTo($storagePatch, ['word/document.xml'],\Chumper\Zipper\Zipper::WHITELIST);
        $content = file_get_contents($xmlFile);

        foreach($replaceArray as $key => $val)
        {
            $content = str_replace($key, $val, $content);
        }

        file_put_contents($xmlFile, $content);
        $zipper->folder('word')->add($xmlFile);
        $zipper->close();

        if($format == 'docx') {
            $fileNewName = storage_path('docxReplace/' . str_random(20) . '.docx');
            File::copy($storageFile, $fileNewName);
        }

        if($format == 'pdf') {
            $fileNewName = storage_path('docxReplace/' . str_random(20) . '.pdf');
            self::SaveAsPdf($storageFile, $storagePatch);
            File::copy($storagePatch . '/' . str_replace('.docx', '.pdf', basename($fileName)), $fileNewName);
        }

        File::deleteDirectory($storagePatch);

        return $fileNewName;
    }

    /**
     * @param $inFilename
     * @param $outFilename
     * @return bool
     */
    static function SaveAsPdf($inFilename, $outDir)
    {
        //convert to pdf with libre office
        $process = new \Symfony\Component\Process\Process([
            'soffice',
            '--headless',
            '--convert-to',
            'pdf',
            $inFilename,
            '--outdir',
            $outDir,
        ]);
        $process->start();
        while ($process->isRunning()) {
            //wait until process is ready
        }
        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new \Symfony\Component\Process\Exception\ProcessFailedException($process);
        } else {
            return true;
        }
    }
}