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
 * @version         $Id: 1.91 ClassHelper.php 12258 2014-01-02 09:33:29Z timgno $
 */
defined('XOOPS_ROOT_PATH') or die('Restricted access');

/**
 * Class ClassHelper
 */
class ClassHelper extends TDMCreateFile
{
    /*
    *  @public function constructor
    *  @param null
    */
    /**
     *
     */
    public function __construct()
    {
        $this->tdmcfile = TDMCreateFile::getInstance();
    }

    /*
    *  @static function &getInstance
    *  @param null
    */
    /**
     * @return ClassHelper
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
    *  @public function write
    *  @param string $module
    *  @param string $filename
    */
    /**
     * @param $module
     * @param $filename
     */
    public function write($module, $filename)
    {
        $this->setModule($module);
        $this->setFileName($filename);
    }

    /*
    *  @public function render
    *  @param null
    */
    /**
     * @return bool|string
     */
    public function render()
    {
        $module             = $this->getModule();
        $filename           = $this->getFileName();
        $module_dirname     = $module->getVar('mod_dirname');
        $ucf_module_dirname = ucfirst($module_dirname);
        $content            = $this->getHeaderFilesComments($module, $filename);
        $content .= <<<EOT
defined('XOOPS_ROOT_PATH') or die('Restricted access');

class {$ucf_module_dirname}Helper
{
    /**
     * @var string
     */
    private \$dirname = null;
    /**
     * @var string
     */
    private \$module = null;
    /**
     * @var string
     */
    private \$handler = null;
    /**
     * @var string
     */
    private \$config = null;
    /**
     * @var string
     */
    private \$debug = null;
    /**
     * @var array
     */
    private \$debugArray = array();
    /*
    *  @protected function constructor class
    *  @param mixed \$debug
    */
    public function __construct(\$debug)
    {
        \$this->debug = \$debug;
        \$this->dirname =  basename(dirname(__DIR__));
    }
    /*
    *  @static function &getInstance
    *  @param mixed \$debug
    */
    public static function &getInstance(\$debug = false)
    {
        static \$instance = false;
        if (!\$instance) {
            \$instance = new self(\$debug);
        }
        return \$instance;
    }
    /*
    *  @static function getModule
    *  @param null
    */
    public function &getModule()
    {
        if (\$this->module == null) {
            \$this->initModule();
        }
        return \$this->module;
    }
    /*
    *  @static function getConfig
    *  @param string \$name
    */
    public function getConfig(\$name = null)
    {
        if (\$this->config == null) {
            \$this->initConfig();
        }
        if (!\$name) {
            \$this->addLog("Getting all config");
            return \$this->config;
        }
        if (!isset(\$this->config[\$name])) {
            \$this->addLog("ERROR :: CONFIG '{\$name}' does not exist");
            return null;
        }
        \$this->addLog("Getting config '{\$name}' : " . \$this->config[\$name]);
        return \$this->config[\$name];
    }
    /*
    *  @static function setConfig
    *  @param string \$name
    *  @param mixed \$value
    */
    public function setConfig(\$name = null, \$value = null)
    {
        if (\$this->config == null) {
            \$this->initConfig();
        }
        \$this->config[\$name] = \$value;
        \$this->addLog("Setting config '{\$name}' : " . \$this->config[\$name]);
        return \$this->config[\$name];
    }
    /*
    *  @static function getHandler
    *  @param string \$name
    */
    public function &getHandler(\$name)
    {
        if (!isset(\$this->handler[\$name . '_handler'])) {
            \$this->initHandler(\$name);
        }
        \$this->addLog("Getting handler '{\$name}'");
        return \$this->handler[\$name . '_handler'];
    }
    /*
    *  @static function initModule
    *  @param null
    */
    public function initModule()
    {
        global \$xoopsModule;
        if (isset(\$xoopsModule) && is_object(\$xoopsModule) && \$xoopsModule->getVar('dirname') == \$this->dirname) {
            \$this->module = \$xoopsModule;
        } else {
            \$hModule = xoops_gethandler('module');
            \$this->module = \$hModule->getByDirname(\$this->dirname);
        }
        \$this->addLog('INIT MODULE');
    }
    /*
    *  @static function initConfig
    *  @param null
    */
    public function initConfig()
    {
        \$this->addLog('INIT CONFIG');
        \$hModConfig = xoops_gethandler('config');
        \$this->config = \$hModConfig->getConfigsByCat(0, \$this->getModule()->getVar('mid'));
    }
    /*
    *  @static function initHandler
    *  @param string \$name
    */
    public function initHandler(\$name)
    {
        \$this->addLog('INIT ' . \$name . ' HANDLER');
        \$this->handler[\$name . '_handler'] = xoops_getModuleHandler(\$name, \$this->dirname);
    }
    /*
    *  @static function addLog
    *  @param string \$log
    */
    public function addLog(\$log)
    {
        if (\$this->debug) {
            if (is_object(\$GLOBALS['xoopsLogger'])) {
                \$GLOBALS['xoopsLogger']->addExtra(\$this->module->name(), \$log);
            }
        }
    }
}
EOT;
        $this->tdmcfile->create($module_dirname, 'class', $filename, $content, _AM_TDMCREATE_FILE_CREATED, _AM_TDMCREATE_FILE_NOTCREATED);

        return $this->tdmcfile->renderFile();
    }
}
