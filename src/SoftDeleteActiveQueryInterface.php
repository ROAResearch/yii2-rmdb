<?php

namespace roaresearch\yii2\rmdb;

/**
 * Interface to apply soft delete filters on a `models\PersistentEntity` object
 */
interface SoftDeleteActiveQueryInterface extends \yii\db\ActiveQueryInterface
{

    /**
     * Adds condition to search only deleted records.
     *
     * @return SoftDeleteActiveQueryInterface this object itself
     */
    public function deleted(): SoftDeleteActiveQueryInterface;

    /**
     * Adds condition to search only not deleted records.
     *
     * @return SoftDeleteActiveQueryInterface this object itself
     */
    public function notDeleted(): SoftDeleteActiveQueryInterface;
}
