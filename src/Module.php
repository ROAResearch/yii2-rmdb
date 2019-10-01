<?php

namespace roaresearch\yii2\rmdb;

use yii2tech\ar\softdelete\SoftDeleteBehavior;
use Yii;
use yii\{
    behaviors\AttributeTypecastBehavior,
    behaviors\BlameableBehavior,
    behaviors\TimestampBehavior,
    db\Expression as DbExpression,
    i18n\PhpMessageSource,
    web\Application as WebApplication,
};

/**
 * Module which contains the configurations and utils for RMDB models.
 *
 * @property-read mixed $userId
 */
class Module extends \yii\base\Module
{
    /**
     * @var string
     */
    public $blameableClass = BlameableBehavior::class;

    /**
     * @var string
     */
    public $timestampClass = TimestampBehavior::class;

    /**
     * @var string
     */
    public $typecastClass = AttributeTypecastBehavior::class;

    /**
     * @var string
     */
    public $softDeleteClass = SoftDeleteBehavior::class;

    /**
     * @var mixed value to be passed on timestamp behavior.
     */
    public $timestampValue;

    /**
     * @var mixed value to be used when the user id can't be determined.
     */
    public $defaultUserId = 1;

    /**
     * @var mixed id of the active user.
     */
    private $userId;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (empty($this->timestampValue)) {
            $this->timestampValue = new DbExpression('NOW()');
        }
        $this->userId = (Yii::$app instanceof WebApplication
                && !Yii::$app->user->isGuest
            )
            ? Yii::$app->user->id
            : $this->defaultUserId;

        $this->registerTranslations();
    }

    /**
     * @return mixed the user id to be stored
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Registers the translations used by the library.
     */
    public static function registerTranslations()
    {
        $i18n = Yii::$app->i18n;
        if (!isset($i18n->translations['tecnocen/rmdb/*'])) {
            $i18n->translations['tecnocen/rmdb/*'] = [
                'class' => PhpMessageSource::class,
                'sourceLanguage' => 'en',
                'basePath' => __DIR__ . '/messages',
            ];
        }
    }

    public static function t(
        string $category,
        string $message,
        array $params = [],
        ?string $language = null
    ): string {
        return Yii::t(
            'roaresearch/rmdb/' . $category,
            $message,
            $params,
            $language
        );
    }
}
