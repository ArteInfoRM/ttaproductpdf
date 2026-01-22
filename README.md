# TTA Product PDF
![Built for PrestaShop](https://img.shields.io/badge/Built%20for-PrestaShop-DF0067?logo=prestashop&logoColor=white)

PrestaShop module to generate a **customizable product PDF** directly from the product page.

Developed by **Tecnoacquisti** for PrestaShop **8.2 – 9.x**.

---

## ✨ Main features

- PDF generation from a **customizable TPL**
- Configurable content:
  - Shop logo
  - Product title
  - Price
  - Brand
  - Reference / EAN / MPN
  - Short and long description (clean text, HTML removed)
  - Product details (features)
  - Custom footer text
- **QR Code** (TCPDF `write2DBarcode`) linking to the product
- QR text **translatable** (handled correctly by PrestaShop)
- Product image included
- **Selectable hook** support from the Back Office:
  - `displayProductAdditionalInfo`
  - `displayProductActions`
  - Custom hook `displayTtaProductPdf`
- Security token for PDF access
- Compatible with **Classic theme** and custom themes

---

## 🧩 Compatibility

| PrestaShop | Support |
|-----------|----------|
| 8.2.x     | ✅ |
| 8.3.x     | ✅ |
| 9.x       | ✅ |
| 1.7.x     | ❌ Not supported |

---

## 📦 Installation

1. Copy the `ttaproductpdf` folder into `/modules`
2. Install the module from BO → Modules
3. Configure options from the module panel

---

## ⚙️ Configuration

From the configuration panel you can:

- Enable/disable individual fields in the PDF
- Set the footer text
- Choose the **position of the PDF button** (hook)
- Use a **custom position** inside the product template

---

## 📍 Custom button position

By selecting **Custom hook: `displayTtaProductPdf`**, add the following line in your theme file:

`themes/YOUR_THEME/templates/catalog/product.tpl`

Insert it in the desired place (recommended before the copyright):

{hook h='displayTtaProductPdf' product=$product}

## 📄 PDF Template

The PDF is generated from:

`/views/templates/front/product.tpl`

The template is fully editable (simple HTML + tables, TCPDF-compatible).

## 🔐 Security

The PDF is accessible only via a signed token.

No direct access without valid parameters.

## 🌍 Translations

All strings are translatable from:
International → Translations → Translations of modules

The QR Code text is managed via a TPL to avoid controller limitations.

## 🛠️ Technologies used

PrestaShop 8/9

TCPDF (PrestaShop core)

Smarty

PHP 8.x

## 👨‍💻 Author

Tecnoacquisti.com

🌐 https://www.tecnoacquisti.com

📧 helpdesk@tecnoacquisti.com

## 📜 License

This module is released under the **Academic Free License (AFL) v. 3.0**.

You are free to:
- Use the software for commercial and non-commercial purposes
- Modify the source code
- Distribute modified or unmodified versions

Under the terms of the AFL v.3.0 license.

Full license text:  
https://opensource.org/licenses/AFL-3.0
