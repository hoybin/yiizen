<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\components\PathBehavior;

/**
 * This is the model class for table "catalog".
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string $cover
 * @property string $path
 * @property double $sort
 */
class Catalog extends ActiveRecord
{
    const PATH_SEPARATOR    = '/';

    const NAME_LEVEL_PREFIX = '┈';

    const INIT_SORT_VALUE   = 20;

    const POSITION_AFTER    = 'A';

    const POSITION_BEFORE   = 'B';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'catalog';
    }

    /**
     * 查找后代
     *
     * @param $parent
     *
     * @return array|\common\models\Catalog[]
     */
    public static function findDescendants(self $model)
    {
        return self::find()->where([
            'like',
            'path',
            $model->getOldAttribute('path') . self::PATH_SEPARATOR . $model->id . '%',
            false,
        ])->orderBy(['sort' => SORT_ASC, 'id' => SORT_ASC])->all();
    }

    /**
     * {@inheritdoc}
     * @return CatalogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CatalogQuery(get_called_class());
    }

    /**
     * 查找祖先
     *
     * @param $model
     *
     * @return array|\common\models\Catalog[]
     */
    public static function findAncestors(self $model)
    {
        return self::find()->where([
            'in',
            'id',
            explode(self:: PATH_SEPARATOR, $model->getOldAttribute('path')),
        ])->orderBy(['sort' => SORT_ASC, 'id' => SORT_ASC])->all();
    }

    /**
     * @param $type
     *
     * @return string
     */
    public static function typeLabel($type)
    {
        return Yii::t('app/catalog', Yii::$app->params['catalog.types'][$type]);
    }

    /**
     * @return mixed
     */
    public static function typeList()
    {
        $types = Yii::$app->params['catalog.types'];
        foreach ($types as &$type) {
            $type = Yii::t('app/catalog', $type);
        }

        return $types;
    }

    /**
     * @return array
     */
    public static function parentList()
    {
        $models = self::findList()->all();
        foreach ($models as $model) {
            $model->name = $model->levelName;
            $model->path = $model->selfPath;
        }

        return array_column($models, 'name', 'path');
    }

    /**
     * @return CatalogQuery the active query used by this AR class.
     */
    public static function findList()
    {
        return self::find()->orderBy(['sort' => SORT_ASC, 'id' => SORT_ASC])->indexBy('id');
    }

    /**
     * @return array
     */
    public static function positionList()
    {
        return [
            self::POSITION_AFTER  => Yii::t('app/catalog', 'after'),
            self::POSITION_BEFORE => Yii::t('app/catalog', 'before'),
        ];
    }

    /**
     * @param $model
     * @param $descendants
     *
     * @return false|int
     * @throws \Exception
     * @throws \Throwable
     */
    public static function updates(self $model, $descendants)
    {
        $transaction = self::getDb()->beginTransaction();
        try {
            $result = 1;
            foreach ($descendants as $descendant) {
                $result = $descendant->updateInternal();
                if ($result === false) {
                    break;
                }
            }
            if ($result) {
                $result = $model->updateInternal();
            }
            if ($result === false) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }

            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @param $model
     * @param $descendants
     *
     * @return false|int
     * @throws \Exception
     * @throws \Throwable
     */
    public static function deletes(self $model, $descendants)
    {
        $transaction = self::getDb()->beginTransaction();
        try {
            $result = 1;
            foreach ($descendants as $descendant) {
                $result = $descendant->deleteInternal();
                if ($result === false) {
                    break;
                }
            }
            if ($result) {
                $result = $model->deleteInternal();
            }
            if ($result === false) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }

            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'path' => [
                'class' => PathBehavior::className(),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'name'], 'required'],
            [
                'type',
                'in',
                'range' => array_keys(Yii::$app->params['catalog.types']),
            ],
            ['name', 'trim'],
            ['name', 'string', 'max' => 64],
            ['cover', 'string', 'max' => 512],
            ['cover', 'default', 'value' => ''],
            ['path', 'string', 'max' => 128],
            ['path', 'default', 'value' => '0'],
//            [['sort'], 'number'],
            ['location', 'string', 'max' => 128],
//            ['location', 'default', 'value' => '0'],
            [
                'position',
                'in',
                'range' => [self::POSITION_AFTER, self::POSITION_BEFORE],
            ],
//            ['position', 'default', 'value' => self::POSITION_AFTER],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'       => Yii::t('app/catalog', 'ID'),
            'type'     => Yii::t('app/catalog', 'Type'),
            'name'     => Yii::t('app/catalog', 'Name'),
            'cover'    => Yii::t('app/catalog', 'Cover'),
            'path'     => Yii::t('app/catalog', 'Path'),
            'sort'     => Yii::t('app/catalog', 'Sort'),
            'location' => Yii::t('app/catalog', 'Location'),
            'position' => Yii::t('app/catalog', 'Position'),
        ];
    }

    /**
     * @param bool $skipIfSet
     *
     * @return yii\db\ActiveRecord
     */
    public function loadDefaultValues($skipIfSet = true)
    {
        $this->escapeSort();

        return parent::loadDefaultValues($skipIfSet);
    }

    /**
     * @return string
     */
    public function getLevelName()
    {
        return str_repeat(self::NAME_LEVEL_PREFIX, $this->getLevel()) . $this->name;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return substr_count($this->path, self::PATH_SEPARATOR);
    }

    /**
     * @return string
     */
    public function getSelfPath()
    {
        return $this->path . self::PATH_SEPARATOR . $this->id;
    }
}
