<?php

namespace roaresearch\yii2\rmdb\models;

use roaresearch\yii2\rmdb\Module as RmdbModule;
use use yii2tech\ar\softdelete\SoftDeleteQueryBehavior;

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
        /** @var RmdbModule $module */
        $module = $this->getRmdbModule();
        $softDelete = ['class' => $module->softDeleteClass];
        
        if ($this->deletedByAttribute) {
            $softDelete['softDeleteAttributeValues'][$this->deletedByAttribute]
                = $module->userId;
            $softDelete['restoreAttributeValues'][$this->deletedByAttribute]
                = null;
        }
        
        if ($this->deletedAtAttribute) {
            $softDelete['softDeleteAttributeValues'][$this->deletedAtAttribute]
                = $module->timestampValue;
            $softDelete['restoreAttributeValues'][$this->deletedByAttribute]
                = null;
        };

        return parent::behaviors() + [$softDelete];
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
     */
    public static function find()
    {
        $deleted = [];
        $notDeleted = [];
        
        if ($this->deletedByAttribute) {
            $deleted[] = "{$this->deletedByAttribute} IS NOT NULL";
            $notDeleted[$this->deletedByAttribute] = null;
        }

        if ($this->deletedAtAttribute) {
            $deleted[] = "{$this->deletedAtAttribute} IS NOT NULL";
            $notDeleted[$this->deletedByAttribute] = null;
        }

        $query = parent::find();
        $query->attachBehavior(
            'softDelete',
            [
                'class' => SoftDeleteQueryBehavior::class,
                'deletedCondition' => $deleted,
                'notDeletedCondition' => $notDeleted,
            ]
        );

        return $query;
    }
}
