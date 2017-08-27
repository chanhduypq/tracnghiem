<?php
/**
 * Core
 *
 * This file is part of Core.
 *
 * Core is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Core is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Core.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category    Core
 * @package     Core_Translate
 * @copyright   Copyright 2008-2012 Core
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Core
 * @package     Core_Translate
 * @author      Core Core Team <core@onegatecommerce.com>
 */
class Core_Translate extends Zend_Translate
{
    /**
     * @var array of Core_Translate
     */
    private static $_instance = array();

    /**
     *
     * @var string
     */
    private $_module;

    /**
     * Current module
     *
     * @param  array $options
     */
    public function __construct($options = array())
    {

        $this->_module = $options['module'];

        $locale     = $this->getLocale();
        $filename   = $this->_getFileName($locale);

        if (!is_readable($filename)) {
            $locale     = 'vi_VN';
            $filename   = $this->_getFileName($locale);
        }

        // custom modules can be without translation for default locale
        if (!is_readable($filename)) {
            $filename = $this->_getSafeFileName();
        }        
        parent::__construct(array(
            'adapter'   => self::AN_CSV,
            'content'   => $filename,
            'locale'    => $locale,
            'delimiter' => ','
        ));
    }

    /**
     * Return instance of Core_Translate
     *
     * @param  string $module [optional]
     * @static
     * @return Core_Translate
     */
    public static function getInstance($module = 'Core')
    {
        if (false === isset(self::$_instance[$module])) {
            self::$_instance[$module] = new self(array(
                'module' => $module
            ));
        }
        return self::$_instance[$module];
    }

    /**
     * Return adapter with loaded translations
     * for current locale and module
     *
     * @return Zend_Translate_Adapter_Csv
     */
    public function getAdapter()
    {
        $locale     = $this->getLocale();
        $adapter    = parent::getAdapter();

        if (!$adapter->isAvailable($locale)) {
            $filename = $this->_getFileName($locale);           
            if (is_readable($filename)) {
                $adapter->addTranslation(array(
                    'content'   => $filename,
                    'locale'    => $locale
                ));
            }
        } else {
            $adapter->setLocale($locale);
        }

        return $adapter;
    }

    /**
     *
     * @param string $locale
     * @param string $module
     * @return string
     */
    protected function _getFileName($locale)
    {
        return APPLICATION_PATH
            . '/locale/'
            . $locale
            . '/' . $this->_module . '.csv';
    }

    /**
     * Returns the existing translation filename.
     * Used when no translation file is found.
     *
     * @param string $locale
     * @param string $module
     * @return string
     */
    protected function _getSafeFileName()
    {
        return APPLICATION_PATH . '/locale/vi_VN/Core.csv';
    }

    /**
     * Translates given text
     *
     * @param array $args
     * @return string
     */
    public function translate(array $args)
    {
        $text = array_shift($args);

        $translated = $this->getAdapter()->translate($text);
        if (!count($args)) {
            return $translated;
        }       
        return @vsprintf($translated, $args);
    }

    /**
     * Translates given text
     *
     * @return string
     */
    public function __()
    {
        return $this->translate(func_get_args());
    }

    /**
     * Retrieve the code of current Core locale
     *
     * @return string
     */
    public function getLocale()
    {
        return Core_Locale::getLocale()->toString();
    }

    /**
     * Add new taransllate (key => value )to localization
     *
     * @param string $text
     * @param string $module
     * @param string $locale
     * @return bool
     */

    public function writeTranslationToFile($text, $module)
    {
        $filename = $this->_getFileName($this->getLocale());

        if (!is_readable($filename)) {
            $dir = dirname($filename);
            if (!is_readable($dir)) {
                mkdir($dir, 0777, true);
            }
            if (!is_writable($dir) && @chmod($dir  , 0777)) {
                Core::message()->addError(
                    'Can\'t write to folder "' . $dir . '". Permission denied'
                );
                Core::message()->addNotice(
                   'Workaround: >chmod -R 0777 [root_path]/app/locale'
                );
                return false;
            }
            touch($filename);
            chmod($filename, 0777);
        }

        if (!$file = @fopen($filename, 'a')) {
            throw new Core_Exception(
                'Error writing translation file \'' . $filename . '\'.'
            );
        }

        fwrite($file, "\n\"{$text}\",\"{$text}\"");
        fclose($file);

        return true;
    }
}