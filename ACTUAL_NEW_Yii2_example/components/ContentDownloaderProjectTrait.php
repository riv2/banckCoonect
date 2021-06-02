<?php
namespace app\components;


use yii;

/**
 * Class ContentDownloaderProjectTrait
 * @package app\components
 *
 * @property string raw
 */
trait ContentDownloaderProjectTrait
{

    protected $_projectFileContents;

    /**
     * Маппинг тегов
     *
     * @var array
     */
    public $tagMapping = [
        'domain'        => 'CD_PARSING_RICH_F9_1',
        'cookies'       => 'F40_M_2',
        'proxy'         => 'F54_E_7',
        'proxies'       => 'F54_LB_1',
        'urls'          => 'CD_PARSING_LB_1',
        'script'        => 'CD_PARSING_EDIT_50_1',
        'cd_title'      => 'CD_PARSING_FORM_CA',
    ];

    /**
     * Маппинг параметров для подстановки в УРЛ скрипта прайсера который принимает спарсенные данные
     *
     * region_id=[DATAENCODE]регион[/DATAENCODE]
     *
     * Слева поля модели, справа параметры из урла в param=[DATAENCODE]*[/DATAENCODE]
     * @var array
     */
    public $scriptParamMapping = [
        'region_id'          => 'region_id',
        'source_id'          => 'source_id',
        'parsing_project_id' => 'parsing_project_id',
        'parsing_id'         => 'parsing_id',
    ];

    //public $cookies     = null;
    //public $proxies     = null;
    //public $urls        = null;
    //public $domain      = null;
    
    public $proxy       = null;
    public $script      = null;
    public $parsing_id  = null;
    public $cd_title    = null;

    public $templateWarnings = [];


    /**
     * @param string $file
     * @return string
     */
    public function loadFromFile($file){
        if (!$this->_projectFileContents) {
            if (file_exists($file)) {
                $this->_projectFileContents = file_get_contents($file);
            }
        }
        foreach ($this->tagMapping as $attribute => $tag) {
            $value = static::getTagValue($this->_projectFileContents, $tag);
            if ($value === false) {
                $this->templateWarnings[] = [
                    'message'   => "Отсутствует тег &lt;$tag&gt; необходимый для свойства [".$this->getAttributeLabel($attribute) ." ($attribute)]",
                    'attribute' => $attribute,
                    'tag'       => $tag,
                    'param'     => $tag,
                ];
            }
            if (property_exists($this, $attribute) ||  $this->hasAttribute($attribute)) {
                $this->$attribute = !empty($this->$attribute) ? $this->$attribute : $value;
            }
        }
        foreach ($this->scriptParamMapping as $attribute => $param) {
            $value = static::getScriptParamValue($this->_projectFileContents, $param);
            if ($value === false) {
                $this->templateWarnings[] = [
                    'message'   => "Отсутствует $param=[DATAENCODE][/DATAENCODE] для свойства [".$this->getAttributeLabel($attribute) ."] ($attribute)",
                    'attribute' => $attribute,
                    'tag'       => $param,
                    'param'     => $param,
                ];
            }
            if (property_exists($this, $attribute) ||  $this->hasAttribute($attribute)) {
                $this->$attribute = !empty($this->$attribute) ? $this->$attribute : $value;
            }
        }
        return $this->_projectFileContents;
    }

