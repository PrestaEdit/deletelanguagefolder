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

/* Security */
if (!defined('_PS_VERSION_')) {
    exit;
}

class DeleteLanguageFolder extends Module
{
    public function __construct()
    {
        $this->name = 'deletelanguagefolder';
        $this->tab = 'seo';
        $this->version = '1.0.6';
        $this->author = 'PrestaEdit';
        $this->ps_versions_compliancy['min'] = '1.6.0.1';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Delete Language Folder');
        $this->description = $this->l('Delete the default language folder in the URL');
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        $default_iso = Configuration::get('PS_LANG_DEFAULT');
        if (!Validate::isLanguageIsoCode($default_iso)) {
            $default_iso = 'fr';
        }
        if (!Configuration::updateValue('DLF_FOLDER_NAME', $default_iso)) {
            return false;
        }

        return true;
    }

    public function getContent()
    {
        $this->_html = '';

        if (Tools::isSubmit('submitConfigure')) {
            $this->postProcess();
        }

        $languages = Language::getLanguages(false);
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = (int)$language['id_lang'] == Configuration::get('PS_LANG_DEFAULT');
        }

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->languages = $languages;
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = true;
        $helper->title = $this->displayName;
        $helper->submit_action = 'submitConfigure';

        $this->fields_form[0]['form'] = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs'
            ),
            'submit' => array(
                'name' => $helper->submit_action,
                'title' => $this->l('Save'),
                'class' => 'button btn btn-default'
            ),
            'input' => array(
                array(
                    'label' => $this->l('Language:'),
                    'name' => 'DLF_FOLDER_NAME',
                    'type' => 'select',
                    'identifier' => 'value',
                    'options' => array(
                        'query' => $languages,
                        'id' => 'iso_code',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Choose the main language of your shop.'),
                )
            )
        );

        $helper->fields_value = array(
            'DLF_FOLDER_NAME' => Configuration::get('DLF_FOLDER_NAME')
        );

        return $this->_html.$helper->generateForm($this->fields_form);
    }

    private function postProcess()
    {
        Configuration::updateValue('DLF_FOLDER_NAME', pSQL(Tools::getValue('DLF_FOLDER_NAME')));

        $this->_html = $this->displayConfirmation($this->l('Settings updated'));
    }
}
