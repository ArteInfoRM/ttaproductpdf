# Changelog
---


## [1.0.1] – 2026-01-22

### ✅ Improvements

#### Added
- Upload of the PDF header logo saved in the module's `views/img/` directory
- CSS rule to limit the header logo width (automatic max, height 50px)

#### Technical
- Git-ignored the uploaded header logo file (`views/img/header-logo.*`)

---

## [1.0.0] – 2026-01-21

### 🎉 First stable release

#### Added
- Product PDF generation via a dedicated controller
- Fully customizable PDF template (`product.tpl`)
- Includes:
  - Shop logo
  - Product title
  - Price formatted with locale (PS 8/9)
  - Brand, reference, EAN, MPN
  - Short and long description (HTML removed)
  - Product details (features)
  - Configurable footer
- Product image (filesystem path, TCPDF-safe)
- Product QR Code using `write2DBarcode()`
- QR text translatable via a dedicated TPL
- Security token for PDF access
- Compatibility with PrestaShop 8.2 – 9.x

#### Hooks
- Support for:
  - `displayProductAdditionalInfo`
  - `displayProductActions`
  - Custom hook `displayTtaProductPdf`
- Hook position selectable from the Back Office
- Automatic creation of the custom hook if not present

#### Admin
- Full configuration from the module Back Office
- Enable/disable individual PDF fields
- Selection of the PDF button position
- Integrated instructions for the custom hook

#### Technical
- No external dependencies
- Uses PrestaShop core APIs (Product, Image, Manufacturer, Locale)
- Isolated and secure PDF controller
- PHP 8.x compatible

---

## Planned
- Optional PDF cache
- Advanced multishop support
- Option to attach the PDF to emails
