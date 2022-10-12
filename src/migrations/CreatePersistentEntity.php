<?php

namespace roaresearch\yii2\rmdb\migrations;

use yii\db\ColumnSchemaBuilder;

/**
 * Migration to create entity tables which contain columns to store the users
 * which created, updated and deleted the record and the datetimes it happened.
 *
 * A persistent entity remains in the database even after being 'deleted' by
 * using `softDelete` technique.
 */
abstract class CreatePersistentEntity extends CreateEntity
{
    /**
     * @var ?string the name of the column to store the user which deleted the
     * record. Set as `null` to prevent this column from being deleted.
     */
    public ?string $deletedByColumn = 'deleted_by';

    /**
     * @var ?string the name of the column to store the datetime when the record
     * was deleted. Set as `null` to prevent this column from being deleted.
     */
    public ?string $deletedAtColumn = 'deleted_at';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (isset($this->deletedByColumn)) {
            $this->defaultColumns[$this->deletedByColumn]
                = $this->deletedByDefinition();
            $this->defaultForeignKeys[$this->deletedByColumn]
                = $this->deletedByForeignKey($this->createdByColumn);
        }
        if (isset($this->deletedAtColumn)) {
            $this->defaultColumns[$this->deletedAtColumn]
                = $this->deletedAtDefinition();
        }
    }

    /**
     * @return ColumnSchemaBuilder definition to delete the column to
     * store which user deleted the record.
     */
    protected function deletedByDefinition(): ColumnSchemaBuilder
    {
        return $this->normalKey()->defaultValue(null);
    }

    /**
     * @return ColumnSchemaBuilder definition to delete the column to
     * store the datetime when the record was deleted.
     */
    protected function deletedAtDefinition(): ColumnSchemaBuilder
    {
        return $this->datetime()->defaultValue(null);
    }

    /**
     * Foreign key definition for the `deleted_by` column.
     *
     * @return array
     * @see defaultUserForeignKey()
     */
    protected function deletedByForeignKey($columnName): array
    {
        return $this->defaultUserForeignKey($columnName);
    }
}
