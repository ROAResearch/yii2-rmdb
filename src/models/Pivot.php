<?php

namespace roaresearch\yii2\rmdb\models;

use roaresearch\yii2\rmdb\Module as RmdbModule;
use Yii;

abstract class Pivot extends \yii\db\ActiveRecord
{
    /**
     * @var ?string name of the attribute to store the user who created the
     * record. Set as `null` to omit the functionality.
     */
    protected ?string $createdByAttribute = 'created_by';

    /**
     * @var ?string name of the attribute to store the datetime when the record
     * was created. Set as `null` to omit the functionality.
     */
    protected ?string $createdAtAttribute = 'created_at';

    /**
     * @var string id to obtain the rmdb module from `Yii::$app`
     */
    protected string $rmdbModuleId = 'rmdb';

    /**
     * @return RmdbModule
     * @throws InvalidConfigException
     */
    protected function getRmdbModule(): RmdbModule
    {
        return Yii::$app->getModule($this->rmdbModuleId);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $module = $this->getRmdbModule();

        return [
            'blameable' => [
                'class' => $module->blameableClass,
                'createdByAttribute' => $this->createdByAttribute,
                'updatedByAttribute' => null,
                'value' => $module->userId,
            ],
            'timestamp' => [
                'class' => $module->timestampClass,
                'createdAtAttribute' => $this->createdAtAttribute,
                'updatedAtAttribute' => null,
                'value' => $module->timestampValue,
            ],
            'typecast' => [
                'class' => $module->typecastClass,
                'attributeTypes' => $this->attributeTypecast(),
                'typecastAfterFind' => true,
            ],
        ];
    }

    /**
     * @return ?array pairs of 'attribute' => 'typecast'. Return null if you
     * want the typecast to be determined by `rules()` method.
     */
    protected function attributeTypecast(): ?array
    {
        return $this->createdByAttribute
            ? [$this->createdByAttribute => 'integer']
            : [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            $this->createdByAttribute => RmdbModule::t('models', 'Created By'),
            $this->createdAtAttribute => RmdbModule::t('models', 'Created At'),
        ];
    }
}
