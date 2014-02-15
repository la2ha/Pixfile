<?
namespace La2ha\Pixfile;
class FileInfo
{
    public $origname;
    public $name;
    public $dirpath;
    public $filepath;
    public $webpatch;
    public $basepatch;
    public $size;
    public $sizeKb;
    public $sizeMb;
    public $mime;

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

    protected function setSize($bytes)
    {
        $this->size   = $bytes;
        $this->sizeKb = round($bytes / 1024, 2);
        $this->sizeMb = round($this->sizeKb / 1024, 2);
    }

    protected function setWebPatch($filepath)
    {
        $this->webpatch  = mb_substr($filepath, mb_strlen(\Config::get('pixfile::webpatchCleaner')));
        $this->basepatch = mb_substr($filepath, mb_strlen(\Config::get('pixfile::basepatchCleaner')));

    }

    protected function setMime($filepath)
    {
        $this->mime = mime_content_type($filepath);
    }

    public function __toString()
    {
        $param = \Config::get('pixfile::toString');
        return (string)$this->$param;
    }

}