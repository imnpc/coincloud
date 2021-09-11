<?php
/**
 * Created by PhpStorm.
 * User: alan
 * Date: 2019-07-26
 * Time: 17:51
 */

namespace solutionforest\LaravelAdmin\Translatable\Extensions\Traits;

use Illuminate\Support\Arr;

trait HasTranslationsField{

    public $isTranslatedForm = true;

    /**
     * Is input data is has-one relation.
     *
     * @param array $inserts
     *
     * @return bool
     */
    protected function isHasOneRelation($inserts): bool
    {
        $first = current($inserts);

        if($this->isTranslatedForm){
            if($this->model()->isTranslatableAttribute(key($inserts))){
                return false;
            }
        }

        if (!is_array($first)) {
            return false;
        }

        if (is_array(current($first))) {
            return false;
        }

        return Arr::isAssoc($first);
    }

    /**
     * Prepare input data for insert.
     *
     * @param $inserts
     *
     * @return array
     */
    protected function prepareInsert($inserts): array
    {
        if ($this->isHasOneRelation($inserts)) {
            $inserts = Arr::dot($inserts);
        }

        foreach ($inserts as $column => $value) {
            if (is_null($field = $this->getFieldByColumn($column))) {
                if($this->isTranslatedForm){
                    if($this->model()->isTranslatableAttribute($column)){
                        continue;
                    }
                }
                unset($inserts[$column]);
                continue;
            }

            $inserts[$column] = $field->prepare($value);
        }

        $prepared = [];

        foreach ($inserts as $key => $value) {
            Arr::set($prepared, $key, $value);
        }
        
        return $prepared;
    }

}
