<?php

namespace Pogran\MyComponents;

use Bitrix\Main\Application;
use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;
use CBitrixComponent;
use \Bitrix\Main\Context;

use Pogran\ReviewTable;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;
use Pogran\Filters\ValidateFormFilter;

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
            ['id' => 'UPDATED_AT', 'name' => 'Дата изменения', 'type' => 'date'],
            [
                'id' => 'ENTITY_TYPE',
                'name' => 'Тип сущности',
                'type' => 'list',
                'items' => ['PRODUCT' => 'Товар', 'MANAGER' => 'Менеджер', 'STORE' => 'Магазин'],
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
            'createReview' => [
                'prefilters' => [
                    new ValidateFormFilter(),
                ],
            ],
            'editReview' => [
                'prefilters' => [
                    new ValidateFormFilter(),
                ],
            ],
        ];
    }

    protected function getReviews()
    {

        $grid_options = new GridOptions($this->arParams['LIST_ID']);
        $sort = $grid_options->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
        $nav_params = $grid_options->GetNavParams();

        $nav = new PageNavigation('reviews_nav');
        $nav->allowAllRecords(true)
            ->setPageSize($nav_params['nPageSize'])
            ->initFromUri();

        $filterOption = new \Bitrix\Main\UI\Filter\Options($this->arParams['LIST_ID']);
        $filterData = $filterOption->getFilter([]);
        // echo '<pre>';
        // print_r($filterData);
        // echo '</pre>';

        $filter = [];
        foreach ($filterData as $k => $v) {

            // $filter[$k] = $v; 
            switch ($k) {
                case 'ENTITY_TYPE':
                    $filter[$k] = $v;
                    break;
                case 'VALID':
                    $filter[$k] = $v;
                    break;
                case 'CREATED_AT_from':
                    // $filter["<=CREATED_AT"] = $v;
                    if(!empty($v)) {
                        $filter[">=CREATED_AT"] = new \Bitrix\Main\Type\DateTime($v);
                    }
                    break;
                case 'CREATED_AT_to':
                    if(!empty($v)) {
                        $filter["<=CREATED_AT"] = new \Bitrix\Main\Type\DateTime($v);
                    }
                    break;
                case 'UPDATED_AT_from':
                    if(!empty($v)) {
                        $filter[">=UPDATED_AT"] = new \Bitrix\Main\Type\DateTime($v);
                    }
                    break;
                case 'UPDATED_AT_to':
                    if(!empty($v)) {
                        $filter["<=UPDATED_AT"] = new \Bitrix\Main\Type\DateTime($v);
                    }
                    break;
                case 'RATING_from':
                    if (!empty($v)) {                        
                        if(!empty($filterData['RATING_to'])) {
                            $filter[">=RATING"] = $v;
                            $filter["<=RATING"] = $filterData['RATING_to'];
                        } else {
                            $filter[">RATING"] = $v;
                        }                        
                    } else {
                        if (!empty($filterData['RATING_to'])) {
                            $filter["<RATING"] = $filterData['RATING_to'];
                        }
                    }
                    break;
            }
            // echo $k . ' - ' . $v . '<br>';
            $filter['TEXT'] = "%" . $filterData['FIND'] . "%";
        }
        // echo '<pre>';
        // print_r($filter);
        // echo '</pre>';

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
                        'onclick' => "openEditForm({$row['ID']}, {$row['RATING']}, '{$row['TEXT']}', '{$row['ENTITY_TYPE']}', '{$row['VALID']}' , '{$row['ENTITY']}')",
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

    public function editReviewAction()
    {

        $request = Context::getCurrent()->getRequest();
        $reviewId = htmlspecialcharsEx(trim($request->get('ID')));
        $reviewText = htmlspecialcharsEx(trim($request->get('TEXT')));
        $reviewRating = htmlspecialcharsEx(trim($request->get('RATING')));
        $reviewEntityType = htmlspecialcharsEx(trim($request->get('ENTITY_TYPE')));
        $reviewEntity = htmlspecialcharsEx(trim($request->get('ENTITY')));
        $reviewValid = htmlspecialcharsEx(trim($request->get('VALID')));

        $data = [
            'TEXT' => htmlspecialcharsEx(trim($reviewText)),
            'RATING' => htmlspecialcharsEx(trim($reviewRating)),
            'ENTITY_TYPE' => $reviewEntityType,
            'VALID' => $reviewValid === 'on' ? 'Y' : 'N',
            'UPDATED_AT' => new \Bitrix\Main\Type\DateTime(),
            'ENTITY' => htmlspecialcharsEx(trim($reviewEntity))
        ];

        $result = ReviewTable::update($reviewId, $data);

        return $data;
    }

    public function deleteReviewAction($reviewId)
    {
        $result = ReviewTable::delete($reviewId);
        return $reviewId;
    }

    public function createReviewAction()
    {

        $request = Context::getCurrent()->getRequest();
        $reviewText = htmlspecialcharsEx(trim($request->get('TEXT')));
        $reviewRating = htmlspecialcharsEx(trim($request->get('RATING')));
        $reviewEntityType = htmlspecialcharsEx(trim($request->get('ENTITY_TYPE')));
        $reviewEntity = htmlspecialcharsEx(trim($request->get('ENTITY')));
        $reviewValid = htmlspecialcharsEx(trim($request->get('VALID')));

        $data = [
            'TEXT' => htmlspecialcharsEx(trim($reviewText)),
            'RATING' => htmlspecialcharsEx(trim($reviewRating)),
            'ENTITY_TYPE' => htmlspecialcharsEx(trim($reviewEntityType)),
            'VALID' => $reviewValid === 'on' ? 'Y' : 'N',
            'CREATED_AT' => new \Bitrix\Main\Type\DateTime(),
            'ENTITY' => htmlspecialcharsEx(trim($reviewEntity))
        ];

        $result = ReviewTable::add($data);

        return $result;
    }

    public function executeComponent()
    {
        $this->getReviews();
        $this->includeComponentTemplate();
    }
}
