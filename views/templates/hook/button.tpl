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
