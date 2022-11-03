[{if $oModule->getInfo('id') == constant('\OxidSolutionCatalysts\Adyen\Core\Module::MODULE_ID')}]
    <h3>[{oxmultilang ident="OSC_ADYEN_CONFIG_HEAD"}]</h3>
    <ol>
        <li>
            [{oxmultilang ident="OSC_ADYEN_CONFIG_SDK" suffix="COLON"}]
            <b>[{$oViewConf->getAdyenSDKVersion()}]</b>
        </li>
        <li>
            [{oxmultilang ident="OSC_ADYEN_CONFIG_WEBHOOKURL" suffix="COLON"}]
            <b>[{$oViewConf->getWebhookControllerUrl()}]</b>
        </li>
    </ol><br />
    <h3>[{oxmultilang ident="OSC_ADYEN_CONFIG_OPTIONS"}]</h3>
[{/if}]
[{$smarty.block.parent}]
