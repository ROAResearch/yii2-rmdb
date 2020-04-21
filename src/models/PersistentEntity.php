<?php

namespace roaresearch\yii2\rmdb\models;

use roaresearch\yii2\rmdb\Module as RmdbModule;

/**
 * Models for records which must remain on database even after being deleted.
 */
abstract class PersistentEntity extends Entity
{
    /**
     * @var string name of the attribute to store the user who deleted the
     * record. Set as `null` to omit the functionality.
     */
    protected $deletedByAttribute = 'deleted_by';

    /**
     * @var string name of the attribute to store the datetime when the record
     * was deleted. Set as `null` to omit the functionality.
     */
    protected $deletedAtAttribute = 'deleted_at';

    /**
     * @inheritdoc
     */
    protected function attributeTypecast(): ?array
    {
        $parentAttributes = parent::attributeTypecast();

        return $this->deletedByAttribute
            ? $parentAttributes + [$this->deletedByAttribute => 'integer']
            : $parentAttributes;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return parent::behaviors()
            + ['softDelete' => $this->softDeleteBehaviorConfig()];
    }

    /**
     * @return mixed configuration for the soft delete behavior
     */
    protected function softDeleteBehaviorConfig()
    {
        /** @var RmdbModule $module */
        $module = $this->getRmdbModule();

        return [
            'class' => $module->softDeleteClass,
            'softDeleteAttributeValues' =>
                (
                    $this->deletedByAttribute
                        ? [$this->deletedByAttribute => $module->userId]
                        : []
                )
                + (
                    $this->deletedAtAttribute
                        ? [$this->deletedAtAttribute => $module->timestampValue]
                        : []
                ),
            'restoreAttributeValues' =>
                (
                    $this->deletedByAttribute
                        ? [$this->deletedByAttribute => null]
                        : []
                )
                + (
                    $this->deletedAtAttribute
                        ? [$this->deletedAtAttribute => null]
                        : []
                ),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            $this->deletedByAttribute => RmdbModule::t('models', 'Deleted By'),
            $this->deletedAtAttribute => RmdbModule::t('models', 'Deleted At'),
        ]);
    }

    /**
     * @inheritdoc
     *
     * @return SoftDeleteActiveQuery
     */
    public static function find()
    {
        return new SoftDeleteActiveQuery(
            get_called_class(),
            [
                'deletedByAttribute' => $this->deletedByAttribute,
                'deletedAtAttribute' => $this->deletedAtAttribute,
            ]
        );
    }
}
