<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class IBlcokTreeComponent extends CBitrixComponents {

    public function onPrepareComponentParams(array $arParams): array {

        $arParams["ROOT_IBLOCK_ID"] ??= 0;
        $arParams["MIDDLE_IBLOCK_ID"] ??= 0;
        $arParams["TOP_IBLOCK_ID"] ??= 0;

        $arParams["TOP_PROPERTY_CODE"] ??= [];
        return $arParams;
    }

    public function executeComponent() {

    }

    private function initResult(): void {

    }
}   
?>