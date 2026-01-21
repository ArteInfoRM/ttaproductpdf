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
<div class="panel">
<h3><i class="icon-code"></i> {l s='Custom hook position' mod='ttaproductpdf'}</h3>

<p>
  {l s='To place the “Download PDF” button in a custom position, edit your theme product template and add the following line:' mod='ttaproductpdf'}
</p>

<pre style="background:#f5f5f5; padding:8px; border:1px solid #ddd;">
{literal}{hook h='displayTtaProductPdf' product=$product}{/literal}
</pre>

<p>
  {l s='Recommended position:' mod='ttaproductpdf'}
  <br />
  <code>themes/YOUR_THEME/templates/catalog/product.tpl</code>
  <br />
</p>
</div>
