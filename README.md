# TTA Product PDF
![Built for PrestaShop](https://img.shields.io/badge/Built%20for-PrestaShop-DF0067?logo=prestashop&logoColor=white)

Modulo PrestaShop per la generazione di un **PDF prodotto personalizzabile**, direttamente dalla scheda prodotto.

Sviluppato da **Tecnoacquisti** per PrestaShop **8.2 – 9.x**.

---

## ✨ Funzionalità principali

- Generazione PDF da **TPL personalizzabile**
- Contenuti configurabili:
  - Logo shop
  - Titolo prodotto
  - Prezzo
  - Marca
  - Riferimento / EAN / MPN
  - Descrizione breve e lunga (testo pulito, HTML rimosso)
  - Dettagli prodotto (features)
  - Testo footer personalizzato
- **QR Code** (TCPDF `write2DBarcode`) con link al prodotto
- Testo QR **traducibile** (gestione corretta PrestaShop)
- Immagine prodotto inclusa
- Supporto **hook selezionabile** da BO:
  - `displayProductAdditionalInfo`
  - `displayProductActions`
  - Hook custom `displayTtaProductPdf`
- Token di sicurezza per accesso PDF
- Compatibile con **Classic theme** e temi custom

---

## 🧩 Compatibilità

| PrestaShop | Supporto |
|-----------|----------|
| 8.2.x     | ✅ |
| 8.3.x     | ✅ |
| 9.x       | ✅ |
| 1.7.x     | ❌ Non supportato |

---

## 📦 Installazione

1. Copiare la cartella `ttaproductpdf` in `/modules`
2. Installare il modulo da **BO → Moduli**
3. Configurare le opzioni dal pannello del modulo

---

## ⚙️ Configurazione

Dal pannello di configurazione è possibile:

- Attivare/disattivare i singoli campi nel PDF
- Impostare il testo del footer
- Scegliere la **posizione del pulsante PDF** (hook)
- Usare una **posizione custom** nel template del prodotto

---

## 📍 Posizione custom del pulsante

Selezionando **Custom hook: `displayTtaProductPdf`**, aggiungere nel file:
themes/YOUR_THEME/templates/catalog/product.tpl

la seguente riga, nel punto desiderato (consigliato prima del copyright):
{hook h='displayTtaProductPdf' product=$product}

## 📄 Template PDF

Il PDF viene generato a partire da:

/views/templates/front/product.tpl


Il template è completamente modificabile (HTML semplice + tabelle, compatibile TCPDF).

## 🔐 Sicurezza

Il PDF è accessibile solo tramite token firmato

Nessun accesso diretto senza parametri validi

🌍 Traduzioni

Tutte le stringhe sono traducibili da:
Internazionale → Traduzioni → Traduzioni dei moduli

Il testo del QR Code è gestito tramite TPL per evitare i limiti dei controller

## 🛠️ Tecnologie utilizzate

PrestaShop 8/9

TCPDF (core PrestaShop)

Smarty

PHP 8.x

## 👨‍💻 Autore

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





