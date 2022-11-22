<?php
/**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Tools extends ToolsCore
{
    public static function switchLanguage(Context $context = null)
    {
        if (!Module::isInstalled('deletelanguagefolder') || !Module::isEnabled('deletelanguagefolder')) {
            parent::switchLanguage($context);
        }

        if (!$context) {
            $context = Context::getContext();
        }

        // Install call the dispatcher and so the switchLanguage
        // Stop this method by checking the cookie
        if (!isset($context->cookie)) {
            return;
        }

        if (($iso = Tools::getValue('isolang')) && Validate::isLanguageIsoCode($iso) && ($id_lang = (int)Language::getIdByIso($iso))) {
            $_GET['id_lang'] = $id_lang;
        } else {
            $_GET['id_lang'] = (int)Language::getIdByIso(Configuration::get('DLF_FOLDER_NAME'));
        }

        // update language only if new id is different from old id
        // or if default language changed
        $cookie_id_lang = $context->cookie->id_lang;
        $configuration_id_lang = Configuration::get('PS_LANG_DEFAULT');
        if ((($id_lang = (int)Tools::getValue('id_lang')) && Validate::isUnsignedId($id_lang) && $cookie_id_lang != (int)$id_lang) || (($id_lang == $configuration_id_lang) && Validate::isUnsignedId($id_lang) && $id_lang != $cookie_id_lang)) {
            $context->cookie->id_lang = $id_lang;
            $language = new Language($id_lang);
            if (Validate::isLoadedObject($language)) {
                $context->language = $language;
            }

            $params = $_GET;
            if (Configuration::get('PS_REWRITING_SETTINGS') || !Language::isMultiLanguageActivated()) {
                unset($params['id_lang']);
            }
        }
    }
}
