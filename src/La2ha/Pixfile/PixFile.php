<?
namespace La2ha\Pixfile;
class PixFile
{
    protected $helper;
    public $folder;
    public $encrypt;

    function __construct(Helper $helper)
    {
        $this->helper  = $helper;
        $this->folder  = \Config::get('pixfile::folder');
        $this->encrypt = \Config::get('pixfile::encrypt');

    }

    public function saveFromUrl($dir, $image_url)
    {
        $urlinfo  = parse_url($image_url);
        $filename = basename($urlinfo['path']); // имя файла узнаем
        $bin      = file_get_contents($image_url);
        if ($bin === FALSE)
            throw new GetFileException('Can`t download file');
        return $this->saveBin($bin, $dir, $filename);
    }

    public function saveBin($bin, $dir = null, $origFilename = null, $ext = null)
    {
        if (!$bin)
            throw new EmptyBinException('Empty bin data');
        if (!$origFilename and !$ext)
            throw new ExtException('Can not get an extension');
        if ($dir and $dir{0} != DIRECTORY_SEPARATOR)
            $dir = DIRECTORY_SEPARATOR . $dir;
        $saveDir = $this->folder . $dir;
        if (!file_exists($saveDir))
            mkdir($saveDir, \Config::get('pixfile::mkdirMode'), true);
        if ($origFilename)
            $origFilename = basename($origFilename);
        $filename = $this->checkFilename($saveDir, $origFilename, $ext);
        $filepath = $saveDir . DIRECTORY_SEPARATOR . $filename;
        $fh       = fopen($filepath, 'w');
        $bytes    = fwrite($fh, $bin);
        fclose($fh);
        if ($bytes !== FALSE) return new FileInfo(array(
                'origname' => $origFilename,
                'name'     => $filename,
                'dirpath'  => $saveDir,
                'filepath' => $filepath,
                'size' => $bytes,
            )
        );
        else throw new FwriteException('Can`t write to file');
    }

    public function checkFilename($path, $filename = null, $ext = null)
    {
        if (substr($path, -1) != DIRECTORY_SEPARATOR)
            $path = $path . DIRECTORY_SEPARATOR;

        if ($filename)
            $ext = '.' . strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        elseif ($ext and $ext{0} != '.')
            $ext = '.' . $ext;


        if (!$ext)
            throw new ExtException('Can not get an extension');

        if ($this->encrypt == true or !$filename) {
            mt_srand();
            $filename = md5(uniqid(mt_rand())) . $ext;
        } else {
            $file_name = mb_substr($filename, 0, mb_strlen($filename) - mb_strlen($ext));

            $new_filename = $this->helper->stringToAlias($file_name);
            $filename     = $new_filename . $ext;
        }
        if (!file_exists($path . $filename)) {
            return $filename;
        }
        $filename = str_replace($ext, '', $filename);

        $new_filename = '';
        for ($i = 1; $i < \Config::get('pixfile::incrementAttempts'); $i++) {
            if (!file_exists($path . $filename . $i . $ext)) {
                $new_filename = $filename . $i . $ext;
                break;
            }
        }
        if (!$new_filename)
            throw new AttemptsException('Ended attempts to get the file name. Try increase increment attempts in config file.');
        else
            return $new_filename;

    }


    public function  setFolder($folder)
    {
        $this->folder = $folder;
        return $this;
    }
}