{*
**
*  2009-2026 Arte e Informatica
*
*  For support feel free to contact us on our website at https://www.tecnoacquisti.com/
*
*  @author    Arte e Informatica <helpdesk@tecnoacquisti.com>
*  @copyright 2009-2026 Arte e Informatica
*  @version   1.0.0
*  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
*
*}

<style>
  .wrap { font-size: 11px; }
  .label { font-weight: bold; }
  h1 { font-size: 18px; margin: 0; padding: 0; }
  .pre { white-space: pre-wrap; }
  .logo { width: auto; height: 50px; }
  .header-text { font-size: 12px; color: #333; }

  table.features { width: 100%; border-collapse: collapse; }
  table.features td { padding: 4px; border: 1px solid #ddd; }

  .section-title { font-weight: bold; margin: 0 0 4px 0; }
</style>

<div class="wrap">

  {* HEADER: 1 riga / 1 cella + spazio a destra per QR *}
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td valign="top" style="padding-right:45mm;">
        {if $shop.header_logo_path}
          <img class="logo" src="{$shop.header_logo_path|escape:'htmlall':'UTF-8'}" alt="{$shop.name|escape:'htmlall':'UTF-8'}" />
          <br />
        {/if}

        {if $config.header_text}
          <div class="header-text">{$config.header_text|escape:'htmlall':'UTF-8'}</div>
          <br />
        {/if}

        {if $config.show_title && $product.name}
          <h1>{$product.name|escape:'htmlall':'UTF-8'}</h1>
          <br />
        {/if}

        {if $config.show_brand && $product.brand}
          <span class="label">{l s='Brand:' mod='ttaproductpdf'}</span>
          {$product.brand|escape:'htmlall':'UTF-8'}<br />
        {/if}

        {if $config.show_price && $product.price}
          <span class="label">{l s='Price:' mod='ttaproductpdf'}</span>
          {$product.price|escape:'htmlall':'UTF-8'}<br />
        {/if}

        {if $config.show_reference && $product.reference}
          <span class="label">{l s='Reference:' mod='ttaproductpdf'}</span>
          {$product.reference|escape:'htmlall':'UTF-8'}<br />
        {/if}

        {if $config.show_ean && $product.ean13}
          <span class="label">EAN:</span>
          {$product.ean13|escape:'htmlall':'UTF-8'}<br />
        {/if}

        {if $config.show_mpn && $product.mpn}
          <span class="label">MPN:</span>
          {$product.mpn|escape:'htmlall':'UTF-8'}<br />
        {/if}
      </td>
    </tr>
  </table>

  <br /><br />

  {* BLOCCO CENTRALE: immagine 30% + spacer 4% + descrizione 66% *}
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="30%" valign="top">
        {if $product.image_path}
          <img src="{$product.image_path|escape:'htmlall':'UTF-8'}" style="width:180px; height:auto;" />
        {/if}
      </td>

      <td width="4%" valign="top">&nbsp;</td>

      <td width="66%" valign="top">
        {if $config.show_short_desc && $product.short_desc}
          <div class="section-title">{l s='Short description' mod='ttaproductpdf'}</div>
          <div class="pre">{$product.short_desc|escape:'htmlall':'UTF-8'}</div>
          <br />
        {/if}

        {if $config.show_long_desc && $product.long_desc}
          <div class="section-title">{l s='Description' mod='ttaproductpdf'}</div>
          <div class="pre">{$product.long_desc|escape:'htmlall':'UTF-8'}</div>
        {/if}
      </td>
    </tr>
  </table>

  <br /><br />

  {* DETTAGLI PRODOTTO *}
  {if $config.show_details && $product.features|@count}
    <div class="section-title">{l s='Product details' mod='ttaproductpdf'}</div>
    <table class="features" width="100%" border="0" cellspacing="0" cellpadding="0">
      {foreach $product.features as $f}
        <tr>
          <td width="40%"><strong>{$f.name|escape:'htmlall':'UTF-8'}</strong></td>
          <td width="60%">{$f.value|escape:'htmlall':'UTF-8'}</td>
        </tr>
      {/foreach}
    </table>
  {/if}

  {* Link prodotto sotto i dettagli *}
  {if $product.url}
    <br /><br />
    <span class="label">{l s='For more information:' mod='ttaproductpdf'}</span>
    <br />
    {$product.url|escape:'htmlall':'UTF-8'}
  {/if}

  {* FOOTER con respiro *}
  {if $config.footer_text}
    <br /><br /><br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td style="border-top:1px solid #ddd; padding-top:12px; font-size:10px; color:#555;">
          <div class="pre">{$config.footer_text|escape:'htmlall':'UTF-8'}</div>
        </td>
      </tr>
    </table>
  {/if}

</div>
