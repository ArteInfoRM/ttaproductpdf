
# Changelog
---


## [1.0.1] – 2026-02-10

### ✅ Improvements

#### Added
- Upload del logo header PDF con salvataggio in `views/img/` del modulo
- Regola CSS per limitare la larghezza del logo header (max automatica, altezza 50px)

#### Technical
- Ignorato in Git il file del logo header caricato (`views/img/header-logo.*`)

---

## [1.0.0] – 2026-01-21

### 🎉 First stable release

#### Added
- Generazione PDF prodotto da controller dedicato
- Template PDF completamente personalizzabile (`product.tpl`)
- Inclusione:
  - Logo shop
  - Titolo prodotto
  - Prezzo formattato con Locale (PS 8/9)
  - Marca, riferimento, EAN, MPN
  - Descrizione breve e lunga (HTML rimosso)
  - Dettagli prodotto (features)
  - Footer configurabile
- Immagine prodotto (filesystem path, TCPDF-safe)
- QR Code prodotto con `write2DBarcode()`
- Testo QR traducibile tramite TPL dedicato
- Token di sicurezza per accesso PDF
- Compatibilità PrestaShop 8.2 – 9.x

#### Hooks
- Supporto a:
  - `displayProductAdditionalInfo`
  - `displayProductActions`
  - Hook custom `displayTtaProductPdf`
- Selezione posizione hook da BO
- Creazione automatica dell’hook custom se non presente

#### Admin
- Configurazione completa da pannello modulo
- Attivazione/disattivazione singoli campi PDF
- Selezione posizione pulsante PDF
- Istruzioni integrate per hook custom

#### Technical
- Nessuna dipendenza esterna
- Uso API core PrestaShop (Product, Image, Manufacturer, Locale)
- Controller PDF isolato e sicuro
- Compatibilità PHP 8.x

---

## Planned
- Cache opzionale PDF
- Supporto multishop avanzato
- Possibilità di allegare il PDF alle email
