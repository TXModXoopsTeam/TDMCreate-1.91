<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * tdmcreate module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         tdmcreate
 * @since           2.5.0
 * @author          Txmod Xoops http://www.txmodxoops.org
 * @version         $Id: TDMCreateStructure.php 12258 2014-01-02 09:33:29Z timgno $
 */
defined('XOOPS_ROOT_PATH') or die('Restricted access');
/**
 * Class TDMCreateStructure
 */
class TDMCreateStructure
{	
	/*
    * @var mixed
    */
    private $xoopsFile;
    /*
    * @var string
    */
    private $moduleName;
    /*
    * @var string
    */
    private $folderName;
    /*
    * @var string
    */
    private $fileName;
    /*
    * @var string
    */
    private $path;
    /*
    * @var mixed
    */
    private $uploadPath;

    /*
    *  @public function constructor class
    *  @param string $path
    */
    /**
     *
     */
    public function __construct()
    {
        //parent::__construct();
		$this->xoopsFile = XoopsFile::getInstance();
    }

    /*
    *  @static function &getInstance
    *  @param null
    */
    /**
     * @return TDMCreateStructure
     */
    public static function &getInstance()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    } 
    
    /*
    *  @protected function setUploadPath
    *  @param string $path
    */
    /**
     * @param $path
     */
    protected function setUploadPath($path)
    {
        $this->uploadPath = $path;
    }

    /*
    *  @protected function getUploadPath
    *  @return string $path
    */
    /**
     * @return null
     */
    protected function getUploadPath()
    {
        return $this->uploadPath;
    }

    /*
    *  @protected function setModuleName
    *  @param string $moduleName
    */
    /**
     * @param $moduleName
     */
    protected function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
    }

    /*
    *  @protected function getModuleName
    *  @return string $moduleName
    */
    /**
     * @return null
     */
    protected function getModuleName()
    {
        return $this->moduleName;
    }

    /*
    *  @private function setFolderName
    *  @param string $folderName
    */
    /**
     * @param $folderName
     */
    private function setFolderName($folderName)
    {
        $this->folderName = $folderName;
    }

    /*
    *  @private function getFolderName
    *  @return string $folderName
    */
    /**
     * @return null
     */
    private function getFolderName()
    {
        return $this->folderName;
    }

    /*
    *  @private function setFileName
    *  @param string $fileName
    */
    /**
     * @param $fileName
     */
    private function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /*
    *  @private function getFileName
    *  @return string $fileName
    */
    /**
     * @return null
     */
    private function getFileName()
    {
        return $this->fileName;
    }

    /*
    *  @private function isDir
    *  @param string $dname
    */
    /**
     * @param $dname
     */
    private function isDir($dname)
    {
        if (!is_dir($dname)) {
            mkdir($dname, 0755);
            chmod($dname, 0755);
        } else {
            chmod($dname, 0755);
        }
    }

    /*
    *  @protected function makeDir
    *  @param string $dir
    */
    /**
     * @param $dir
     */
    protected function makeDir($dir)
    {
        $this->isDir(strtolower(trim($dir)));
    }

    /*
    *  @public function addFolderPath
    *  @param string $folderName
    *  @param string $fileName
    */
    /**
     * @param      $folderName
     * @param bool $fileName
     * @return string
     */
    private function addFolderPath($folderName, $fileName = false)
    {
        $this->setFolderName($folderName);
        if ($fileName) {
            $this->setFileName($fileName);
            $ret = $this->getUploadPath() . DS . $this->getModuleName() . DS . $this->getFolderName() . DS . $this->getFileName();
        } else {
            $ret = $this->getUploadPath() . DS . $this->getModuleName() . DS . $this->getFolderName();
        }

        return $ret;
    }

    /*
    *  @public function makeDirInModule
    *  @param string $dirName
    */
    /**
     * @param $dirName
     */
    public function makeDirInModule($dirName)
    {
        $fname = $this->addFolderPath($dirName);
        $this->makeDir($fname);
    }

    /*
    *  @public function makeDir & copy file
    *  @param string $folderName
    *  @param string $fromFile
    *  @param string $toFile
    */
    /**
     * @param $folderName
     * @param $fromFile
     * @param $toFile
     */
    public function makeDirAndCopyFile($folderName, $fromFile, $toFile)
    {
        $dname = $this->addFolderPath($folderName);
        $this->makeDir($dname);
        $this->copyFile($folderName, $fromFile, $toFile);
    }

    /*
    *  @public function copy file
    *  @param string $folderName
    *  @param string $fromFile
    *  @param string $toFile
    */
    /**
     * @param $folderName
     * @param $fromFile
     * @param $toFile
     */
    public function copyFile($folderName, $fromFile, $toFile)
    {
        $dname = $this->addFolderPath($folderName);
        $fname = $this->addFolderPath($folderName, $toFile);
        $this->setCopy($dname, $fromFile, $fname);
    }

    /*
    *  @public function setCopy
    *  @param string $dname
    *  @param string $fname
    */
    /**
     * @param $dname
     * @param $fromFile
     * @param $fname
     */
    public function setCopy($dname, $fromFile, $fname)
    {
        if (is_dir($dname)) {
            chmod($dname, 0777);
            copy($fromFile, $fname);
        } else {
            $this->makeDir($dname);
            copy($fromFile, $fname);
        }
    }
}
