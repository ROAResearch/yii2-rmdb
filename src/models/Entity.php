<?php

namespace roaresearch\yii2\rmdb\models;

use roaresearch\yii2\rmdb\Module as RmdbModule;

abstract class Entity extends Pivot
{
    /**
     * @var string name of the attribute to store the user who updated the
     * record. Set as `null` to omit the functionality.
     */
    protected $updatedByAttribute = 'updated_by';

    /**
     * @var string name of the attribute to store the datetime when the record
     * was updated. Set as `null` to omit the functionality.
     */
    protected $updatedAtAttribute = 'updated_at';

    /**
     * @inheritdoc
     */
    protected function attributeTypecast(): ?array
    {
        return parent::attributeTypecast() + [
            $this->updatedByAttribute => 'integer',
        ];
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['blameable']['updatedByAttribute']
            = $this->updatedByAttribute;
        $behaviors['timestamp']['updatedAtAttribute']
            = $this->updatedAtAttribute;

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            $this->updatedByAttribute => RmdbModule::t('models', 'Updated By'),
            $this->updatedAtAttribute => RmdbModule::t('models', 'Updated At'),
        ]);
    }
}
