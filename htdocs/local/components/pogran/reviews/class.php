<?php

namespace Pogran\MyComponents;

use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;
use CBitrixComponent;
use \Bitrix\Main\Context;
use Bitrix\Sale\Order;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use Bitrix\Main\Loader;
use Filters\ValidatePhoneFilter;
use Bitrix\Main\Engine\CurrentUser;
use \Bitrix\Iblock\Iblock;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Iblock\PropertyTable;

use Pogran\ReviewTable;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;

class ReviewsComponent extends CBitrixComponent implements Controllerable, Errorable
{
    protected ErrorCollection $errorCollection;

    public function onPrepareComponentParams($arParams)
    {
        $this->errorCollection = new ErrorCollection();
        $arParams['LIST_ID'] = 'reviews_list';
        $arParams['COLUMNS'] = array(
            array('id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true),
            array('id' => 'CREATED_AT', 'name' => 'Создан', 'sort' => 'CREATED_AT', 'default' => true),
            array('id' => 'UPDATED_AT', 'name' => 'Изменен', 'sort' => 'UPDATED_AT', 'default' => true),
            array('id' => 'RATING', 'name' => 'Рейтинг', 'sort' => 'RATING', 'default' => true, 'first_order' => 'asc'),
            array('id' => 'TEXT', 'name' => 'Отзыв', 'sort' => 'TEXT', 'default' => true, 'align' => 'right'),
            array('id' => 'VALID', 'name' => 'Модерация', 'sort' => 'VALID', 'default' => true),
            array('id' => 'ENTITY_TYPE', 'name' => 'Тип сущности', 'sort' => 'ENTITY_TYPE', 'default' => true),
            array('id' => 'ENTITY', 'name' => 'Связная сущность', 'sort' => 'ENTITY', 'default' => true),
        );

        $arParams['UI_FILTER'] = [
            ['id' => 'CREATED_AT', 'name' => 'Дата создания', 'type' => 'date'],
            [
                'id' => 'ENTITY_TYPE',
                'name' => 'Тип сущности',
                'type' => 'list',
                'items' => ['' => 'Любой', 'PRODUCT' => 'Товар', 'MANAGER' => 'Менеджер', 'STORE' => 'Магазин'],
                'params' => ['multiple' => 'Y']
            ],
            ['id' => 'VALID', 'name' => 'Модерация', 'type' => 'checkbox'],
            ['id' => 'RATING', 'name' => 'Рейтинг', 'type' => 'number'],
        ];

        return $arParams;
    }

    public function getErrors(): array
    {
        return $this->errorCollection->toArray();
    }

    public function getErrorByCode($code): Error
    {
        return $this->errorCollection->getErrorByCode($code);
    }

    public function configureActions()
    {
        return [

        ];
    }

    protected function getReviews()
    {
        // $list_id = 'reviews_list';

        $grid_options = new GridOptions($this->arParams['LIST_ID']);
        $sort = $grid_options->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
        $nav_params = $grid_options->GetNavParams();

        $nav = new PageNavigation('reviews_nav');
        $nav->allowAllRecords(true)
            ->setPageSize($nav_params['nPageSize'])
            ->initFromUri();

        $filterOption = new \Bitrix\Main\UI\Filter\Options($this->arParams['LIST_ID']);
        $filterData = $filterOption->getFilter([]);
        echo '<pre>';
        print_r($filterData);
        echo '</pre>';

        $filter = [];
        foreach ($filterData as $k => $v) {

            // $filter[$k] = $v; 
            switch ($k) {
                case 'ENTITY_TYPE':
                    $filter[$k] = $v;
                    break;
                // case 'VALID':
                //     $filter[$k] = $v;
                //     break;
                // case 'RATING':
                //     $filter[$k] = $v;
                //     break;
                // case 'CREATED_AT':
                //     $filter[$k] = $v;
                //     break;
                // case 'CREATED_AT_from':
                //     echo 
                //     $filter[">=CREATED_AT"] = $v;
                //     break;
                // case 'CREATED_AT_to':
                //     $filter["<=CREATED_AT"] = $v;
                //     break;
                // case 'CREATED_AT_to':
                //     $filter["<{$k}"] = $v;
                //     break;  
                case 'RATING_from':
                    $filter[">=RATING"] = $v;
                    // echo ">RATING";
                    break;
                case 'RATING_to':
                    $filter["<=RATING"] = $v;
                    // echo ">RATING";
                    break;   
                    
                
            }
            echo $k . ' - ' . $v . '<br>';
            $filter['TEXT'] = "%" . $filterData['FIND'] . "%";
        }

        $result = ReviewTable::getList(array(
            'order' => $sort['sort'],
            'select' => array('*'),
            'filter'      => $filter,
            'offset'      => $nav->getOffset(),
            'limit'       => $nav->getLimit(),
            'count_total' => true,
        ));
        $nav->setRecordCount($result->getCount());

        while ($row = $result->fetch()) {

            $list[] = [
                'data' => $row,
                'actions' => [
                    [
                        'text'    => 'Редактировать',
                        'default' => true,
                        'onclick' => "openEditForm({$row['ID']}, '{$row['TEXT']}', '{$row['ENTITY_TYPE']}', '{$row['VALID']}' , '{$row['ENTITY']}')",
                    ],
                    [
                        'text'    => 'Удалить',
                        'default' => true,
                        'onclick' => "if(confirm('Удалить отзыв?')){deleteReview({$row['ID']})}"
                    ]
                ]
            ];
        }

        $this->arResult['LIST'] = $list;
        $this->arResult['NAV_OBJ'] = $nav;
    }

    public function editReviewAction($reviewId, $reviewText, $reviewEntityType, $reviewValid, $reviewEntity) {

        $data = [
            'TEXT' => htmlspecialcharsEx(trim($reviewText)),
            'ENTITY_TYPE' => $reviewEntityType,
            'VALID' => $reviewValid,
            'UPDATED_AT' => new \Bitrix\Main\Type\DateTime(),
            'ENTITY' => htmlspecialcharsEx(trim($reviewEntity))
        ];

        $result = ReviewTable::update($reviewId, $data);
        
        return $result;
    }

    public function deleteReviewAction($reviewId) {
        $result = ReviewTable::delete($reviewId);
        return $reviewId;
    }

    public function createReviewAction($reviewText, $reviewRating, $reviewEntityType, $reviewValid, $reviewEntity) {
        $data = [
            'TEXT' => htmlspecialcharsEx(trim($reviewText)),
            'RATING' => htmlspecialcharsEx(trim($reviewRating)),
            'ENTITY_TYPE' => $reviewEntityType,
            'VALID' => $reviewValid,
            'CREATED_AT' => new \Bitrix\Main\Type\DateTime(),
            'ENTITY' => htmlspecialcharsEx(trim($reviewEntity))
        ];
        $result = ReviewTable::add($data);
        return $data;
    }

    public function executeComponent()
    {
        $this->getReviews();
        $this->includeComponentTemplate();
    }
}
