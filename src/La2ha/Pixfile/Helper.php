<?
namespace La2ha\Pixfile;
/**
 * Class Helper
 * @package La2ha\Pixfile
 */
class Helper
{
    /**
     * @param $str
     * @return string
     */
    function stringToAlias($str)
    {
        if (preg_match('/[^A-Za-z0-9_\-]/', $str)) {
            $str = translit($str);
            $str = preg_replace('/[^A-Za-z0-9_\-]/', '', $str);
        }

        return $str;
    }

    /**
     * @param $str
     * @return string
     */
    function translit($str)
    {
        return strtr($str, \Config::get('pixfile::filenameTranslit'));
    }

}