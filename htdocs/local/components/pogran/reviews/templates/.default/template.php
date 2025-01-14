<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>


<div class="reviews-container">
<h2>Отзывы</h2>
<?

$APPLICATION->IncludeComponent('bitrix:main.ui.filter', '', [
    'FILTER_ID' => $arParams['LIST_ID'],
    'GRID_ID' => $arParams['LIST_ID'],
    'FILTER' => $arParams['UI_FILTER'],
    'ENABLE_LIVE_SEARCH' => true,
    'ENABLE_LABEL' => true,
	"FILTER_PRESETS" => [
]
]);

?>

<div class='new-review-button-container'>
	<button class="ui-btn ui-btn-success" id="new-review-button" onclick="openCreateForm()">Создать отзыв</button>
</div>

<?

$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
	'GRID_ID' => $arParams['LIST_ID'],
	'COLUMNS' => $arParams['COLUMNS'],
	'ROWS' => $arResult['LIST'],
	'SHOW_ROW_CHECKBOXES' => false,
	'NAV_OBJECT' => $arResult['NAV_OBJ'],
	'AJAX_MODE' => 'Y',
	'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
	'PAGE_SIZES' =>  [
		['NAME' => '3', 'VALUE' => '3'],
		['NAME' => '5', 'VALUE' => '5'],
		['NAME' => '10', 'VALUE' => '10'],
		['NAME' => '20', 'VALUE' => '20']
	],
	'AJAX_OPTION_JUMP'          => 'N',
	'SHOW_CHECK_ALL_CHECKBOXES' => false,
	'SHOW_ROW_ACTIONS_MENU'     => true,
	'SHOW_GRID_SETTINGS_MENU'   => true,
	'SHOW_NAVIGATION_PANEL'     => true,
	'SHOW_PAGINATION'           => true,
	'SHOW_SELECTED_COUNTER'     => true,
	'SHOW_TOTAL_COUNTER'        => true,
	'SHOW_PAGESIZE'             => true,
	'SHOW_ACTION_PANEL'         => true,
	'ALLOW_COLUMNS_SORT'        => true,
	'ALLOW_COLUMNS_RESIZE'      => true,
	'ALLOW_HORIZONTAL_SCROLL'   => true,
	'ALLOW_SORT'                => true,
	'ALLOW_PIN_HEADER'          => true,
	'AJAX_OPTION_HISTORY'       => 'N'
]);

?>

</div>

<!-- <script>
	const newElement = document.createElement('div');
	newElement.innerHTML = '<button class="ui-btn ui-btn-success" onclick="openCreateForm()">Создать отзыв</button>';
	newElement.classList.add('new-review-button-container');
	const filterDiv = document.querySelector('#reviews_list_search_container');
	filterDiv.insertAdjacentElement('afterend', newElement);
</script> -->