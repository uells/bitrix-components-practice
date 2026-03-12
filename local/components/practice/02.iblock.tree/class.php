<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

const LIMIT = 100;

class IBlockTreeComponent extends CBitrixComponent
{

    public function onPrepareComponentParams(array $arParams): array
    {
        // Тип инфоблоков
        $arParams["IBLOCK_TYPE"] ??= 0;

        // id инфоблоков
        $arParams["ROOT_IBLOCK_ID"] ??= 0;
        $arParams["MIDDLE_IBLOCK_ID"] ??= 0;
        $arParams["TOP_IBLOCK_ID"] ??= 0;

        // свойства инфоблоков
        $arParams["ROOT_PROPERTY_CODE"] ??= [];
        $arParams["MIDDLE_PROPERTY_CODE"] ??= [];
        $arParams["TOP_PROPERTY_CODE"] ??= [];

        // Коды привязок
        $arParams["MIDDLE_TO_ROOT_PROPERTY_CODE"] ??= [];
        $arParams["TOP_TO_MIDDLE_PROPERTY_CODE"] ??= [];

        return $arParams;
    }

    public function executeComponent()
    {
        if (!Loader::includeModule('iblock')) return;

        $this->includeComponentTemlate();
    }

    private function initResult(): void
    {
        if ($rootId = (int)$this->arParams["ROOT_IBLOCK_ID"]) {
            $rootElement = $this->getElementsFromIBlock(
                ["SORT" => "ASC"],
                ["IBLOCK_ID" => $rootId],
                $rootId
            );
        }
    }

    private function getElementsFromIBlock(array $filter, array $select, int $iblockId): array
    {
        // Получение элементов по фильтру
        $rsElements = CIBlockElement::GetList(
            ["SORT" => "ASC"],
            $filter,
            false,
            ["nTopCount" => LIMIT] // Ограничиваемся пока что предустановленным лимитом
        );

        $elements = [];
        while ($element = $rsElements->GetNext()) {
            $elements[(int)$element["ID"]] = $element;
        }

        if (empty($elements)) return $elements;

        // получаем свойства элементов одним вопросом
        CIBlockElement::GetPropertyValuesArray(
            $elements, // мутируемый массив
            $iblockId, // id инфоблока
            ["ID" => array_keys($elements)], // Взять записи из таблицы у которых ID элемента содержится в массиве, формируемом array_keys($elements)
            ["CODE" => array_values($select)], // выбрать свойства со значениями из массива $select
        );

        return $elements;
    }
}
