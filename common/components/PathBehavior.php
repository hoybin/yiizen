<?php
/**
 * User: Hoybin
 * Time: 2018/11/26 15:02
 */

namespace common\components;

use yii\base\Behavior;
use common\models\Catalog;

class PathBehavior extends Behavior
{
    private $location      = '0';
    private $position      = Catalog::POSITION_AFTER;
    private $sortHasChange = false;

    /**
     *
     */
    public function escapeSort()
    {
        if ($this->owner->id) {
            $prev = Catalog::find()->select(['id', 'path'])->where(['path' => $this->owner->path])->andWhere([
                'or',
                ['<', 'sort', $this->owner->sort],
                ['and', ['sort' => $this->owner->sort], ['<', 'id', $this->owner->id]],
            ])->orderBy(['sort' => SORT_DESC, 'id' => SORT_DESC])->limit(1)->one();
            $next = Catalog::find()->select(['id', 'path'])->where(['path' => $this->owner->path])->andWhere([
                'or',
                ['>', 'sort', $this->owner->sort],
                ['and', ['sort' => $this->owner->sort], ['>', 'id', $this->owner->id]],
            ])->orderBy(['sort' => SORT_ASC, 'id' => SORT_ASC])->limit(1)->one();

            if ($prev) {
                $this->position = 'A';
                $this->location = $prev->path . Catalog::PATH_SEPARATOR . $prev->id;
            } elseif ($next) {
                $this->position = 'B';
                $this->location = $next->path . Catalog::PATH_SEPARATOR . $next->id;
            } else {
                $this->position = 'A';
                $this->location = $this->owner->path;
            }
        }
    }

    /**
     * @param array $descendants
     *
     * @return false|int
     */
    public function theUpdate($descendants = [])
    {
        if (empty($descendants)) {
            return $this->owner->update();
        } else {
            if ($delta = $this->captureSort(count($descendants))) {
                foreach ($descendants as $i => &$child) {
                    $child->sort = $this->owner->sort + ($i + 1) * $delta;
                    if ($this->owner->path !== $this->owner->getOldAttribute('path')) {
                        $_path       = self::escPathSeparator($this->owner->getOldAttribute('path'));
                        $_separator  = self::escPathSeparator();
                        $child->path = preg_replace('/^' . $_path . '(?=' . $_separator . ')/',
                            $this->owner->path, $child->path);
                    }
                }
            }

            return Catalog::updates($this->owner, array_reverse($descendants));
        }
    }

    /**
     * @param $descendantsCount
     *
     * @return float|int
     */
    public function captureSort($descendantsCount)
    {
        if ('0' === $this->location) {
            $this->owner->sort = Catalog::INIT_SORT_VALUE;

            return 0;
        }

        if (!$this->sortHasChange) {
            return 0;
        }

        $location = Catalog::find()->where(['id' => self::getLocationId($this->location)])->limit(1)->one();

        $count = $descendantsCount + 2;
        if ($this->position == Catalog::POSITION_BEFORE) {
            $before            = Catalog::find()->where(['<', 'sort', $location->sort])->orderBy([
                'sort' => SORT_DESC,
                'id'   => SORT_DESC,
            ])->limit(1)->one();
            $delta             = !$before ? $location->sort / $count : ($location->sort - $before->sort) / $count;
            $this->owner->sort = $location->sort - $delta * ($count - 1);
        } else {
            if ($this->owner->path != $this->location) {
                $lastChild = Catalog::find()->where([
                    'like',
                    'path',
                    $this->location . '%',
                    false,
                ])->orderBy(['sort' => SORT_DESC, 'id' => SORT_DESC])->limit(1)->one();
                $location  = $lastChild ?: $location;
            }
            $after             = Catalog::find()->where(['>', 'sort', $location->sort])->orderBy([
                'sort' => SORT_ASC,
                'id'   => SORT_ASC,
            ])->limit(1)->one();
            $delta             = !$after ? $location->sort / $count : ($after->sort - $location->sort) / $count;
            $this->owner->sort = $location->sort + $delta;
        }

        return doubleval($delta);
    }

    /**
     * @param $location
     *
     * @return int
     */
    private static function getLocationId($location)
    {
        $path  = $location ?: '0';
        $start = strrpos($path, Catalog::PATH_SEPARATOR);

        return intval(substr($path, $start ? ($start + 1) : 0));
    }

    /**
     * @param string $path
     *
     * @return mixed|string
     */
    public static function escPathSeparator($path = '')
    {
        if (strlen($path) === 0) {
            $path = Catalog::PATH_SEPARATOR;
        }
        if (in_array(Catalog::PATH_SEPARATOR, ['/', '\'', '\\', '.', '^', '$'])) {
            $path = str_replace(Catalog::PATH_SEPARATOR, '\\' . Catalog::PATH_SEPARATOR, $path);
        }

        return $path;
    }

    /**
     * @param array $descendants
     *
     * @return false|int
     */
    public function theDelete($descendants = [])
    {
        if (empty($descendants)) {
            return $this->owner->delete();
        } else {
            return Catalog::deletes($this->owner, array_reverse($descendants));
        }
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param $value
     */
    public function setLocation($value)
    {
        $path = $value ?: $this->owner->path;
        if ($path !== $this->location) {
            $this->location      = $path;
            $this->sortHasChange = true;
        }
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param $value
     */
    public function setPosition($value)
    {
        if (array_key_exists($value, Catalog::positionList())) {
            if ($value !== $this->position) {
                $this->position      = $value;
                $this->sortHasChange = true;
            }
        }
    }
}
