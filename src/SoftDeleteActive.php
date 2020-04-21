<?php

namespace roaresearch\yii2\rmdb;

/**
 * Query to apply soft delete filters on a `models\PersistentEntity` object
 */
class SoftDeleteActiveQuery extends \yii\db\ActiveQuery implements
    SoftDeleteActiveQueryInterface
{
    use SoftDeleteActiveQueryTrait;
}
