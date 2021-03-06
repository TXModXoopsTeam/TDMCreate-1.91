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
 * @version         $Id: UserPrint.php 12258 2014-01-02 09:33:29Z timgno $
 */
defined('XOOPS_ROOT_PATH') or die('Restricted access');

/**
 * Class UserPrint
 */
class UserPrint extends TDMCreateFile
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
     * @return UserPrint
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
    *  @param mixed $table
    *  @param string $filename
    */
    /**
     * @param $module
     * @param $table
     * @param $filename
     */
    public function write($module, $table, $filename)
    {
        $this->setModule($module);
        $this->setTable($table);
        $this->setFileName($filename);
    }

    /*
    *  @public function getUserPrint
    *  @param string $moduleDirname
    *  @param string $language
    */
    /**
     * @param $moduleDirname
     * @param $language
     * @return string
     */
    public function getUserPrint($moduleDirname, $language)
    {
        $stuModuleDirname = strtoupper($moduleDirname);
        $table            = $this->getTable();
        $tableName        = $table->getVar('table_name');
        $ucfModuleDirname = ucfirst($moduleDirname);
        $ucfTableName     = ucfirst($tableName);
        $fields           = $this->tdmcfile->getTableFields($table->getVar('table_mid'), $table->getVar('table_id'));
        foreach (array_keys($fields) as $f) {
            $fieldName    = $fields[$f]->getVar('field_name');
            $rpFieldName  = $fieldName;
            if (strpos($fieldName, '_')) {
                $str = strpos($fieldName, '_');
                if ($str !== false) {
                    $rpFieldName = substr($fieldName, $str + 1, strlen($fieldName));
                }
            }
            $lpFieldName = substr($fieldName, 0, strpos($fieldName, '_'));
            if ((0 == $f) && (1 == $this->table->getVar('table_autoincrement'))) {
                $fieldId = $fieldName;
            } else {
                if (1 == $fields[$f]->getVar('field_main')) {
                    $fieldMain = $fieldName; // fieldMain = fields parameters main field
                }
            }
        }
        $stuLpFieldName = strtoupper($lpFieldName);
        $ret               = <<<EOT
\ninclude  __DIR__ . '/header.php';
{$lpFieldName} = isset(\$_GET['{$fieldId}']) ? (int) (\$_GET['{$fieldId}']) : 0;
if ( empty({$fieldId}) ) {
    redirect_header({$stuModuleDirname}_URL . '/index.php', 2, {$language}NO{$stuLpFieldName});
}
EOT;
        if ($fieldName == $lpFieldName . '_published') {
            $ret .= <<<EOT
// Verify that the article is published
{$lpFieldName} = new {$ucfModuleDirname}{$ucfTableName}({$fieldId});
// Not yet published
if ( {$lpFieldName}->getVar('{$lpFieldName}_published') == 0 || {$lpFieldName}->getVar('{$lpFieldName}_published') > time() ) {
    redirect_header({$stuModuleDirname}_URL . '/index.php', 2, {$language}NO{$stuLpFieldName});
    exit();
}
EOT;
        }
        if ($fieldName == 'published') {
            $ret .= <<<EOT
// Verify that the article is published
{$lpFieldName} = new {$ucfModuleDirname}{$ucfTableName}({$fieldId});
// Not yet published
if ( {$lpFieldName}->getVar('published') == 0 || {$lpFieldName}->getVar('published') > time() ) {
    redirect_header({$stuModuleDirname}_URL . '/index.php', 2, {$language}NO{$stuLpFieldName});
    exit();
}
EOT;
        }
        if ($fieldName == $lpFieldName . '_expired') {
            $ret .= <<<EOT
// Expired
if ( {$lpFieldName}->getVar('{$lpFieldName}_expired') != 0 && {$lpFieldName}->getVar('{$lpFieldName}_expired') < time() ) {
    redirect_header({$stuModuleDirname}_URL . '/index.php', 2, {$language}NO{$stuLpFieldName});
    exit();
}
EOT;
        }
        if ($fieldName == 'expired') {
            $ret .= <<<EOT
// Expired
if ( {$lpFieldName}->getVar('expired') != 0 && {$lpFieldName}->getVar('expired') < time() ) {
    redirect_header({$stuModuleDirname}_URL . '/index.php', 2, {$language}NO{$stuLpFieldName});
    exit();
}
EOT;
        }
        $ret .= <<<EOT

// Verify permissions
\$gperm_handler =& xoops_gethandler('groupperm');
if (is_object(\$xoopsUser)) {
    \$groups = \$xoopsUser->getGroups();
} else {
    \$groups = XOOPS_GROUP_ANONYMOUS;
}
if (!\$gperm_handler->checkRight('{$moduleDirname}_view', {$lpFieldName}->getVat('{$fieldId}'), \$groups, \$xoopsModule->getVar('mid'))) {
    redirect_header({$stuModuleDirname}_URL . '/index.php', 3, _NOPERM);
    exit();
}
EOT;

        return $ret;
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
        $module        = $this->getModule();
        $filename      = $this->getFileName();
        $moduleDirname = $module->getVar('mod_dirname');
        $language      = $this->getLanguage($moduleDirname, 'MA');
        $content       = $this->getHeaderFilesComments($module, $filename);
        $content .= $this->getUserPrint($moduleDirname, $language);
        $this->tdmcfile->create($moduleDirname, '/', $filename, $content, _AM_TDMCREATE_FILE_CREATED, _AM_TDMCREATE_FILE_NOTCREATED);

        return $this->tdmcfile->renderFile();
    }
}