    /**
     * @param $file
     * @return mixed
     * @throws \Exception
     */
    public function saveToFile($file){
        if (empty($this->_projectFileContents)) {
            throw new \Exception("Empty CDP template");
        }
        foreach ($this->tagMapping as $attribute => $tag) {
            if (property_exists($this, $attribute) ||  $this->hasAttribute($attribute)) {
                $this->_projectFileContents = static::setTagValue($this->_projectFileContents, $tag, $this->$attribute);
            }
        }
        foreach ($this->scriptParamMapping as $attribute => $param) {
            if (property_exists($this, $attribute) ||  $this->hasAttribute($attribute)) {
                $this->_projectFileContents = static::setScriptParamValue($this->_projectFileContents, $param, $this->$attribute);
            }
        }
        file_put_contents($file, $this->_projectFileContents);
        return $this->_projectFileContents;
    }
    /**
     * @return string
     */
    public static function runningDir() {
        $dir = Yii::getAlias(Yii::$app->params['contentDownloader']['runningDir']);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }
    /**
     * @return string
     */
    public static function remoteProxyDir() {
        return Yii::$app->params['contentDownloader']['remoteProxyDir'];
    }
    /**
     * @return string
     */
    public static function proxyDir() {
        $dir =  Yii::getAlias(Yii::$app->params['contentDownloader']['proxyDir']);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }
    /**
     * @return string
     */
    public static function finishedDir() {
        $dir =  Yii::getAlias(Yii::$app->params['contentDownloader']['finishedDir']);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }
    /**
     * @return string
     */
    public static function queueDir() {
        $dir =  Yii::getAlias(Yii::$app->params['contentDownloader']['queueDir']);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }
    /**
     * @return string
     */
    public static function projectsDir() {
        $dir =  Yii::getAlias(Yii::$app->params['contentDownloader']['projectsDir']);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    /**
     * @param $filePath
     * @return string
     */
    public static function relativeProjectPath($filePath) {
        return str_ireplace(static::projectsDir().DIRECTORY_SEPARATOR, '', $filePath);
    }

    /**
     * @param  string $tag
     * @param  string $context
     * @return string
     */
    public static function getTagValue($context, $tag) {
        $tag     = preg_quote($tag);
        $pattern = "/<$tag>(.*)<\/$tag>/s";
        $matches = [];
        preg_match($pattern, $context, $matches);
        if (isset($matches[1])) {
            if (empty(trim($matches[1]))) {
                return null;
            }
            return $matches[1];
        }
        return false;
    }

    /**
     * @param  string $context
     * @param  string $tag
     * @param  string $value
     * @return string
     */
    public static function setTagValue($context, $tag, $value) {
        $tag     = preg_quote($tag);
        $pattern = "/(<$tag>).*?(<\/$tag>)/s";
        $result = preg_replace($pattern, '${1}'.$value.'${2}', $context);
        if ($result) {
            $context = $result;
        }
        return $context;
    }

    /**
     * @param  string $param
     * @param  string $context
     * @return string
     */
    public static function getScriptParamValue($context, $param) {
        $param     = preg_quote($param);
        $pattern = "/$param=\[DATAENCODE\](.*?)\[\/DATAENCODE\]/";
        $matches = [];
        preg_match($pattern, $context, $matches);
        if (isset($matches[1])) {
            if (empty(trim($matches[1]))) {
                return null;
            }
            return $matches[1];
        }
        return false;
    }

    /**
     * @param  string $context
     * @param  string $param
     * @param  string $value
     * @return string
     */
    public static function setScriptParamValue($context, $param, $value) {
        $param     = preg_quote($param);
        $pattern = "/($param=\[DATAENCODE\]).*?(\[\/DATAENCODE\])/";
        $result = preg_replace($pattern, '${1}'.$value.'${2}', $context, ($param == 'region_id') ? 1 : -1);
        if ($result) {
            $context = $result;
        }
        return $context;
    }


    public static function getProxyFiles() {
        $projectsDir    = static::proxyDir();
        $allFiles       = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($projectsDir));
        $result = ['' => 'Нет proxy-файла'];
        foreach ($allFiles as $proxyFile) {
            if ($proxyFile->getFilename() == '.' || $proxyFile->getFilename() == '..') continue;
            $result[self::remoteProxyDir().'\\'.$proxyFile->getFilename()] = $proxyFile->getFilename();
        }
        return $result;
    }

    public function getRaw() {
        return $this->_projectFileContents;
    }

}