<?php
namespace Pogran\Filters;

use Bitrix\Main\Engine\ActionFilter\Base;
use Bitrix\Main\Application;
use Bitrix\Main\Error;
use Bitrix\Main\EventResult;
use Bitrix\Main\Event;

class ValidateFormFilter extends Base
{
    public function onBeforeAction(Event $event)
    {
        $request = Application::getInstance()->getContext()->getRequest();

        $text = htmlspecialcharsEx(trim($request->getPost('TEXT')));
        $entityType = htmlspecialcharsEx(trim($request->getPost('ENTITY_TYPE')));
        $entity = htmlspecialcharsEx(trim($request->getPost('ENTITY')));
        $rating = htmlspecialcharsEx(trim($request->getPost('RATING')));
        
        define('TYPE_AND_ID_ENTITY', ['MANAGER' => [1,2,3,4,5], 'PRODUCT' => [10,11,12,13,14,15], 'STORE' => [20, 21, 22, 23, 24, 25]]);

        // Валидация
        if (empty($text)) {
            $this->addError(new Error(
                "Заполните поле",
                "EMPTY_TEXT",
                ['field' => 'TEXT']
            ));
        }

        if (empty($entityType)) {
            $this->addError(new Error(
                "Заполните поле",
                "EMPTY_ENTITY_TYPE",
                ['field' => 'ENTITY_TYPE']
            ));
        } elseif (!in_array($entityType, ['MANAGER','STORE','PRODUCT']) ) {
            $this->addError(new Error(
                "Не существующий тип. Выберите из списка.",
                "ERROR_ENTITY_TYPE",
                ['field' => 'ENTITY_TYPE']
            ));
        }

        if (empty($entity)) {
            $this->addError(new Error(
                "Заполните поле",
                "EMPTY_ENTITY",
                ['field' => 'ENTITY']
            ));
        } elseif (!in_array($entity, TYPE_AND_ID_ENTITY[$entityType])) {
            $this->addError(new Error(
                "Нет сущности выбранного типа с указанным id",
                "ERROR_ENTITY_ID",
                ['field' => 'ENTITY']
            ));
        }

        if (!empty($rating) && !in_array($rating, [1, 2, 3, 4, 5])) {
            $this->addError(new Error(
                "Введите целое число от 1 до 5",
                "ERROR_RATING",
                ['field' => 'RATING']
            ));
        }

        if ($this->getErrors()) {
            return new EventResult(EventResult::ERROR, null, null, $this);
        }       

        return null;
    }
}