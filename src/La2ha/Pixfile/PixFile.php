<?
namespace La2ha\Pixfile;
/**
 * Class PixFile
 * @package La2ha\Pixfile
 */
class PixFile
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var mixed
     */
    public $folder;

    /**
     * @var mixed
     */
    public $encrypt;

    /**
     * @var mixed
     */
    public $overwrite;

    /**
     * @param Helper $helper
     */
    function __construct(Helper $helper)
    {
        $this->helper    = $helper;
        $this->folder    = \Config::get('pixfile::folder');
        $this->encrypt   = \Config::get('pixfile::encrypt');
        $this->overwrite = \Config::get('pixfile::overwrite');

    }

    /**
     * @param null $dir
     * @param $name
     * @return FileInfo
     * @throws GetFileException
     */
    public function saveFile($dir = null, $name)
    {
        if (!\Input::hasFile($name))
            throw new GetFileException('Can`t get file');
        $file         = \Input::file($name);
        $ext          = $file->getClientOriginalExtension();
        $origFilename = $file->getClientOriginalName();
        try {
            $bin = file_get_contents($file->getPathname());
        } catch (\Exception $e) {
            throw new GetFileException($e->getMessage());
        }
        return $this->saveBin($bin, $dir, $origFilename, $ext);
    }

    /**
     * @param $dir
     * @param $image_url
     * @return FileInfo
     * @throws GetFileException
     */
    public function saveFromUrl($dir, $image_url)
    {
        $urlinfo  = parse_url($image_url);
        $filename = basename($urlinfo['path']); // имя файла узнаем
        try {
            $bin = file_get_contents($image_url);
        } catch (\Exception $e) {
            throw new GetFileException($e->getMessage());
        }
        if ($bin === FALSE)
            throw new GetFileException('Can`t download file');
        return $this->saveBin($bin, $dir, $filename);
    }

    /**
     * @param $bin
     * @param null $dir
     * @param null $origFilename
     * @param null $ext
     * @return FileInfo
     * @throws FwriteException
     * @throws EmptyBinException
     * @throws ExtException
     */
    public function saveBin($bin, $dir = null, $origFilename = null, $ext = null)
    {
        if (!$bin)
            throw new EmptyBinException('Empty bin data');
        if (!$origFilename and !$ext)
            throw new ExtException('Can not get an extension');
        $saveDir = $this->checkDir($dir);
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
                'size'     => $bytes,
            )
        );
        else throw new FwriteException('Can`t write to file');
    }

    /**
     * @param $path
     * @param null $filename
     * @param null $ext
     * @return mixed|null|string
     * @throws ExtException
     * @throws AttemptsException
     */
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
        if ($this->overwrite or !file_exists($path . $filename)) {
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

    /**
     * @param $dir
     * @return string
     */
    protected function checkDir($dir)
    {
        if ($dir and $dir{0} != DIRECTORY_SEPARATOR)
            $dir = DIRECTORY_SEPARATOR . $dir;
        $saveDir = $this->folder . $dir;
        if (!file_exists($saveDir))
            mkdir($saveDir, \Config::get('pixfile::mkdirMode'), true);
        return $saveDir;
    }


    /**
     * @param $folder
     * @return $this
     */
    public function  setFolder($folder)
    {
        $this->folder = $folder;
        return $this;
    }

    /**
     * @param $overwrite
     * @return $this
     */
    public function  setOverwrite($overwrite)
    {
        $this->overwrite = $overwrite;
        return $this;
    }
}