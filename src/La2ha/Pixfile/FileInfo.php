<?
namespace La2ha\Pixfile;
/**
 * Class FileInfo
 * @package La2ha\Pixfile
 */
class FileInfo
{
    /**
     * @var
     */
    public $origname;
    /**
     * @var
     */
    public $name;
    /**
     * @var
     */
    public $dirpath;
    /**
     * @var
     */
    public $filepath;
    /**
     * @var
     */
    public $webpatch;
    /**
     * @var
     */
    public $basepatch;
    /**
     * @var
     */
    public $size;
    /**
     * @var
     */
    public $sizeKb;
    /**
     * @var
     */
    public $sizeMb;
    /**
     * @var
     */
    public $mime;

    /**
     * @param $data
     */
    function __construct($data)
    {
        $this->origname = $data['origname'];
        $this->name     = $data['name'];
        $this->dirpath  = $data['dirpath'];
        $this->filepath = $data['filepath'];
        $this->setSize($data['size']);
        $this->setWebPatch($data['filepath']);
        $this->setMime($data['filepath']);
    }

    /**
     * @param $bytes
     */
    protected function setSize($bytes)
    {
        $this->size   = $bytes;
        $this->sizeKb = round($bytes / 1024, 2);
        $this->sizeMb = round($this->sizeKb / 1024, 2);
    }

    /**
     * @param $filepath
     */
    protected function setWebPatch($filepath)
    {
        $this->webpatch  = mb_substr($filepath, mb_strlen(\Config::get('pixfile::webpatchCleaner')));
        $this->basepatch = mb_substr($filepath, mb_strlen(\Config::get('pixfile::basepatchCleaner')));

    }

    /**
     * @param $filepath
     */
    protected function setMime($filepath)
    {
        $this->mime = mime_content_type($filepath);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $param = \Config::get('pixfile::toString');
        return (string)$this->$param;
    }

}