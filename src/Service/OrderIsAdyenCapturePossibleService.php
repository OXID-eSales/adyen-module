<?php

namespace OxidSolutionCatalysts\Adyen\Service;

use OxidSolutionCatalysts\Adyen\Model\AdyenHistory;
use OxidSolutionCatalysts\Adyen\Model\AdyenHistoryList;
use OxidSolutionCatalysts\Adyen\Core\Module;

class OrderIsAdyenCapturePossibleService
{
    private OxNewService $oxNewService;

    public function __construct(OxNewService $oxNewService)
    {
        $this->oxNewService = $oxNewService;
    }

    public function isAdyenCapturePossible(string $orderId): bool
    {
        $adyenHistoryList = $this->getAdyenHistoryList($orderId);
        if ($adyenHistoryList->count() > 0) {
            $adyenHistoryArray = $adyenHistoryList->getArray();
            $adyenHistory = reset($adyenHistoryArray);
            return $adyenHistory->getAdyenStatus() === Module::ADYEN_STATUS_AUTHORISED;
        }

        return false;
    }

    protected function getAdyenHistoryList(string $orderId): AdyenHistoryList
    {
        $adyenHistoryList = $this->oxNewService->oxNew(AdyenHistoryList::class, [AdyenHistory::class]);
        $adyenHistoryList->getAdyenHistoryList($orderId, 'desc');

        return $adyenHistoryList;
    }
}
