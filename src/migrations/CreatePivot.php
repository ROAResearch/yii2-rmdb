<?php

namespace roaresearch\yii2\rmdb\migrations;

use roaresearch\yii2\migrate\CreateTableMigration as CreateTable;
use yii\db\ColumnSchemaBuilder;

/**
 * Migration to create pivot tables which contain columns to store the user
 * which created the record and the datetime it was created.
 */
abstract class CreatePivot extends CreateTable
{
    /**
     * @var ?string the name of the column to store the user which created the
     * record. If this property is set as `null` the column won't be created.
     */
    public $createdByColumn = 'created_by';

    /**
     * @var ?string the name of the column to store the datetime when the record
     * was created. If this property is set as `null` the column won't be created.
     */
    public $createdAtColumn = 'created_at';

    /**
     * @var string name of the table used for the foreign key in user columns.
     *
     * @see defaultUserForeignKey()
     */
    public $userTable = 'user';

    /**
     * @var string name of the primary column of the user table for the foreign
     * key in user columns.
     *
     * @see defaultUserForeignKey()
     */
    public $userTablePrimaryKey = 'id';

    /**
     * @var \yii\db\ColumnSchemaBuilder[]
     */
    protected $defaultColumns = [];

    /**
     * @var string[]
     */
    protected $defaultForeignKeys = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (isset($this->createdByColumn)) {
            $this->defaultColumns[$this->createdByColumn]
                = $this->createdByDefinition();
            $this->defaultForeignKeys[$this->createdByColumn]
                = $this->createdByForeignKey($this->createdByColumn);
        }
        if (isset($this->createdAtColumn)) {
            $this->defaultColumns[$this->createdAtColumn]
                = $this->createdAtDefinition();
        }
    }

    /**
     * @inheritdoc
     */
    public function defaultColumns(): array
    {
        return $this->defaultColumns;
    }

    /**
     * @inheritdoc
     */
    public function defaultForeignKeys(): array
    {
        return $this->defaultForeignKeys;
    }

    /**
     * @return ColumnSchemaBuilder definition to create the column to
     * store which user created the record.
     */
    protected function createdByDefinition(): ColumnSchemaBuilder
    {
        return $this->normalKey()->notNull();
    }

    /**
     * @return ColumnSchemaBuilder definition to create the column to
     * store the datetime when the record was created.
     */
    protected function createdAtDefinition(): ColumnSchemaBuilder
    {
        return $this->datetime()->notNull();
    }

    /**
     * Default definition for the foreign keys in user columns.
     *
     * @return array
     * @see $userTable
     * @see $userTablePrimaryKey
     */
    protected function defaultUserForeignKey($columnName): array
    {
        return [
            'table' => $this->userTable,
            'columns' => [$columnName => $this->userTablePrimaryKey],
        ];
    }

    /**
     * Foreign key definition for the `created_by` column.
     *
     * @return array
     * @see defaultUserForeignKey()
     */
    protected function createdByForeignKey($columnName): array
    {
        return $this->defaultUserForeignKey($columnName);
    }
}
