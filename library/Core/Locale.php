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
 * @package     Core_Locale
 * @copyright   Copyright 2008-2012 Core
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Core
 * @package     Core_Locale
 * @author      Core Core Team <core@onegatecommerce.com>
 */
class Core_Locale
{
    const DEFAULT_LOCALE    = 'vi_VN';
    const DEFAULT_CURRENCY  = 'Đồng';
    const DEFAULT_DATE  = 'dd/MM/yyyy';
    const DEFAULT_TIMEZONE  = 'Asia/Ho_Chi_Minh';

    /**
     * Set locale and language if possible
     *
     * @static
     * @param string (locale or language) $locale
     */
    public static function setLocale($locale = 'auto')
    {
        if (Zend_Registry::isRegistered('Zend_Locale')) {
            $currentLocale = Zend_Registry::get('Zend_Locale');
            if ($locale === $currentLocale->toString()) {
                return;
            }
        }
        $session = Core::session();

        if (!strstr($locale, '_')) {
            $locale = self::_getLocaleFromLanguageCode($locale);
        }

        if (Zend_Registry::isRegistered('Zend_Locale')) {
            $currentLocale = Zend_Registry::get('Zend_Locale');
            $currentLocale->setLocale($locale);
        } else {
            try {
                $currentLocale = new Zend_Locale($locale);
            } catch (Zend_Locale_Exception $e) {
                $currentLocale = new Zend_Locale(self::DEFAULT_LOCALE);
            }
            Zend_Locale::setCache(Core::cache());
            Zend_Registry::set('Zend_Locale', $currentLocale);
        }
       $session->locale = $locale;
        self::setTimezone();
    }

    /**
     * Retrieve first suitable locale with language
     *
     * @static
     * @param string $code Language ISO code
     * @return string Locale ISO code
     */
    private static function _getLocaleFromLanguageCode($code)
    {
        if (!empty($code)) {
            $localeList = self::getLocaleList(true);
            foreach ($localeList as $locale) {
                if (strpos($locale, $code) === 0) {
                    return $locale;
                }
            }
        }

        return self::DEFAULT_LOCALE;
    }

    /**
     * Retrieve locale object
     *
     * @static
     * @return Zend_Locale
     */
    public static function getLocale()
    {
        if (!Zend_Registry::isRegistered('Zend_Locale')) {
                self::setLocale(self::DEFAULT_LOCALE);
        }
        return Zend_Registry::get('Zend_Locale');
    }

    /**
     * Retrieve default locale from config
     *
     * @static
     * @return string Locale ISO code
     */
    public static function getDefaultLocale()
    {
        return self::DEFAULT_LOCALE;
    }

    /**
     * Returns a list of all known locales, or all installed locales
     *
     * @param $installedOnly bool
     * @static
     * @return array
     */
    public static function getLocaleList($installedOnly = false)
    {
        if (!$installedOnly) {
            return array_keys(Zend_Locale::getLocaleList());
        }
        return array();
    }

    /**
     * @static
     * @return array
     */
    public static function getInstallLocaleList()
    {
        $options = array();

        $locales = Zend_Locale::getLocaleList();
        $languages = Zend_Locale::getTranslationList('language', self::getLocale());
        $countries = Zend_Locale::getTranslationList('territory', self::getLocale(), 2);

        foreach ($locales as $code => $is_active) {
            if (strstr($code, '_')) {
                $data = explode('_', $code);
                if (!isset($languages[$data[0]]) || !isset($countries[$data[1]])) {
                    continue;
                }
                $options[$code] = ucfirst($languages[$data[0]]) . ' (' . $countries[$data[1]] . ')';
            }
        }
        return $options;
    }

    /**
     * Retrieve languageId from session;
     *
     * @static
     * @return int
     */
    public static function getLanguageId()
    {
        if (!isset(Core::session()->language)) {
            Core::session()->language = 1;
        }
        return Core::session()->language;
    }

    /**
     * Retrieve part of url, responsible for locale
     *
     * @static
     * @param string $locale Locale ISO code
     * @return string Part of url ('/uk')
     */
    public static function getLanguageUrl($locale = null)
    {
        if (null !== $locale) {
            list($language) = explode('_', $locale);
        } else {
            $language = self::getLocale()->getLanguage();
            $locale   = self::getLocale()->toString();
        }

        if ($locale == self::getDefaultLocale()) {
            return '';
        }
        if ($locale == self::_getLocaleFromLanguageCode($language)) {
            return '/' . $language;
        }

        return '/' . $locale;
    }

