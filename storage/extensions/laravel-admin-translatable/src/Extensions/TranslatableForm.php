<?php

namespace solutionforest\LaravelAdmin\Translatable\Extensions;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Form\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class TranslatableForm {


    const DEFAULT_KEY_NAME = '__LA_KEY__';

    /** @var \Closure|null */
    protected static $displayLocalizedNameByDefaultUsingCallback;

    /** @var string[] */
    protected $locales = [];

    /**
     * @var mixed
     */
    protected $key;

    /**
     * @var string
     */
    protected $relationName;

    /**
     * NestedForm key.
     *
     * @var Model
     */
    protected $model;

    /**
     * Fields in form.
     *
     * @var Collection
     */
    protected $fields;

    /**
     * Fields in form.
     *
     * @var Collection
     */
    protected $translatedFields;

    /**
     * Original data for this field.
     *
     * @var array
     */
    protected $original = [];

    /**
     * @var \Encore\Admin\Form
     */
    protected $form;


    /**
     * Create a new NestedForm instance.
     *
     * TranslatableForm constructor.
     *
     */
    public function __construct($model = null , $relationName = null)
    {
        $this->locales = config('app.locales') ?? [ 'en','zh-TW' ];
        $this->model = $model;

        $this->relationName = $relationName;
        $this->fields = new Collection();
        $this->translatedFields = new Collection();
        $this->displayLocalizedNameUsingCallback = self::$displayLocalizedNameByDefaultUsingCallback ?? function (Field $field, string $locale) {
                return ucfirst($field->label())." ({$locale})";
            };
    }

    /**
     * Get current model.
     *
     * @return Model|null
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Set Form.
     *
     * @param Form $form
     *
     * @return $this
     */
    public function setForm(Form $form = null)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form.
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set original values for fields.
     *
     * @param array  $data
     *
     * @return $this
     */
    public function setOriginal($data)
    {
        if (empty($data)) {
            return $this;
        }
        $this->original = $data;

        foreach ($this->fields() as $field) {
            collect($this->locales)->each(function ($locale) use ($field , $data) {
                $translatedField = $this->createTranslatedField($field, $locale);
                $translatedField->value($data[$field->column()][$locale] ?? '');
                $this->translatedFields->push($translatedField);
            });
        }
        return $this;
    }

    public function getColumns(){
        $columns = [];
        foreach($this->fields() as $field){
            $columns[] = $field->column();
        }
        return $columns;
    }

    /**
     * Get Orginal fields of this form.
     *
     * @return Collection
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * Get Translated fields of this form.
     *
     * @return Collection
     */
    public function translatedfields()
    {
        return $this->translatedFields;
    }


    /**
     * @param Field $field
     *
     * @return $this
     */
    public function pushField(Field $field)
    {
        $field->setForm($this->getForm());

        $this->fields->push($field);

        return $this;
    }


    /**
     * Add translatable-form fields dynamically.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if ($className = Form::findFieldClass($method)) {
            $column = Arr::get($arguments, 0, '');

            /* @var Field $field */
            $field = new $className($column, array_slice($arguments, 1));

            $field->setForm($this->form);

            if(!empty($this->relationName)){
                $field = $this->formatField($field);
            }

            $this->pushField($field);

            return $field;
        }

        return $this;
    }


    public function createTranslatedField(Field $originalField, string $locale): Field
    {
        // ERROR : Field's field is protected field
//        $field = clone $originalField;

        $clazzName = get_class($originalField);
        $shortname = Lang::get('admin.lang_shortname', [], $locale);
        $label = $originalField->label() . " [$shortname]";
        $column = $originalField->column();

        if($this->relationName){
            $key = $this->getKey();

            if (is_array($column)) {
                foreach ($column as $k => $name) {
                    $errorKey[$k] = sprintf('%s.%s.%s', $this->relationName, $key, $name);
                    $elementName[$k] =  sprintf('%s[%s][%s][%s]', $this->relationName, $key, $column,$locale);
                    $elementClass[$k] = [$this->relationName, $name];
                }
            } else {
                $errorKey = sprintf('%s.%s.%s', $this->relationName, $key, $column);
                $elementName = sprintf('%s[%s][%s][%s]', $this->relationName, $key, $column,$locale);
                $elementClass = [$this->relationName, $column];
            }

        }
        // clone Original Field to new Translated Field
        $column = $column . "[$locale]";
        $field = new $clazzName($column, [$label]);
        $field->attribute($originalField->variables()['attributes']);
        $field->attribute('translations', true);
        $field->attribute('curr_lang' , $locale);
        $field->value($originalField->variables()['value'][$locale] ?? '');

        if($this->relationName) {
            $field->setErrorKey($errorKey)
                ->setElementName($elementName)
                ->setElementClass($elementClass);
        }

        return $field;
    }

    /**
     * Fill data to all fields in form.
     *
     * @param array $data
     *
     * @return $this
     */
    public function fill($data)
    {
        /* @var Field $field */
        foreach ($this->fields() as $field) {
            $field->fill($data);
        }

        return $this;
    }

    /**
     * Get the html and script of template.
     *
     * @return array
     */
    public function getTemplateHtmlAndScript()
    {
        $html = '';
        $scripts = [];
        $currentLocale = config('app.locale') ?: session('locale');

        /* @var Field $field */
        foreach ($this->fields() as $field) {

            collect($this->locales)->each(function($locale) use ($field , &$html , $currentLocale){

                $translatedField = $this->createTranslatedField($field , $locale);

                //when field render, will push $script to Admin
                $field_html = $translatedField->render();

                if(config("admin.extensions.laravel-admin-translatable.options.isDisplayOneLanguageField" , true)) {
                    if ($currentLocale != $locale)
                        $field_html = str_replace('class="form-group', 'style="display:none" class="form-group', $field_html);
                }

                $html .= $field_html;

                /*
                 * Get and remove the last script of Admin::$script stack.
                 */
                if ($field->getScript()) {
                    $scripts[] = array_pop(Admin::$script);
                }
            });

        }

        return [$html, implode("\r\n", $scripts)];
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed|null
     */
    public function getKey()
    {
        if ($this->model) {
            $key = $this->model->getKey();
        }

        if (!is_null($this->key)) {
            $key = $this->key;
        }

        if (isset($key)) {
            return $key;
        }

        return 'new_'.static::DEFAULT_KEY_NAME;
    }

    /**
     * Set `errorKey` `elementName` `elementClass` for fields inside hasmany fields.
     *
     * @param Field $field
     *
     * @return Field
     */
    protected function formatField(Field $field)
    {
        $column = $field->column();

        $elementName = $elementClass = $errorKey = [];

        $key = $this->getKey();

        if (is_array($column)) {
            foreach ($column as $k => $name) {
                $errorKey[$k] = sprintf('%s.%s.%s', $this->relationName, $key, $name);
                $elementName[$k] = sprintf('%s[%s][%s]', $this->relationName, $key, $name);
                $elementClass[$k] = [$this->relationName, $name];
            }
        } else {
            $errorKey = sprintf('%s.%s.%s', $this->relationName, $key, $column);
            $elementName = sprintf('%s[%s][%s]', $this->relationName, $key, $column);
            $elementClass = [$this->relationName, $column];
        }

        return $field->setErrorKey($errorKey)
            ->setElementName($elementName)
            ->setElementClass($elementClass);
    }

}
