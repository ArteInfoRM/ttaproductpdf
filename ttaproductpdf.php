<?php
/**
 *  2009-2026 Tecnoacquisti.com
 *
 *  For support feel free to contact us on our website at http://www.tecnoacquisti.com
 *
 *  @author    Arte e Informatica <helpdesk@tecnoacquisti.com>
 *  @copyright 2009-2026 Arte e Informatica
 *  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
 *  @version   1.0.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Ttaproductpdf extends Module
{
    // Config keys
    private const CFG_HEADER_LOGO = 'TTAPDF_HEADER_LOGO';
    private const CFG_HEADER_TEXT = 'TTAPDF_HEADER_TEXT';
    private const CFG_SHOW_TITLE = 'TTAPDF_SHOW_TITLE';
    private const CFG_SHOW_PRICE = 'TTAPDF_SHOW_PRICE';
    private const CFG_SHOW_REFERENCE = 'TTAPDF_SHOW_REFERENCE';
    private const CFG_SHOW_EAN = 'TTAPDF_SHOW_EAN';
    private const CFG_SHOW_MPN = 'TTAPDF_SHOW_MPN';
    private const CFG_SHOW_BRAND = 'TTAPDF_SHOW_BRAND';
    private const CFG_SHOW_SHORT_DESC = 'TTAPDF_SHOW_SHORT_DESC';
    private const CFG_SHOW_LONG_DESC = 'TTAPDF_SHOW_LONG_DESC';
    private const CFG_SHOW_DETAILS = 'TTAPDF_SHOW_DETAILS';
    private const CFG_SHOW_QR = 'TTAPDF_SHOW_QR';
    private const CFG_FOOTER_TEXT = 'TTAPDF_FOOTER_TEXT';
    private const CFG_HOOK_POSITION = 'TTAPDF_HOOK_POSITION';
    private const HOOK_POS_ADDITIONAL = 'displayProductAdditionalInfo';
    private const HOOK_POS_ACTIONS   = 'displayProductActions';
    private const HOOK_POS_CUSTOM    = 'displayTtaProductPdf'; // hook custom

    public function __construct()
    {
        $this->name = 'ttaproductpdf';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Tecnoacquisti.com';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Product PDF (simple)');
        $this->description = $this->l('Generate a PDF from product page using a customizable TPL.');
        $this->ps_versions_compliancy = ['min' => '8.2.0', 'max' => '9.99.99'];
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayProductAdditionalInfo')
            && $this->registerHook('displayProductActions')
            && $this->registerHook(self::HOOK_POS_CUSTOM)
            && $this->ensureCustomHook()
            && $this->installDefaultConfig();
    }

    public function uninstall()
    {
        return $this->deleteConfig()
            && parent::uninstall();
    }

    private function installDefaultConfig(): bool
    {
        $defaults = [
            self::CFG_HEADER_LOGO => '',
            self::CFG_HEADER_TEXT => '',
            self::CFG_SHOW_TITLE => 1,
            self::CFG_SHOW_PRICE => 1,
            self::CFG_SHOW_REFERENCE => 1,
            self::CFG_SHOW_EAN => 1,
            self::CFG_SHOW_MPN => 1,
            self::CFG_SHOW_BRAND => 1,
            self::CFG_SHOW_SHORT_DESC => 1,
            self::CFG_SHOW_LONG_DESC => 1,
            self::CFG_SHOW_DETAILS => 1,
            self::CFG_SHOW_QR => 1,
            self::CFG_FOOTER_TEXT => '',
            self::CFG_HOOK_POSITION => self::HOOK_POS_ADDITIONAL,
        ];

        foreach ($defaults as $k => $v) {
            if (!Configuration::updateValue($k, $v)) {
                return false;
            }
        }
        return true;
    }

    private function deleteConfig(): bool
    {
        $keys = [
            self::CFG_HEADER_LOGO,
            self::CFG_HEADER_TEXT,
            self::CFG_SHOW_TITLE,
            self::CFG_SHOW_PRICE,
            self::CFG_SHOW_REFERENCE,
            self::CFG_SHOW_EAN,
            self::CFG_SHOW_MPN,
            self::CFG_SHOW_BRAND,
            self::CFG_SHOW_SHORT_DESC,
            self::CFG_SHOW_LONG_DESC,
            self::CFG_SHOW_DETAILS,
            self::CFG_SHOW_QR,
            self::CFG_FOOTER_TEXT,
            self::CFG_HOOK_POSITION,
        ];

        foreach ($keys as $k) {
            if (!Configuration::deleteByName($k)) {
                return false;
            }
        }
        return true;
    }

    private function ensureCustomHook(): bool
    {
        $name = self::HOOK_POS_CUSTOM;
        $id = (int)Hook::getIdByName($name);

        if ($id > 0) {
            return true;
        }

        $hook = new Hook();
        $hook->name = $name;
        $hook->title = 'TTA Product PDF (custom position)';
        $hook->description = 'Custom hook position for ttaproductpdf button.';
        $hook->position = true;

        return (bool)$hook->add();
    }

    // -------------------------
    // BO Configuration
    // -------------------------
    public function getContent()
    {
        $output = '';
        $useSsl = (bool)Configuration::get('PS_SSL_ENABLED_EVERYWHERE') || (bool)Configuration::get('PS_SSL_ENABLED');
        $shop_base_url = $this->context->link->getBaseLink((int)$this->context->shop->id, $useSsl);

        if (Tools::isSubmit('submit_ttapdf')) {
            $output .= $this->postProcess();
        }

        $this->context->smarty->assign(array(
            'shop_base_url' => $shop_base_url,
        ));

        $output .= $this->renderForm();
        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/help_hook_position.tpl');
        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/copyright.tpl');
        return $output;
    }

    private function postProcess(): string
    {
        $errors = [];
        $keysBool = [
            // self::CFG_SHOW_HEADER_LOGO, // removed: header logo is a file, not a boolean flag
            self::CFG_SHOW_TITLE,
            self::CFG_SHOW_PRICE,
            self::CFG_SHOW_REFERENCE,
            self::CFG_SHOW_EAN,
            self::CFG_SHOW_MPN,
            self::CFG_SHOW_BRAND,
            self::CFG_SHOW_SHORT_DESC,
            self::CFG_SHOW_LONG_DESC,
            self::CFG_SHOW_DETAILS,
            self::CFG_SHOW_QR,
        ];

        foreach ($keysBool as $k) {
            Configuration::updateValue($k, (int)Tools::getValue($k));
        }

        $hookPosition = (string)Tools::getValue(self::CFG_HOOK_POSITION);
        $allowedHooks = [
            self::HOOK_POS_ADDITIONAL,
            self::HOOK_POS_ACTIONS,
            self::HOOK_POS_CUSTOM,
        ];
        if (!in_array($hookPosition, $allowedHooks, true)) {
            $hookPosition = self::HOOK_POS_ADDITIONAL;
        }
        Configuration::updateValue(self::CFG_HOOK_POSITION, $hookPosition);
        Configuration::updateValue(self::CFG_HEADER_TEXT, (string)Tools::getValue(self::CFG_HEADER_TEXT));
        $this->handleHeaderLogoUpload($errors);
        Configuration::updateValue(self::CFG_FOOTER_TEXT, (string)Tools::getValue(self::CFG_FOOTER_TEXT));

        if (!empty($errors)) {
            return $this->displayError(implode("\n", $errors));
        }

        return $this->displayConfirmation($this->l('Settings updated.'));
    }

    private function renderForm(): string
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('PDF settings'),
                    'icon' => 'icon-file-text',
                ],
                'input' => [
                    [
                        'type' => 'select',
                        'label' => $this->l('Button position (hook)'),
                        'name' => self::CFG_HOOK_POSITION,
                        'options' => [
                            'query' => [
                                ['id' => self::HOOK_POS_ADDITIONAL, 'name' => $this->l('displayProductAdditionalInfo (recommended)')],
                                ['id' => self::HOOK_POS_ACTIONS,   'name' => $this->l('displayProductActions')],
                                ['id' => self::HOOK_POS_CUSTOM,    'name' => $this->l('Custom hook: displayTtaProductPdf (theme edit)')],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'desc' => $this->l('Choose where to display the “Download PDF” button on product page.'),
                    ],
                    [
                        'type' => 'file',
                        'label' => $this->l('Header logo (custom)'),
                        'name' => self::CFG_HEADER_LOGO,
                        'desc' => $this->l('Upload a custom logo for the PDF header (JPG, PNG, WEBP).'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Header text'),
                        'name' => self::CFG_HEADER_TEXT,
                        'desc' => $this->l('Optional text shown in the PDF header.'),
                    ],
                    $this->mkSwitch(self::CFG_SHOW_TITLE, $this->l('Show product title')),
                    $this->mkSwitch(self::CFG_SHOW_PRICE, $this->l('Show price')),
                    $this->mkSwitch(self::CFG_SHOW_REFERENCE, $this->l('Show reference')),
                    $this->mkSwitch(self::CFG_SHOW_EAN, $this->l('Show EAN')),
                    $this->mkSwitch(self::CFG_SHOW_MPN, $this->l('Show MPN')),
                    $this->mkSwitch(self::CFG_SHOW_BRAND, $this->l('Show brand')),
                    $this->mkSwitch(self::CFG_SHOW_SHORT_DESC, $this->l('Show short description (plain text)')),
                    $this->mkSwitch(self::CFG_SHOW_LONG_DESC, $this->l('Show long description (plain text)')),
                    $this->mkSwitch(self::CFG_SHOW_DETAILS, $this->l('Show product details (features)')),
                    $this->mkSwitch(self::CFG_SHOW_QR, $this->l('Show QR code (product URL)')),
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Footer text'),
                        'name' => self::CFG_FOOTER_TEXT,
                        'rows' => 5,
                        'cols' => 40,
                        'desc' => $this->l('Plain text footer printed at the end of the PDF.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submit_ttapdf',
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit_ttapdf';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->multipart = true;

        $helper->fields_value = $this->getConfigFormValues();

        return $helper->generateForm([$fields_form]);
    }

    private function mkSwitch(string $name, string $label): array
    {
        return [
            'type' => 'switch',
            'label' => $label,
            'name' => $name,
            'is_bool' => true,
            'values' => [
                ['id' => $name . '_on', 'value' => 1, 'label' => $this->l('Yes')],
                ['id' => $name . '_off', 'value' => 0, 'label' => $this->l('No')],
            ],
        ];
    }

    private function getConfigFormValues(): array
    {
        return [
            self::CFG_HOOK_POSITION => (string)Configuration::get(self::CFG_HOOK_POSITION),
            self::CFG_HEADER_TEXT => (string)Configuration::get(self::CFG_HEADER_TEXT),
            self::CFG_SHOW_TITLE => (int)Configuration::get(self::CFG_SHOW_TITLE),
            self::CFG_SHOW_PRICE => (int)Configuration::get(self::CFG_SHOW_PRICE),
            self::CFG_SHOW_REFERENCE => (int)Configuration::get(self::CFG_SHOW_REFERENCE),
            self::CFG_SHOW_EAN => (int)Configuration::get(self::CFG_SHOW_EAN),
            self::CFG_SHOW_MPN => (int)Configuration::get(self::CFG_SHOW_MPN),
            self::CFG_SHOW_BRAND => (int)Configuration::get(self::CFG_SHOW_BRAND),
            self::CFG_SHOW_SHORT_DESC => (int)Configuration::get(self::CFG_SHOW_SHORT_DESC),
            self::CFG_SHOW_LONG_DESC => (int)Configuration::get(self::CFG_SHOW_LONG_DESC),
            self::CFG_SHOW_DETAILS => (int)Configuration::get(self::CFG_SHOW_DETAILS),
            self::CFG_SHOW_QR => (int)Configuration::get(self::CFG_SHOW_QR),
            self::CFG_FOOTER_TEXT => (string)Configuration::get(self::CFG_FOOTER_TEXT),
        ];
    }

    // -------------------------
    // Front hooks (button)
    // -------------------------
    public function hookDisplayProductAdditionalInfo($params)
    {
        return $this->renderButtonIfEnabled(self::HOOK_POS_ADDITIONAL, $params);
    }

    public function hookDisplayProductActions($params)
    {
        return $this->renderButtonIfEnabled(self::HOOK_POS_ACTIONS, $params);
    }

    // hook custom
    public function hookDisplayTtaProductPdf($params)
    {
        return $this->renderButtonIfEnabled(self::HOOK_POS_CUSTOM, $params);
    }

    private function renderButtonIfEnabled(string $currentHook, $params): string
    {
        $selected = (string)Configuration::get(self::CFG_HOOK_POSITION);
        if ($selected !== $currentHook) {
            return '';
        }
        return $this->renderButtonFromHookParams($params);
    }

    private function renderButtonFromHookParams($params): string
    {
        // Classic theme usually passes $params['product'] as array; some contexts differ
        $idProduct = 0;

        if (isset($params['product']['id_product'])) {
            $idProduct = (int)$params['product']['id_product'];
        } elseif (isset($params['product']->id)) {
            $idProduct = (int)$params['product']->id;
        } elseif (Tools::getValue('id_product')) {
            $idProduct = (int)Tools::getValue('id_product');
        }

        if ($idProduct <= 0) {
            return '';
        }

        $url = $this->context->link->getModuleLink(
            $this->name,
            'pdf',
            [
                'id_product' => $idProduct,
                'token' => $this->buildToken($idProduct),
            ],
            true
        );

        $this->context->smarty->assign([
            'ttapdf_url' => $url,
        ]);

        return $this->fetch('module:ttaproductpdf/views/templates/hook/button.tpl');
    }

    // -------------------------
    // Helpers (token, config, text)
    // -------------------------
    public function getPdfConfig(): array
    {
        return [
            'header_logo' => (string)Configuration::get(self::CFG_HEADER_LOGO),
            'header_text' => (string)Configuration::get(self::CFG_HEADER_TEXT),
            'show_title' => (bool)Configuration::get(self::CFG_SHOW_TITLE),
            'show_price' => (bool)Configuration::get(self::CFG_SHOW_PRICE),
            'show_reference' => (bool)Configuration::get(self::CFG_SHOW_REFERENCE),
            'show_ean' => (bool)Configuration::get(self::CFG_SHOW_EAN),
            'show_mpn' => (bool)Configuration::get(self::CFG_SHOW_MPN),
            'show_brand' => (bool)Configuration::get(self::CFG_SHOW_BRAND),
            'show_short_desc' => (bool)Configuration::get(self::CFG_SHOW_SHORT_DESC),
            'show_long_desc' => (bool)Configuration::get(self::CFG_SHOW_LONG_DESC),
            'show_details' => (bool)Configuration::get(self::CFG_SHOW_DETAILS),
            'show_qr' => (bool)Configuration::get(self::CFG_SHOW_QR),
            'footer_text' => (string)Configuration::get(self::CFG_FOOTER_TEXT),
        ];
    }

    public function buildToken(int $idProduct): string
    {
        $customerId = (int)$this->context->customer->id;
        $secret = (string)_COOKIE_KEY_;
        // include shop id to avoid cross-shop reuse
        $shopId = (int)$this->context->shop->id;
        return hash('sha256', $shopId . '|' . $idProduct . '|' . $customerId . '|' . $secret);
    }

    public function isValidToken(int $idProduct, string $token): bool
    {
        return hash_equals($this->buildToken($idProduct), (string)$token);
    }

    public static function toPlainText(string $html): string
    {
        $txt = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $txt = strip_tags($txt);
        $txt = preg_replace("/[ \t]+/", " ", $txt);
        $txt = preg_replace("/\R{3,}/", "\n\n", $txt);
        return trim($txt);
    }

    private function handleHeaderLogoUpload(array &$errors): void
    {
        if (empty($_FILES[self::CFG_HEADER_LOGO]['tmp_name'])) {
            return;
        }

        $file = $_FILES[self::CFG_HEADER_LOGO];
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return;
        }

        $extension = Tools::strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($extension, $allowed, true)) {
            $errors[] = $this->l('Invalid header logo file type. Please upload JPG, PNG, or WEBP.');
            return;
        }

        $dir = _PS_IMG_DIR_ . 'ttaproductpdf/';
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            $errors[] = $this->l('Unable to create logo upload directory.');
            return;
        }

        $filename = 'header-logo.' . $extension;
        $target = $dir . $filename;
        $uploadError = ImageManager::validateUpload($file, 0, ['jpg', 'jpeg', 'png', 'webp']);
        if ($uploadError !== false && $uploadError !== '') {
            $errors[] = $uploadError;
            return;
        }

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            $errors[] = $this->l('Unable to upload header logo.');
            return;
        }

        Configuration::updateValue(self::CFG_HEADER_LOGO, $filename);
    }

}
