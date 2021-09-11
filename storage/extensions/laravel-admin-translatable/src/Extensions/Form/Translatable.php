<?php

namespace solutionforest\LaravelAdmin\Translatable\Extensions\Form;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Form\Field;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Psr\Log\InvalidArgumentException;
use solutionforest\LaravelAdmin\Translatable\Extensions\TranslatableForm;
use SolutionForest\Translatable\HasTranslationsConfig;

class Translatable extends Field
{
    /**
     * @var string
     */
    protected $relationName;
    /**
     * Form builder.
     *
     * @var \Closure
     */
    protected $builder = null;

    /**
     * @var Form\NestedForm null
     */
    protected $nestedForm = null;


    /**
     * @var TranslatableForm
     */
    protected $translatableForm = null;


    /**
     * Translatable constructor.
     *
     * @param \Closure $builder
     * @param null $relationName
     */
    public function __construct(\Closure $builder , $relationName = null)
    {
        if ($builder instanceof \Closure) {
            $this->builder = $builder;
            $this->relationName = $relationName ? $relationName[0] : null;
        }else{
            return new InvalidArgumentException();
        }
    }

    public function setForm(Form $form = null)
    {
        HasTranslationsConfig::setModifyToArrayAttributes(true);

        return parent::setForm($form);
    }

    /**
     * Build a Translatable form.
     *
     * @param \Closure $builder
     * @param null     $model
     *
     * @return TranslatableForm
     */
    protected function buildTranslatableForm(\Closure $builder, $model = null)
    {
        $this->translatableForm = new TranslatableForm($model,$this->relationName);

        $this->translatableForm->setForm($this->form);

        call_user_func($builder, $this->translatableForm);

        return $this->translatableForm;
    }

    public function setOriginal($data)
    {
        if(!$this->translatableForm) {
            $model = $this->form->model();
            $this->translatableForm = $this->buildTranslatableForm($this->builder, $model);
        }

        $this->translatableForm->setOriginal($data);
    }

    public function column()
    {
        if(!$this->translatableForm){
            $column = parent::column();
        }else{
            $column = $this->translatableForm->getColumns();
        }

        return $column;
    }

    public function fill($data)
    {
        parent::fill($data);
    }

    public function render()
    {
        $model = $this->form->model();

        if($this->relationName) {

            $relation = call_user_func([$model, $this->relationName]);

            if (!$relation instanceof Relation && !$relation instanceof MorphMany) {
                throw new \Exception('hasMany field must be a HasMany or MorphMany relation.');
            }

            $model = $relation->getRelated()->replicate();

            if($data = $this->data ?? []){
                $model->forceFill($data);
            }

        }

        list($template, $script) =  $this->buildTranslatableForm($this->builder , $model)
            ->fill($this->data)
            ->getTemplateHtmlAndScript();

        Admin::script($script);

        return $template;
    }
}