    /**
     * get default store timezone
     *
     * @static
     * @return  string  example : "Australia/Hobart"
     */
    public static function getDefaultTimezone()
    {
        return self::DEFAULT_TIMEZONE;
    }

    /**
     * get timezone
     *
     * @static
     * @return string
     */
    public static function getTimezone()
    {
        return date_default_timezone_get();
    }

    /**
     * set timezone
     *
     * @static
     * @param mixed void|string
     * @return bool
     */
    public static function setTimezone($timezone = null)
    {
        if (null === $timezone) {
            $timezone = self::DEFAULT_TIMEZONE;
        }
        if (@date_default_timezone_set($timezone)) {
            return true;
        }
        return @date_default_timezone_set(self::DEFAULT_TIMEZONE);
    }

    /**
     * Retrieve the list of available admin intrerface tranlations
     *
     * @static
     * @return array
     */
    public static function getAdminLocales()
    {
        if (!$locales = Core::cache()->load('locales_list')) {
            $path = APPLICATION_PATH . '/locale';

            try {
                $locales_dir = new DirectoryIterator($path);
            } catch (Exception $e) {
                throw new Core_Exception("Directory $path not readable");
            }

            $locale = Core_Locale::getLocale();
            $locales = array();

            foreach ($locales_dir as $localeDir) {
                $localeCode = $localeDir->getFilename();
                if ($localeCode[0] == '.' || !strstr($localeCode, '_')) {
                    continue;
                }
                list($language, $country) = explode('_', $localeCode, 2);

                $language = $locale->getTranslation($language, 'language', $localeCode);
                $country = $locale->getTranslation($country, 'country', $localeCode);
                if (!$language) {
                    $language = $locale->getTranslation(
                        $language, 'language', Core_Locale::DEFAULT_LOCALE
                    );
                }
                if (!$country) {
                    $country = $locale->getTranslation(
                        $country, 'country', Core_Locale::DEFAULT_LOCALE
                    );
                }
                $locales[$localeCode] = ucfirst($language) . ' (' . $country . ')';
            }
            ksort($locales);
            Core::cache()->save($locales, 'locales_list', array('locales'));
        }
        return $locales;
    }

    /**
     * Retrieve array of available translations
     *
     * @return array
     */
    public static function getAvailableLocales()
    {
         $path = APPLICATION_PATH . '/locale/';

        try {
            $localeDir = new DirectoryIterator($path);
        } catch (Exception $e) {
            throw new Core_Exception("Directory $path not readable");
        }

        $currentLocale = self::getLocale();
        $locales = array();

        foreach ($localeDir as $locale) {
            if ($locale->isDot() || !$locale->isDir()) {
                continue;
            }
            $localeName = $locale->getFilename();
            list($language, $country) = explode('_', $localeName, 2);

            $language = $currentLocale->getTranslation($language, 'language', $localeName);
            $country = $currentLocale->getTranslation($country, 'country', $localeName);
            if (!$language) {
                $language = $currentLocale->getTranslation($language, 'language', 'en_US');
            }
            if (!$country) {
                $country = $currentLocale->getTranslation($country, 'country', 'en_US');
            }
            $locales[$localeName] = ucfirst($language) . ' (' . $country . ')';
        }
        ksort($locales);

        return $locales;
    }

    /**
     *
     * @static
     * @return array
     */
    public static function getTimeZoneList()
    {
        $options = array();
        $zones = Zend_Locale::getTranslationList('WindowsToTimezone', self::getLocale());

        ksort($zones);
        foreach ($zones as $code => $name) {
            $name = trim($name);

            $windowTimezone = explode('/', $code);
            $label = $windowTimezone[1];
            if (!empty($name)) {
                $label .= ' (' . $name . ')';
            }
            $options[$windowTimezone[0]][$code] = $label;
        }
        return $options;
    }

    /**
     * @static
     * @return array
     */
    public static function getCurrencyList()
    {
        return Zend_Locale::getTranslationList('NameToCurrency', self::getLocale());
    }

    /**
     * Returns number from string
     *
     * @param string $value
     * @return float
     */
    public static function getNumber($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_string($value)) {
            return floatval($value);
        }

        $value = str_replace('\'', '', $value);
        $value = str_replace(' ', '', $value);
        $value = str_replace(',', '.', $value);

        return floatval($value);
    }
    /**
     * 
     */
    public static function getDateFormat(){
    	return  self::DEFAULT_DATE;
    }
}
