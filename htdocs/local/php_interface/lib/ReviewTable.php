<?php
namespace  Pogran;
use Bitrix\Main\Entity;
class ReviewTable extends Entity\DataManager
{
	public static function getTableName()
	{
		return 'mnd_reviews';
	}
		
	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
			new Entity\DatetimeField('CREATED_AT', [
                'default_value' => new \Bitrix\Main\Type\DateTime(),
            ]),
			new Entity\DatetimeField('UPDATED_AT'),
			new Entity\IntegerField('RATING'),
			new Entity\StringField('TEXT', [
                'required' => true,
            ]),
			new Entity\EnumField('VALID', [
				'values' => ['N', 'Y'],
                'default_value' => 'N',
            ]),
			new Entity\StringField('ENTITY_TYPE', [
                'required' => true,
            ]),
			new Entity\IntegerField('ENTITY', [
                'required' => true,
            ]),
		);
	}
}