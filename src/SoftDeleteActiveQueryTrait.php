<?php

namespace roaresearch\yii2\rmdb;

/**
 * Trait with a default implementation to apply soft delete filters on a
 * `models\PersistentEntity` object
 */
trait SoftDeleteActiveQueryTrait
{
    /**
     * @var string name of the attribute to store the user who deleted the
     * record. Set as `null` to omit the functionality.
     */
    protected string $deletedByAttribute = 'deleted_by';

    /**
     * @var string name of the attribute to store the datetime when the record
     * was deleted. Set as `null` to omit the functionality.
     */
    protected string $deletedAtAttribute = 'deleted_at';

    /**
     * @param string $append condition attached to the attributes
     * @return string[] normalized attributes for usage.
     */
    protected function softDeleteAttributes(string $append = ''): array
    {
        $attributes = [];
        $alias = array_keys($this->getTablesUsedInFrom())[0];

        if ($this->deletedByAttribute) {
            $attributes[] = "$alias.[[{$this->deletedByAttribute}]] $append";
        }

        if ($this->deletedAtAttribute) {
            $attributes[] = "$alias.[[{$this->deletedAtAttribute}]] $append";
        }

        return $attributes;
    }

    /**
     * Adds condition to search only deleted records.
     *
     * @return SoftDeleteActiveQueryInterface this object itself
     */
    public function deleted(): SoftDeleteActiveQueryInterface
    {
        return $this->andOnCondition(
            implode(' AND ', $this->softDeleteAttributes('IS NOT NULL'))
        );
    }

    /**
     * Adds condition to search only not deleted records.
     *
     * @return SoftDeleteActiveQueryInterface this object itself
     */
    public function notDeleted(): SoftDeleteActiveQueryInterface
    {
        return $this->andOnCondition(
            implode(' AND ', $this->softDeleteAttributes('IS NULL'))
        );
    }

    abstract public function andOnCondition($condition, $params = []);

    abstract public function getTablesUsedInFrom();
}
