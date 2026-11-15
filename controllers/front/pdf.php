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

class TtaproductpdfPdfModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    /** Validate the product identifier before the front controller lifecycle. */
    public function init()
    {
        foreach ([$_GET, $_POST] as $input) {
            if (array_key_exists('id_product', $input) && !$this->isValidProductId($input['id_product'])) {
                $this->rejectProductRequest(400);
            }
        }
        if (!$this->isValidProductId(Tools::getValue('id_product'))) {
            $this->rejectProductRequest(400);
        }
        parent::init();
    }

    /** @return bool Whether the raw value is a bounded positive decimal ID. */
    private function isValidProductId($value)
    {
        return is_string($value) && preg_match('/^[1-9][0-9]{0,9}$/D', $value)
            && (float) $value <= 2147483647;
    }

    /** Return a plain error without reflecting input or product details. */
    private function rejectProductRequest($status)
    {
        http_response_code($status);
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: no-store');
        exit($status === 400 ? 'Invalid product identifier.' : 'Product not available.');
    }

    /** Render only the PDF, without constructing an unrelated HTML page URL. */
    public function initContent()
    {
        $idProduct = (int)Tools::getValue('id_product');
        if ($idProduct <= 0) {
            header('HTTP/1.1 400 Bad Request');
            die('Missing id_product');
        }

        $module = Module::getInstanceByName('ttaproductpdf');
        if (!$module instanceof Ttaproductpdf) {
            header('HTTP/1.1 500 Internal Server Error');
            die('Module not available');
        }

        $idLang = (int)$this->context->language->id;

        $idShop = (int) $this->context->shop->id;
        $product = new Product($idProduct, true, $idLang, $idShop);
        if (!Validate::isLoadedObject($product) || !(bool) $product->active
            || !$product->isAssociatedToShop($idShop)
            || !$product->checkAccess((int) $this->context->customer->id)) {
            $this->rejectProductRequest(404);
        }

        // Cover image (filesystem path for TCPDF)
        $imagePath = '';
        $cover = Image::getCover((int)$product->id);
        if (!empty($cover['id_image'])) {
            $img = new Image((int)$cover['id_image']);
            $base = _PS_IMG_DIR_ . 'p/';
            $relative = $img->getImgPath();

            $candidate = $base . $relative . '.jpg';
            if (file_exists($candidate)) {
                $imagePath = $candidate;
            } else {
                foreach (['jpeg', 'png', 'webp'] as $ext) {
                    $candidate2 = $base . $relative . '.' . $ext;
                    if (file_exists($candidate2)) {
                        $imagePath = $candidate2;
                        break;
                    }
                }
            }
        }

        // Brand
        $manufacturerName = '';
        if ((int)$product->id_manufacturer) {
            $manufacturer = new Manufacturer((int)$product->id_manufacturer, $idLang);
            if (Validate::isLoadedObject($manufacturer)) {
                $manufacturerName = (string)$manufacturer->name;
            }
        }

        // Price + formatting (PS 8/9)
        $price = (float)Product::getPriceStatic((int)$product->id, true);
        $currencyIso = (string)$this->context->currency->iso_code;

        $locale = null;
        if (method_exists($this->context, 'getCurrentLocale')) {
            $locale = $this->context->getCurrentLocale();
        } elseif (property_exists($this->context, 'currentLocale')) {
            $locale = $this->context->currentLocale;
        }
        $priceFormatted = $locale ? $locale->formatPrice($price, $currencyIso) : (string)$price;

        // Features
        $features = (array)$product->getFrontFeatures($idLang);

        // Plain text descriptions
        $shortPlain = Ttaproductpdf::toPlainText((string)$product->description_short);
        $longPlain  = Ttaproductpdf::toPlainText((string)$product->description);

        $conf = $module->getPdfConfig();
        $headerLogoPath = '';
        if (!empty($conf['header_logo'])) {
            $logoCandidate = _PS_MODULE_DIR_ . 'ttaproductpdf/views/img/' . $conf['header_logo'];
            if (file_exists($logoCandidate)) {
                $headerLogoPath = $logoCandidate;
            }
        }

        // Product URL for QR payload
        $productUrl = $this->context->link->getProductLink($product);

        $pdfData = [
            'shop' => [
                'name' => (string)Configuration::get('PS_SHOP_NAME'),
                'header_logo_path' => $headerLogoPath,
            ],
            'product' => [
                'id' => (int)$product->id,
                'name' => (string)$product->name,
                'image_path' => (string)$imagePath,
                'price' => (string)$priceFormatted,
                'reference' => (string)$product->reference,
                'ean13' => (string)$product->ean13,
                'mpn' => (string)$product->mpn,
                'brand' => (string)$manufacturerName,
                'short_desc' => (string)$shortPlain,
                'long_desc' => (string)$longPlain,
                'features' => $features,
                'url' => (string)$productUrl,
            ],
            'config' => $conf,
        ];

        // Render HTML from TPL
        $this->context->smarty->assign($pdfData);
        $html = $module->fetch('module:ttaproductpdf/views/templates/front/product.tpl');

        // QR label translated via TPL (so it appears in module translations)
        $qrLabelRaw = $module->fetch('module:ttaproductpdf/views/templates/front/qr_label.tpl');
        $qrLabelRaw = trim((string)$qrLabelRaw);

        // Remove Smarty debug comments (<!-- begin ... -->)
        $qrLabelRaw = preg_replace('/<!--.*?-->/s', '', $qrLabelRaw);
        $qrLabelRaw = trim((string)$qrLabelRaw);

        // %0A -> newline
        $qrLabel = str_replace('%0A', "\n", $qrLabelRaw);
        if ($qrLabel === '') {
            $qrLabel = "Scan to open\nthe product";
        }

        // PDF
        $pdf = new PDFGenerator();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);

        $pdf->AddPage();

        $margins = $pdf->getMargins();
        $pdf->SetY($margins['top']);
        $pdf->SetX($margins['left']);

        // Save HTML flow position (CRUCIAL)
        $yStart = $pdf->GetY();
        $xStart = $pdf->GetX();

        // QR + label drawn by controller, without moving HTML flow
        if (!empty($conf['show_qr']) && !empty($productUrl)) {
            $qrSize = 25; // mm
            $pageW = $pdf->getPageWidth();

            $x = $pageW - $margins['right'] - $qrSize;
            $y = $margins['top'];

            $style = [
                'border' => 0,
                'padding' => 0,
                'fgcolor' => [0, 0, 0],
                'bgcolor' => false,
            ];

            // QR
            $pdf->write2DBarcode($productUrl, 'QRCODE,M', $x, $y, $qrSize, $qrSize, $style, 'N');

            // Label under QR (tiny)
            $pdf->SetFont('helvetica', '', 7);
            $pdf->SetTextColor(90, 90, 90);

            $pdf->MultiCell(
                $qrSize,
                0,
                $qrLabel,
                0,
                'R',
                false,
                0,                  // ln=0: do NOT advance global cursor
                $x,
                $y + $qrSize + 1.5,
                true
            );

            // Restore defaults
            $pdf->SetFont('helvetica', '', 11);
            $pdf->SetTextColor(0, 0, 0);
        }

        // Restore HTML flow position
        $pdf->SetY($yStart);
        $pdf->SetX($xStart);

        // Body HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        // Filename
        $safeName = Tools::str2url((string)$product->name);
        if ($safeName === '') {
            $safeName = 'product';
        }
        $filename = 'product-' . (int)$product->id . '-' . $safeName . '.pdf';

        header('X-Robots-Tag: noindex, nofollow');
        $pdf->render($filename, true);
        exit;
    }
}
