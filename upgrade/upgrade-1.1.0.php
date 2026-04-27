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

function upgrade_module_1_1_0(Module $module): bool
{
    return $module->registerHook('moduleRoutes');
}
