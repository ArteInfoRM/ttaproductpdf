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

{if isset($ttapdf_url) && $ttapdf_url}
  <div class="ttapdf-wrap mt-2">
    <a class="btn btn-outline-primary"
       href="{$ttapdf_url|escape:'htmlall':'UTF-8'}"
       target="_blank"
       rel="nofollow noopener">
      {l s='Download PDF sheet' mod='ttaproductpdf'}
    </a>
  </div>
{/if}
