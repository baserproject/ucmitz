<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\View\Helper;

use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;
use Cake\View\Helper\FormHelper;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;
use Cake\View\Form\ContextInterface;
use BaserCore\View\Helper\BcUploadHelper;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\View\Helper\BcCkeditorHelper;

/**
 * FormHelper 拡張クラス
 *
 * @package Baser.View.Helper
 * @property BcHtmlHelper $BcHtml
 * @property BcUploadHelper $BcUpload
 */
class BcFormHelper extends FormHelper
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * Other helpers used by FormHelper
     *
     * @var array
     */
    public $helpers = [
        'Url',
        'Js',
        'Html',
        'BaserCore.BcHtml',
        'BaserCore.BcTime',
        'BaserCore.BcText',
        'BaserCore.BcUpload',
        'BaserCore.BcCkeditor'
    ];

// CUSTOMIZE ADD 2014/07/02 ryuring
// >>>
    /**
     * sizeCounter用の関数読み込み可否
     *
     * @var boolean
     */
    public $sizeCounterFunctionLoaded = false;

    /**
     * フォームID
     *
     * @var string
     */
    private $formId = null;
// <<<

    /**
     * フォームの最後のフィールドの後に発動する前提としてイベントを発動する
     *
     * ### 発動側
     * フォームの</table>の直前に記述して利用する
     *
     * ### コールバック処理
     * プラグインのコールバック処理で CakeEvent::data['fields'] に
     * 配列で行データを追加する事でフォームの最後に行を追加する事ができる。
     *
     * ### イベント名
     * コントローラー名.Form.afterForm Or コントローラー名.Form.afterOptionForm
     *
     * ### 行データのキー（配列）
     * - title：見出欄
     * - input：入力欄
     *
     * ### 行データの追加例
     *  $View = $event->subject();    // $event は、CakeEvent
     *  $input = $View->BcForm->input('Page.add_field', ['type' => 'input']);
     *  $event->setData('fields', [
     *      [
     *          'title'    => '追加フィールド',
     *          'input'    => $input
     *      ]
     *  ]);
     *
     * @param string $type フォームのタイプ タイプごとにイベントの登録ができる
     * @return string 行データ
     * @checked
     */
    public function dispatchAfterForm($type = ''): string
    {
        // TODO ucmitz 未実装のため代替措置
        // >>>
        return '';
        // <<<

        if ($type) {
            $type = Inflector::camelize($type);
        }

        $event = $this->dispatchLayerEvent('after' . $type . 'Form', ['fields' => [], 'id' => $this->__id], ['class' => 'Form', 'plugin' => '']);
        $out = '';
        if ($event !== false) {
            if (!empty($event->getData('fields'))) {
                foreach($event->getData('fields') as $field) {
                    $out .= "<tr>";
                    $out .= "<th class=\"col-head bca-form-table__label\">" . $field['title'] . "</th>\n";
                    $out .= "<td class=\"col-input bca-form-table__input\">" . $field['input'] . "</td>\n";
                    $out .= "</tr>";
                }
            }
        }
        return $out;
    }

    /**
     * コントロールソースを取得する
     * Model側でメソッドを用意しておく必要がある
     *
     * @param string $field フィールド名
     * @param array $options
     * @return Query|false コントロールソース
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource($field, $options = [])
    {
        $count = preg_match_all('/\./is', $field, $matches);
        if ($count === 1) {
            [$modelName, $field] = explode('.', $field);
            $plugin = $this->_View->getPlugin();
            if ($plugin) {
                $modelName = $plugin . '.' . $modelName;
            }
        } elseif ($count === 2) {
            [$plugin, $modelName, $field] = explode('.', $field);
            $modelName = $plugin . '.' . $modelName;
        }
        if (empty($modelName)) {
            return false;
        }
        $model = TableRegistry::getTableLocator()->get($modelName);
        if ($model && method_exists($model, 'getControlSource')) {
            return $model->getControlSource($field, $options);
        } else {
            return false;
        }
    }

    /**
     * カレンダーピッカー
     *
     * jquery-ui-1系 必須
     *
     * @param string フィールド文字列
     * @param array オプション
     * @return string html
     * @checked
     * @noTodo
     * @unitTest
     */
    public function datePicker($fieldName, $options = [])
    {
        $options = array_merge([
            'autocomplete' => 'off',
            'id' => $this->_domId($fieldName),
            'value' => $this->context()->val($fieldName)
        ], $options);
        if ($options['value']) {
            [$options['value'],] = explode(" ", str_replace('-', '/', $options['value']));
        }
        unset($options['type']);
        $input = $this->text($fieldName, $options);
        $script = <<< SCRIPT_END
<script>
jQuery(function($){
	$("#{$options['id']}").datepicker();
});
</script>
SCRIPT_END;
        return $input . "\n" . $script;
    }

    /**
     * カレンダピッカーとタイムピッカー
     *
     * jquery.timepicker.js 必須
     *
     * @param string $fieldName
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function dateTimePicker($fieldName, $options = [])
    {
        $options = array_merge([
            'div' => ['tag' => 'span'],
            'dateInput' => [],
            'dateDiv' => ['tag' => 'span'],
            'dateLabel' => ['text' => '日付'],
            'timeInput' => [],
            'timeDiv' => ['tag' => 'span'],
            'timeLabel' => ['text' => '時間'],
            'id' => $this->_domId($fieldName)
        ], $options);

        $dateOptions = array_merge($options, [
            'type' => 'datepicker',
            'div' => $options['dateDiv'],
            'label' => $options['dateLabel'],
            'autocomplete' => 'off'
        ], $options['dateInput']);

        $timeOptions = array_merge($options, [
            'type' => 'text',
            'div' => $options['timeDiv'],
            'label' => $options['timeLabel'],
            'autocomplete' => 'off',
            'size' => 8,
            'maxlength' => 8,
            'escape' => true,
            'id' => $options['id'] . '-time'
        ], $options['timeInput']);

        unset($options['dateDiv'], $options['dateLabel'], $options['timeDiv'], $options['timeLabel'], $options['dateInput'], $options['timeInput']);
        unset($dateOptions['dateDiv'], $dateOptions['dateLabel'], $dateOptions['timeDiv'], $dateOptions['timeLabel'], $dateOptions['dateInput'], $dateOptions['timeInput']);
        unset($timeOptions['dateDiv'], $timeOptions['dateLabel'], $timeOptions['timeDiv'], $timeOptions['timeLabel'], $timeOptions['dateInput'], $timeOptions['timeInput']);

        if (!isset($options['value'])) {
            $value = $this->context()->val($fieldName);
        } else {
            $value = $options['value'];
            unset($options['value']);
        }

        if ($value && $value != '0000-00-00 00:00:00') {
            [$dateValue, $timeValue] = explode(' ', $value);
            $dateOptions['value'] = $dateValue;
            $timeOptions['value'] = $timeValue;
        }

        $dateDivOptions = $timeDivOptions = $dateLabelOptions = $timeLabelOptions = null;
        if (!empty($dateOptions['div'])) {
            $dateDivOptions = $dateOptions['div'];
            unset($dateOptions['div']);
        }
        if (!empty($timeOptions['div'])) {
            $timeDivOptions = $timeOptions['div'];
            unset($timeOptions['div']);
        }
        if (!empty($dateOptions['label'])) {
            $dateLabelOptions = $dateOptions;
            unset($dateOptions['type'], $dateOptions['label']);
        }
        if (!empty($timeOptions['label'])) {
            $timeLabelOptions = $timeOptions;
            unset($timeOptions['type'], $timeOptions['label']);
        }

        $dateTag = $this->datePicker($fieldName . '_date', $dateOptions);
        if ($dateLabelOptions['label']) {
            $dateTag = $this->_getLabel($fieldName, $dateLabelOptions) . $dateTag;
        }
        if ($dateDivOptions) {
            $tag = 'div';
            if (!empty($dateDivOptions['tag'])) {
                $tag = $dateDivOptions['tag'];
                unset($dateDivOptions['tag']);
            }
            $dateTag = $this->BcHtml->tag($tag, $dateTag, $dateDivOptions);
        }

        $timeTag = $this->text($fieldName . '_time', $timeOptions);
        if ($timeLabelOptions['label']) {
            $timeTag = $this->_getLabel($fieldName, $timeLabelOptions) . $timeTag;
        }
        if ($timeDivOptions) {
            $tag = 'div';
            if (!empty($timeDivOptions['tag'])) {
                $tag = $timeDivOptions['tag'];
                unset($timeDivOptions['tag']);
            }
            $timeTag = $this->BcHtml->tag($tag, $timeTag, $timeDivOptions);
        }
        $hiddenTag = $this->hidden($fieldName, ['value' => $value]);
        $script = <<< SCRIPT_END
<script>
$(function(){
    var id = "{$options['id']}";
    var time = $("#" + id + "-time");
    var date = $("#" + id + "-date");
    time.timepicker({ 'timeFormat': 'H:i' });
    $([time, date]).change(function(){
        if(date.val() && !time.val()) {
            time.val('00:00');
        }
        var value = date.val().replace(/\//g, '-');
        if(time.val()) {
            value += ' ' + time.val();
        }
        $("#" + id).val(value);
    });
});
</script>
SCRIPT_END;
        return $dateTag . $timeTag . $hiddenTag . $script;
    }

    /**
     * Returns an HTML form element.
     *
     * ### Options:
     *
     * - `type` Form method defaults to autodetecting based on the form context. If
     *   the form context's isCreate() method returns false, a PUT request will be done.
     * - `method` Set the form's method attribute explicitly.
     * - `url` The URL the form submits to. Can be a string or a URL array.
     * - `encoding` Set the accept-charset encoding for the form. Defaults to `Configure::read('App.encoding')`
     * - `enctype` Set the form encoding explicitly. By default `type => file` will set `enctype`
     *   to `multipart/form-data`.
     * - `templates` The templates you want to use for this form. Any templates will be merged on top of
     *   the already loaded templates. This option can either be a filename in /config that contains
     *   the templates you want to load, or an array of templates to use.
     * - `context` Additional options for the context class. For example the EntityContext accepts a 'table'
     *   option that allows you to set the specific Table class the form should be based on.
     * - `idPrefix` Prefix for generated ID attributes.
     * - `valueSources` The sources that values should be read from. See FormHelper::setValueSources()
     * - `templateVars` Provide template variables for the formStart template.
     *
     * @param mixed $context The context for which the form is being defined.
     *   Can be a ContextInterface instance, ORM entity, ORM resultset, or an
     *   array of meta data. You can use `null` to make a context-less form.
     * @param array $options An array of html attributes and options.
     * @return string An formatted opening FORM tag.
     * @link https://book.cakephp.org/4/en/views/helpers/form.html#Cake\View\Helper\FormHelper::
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create($context = null, $options = []): string
    {


        // CUSTOMIZE ADD 2014/07/03 ryuring
        // ブラウザの妥当性のチェックを除外する
        // >>>
        $options = array_merge([
            'novalidate' => true
        ], $options);

        $formId = $this->setId($this->createId($context, $options));

        /*** beforeCreate ***/
        $event = $this->dispatchLayerEvent('beforeCreate', [
            'id' => $formId,
            'options' => $options
        ], ['class' => 'Form', 'plugin' => '']);
        if ($event !== false) {
            $options = ($event->getResult() === null || $event->getResult() === true)? $event->getData('options') : $event->getResult();
        }
        // <<<

        // 第１引数の $model が $context に変わった
        // >>>
        $out = parent::create($context, $options);
        // <<<

        // CUSTOMIZE ADD 2014/07/03 ryuring
        // >>>
        /*** afterCreate ***/
        $event = $this->dispatchLayerEvent('afterCreate', [
            'id' => $formId,
            'out' => $out
        ], ['class' => 'Form', 'plugin' => '']);
        if ($event !== false) {
            $out = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }

        return $out;
        // <<<

    }

    /**
     * Closes an HTML form, cleans up values set by FormHelper::create(), and writes hidden
     * input fields where appropriate.
     *
     * Resets some parts of the state, shared among multiple FormHelper::create() calls, to defaults.
     *
     * @param array $secureAttributes Secure attributes which will be passed as HTML attributes
     *   into the hidden input elements generated for the Security Component.
     * @return string A closing FORM tag.
     * @link https://book.cakephp.org/4/en/views/helpers/form.html#closing-the-form
     * @checked
     */
    public function end(array $secureAttributes = []): string
    {

        // CUSTOMIZE ADD 2014/07/03 ryuring
        // >>>
        $formId = $this->getId();
        $this->setId(null);

        /*** beforeEnd ***/
        $event = $this->dispatchLayerEvent('beforeEnd', [
            'id' => $formId,
            // 'options' => $options // TODO: ucmitz $secureAttributes = []どうするか考える
        ], ['class' => 'Form', 'plugin' => '']);
        if ($event !== false) {
            $options = ($event->getResult() === null || $event->getResult() === true)? $event->getData('options') : $event->getResult();
        }
        // <<<

        // 第１引数の $options が なくなった
        // >>>
        $out = parent::end($secureAttributes);
        // <<<

        // CUSTOMIZE ADD 2014/07/03 ryuring
        // >>>
        /*** afterEnd ***/
        $event = $this->dispatchLayerEvent('afterEnd', [
            'id' => $formId,
            'out' => $out
        ], ['class' => 'Form', 'plugin' => '']);
        if ($event !== false) {
            $out = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }

        return $out;
        // <<<
    }

    /**
     * Generates a form input element complete with label and wrapper div
     *
     * ### Options
     *
     * See each field type method for more information. Any options that are part of
     * $attributes or $options for the different **type** methods can be included in `$options` for input().i
     * Additionally, any unknown keys that are not in the list below, or part of the selected type's options
     * will be treated as a regular html attribute for the generated input.
     *
     * - `type` - Force the type of widget you want. e.g. `type => 'select'`
     * - `label` - Either a string label, or an array of options for the label. See FormHelper::label().
     * - `div` - Either `false` to disable the div, or an array of options for the div.
     *    See HtmlHelper::div() for more options.
     * - `options` - For widgets that take options e.g. radio, select.
     * - `error` - Control the error message that is produced. Set to `false` to disable any kind of error reporting (field
     *    error and error messages).
     * - `errorMessage` - Boolean to control rendering error messages (field error will still occur).
     * - `empty` - String or boolean to enable empty select box options.
     * - `before` - Content to place before the label + input.
     * - `after` - Content to place after the label + input.
     * - `between` - Content to place between the label + input.
     * - `format` - Format template for element order. Any element that is not in the array, will not be in the output.
     *    - Default input format order: array('before', 'label', 'between', 'input', 'after', 'error')
     *    - Default checkbox format order: array('before', 'input', 'between', 'label', 'after', 'error')
     *    - Hidden input will not be formatted
     *    - Radio buttons cannot have the order of input and label elements controlled with these settings.
     *
     * @param string $fieldName This should be "Modelname.fieldname"
     * @param array $options Each type of input takes different options.
     * @return string Completed form widget.
     * @link https://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#creating-form-elements
     */
    public function input($fieldName, $options = [])
    {

        // CUSTOMIZE ADD 2014/07/03 ryuring
        // >>>
        /*** beforeInput ***/
        $event = $this->dispatchLayerEvent('beforeInput', [
            'formId' => $this->__id,
            'data' => $this->request->data,
            'fieldName' => $fieldName,
            'options' => $options
        ], ['class' => 'Form', 'plugin' => '']);
        if ($event !== false) {
            $options = ($event->getResult() === null || $event->getResult() === true)? $event->getData('options') : $event->getResult();
            if (!$options) {
                $options = [];
            }
        }

        $type = '';
        if (isset($options['type'])) {
            $type = $options['type'];
        }

        if (!isset($options['error'])) {
            $options['error'] = false;
        }

        $class = 'bca-hidden__input';
        $divClass = 'bca-hidden';
        $labelClass = $childDivClass = $label = '';
        switch($type) {
            default :
                if (!isset($options['label'])) {
                    $options['label'] = false;
                }
                break;
            case 'file':
                $class = 'bca-file__input';
                $divClass = 'bca-file';
                if (!isset($options['label'])) {
                    $options['label'] = false;
                }
                $options = array_replace_recursive([
                    'link' => ['class' => 'bca-file__link'],
                    'class' => 'bca-file__input',
                    'div' => ['tag' => 'span', 'class' => 'bca-file'],
                    'deleteSpan' => ['class' => 'bca-file__delete'],
                    'deleteCheckbox' => ['class' => 'bca-file__delete-input'],
                    'deleteLabel' => ['class' => 'bca-file__delete-label'],
                    'figure' => ['class' => 'bca-file__figure'],
                    'img' => ['class' => 'bca-file__img'],
                    'figcaption' => ['class' => 'bca-file__figcaption']
                ], $options);
                break;
            case 'dateTimePicker':
                $divClass = 'bca-datetimepicker';
                $options['label'] = false;
                $options = array_replace_recursive([
                    'dateInput' => ['class' => 'bca-datetimepicker__date-input'],
                    'dateDiv' => ['tag' => 'span', 'class' => 'bca-datetimepicker__date'],
                    'dateLabel' => ['text' => '日付', 'class' => 'bca-datetimepicker__date-label'],
                    'timeInput' => ['class' => 'bca-datetimepicker__time-input'],
                    'timeDiv' => ['tag' => 'span', 'class' => 'bca-datetimepicker__time'],
                    'timeLabel' => ['text' => '時間', 'class' => 'bca-datetimepicker__time-label']
                ], $options);
                break;
            case 'text':
            case 'password':
            case 'datePicker':
                if (!isset($options['label'])) {
                    $options['label'] = false;
                }
                $class = 'bca-textbox__input';
                $divClass = 'bca-textbox';
                $labelClass = 'bca-textbox__label';
                break;
            case 'textarea':
                if (!isset($options['label'])) {
                    $options['label'] = false;
                }
                $class = 'bca-textarea__textarea';
                $divClass = 'bca-textarea';
                break;
            case 'checkbox':
                if (!isset($options['label'])) {
                    $options['label'] = false;
                }
                $class = 'bca-checkbox__input';
                $divClass = 'bca-checkbox';
                $labelClass = 'bca-checkbox__label';
                break;
            case 'select':
                if (!empty($options['multiple']) && $options['multiple'] === 'checkbox') {
                    $options['label'] = true;
                    $class = 'bca-checkbox__input';
                    $divClass = 'bca-checkbox-group';
                    $labelClass = 'bca-checkbox__label';
                    $childDivClass = 'bca-checkbox';
                } else {
                    if (!isset($options['label'])) {
                        $options['label'] = false;
                    }
                    $class = 'bca-select__select';
                    $divClass = 'bca-select';
                }
                break;
            case 'radio':
                if (!isset($options['legend'])) {
                    $options['legend'] = false;
                }
                if (!isset($options['separator'])) {
                    $options['separator'] = '　';
                }
                $options['label'] = true;
                $class = 'bca-radio__input';
                $divClass = 'bca-radio-group';
                $labelClass = 'bca-radio__label';
                $childDivClass = 'bca-radio';
                break;
        }

        if (!isset($options['div'])) {
            $options['div'] = ['tag' => 'span', 'class' => $divClass];
        }
        if (!isset($options['class'])) {
            $options['class'] = $class;
        }
        if (!isset($options['label']['class'])) {
            if (!empty($options['label'])) {
                if ($options['label'] !== true) {
                    $options['label'] = ['text' => $options['label'], 'class' => $labelClass];
                } else {
                    $options['label'] = ['class' => $labelClass];
                }
            }

        }
        if (!$type) {
            unset($options['class']);
        }
        // <<<

        $this->setEntity($fieldName);
        $options = $this->_parseOptions($options);

        $divOptions = $this->_divOptions($options);
        // CUSTOMIZE MODIFY 2018/10/13 ryuring
        // checkboxのdivを外せるオプションを追加
        // >>>
        //unset($options['div']);
        // ---
        if ($childDivClass && $options['div'] !== false) {
            $options['div']['class'] = $childDivClass;
        } elseif ($options['div'] !== false) {
            unset($options['div']);
        }
        $counter = false;
        if (isset($options['counter'])) {
            $counter = true;
            unset($options['counter']);
        }
        // <<<

        if ($options['type'] === 'radio' && isset($options['options'])) {
            $radioOptions = (array)$options['options'];
            unset($options['options']);
        } else {
            $radioOptions = [];
        }

        // CUSTOMIZE MODIFY 2014/10/27 ryuring
        // >>>
        //$label = $this->_getLabel($fieldName, $options);
        //if ($options['type'] !== 'radio') {
        // ---
        if ($options['type'] === 'checkbox' || (isset($options['multiple']) && $options['multiple'] === 'checkbox')) {
            $label = '';
        } else {
            $label = $this->_getLabel($fieldName, $options);
        }
        if ($options['type'] !== 'radio' && $options['type'] !== 'checkbox' && (!isset($options['multiple']) || $options['multiple'] !== 'checkbox')) {
            // <<<
            unset($options['label']);
        }

        $error = $this->_extractOption('error', $options, null);
        unset($options['error']);

        $errorMessage = $this->_extractOption('errorMessage', $options, true);
        unset($options['errorMessage']);

        $selected = $this->_extractOption('selected', $options, null);
        unset($options['selected']);

        if ($options['type'] === 'datetime' || $options['type'] === 'date' || $options['type'] === 'time') {
            $dateFormat = $this->_extractOption('dateFormat', $options, 'MDY');
            $timeFormat = $this->_extractOption('timeFormat', $options, 12);
            unset($options['dateFormat'], $options['timeFormat']);
        } else {
            $dateFormat = 'MDY';
            $timeFormat = 12;
        }

        $type = $options['type'];
        $out = ['before' => $options['before'], 'label' => $label, 'between' => $options['between'], 'after' => $options['after']];
        $format = $this->_getFormat($options);

        unset($options['type'], $options['before'], $options['between'], $options['after'], $options['format']);

        $out['error'] = null;
        if ($type !== 'hidden' && $error !== false) {
            $errMsg = $this->error($fieldName, $error);
            if ($errMsg) {
                $divOptions = $this->addClass($divOptions, Hash::get($divOptions, 'errorClass', 'error'));
                if ($errorMessage) {
                    $out['error'] = $errMsg;
                }
            }
        }

        if ($type === 'radio' && isset($out['between'])) {
            $options['between'] = $out['between'];
            $out['between'] = null;
        }
        $out['input'] = $this->_getInput(compact('type', 'fieldName', 'options', 'radioOptions', 'selected', 'dateFormat', 'timeFormat'));

        $output = '';
        foreach($format as $element) {
            $output .= $out[$element];
        }

        if (!empty($divOptions['tag'])) {
            $tag = $divOptions['tag'];
            unset($divOptions['tag'], $divOptions['errorClass']);
            $output = $this->Html->tag($tag, $output, $divOptions);
        }

        // CUSTOMIZE MODIFY 2014/07/03 ryuring
        // >>>
        // return $output;
        // ---

        /* カウンター */
        if (!empty($counter)) {
            $domId = $this->domId($fieldName, $options);
            $counter = '<span id="' . $domId . 'Counter' . '" class="bca-size-counter size-counter"></span>';
            $script = '$("#' . $domId . '").keyup(countSize);$("#' . $domId . '").keyup();';
            if (!$this->sizeCounterFunctionLoaded) {
                $script .= <<< DOC_END
function countSize() {
	var len = $(this).val().length;
	var maxlen = $(this).attr('maxlength');
	if(!maxlen || maxlen == -1){
		maxlen = '-';
	}
	$("#"+$(this).attr('id')+'Counter').html(len+' /<small>'+maxlen+'</small>');
}
DOC_END;
                $this->sizeCounterFunctionLoaded = true;
            }
            $output = $output . $counter . $this->Html->scriptblock($script);
        }

        /*** afterInput ***/
        $event = $this->dispatchLayerEvent('afterInput', [
            'formId' => $this->__id,
            'data' => $this->request->data,
            'fieldName' => $fieldName,
            'out' => $output
        ], ['class' => 'Form', 'plugin' => '']);

        if ($event !== false) {
            $output = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }

        return $output;
        // <<<
    }

    /**
     * Creates a checkbox input widget.
     * MODIFIED 2008/10/24 egashira
     *          hiddenタグを出力しないオプションを追加
     *
     * ### Options:
     *
     * - `value` - the value of the checkbox
     * - `checked` - boolean indicate that this checkbox is checked.
     * - `hiddenField` - boolean to indicate if you want the results of checkbox() to include
     *    a hidden input with a value of ''.
     * - `disabled` - create a disabled input.
     * - `default` - Set the default value for the checkbox. This allows you to start checkboxes
     *    as checked, without having to check the POST data. A matching POST data value, will overwrite
     *    the default value.
     *
     * @param string $fieldName Name of a field, like this "Modelname.fieldname"
     * @param array $options Array of HTML attributes.
     * @return string An HTML text input element.
     * @link https://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#options-for-select-checkbox-and-radio-inputs
     * @checked
     */
    public function checkbox($fieldName, $options = [])
    {

        // TODO ucmitz 未実装のため代替措置
        // >>>
        return parent::checkbox($fieldName, $options);
        // <<<

        // CUSTOMIZE ADD 2011/05/07 ryuring
        // >>> hiddenをデフォルトオプションに追加
        $options = array_merge([
            'hidden' => true
        ], $options);
        $hidden = $options['hidden'];
        $labelOptions = [];
        if (!empty($options['label'])) {
            if (is_array($options['label'])) {
                $label = $options['label']['text'];
                unset($options['label']['text']);
                $labelOptions = $options['label'];
            } else {
                $label = $options['label'];
            }
        }
        unset($options['label'], $options['hidden']);
        // <<<

        $valueOptions = [];
        if (isset($options['default'])) {
            $valueOptions['default'] = $options['default'];
            unset($options['default']);
        }

        $options += ['value' => 1, 'required' => false];
        $options = $this->_initInputField($fieldName, $options) + ['hiddenField' => true];
        $value = current($this->getSourceValue($valueOptions));
        $output = '';

        if ((!isset($options['checked']) && !empty($value) && $value == $options['value']) ||
            !empty($options['checked'])
        ) {
            $options['checked'] = 'checked';
        }

        // CUSTOMIZE MODIFY 2011/05/07 ryuring
        // >>> hiddenオプションがある場合のみ、hiddenタグを出力
        // 2014/03/23 ryuring CakePHP側が実装していたが互換性のために残す
        //if ($options['hiddenField']) {
        // ---
        if ($hidden !== false && $options['hiddenField'] !== false) {
            // <<<
            $hiddenOptions = [
                'id' => $options['id'] . '_',
                'name' => $options['name'],
                'value' => ($options['hiddenField'] !== true? $options['hiddenField'] : '0'),
                'form' => isset($options['form'])? $options['form'] : null,
                'secure' => false,
            ];
            if (isset($options['disabled']) && $options['disabled']) {
                $hiddenOptions['disabled'] = 'disabled';
            }
            $output = $this->hidden($fieldName, $hiddenOptions);
        }
        unset($options['hiddenField']);

        // CUSTOMIZE MODIFY 2011/05/07 ryuring
        // label を追加
        // CUSTOMIZE MODIRY 2014/10/27 ryuring
        // チェックボックスをラベルで囲う仕様に変更
        // CUSTOMIZE MODIRY 2017/2/19 ryuring
        // チェックボックスをラベルタグで囲わない仕様に変更した
        // >>>
        //return $output . $this->Html->useTag('checkbox', $options['name'], array_diff_key($options, array('name' => null)));
        // ---
        if (!empty($label)) {
            return $output . $this->Html->useTag('checkbox', $options['name'], array_diff_key($options, ['name' => null])) . parent::label($fieldName, $label, $labelOptions);
        } else {
            return $output . $this->Html->useTag('checkbox', $options['name'], array_diff_key($options, ['name' => null]));
        }
        // <<<
    }

    /**
     * Creates a hidden input field.
     *
     * @param string $fieldName Name of a field, in the form of "Modelname.fieldname"
     * @param array $options Array of HTML attributes.
     * @return string A generated hidden input
     * @link https://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#FormHelper::hidden
     * @checked
     */
    public function hidden($fieldName, $options = []): string
    {

        // TODO ucmitz 未実装のため代替措置
        // >>>
        return parent::hidden($fieldName, $options);
        // <<<

        $options += ['required' => false, 'secure' => true];

        $secure = $options['secure'];
        unset($options['secure']);

        // CUSTOMIZE ADD 2010/07/24 ryuring
        // セキュリティコンポーネントのトークン生成の仕様として、
        // ・hiddenタグ以外はフィールド情報のみ
        // ・hiddenタグはフィールド情報と値
        // をキーとして生成するようになっている。
        // その場合、生成の元のなる値は、multipleを想定されておらず、先頭の値のみとなるが
        // multiple な hiddenタグの場合、送信される値は配列で送信されるので値違いで認証がとおらない。
        // という事で、multiple の場合は、あくまでhiddenタグ以外のようにフィールド情報のみを
        // トークンのキーとする事で認証を通すようにする。
        // >>>
        if (!empty($options['multiple'])) {
            $secure = false;
            $this->_secure(true); //lock
        }
        // <<<

        $options = $this->_initInputField($fieldName, array_merge(
            $options, ['secure' => static::SECURE_SKIP]
        ));

        if ($secure === true) {
            $this->_secure(true, null, '' . $options['value']);
        }

        // CUSTOMIZE 2010/07/24 ryuring
        // 配列用のhiddenタグを出力できるオプションを追加
        // CUSTOMIZE 2010/08/01 ryuring
        // class属性を指定できるようにした
        // CUSTOMIZE 2011/03/11 ryuring
        // multiple で送信する値が配列の添字となっていたので配列の値に変更した
        // >>> ADD
        $multiple = false;
        $value = '';
        if (!empty($options['multiple'])) {
            $multiple = true;
            $options['id'] = null;
            if (!isset($options['value'])) {
                $value = $this->getSourceValue($fieldName);
            } else {
                $value = $options['value'];
            }
            if (is_array($value) && !$value) {
                unset($options['value']);
            }
            unset($options['multiple']);
        }
        // <<<
        // >>> MODIFY
        // $this->Html->useTag('hidden', $options['name'], array_diff_key($options, array('name' => null)));
        // ---
        if ($multiple && is_array($value)) {
            $out = [];
            foreach($value as $_value) {
                $options['value'] = $_value;
                $out[] = $this->Html->useTag('hiddenmultiple', $options['name'], array_diff_key($options, ['name' => '']));
            }
            return implode("\n", $out);
        } else {
            return $this->Html->useTag('hidden', $options['name'], array_diff_key($options, ['name' => '']));
        }
        // <<<
    }

    /**
     * Creates a submit button element. This method will generate `<input />` elements that
     * can be used to submit, and reset forms by using $options. image submits can be created by supplying an
     * image path for $caption.
     *
     * ### Options
     *
     * - `div` - Include a wrapping div?  Defaults to true. Accepts sub options similar to
     *   FormHelper::input().
     * - `before` - Content to include before the input.
     * - `after` - Content to include after the input.
     * - `type` - Set to 'reset' for reset inputs. Defaults to 'submit'
     * - `confirm` - JavaScript confirmation message.
     * - Other attributes will be assigned to the input element.
     *
     * ### Options
     *
     * - `div` - Include a wrapping div?  Defaults to true. Accepts sub options similar to
     *   FormHelper::input().
     * - Other attributes will be assigned to the input element.
     *
     * @param string $caption The label appearing on the button OR if string contains :// or the
     *  extension .jpg, .jpe, .jpeg, .gif, .png use an image if the extension
     *  exists, AND the first character is /, image is relative to webroot,
     *  OR if the first character is not /, image is relative to webroot/img.
     * @param array $options Array of options. See above.
     * @return string A HTML submit button
     * @link https://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#FormHelper::submit
     * @checked
     * @noTodo
     * @unitTest
     */
    public function submit($caption = null, $options = []): string
    {
        // CUSTOMIZE ADD 2016/06/08 ryuring
        // >>>
        /*** beforeInput ***/
        $event = $this->dispatchLayerEvent('beforeSubmit', [
            'id' => $this->getId(),
            'caption' => $caption,
            'options' => $options
        ], ['class' => 'Form', 'plugin' => '']);
        if ($event !== false) {
            $options = ($event->getResult() === null || $event->getResult() === true)? $event->getData('options') : $event->getResult();
        }

        $output = parent::submit($caption, $options);

        /*** afterInput ***/
        $event = $this->dispatchLayerEvent('afterSubmit', [
            'id' => $this->getId(),
            'caption' => $caption,
            'out' => $output
        ], ['class' => 'Form', 'plugin' => '']);
        if ($event !== false) {
            $output = ($event->getResult() === null || $event->getResult() === true)? $event->getData('out') : $event->getResult();
        }
        return $output;
        // <<<

    }

    /**
     * Returns a set of SELECT elements for a full datetime setup: day, month and year, and then time.
     *
     * ### Attributes:
     *
     * - `monthNames` If false, 2 digit numbers will be used instead of text.
     *   If an array, the given array will be used.
     * - `minYear` The lowest year to use in the year select
     * - `maxYear` The maximum year to use in the year select
     * - `interval` The interval for the minutes select. Defaults to 1
     * - `separator` The contents of the string between select elements. Defaults to '-'
     * - `empty` - If true, the empty select option is shown. If a string,
     *   that string is displayed as the empty element.
     * - `round` - Set to `up` or `down` if you want to force rounding in either direction. Defaults to null.
     * - `value` | `default` The default value to be used by the input. A value in `$this->data`
     *   matching the field name will override this value. If no default is provided `time()` will be used.
     *
     * @param string $fieldName Prefix name for the SELECT element
     * @param string $dateFormat DMY, MDY, YMD, or null to not generate date inputs.
     * - W が入力された場合、和暦のselectと年月日の接尾辞が付与される
     * @param string $timeFormat 12, 24, or null to not generate time inputs.
     * @param array $attributes Array of Attributes
     * @return string Generated set of select boxes for the date and time formats chosen.
     * @link https://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#FormHelper::dateTime
     */
    public function dateTime($fieldName, $dateFormat = 'DMY', $timeFormat = '12', $attributes = []): string
    {
        $attributes += ['empty' => true, 'value' => null];
        $year = $month = $day = $hour = $min = $meridian = null;

        if (empty($attributes['value'])) {
            $attributes = $this->getSourceValue($attributes, $fieldName);
        }

        if ($attributes['value'] === null && $attributes['empty'] != true) {
            $attributes['value'] = time();
            if (!empty($attributes['maxYear']) && $attributes['maxYear'] < date('Y')) {
                $attributes['value'] = strtotime(date($attributes['maxYear'] . '-m-d'));
            }
        }

        if (!empty($attributes['value'])) {
            [$year, $month, $day, $hour, $min, $meridian] = $this->_getDateTimeValue(
                $attributes['value'],
                $timeFormat
            );
        }

        // >>> CUSTOMIZE MODIFY 2011/01/11 ryuring	日本対応
        /* $defaults = array(
            'minYear' => null, 'maxYear' => null, 'separator' => '-',
            'interval' => 1, 'monthNames' => true, 'round' => null
        ); */
        // ---
        $defaults = [
            'minYear' => null, 'maxYear' => null, 'separator' => ' ',
            'interval' => 1, 'monthNames' => '', 'round' => null
        ];
        // <<<

        $attributes = array_merge($defaults, (array)$attributes);
        if (isset($attributes['minuteInterval'])) {
            $attributes['interval'] = $attributes['minuteInterval'];
            unset($attributes['minuteInterval']);
        }
        $minYear = $attributes['minYear'];
        $maxYear = $attributes['maxYear'];
        $separator = $attributes['separator'];
        $interval = $attributes['interval'];
        $monthNames = $attributes['monthNames'];
        $round = $attributes['round'];
        $attributes = array_diff_key($attributes, $defaults);

        if (!empty($interval) && $interval > 1 && !empty($min)) {
            $current = new DateTime();
            if ($year !== null) {
                $current->setDate($year, $month, $day);
            }
            if ($hour !== null) {
                $current->setTime($hour, $min);
            }
            $changeValue = $min * (1 / $interval);
            switch($round) {
                case 'up':
                    $changeValue = ceil($changeValue);
                    break;
                case 'down':
                    $changeValue = floor($changeValue);
                    break;
                default:
                    $changeValue = round($changeValue);
            }
            $change = ($changeValue * $interval) - $min;
            $current->modify($change > 0? "+$change minutes" : "$change minutes");
            $format = ($timeFormat == 12)? 'Y m d h i a' : 'Y m d H i a';
            $newTime = explode(' ', $current->format($format));
            [$year, $month, $day, $hour, $min, $meridian] = $newTime;
        }

        $keys = ['Day', 'Month', 'Year', 'Hour', 'Minute', 'Meridian'];
        $attrs = array_fill_keys($keys, $attributes);

        $hasId = isset($attributes['id']);
        if ($hasId && is_array($attributes['id'])) {
            // check for missing ones and build selectAttr for each element
            $attributes['id'] += [
                'month' => '',
                'year' => '',
                'day' => '',
                'hour' => '',
                'minute' => '',
                'meridian' => ''
            ];
            foreach($keys as $key) {
                $attrs[$key]['id'] = $attributes['id'][strtolower($key)];
            }
        }
        if ($hasId && is_string($attributes['id'])) {
            // build out an array version
            foreach($keys as $key) {
                $attrs[$key]['id'] = $attributes['id'] . $key;
            }
        }

        if (is_array($attributes['empty'])) {
            $attributes['empty'] += [
                'month' => true,
                'year' => true,
                'day' => true,
                'hour' => true,
                'minute' => true,
                'meridian' => true
            ];
            foreach($keys as $key) {
                $attrs[$key]['empty'] = $attributes['empty'][strtolower($key)];
            }
        }

        $selects = [];
        foreach(preg_split('//', $dateFormat, -1, PREG_SPLIT_NO_EMPTY) as $char) {
            switch($char) {
                // >>> CUSTOMIZE ADD 2011/01/11 ryuring	和暦対応
                case 'W':
                    $selects[] = $this->wyear($fieldName, $minYear, $maxYear, $year, $attributes, $attributes['empty']) . "年";
                    break;
                // <<<
                case 'Y':
                    $attrs['Year']['value'] = $year;

                    // >>> CUSTOMIZE MODIFY 2011/01/11 ryuring	日本対応
                    /* $selects[] = $this->year(
                        $fieldName, $minYear, $maxYear, $attrs['Year']
                    ); */
                    // ---
                    $suffix = (preg_match('/^W/', $dateFormat))? '年' : '';
                    $selects[] = $this->year(
                            $fieldName, $minYear, $maxYear, $attrs['Year']
                        ) . $suffix;
                    // <<<

                    break;
                case 'M':
                    $attrs['Month']['value'] = $month;
                    $attrs['Month']['monthNames'] = $monthNames;

                    // >>> CUSTOMIZE MODIFY 2011/01/11 ryuring	日本対応
                    /* $selects[] = $this->month($fieldName, $attrs['Month']); */
                    // ---
                    $suffix = (preg_match('/^W/', $dateFormat))? '月' : '';
                    $selects[] = $this->month($fieldName, $attrs['Month']) . $suffix;
                    // <<<

                    break;
                case 'D':
                    $attrs['Day']['value'] = $day;

                    // >>> CUSTOMIZE MODIFY 2011/01/11 ryuring	日本対応
                    /* $selects[] = $this->day($fieldName, $attrs['Day']); */
                    // ---
                    $suffix = (preg_match('/^W/', $dateFormat))? '日' : '';
                    $selects[] = $this->day($fieldName, $attrs['Day']) . $suffix;
                    // <<<

                    break;
            }
        }
        $opt = implode($separator, $selects);

        $attrs['Minute']['interval'] = $interval;
        switch($timeFormat) {
            case '24':
                $attrs['Hour']['value'] = $hour;
                $attrs['Minute']['value'] = $min;
                $opt .= $this->hour($fieldName, true, $attrs['Hour']) . ':' .
                    $this->minute($fieldName, $attrs['Minute']);
                break;
            case '12':
                $attrs['Hour']['value'] = $hour;
                $attrs['Minute']['value'] = $min;
                $attrs['Meridian']['value'] = $meridian;
                $opt .= $this->hour($fieldName, false, $attrs['Hour']) . ':' .
                    $this->minute($fieldName, $attrs['Minute']) . ' ' .
                    $this->meridian($fieldName, $attrs['Meridian']);
                break;
        }
        return $opt;
    }

    /**
     * Returns a formatted SELECT element.
     *
     * ### Attributes:
     *
     * - `showParents` - If included in the array and set to true, an additional option element
     *   will be added for the parent of each option group. You can set an option with the same name
     *   and it's key will be used for the value of the option.
     * - `multiple` - show a multiple select box. If set to 'checkbox' multiple checkboxes will be
     *   created instead.
     * - `empty` - If true, the empty select option is shown. If a string,
     *   that string is displayed as the empty element.
     * - `escape` - If true contents of options will be HTML entity encoded. Defaults to true.
     * - `value` The selected value of the input.
     * - `class` - When using multiple = checkbox the class name to apply to the divs. Defaults to 'checkbox'.
     * - `disabled` - Control the disabled attribute. When creating a select box, set to true to disable the
     *   select box. When creating checkboxes, `true` will disable all checkboxes. You can also set disabled
     *   to a list of values you want to disable when creating checkboxes.
     *
     * ### Using options
     *
     * A simple array will create normal options:
     *
     * ```
     * $options = array(1 => 'one', 2 => 'two);
     * $this->Form->select('Model.field', $options));
     * ```
     *
     * While a nested options array will create optgroups with options inside them.
     * ```
     * $options = array(
     *  1 => 'bill',
     *  'fred' => array(
     *     2 => 'fred',
     *     3 => 'fred jr.'
     *  )
     * );
     * $this->Form->select('Model.field', $options);
     * ```
     *
     * In the above `2 => 'fred'` will not generate an option element. You should enable the `showParents`
     * attribute to show the fred option.
     *
     * If you have multiple options that need to have the same value attribute, you can
     * use an array of arrays to express this:
     *
     * ```
     * $options = array(
     *  array('name' => 'United states', 'value' => 'USA'),
     *  array('name' => 'USA', 'value' => 'USA'),
     * );
     * ```
     *
     * @param string $fieldName Name attribute of the SELECT
     * @param array $options Array of the OPTION elements (as 'value'=>'Text' pairs) to be used in the
     *    SELECT element
     * @param array $attributes The HTML attributes of the select element.
     * @return string Formatted SELECT element
     * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#options-for-select-checkbox-and-radio-inputs
     * @checked
     */
    public function select($fieldName, $options = [], $attributes = []): string
    {
        // TODO ucmitz 未実装のため代替措置
        // >>>
        return parent::select($fieldName, $options, $attributes);
        // <<<

        $select = [];
        $style = null;
        $tag = null;
        $attributes += [
            'class' => null,
            'escape' => true,
            'secure' => true,
            'empty' => '',
            'showParents' => false,
            'hiddenField' => true,
            'disabled' => false,
            // CUSTOMIZE ADD 2016/01/26 ryuring
            // checkboxのdivを外せるオプションを追加
            // CUSTOMIZE ADD 2018/10/14 ryuring
            // label のオプションを指定できるようにした
            // >>>
            'div' => true,
            'label' => false
            // <<<
        ];

        // CUSTOMIZE ADD 2016/01/28 ryuring
        // checkboxのdivを外せるオプションを追加
        // >>>
        if ($attributes['div'] === 'false' || $attributes['div'] === '0') {
            $attributes['div'] = false;
        }
        $div = $this->_extractOption('div', $attributes);
        unset($attributes['div']);
        // <<<

        $escapeOptions = $this->_extractOption('escape', $attributes);
        $secure = $this->_extractOption('secure', $attributes);
        $showEmpty = $this->_extractOption('empty', $attributes);
        $showParents = $this->_extractOption('showParents', $attributes);
        $hiddenField = $this->_extractOption('hiddenField', $attributes);
        unset($attributes['escape'], $attributes['secure'], $attributes['empty'], $attributes['showParents'], $attributes['hiddenField']);
        $id = $this->_extractOption('id', $attributes);

        $attributes = $this->_initInputField($fieldName, array_merge(
            (array)$attributes, ['secure' => static::SECURE_SKIP]
        ));

        if (is_string($options) && isset($this->_options[$options])) {
            $options = $this->_generateOptions($options);
        } elseif (!is_array($options)) {
            $options = [];
        }
        if (isset($attributes['type'])) {
            unset($attributes['type']);
        }

        if (!empty($attributes['multiple'])) {
            $style = ($attributes['multiple'] === 'checkbox')? 'checkbox' : null;
            $template = ($style)? 'checkboxmultiplestart' : 'selectmultiplestart';
            $tag = $template;
            if ($hiddenField) {
                $hiddenAttributes = [
                    'value' => '',
                    'id' => $attributes['id'] . ($style? '' : '_'),
                    'secure' => false,
                    'form' => isset($attributes['form'])? $attributes['form'] : null,
                    'name' => $attributes['name'],
                    'disabled' => $attributes['disabled'] === true || $attributes['disabled'] === 'disabled'
                ];
                $select[] = $this->hidden(null, $hiddenAttributes);
            }
        } else {
            $tag = 'selectstart';
        }

        if ($tag === 'checkboxmultiplestart') {
            unset($attributes['required']);
        }

        if (!empty($tag) || isset($template)) {
            $hasOptions = (count($options) > 0 || $showEmpty);
            // Secure the field if there are options, or its a multi select.
            // Single selects with no options don't submit, but multiselects do.
            if ((!isset($secure) || $secure) &&
                empty($attributes['disabled']) &&
                (!empty($attributes['multiple']) || $hasOptions)
            ) {
                $this->_secure(true, $this->_secureFieldName($attributes));
            }
            $filter = ['name' => null, 'value' => null];
            if (is_array($attributes['disabled'])) {
                $filter['disabled'] = null;
            }
            $select[] = $this->Html->useTag($tag, $attributes['name'], array_diff_key($attributes, $filter));
        }
        $emptyMulti = (
            $showEmpty !== null && $showEmpty !== false && !(
                empty($showEmpty) && (isset($attributes) &&
                    array_key_exists('multiple', $attributes))
            )
        );

        if ($emptyMulti) {
            $showEmpty = ($showEmpty === true)? '' : $showEmpty;
            $options = ['' => $showEmpty] + $options;
        }

        if (!$id) {
            $attributes['id'] = Inflector::camelize($attributes['id']);
        }

        $select = array_merge($select, $this->_selectOptions(
            array_reverse($options, true),
            [],
            $showParents,
            [
                'escape' => $escapeOptions,
                'style' => $style,
                'name' => $attributes['name'],
                'value' => $attributes['value'],
                'class' => $attributes['class'],
                'id' => $attributes['id'],
                'disabled' => $attributes['disabled'],
                // CUSTOMIZE ADD 2016/01/26 ryuring
                // checkboxのdivを外せるオプションを追加
                // CUSTOMIZE ADD 2018/10/14 ryuring
                // label のオプションを指定できるようにした
                // >>>
                'div' => $div,
                'label' => $attributes['label']
                // <<<
            ]
        ));

        $template = ($style === 'checkbox')? 'checkboxmultipleend' : 'selectend';
        $select[] = $this->Html->useTag($template);
        return implode("\n", $select);
    }

    /**
     * Returns an array of formatted OPTION/OPTGROUP elements
     *
     * @param array $elements Elements to format.
     * @param array $parents Parents for OPTGROUP.
     * @param bool $showParents Whether to show parents.
     * @param array $attributes HTML attributes.
     * @return array
     */
    protected function _selectOptions($elements = [], $parents = [], $showParents = null, $attributes = [])
    {
        $select = [];
        $attributes = array_merge(
            ['escape' => true, 'style' => null, 'value' => null, 'class' => null],
            $attributes
        );
        $selectedIsEmpty = ($attributes['value'] === '' || $attributes['value'] === null);
        $selectedIsArray = is_array($attributes['value']);

        // Cast boolean false into an integer so string comparisons can work.
        if ($attributes['value'] === false) {
            $attributes['value'] = 0;
        }

        $this->_domIdSuffixes = [];
        foreach($elements as $name => $title) {
            $htmlOptions = [];
            if (is_array($title) && (!isset($title['name']) || !isset($title['value']))) {
                if (!empty($name)) {
                    if ($attributes['style'] === 'checkbox') {
                        $select[] = $this->Html->useTag('fieldsetend');
                    } else {
                        $select[] = $this->Html->useTag('optiongroupend');
                    }
                    $parents[] = (string)$name;
                }
                $select = array_merge($select, $this->_selectOptions(
                    $title, $parents, $showParents, $attributes
                ));

                if (!empty($name)) {
                    $name = $attributes['escape']? h($name) : $name;
                    if ($attributes['style'] === 'checkbox') {
                        $select[] = $this->Html->useTag('fieldsetstart', $name);
                    } else {
                        $select[] = $this->Html->useTag('optiongroup', $name, '');
                    }
                }
                $name = null;
            } elseif (is_array($title)) {
                $htmlOptions = $title;
                $name = $title['value'];
                $title = $title['name'];
                unset($htmlOptions['name'], $htmlOptions['value']);
            }

            if ($name !== null) {
                $isNumeric = is_numeric($name);
                if ((!$selectedIsArray && !$selectedIsEmpty && (string)$attributes['value'] == (string)$name) ||
                    ($selectedIsArray && in_array((string)$name, $attributes['value'], !$isNumeric))
                ) {
                    if ($attributes['style'] === 'checkbox') {
                        $htmlOptions['checked'] = true;
                    } else {
                        $htmlOptions['selected'] = 'selected';
                    }
                }

                if ($showParents || (!in_array($title, $parents))) {
                    $title = ($attributes['escape'])? h($title) : $title;

                    $hasDisabled = !empty($attributes['disabled']);
                    if ($hasDisabled) {
                        $disabledIsArray = is_array($attributes['disabled']);
                        if ($disabledIsArray) {
                            $disabledIsNumeric = is_numeric($name);
                        }
                    }
                    if ($hasDisabled &&
                        $disabledIsArray &&
                        in_array((string)$name, $attributes['disabled'], !$disabledIsNumeric)
                    ) {
                        $htmlOptions['disabled'] = 'disabled';
                    }
                    if ($hasDisabled && !$disabledIsArray && $attributes['style'] === 'checkbox') {
                        $htmlOptions['disabled'] = $attributes['disabled'] === true? 'disabled' : $attributes['disabled'];
                    }

                    if ($attributes['style'] === 'checkbox') {
                        $htmlOptions['value'] = $name;

                        $tagName = $attributes['id'] . $this->domIdSuffix($name);
                        $htmlOptions['id'] = $tagName;
                        $label = ['for' => $tagName];

                        if (isset($htmlOptions['checked']) && $htmlOptions['checked'] === true) {
                            $label['class'] = 'selected';
                        }

                        $name = $attributes['name'];

                        if (empty($attributes['class'])) {
                            $attributes['class'] = 'checkbox';
                        } elseif ($attributes['class'] === 'form-error') {
                            $attributes['class'] = 'checkbox ' . $attributes['class'];
                        }

                        // CUSTOMIZE MODIFY 2014/02/24 ryuring
                        // checkboxのdivを外せるオプションを追加
                        // CUSTOMIZE MODIFY 2014/10/27 ryuring
                        // チェックボックスをラベルタグで囲う仕様に変更した
                        // CUSTOMIZE MODIFY 2017/2/19 ryuring
                        // チェックボックスをラベルタグで囲わない仕様に変更した
                        // CUSTOMIZE MODIFY 2018/09/14 ryuring
                        // チェックボックスとラベルを span タグで挟めるようにした
                        // CUSTOMIZE MODIFY 2018/10/14 ryuring
                        // ラベルのクラスを指定できるようにした
                        // span のクラスを指定できるようにした
                        // >>>
                        // $label = $this->label(null, $title, $label);
                        // $item = $this->Html->useTag('checkboxmultiple', $name, $htmlOptions);
                        // $select[] = $this->Html->div($attributes['class'], $item . $label);
                        // ---
                        if (!empty($attributes['class'])) {
                            $htmlOptions['class'] = $attributes['class'];
                        }
                        if (!empty($attributes['label']) && is_array($attributes['label'])) {
                            if (!empty($attributes['label']['class'])) {
                                if (!empty($label['class']) && $label['class'] === 'selected') {
                                    $label['class'] .= ' ' . $attributes['label']['class'];
                                } else {
                                    $label['class'] = $attributes['label']['class'];
                                }
                            }
                            $label = array_merge($label, $attributes['label']);
                        }
                        $item = $this->Html->useTag('checkboxmultiple', $name, $htmlOptions) . $this->label(null, $title, $label);
                        if (isset($attributes['div'])) {
                            if ($attributes['div'] === false) {
                                $select[] = $item;
                            } elseif (is_array($attributes['div'])) {
                                $divOptions = $attributes['div'];
                                $tag = 'div';
                                if (!empty($divOptions['tag'])) {
                                    $tag = $divOptions['tag'];
                                }
                                unset($divOptions['tag'], $divOptions['errorClass']);
                                $select[] = $this->Html->tag($tag, $item, $divOptions);
                            } else {
                                $select[] = $this->Html->div($attributes['class'], $item);
                            }
                        } else {
                            $select[] = $this->Html->div($attributes['class'], $item);
                        }
                        // <<<

                    } else {
                        if ($attributes['escape']) {
                            $name = h($name);
                        }
                        $select[] = $this->Html->useTag('selectoption', $name, $htmlOptions, $title);
                    }
                }
            }
        }

        return array_reverse($select, true);
    }

    /**
     * Generates option lists for common <select /> menus
     *
     * @param string $name List type name.
     * @param array $options Options list.
     * @return array
     */
    protected function _generateOptions($name, $options = [])
    {
        if (!empty($this->options[$name])) {
            return $this->options[$name];
        }
        $data = [];

        switch($name) {
            case 'minute':
                if (isset($options['interval'])) {
                    $interval = $options['interval'];
                } else {
                    $interval = 1;
                }
                $i = 0;
                while($i < 60) {
                    $data[sprintf('%02d', $i)] = sprintf('%02d', $i);
                    $i += $interval;
                }
                break;
            case 'hour':
                for($i = 1; $i <= 12; $i++) {
                    $data[sprintf('%02d', $i)] = $i;
                }
                break;
            case 'hour24':
                for($i = 0; $i <= 23; $i++) {
                    $data[sprintf('%02d', $i)] = $i;
                }
                break;
            case 'meridian':
                $data = ['am' => 'am', 'pm' => 'pm'];
                break;
            case 'day':
                for($i = 1; $i <= 31; $i++) {
                    $data[sprintf('%02d', $i)] = $i;
                }
                break;
            case 'month':
                if ($options['monthNames'] === true) {
                    $data['01'] = __d('cake', 'January');
                    $data['02'] = __d('cake', 'February');
                    $data['03'] = __d('cake', 'March');
                    $data['04'] = __d('cake', 'April');
                    $data['05'] = __d('cake', 'May');
                    $data['06'] = __d('cake', 'June');
                    $data['07'] = __d('cake', 'July');
                    $data['08'] = __d('cake', 'August');
                    $data['09'] = __d('cake', 'September');
                    $data['10'] = __d('cake', 'October');
                    $data['11'] = __d('cake', 'November');
                    $data['12'] = __d('cake', 'December');
                } elseif (is_array($options['monthNames'])) {
                    $data = $options['monthNames'];
                } else {
                    for($m = 1; $m <= 12; $m++) {
                        $data[sprintf("%02s", $m)] = strftime("%m", mktime(1, 1, 1, $m, 1, 1999));
                    }
                }
                break;
            case 'year':
                $current = (int)date('Y');

                $min = !isset($options['min'])? $current - 20 : (int)$options['min'];
                $max = !isset($options['max'])? $current + 20 : (int)$options['max'];

                if ($min > $max) {
                    [$min, $max] = [$max, $min];
                }
                if (!empty($options['value']) &&
                    (int)$options['value'] < $min &&
                    (int)$options['value'] > 0
                ) {
                    $min = (int)$options['value'];
                } elseif (!empty($options['value']) && (int)$options['value'] > $max) {
                    $max = (int)$options['value'];
                }

                for($i = $min; $i <= $max; $i++) {
                    $data[$i] = $i;
                }
                if ($options['order'] !== 'asc') {
                    $data = array_reverse($data, true);
                }
                break;
            // >>> CUSTOMIZE ADD 2011/01/11 ryuring	和暦対応
            case 'wyear':
                $current = intval(date('Y'));

                if (!isset($options['min'])) {
                    $min = $current - 20;
                } else {
                    $min = $options['min'];
                }

                if (!isset($options['max'])) {
                    $max = $current + 20;
                } else {
                    $max = $options['max'];
                }
                if ($min > $max) {
                    [$min, $max] = [$max, $min];
                }
                for($i = $min; $i <= $max; $i++) {
                    $wyears = $this->BcTime->convertToWarekiYear($i);
                    if ($wyears) {
                        foreach($wyears as $value) {
                            [$w, $year] = explode('-', $value);
                            $data[$value] = $this->BcTime->nengo($w) . ' ' . $year;
                        }
                    }
                }
                $data = array_reverse($data, true);
                break;
            // <<<
        }
        $this->_options[$name] = $data;
        return $this->_options[$name];
    }

    /**
     * Creates a set of radio widgets. Will create a legend and fieldset
     * by default. Use $options to control this
     *
     * You can also customize each radio input element using an array of arrays:
     *
     * ```
     * $options = array(
     *  array('name' => 'United states', 'value' => 'US', 'title' => 'My title'),
     *  array('name' => 'Germany', 'value' => 'DE', 'class' => 'de-de', 'title' => 'Another title'),
     * );
     * ```
     *
     * ### Attributes:
     *
     * - `separator` - define the string in between the radio buttons
     * - `between` - the string between legend and input set or array of strings to insert
     *    strings between each input block
     * - `legend` - control whether or not the widget set has a fieldset & legend
     * - `fieldset` - sets the class of the fieldset. Fieldset is only generated if legend attribute is provided
     * - `value` - indicate a value that is should be checked
     * - `label` - boolean to indicate whether or not labels for widgets show be displayed
     * - `hiddenField` - boolean to indicate if you want the results of radio() to include
     *    a hidden input with a value of ''. This is useful for creating radio sets that non-continuous
     * - `disabled` - Set to `true` or `disabled` to disable all the radio buttons.
     * - `empty` - Set to `true` to create an input with the value '' as the first option. When `true`
     *   the radio label will be 'empty'. Set this option to a string to control the label value.
     *
     * @param string $fieldName Name of a field, like this "Modelname.fieldname"
     * @param array $options Radio button options array.
     * @param array $attributes Array of HTML attributes, and special attributes above.
     * @return string Completed radio widget set.
     * @link https://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#options-for-select-checkbox-and-radio-inputs
     */
    public function radio($fieldName, $options = [], $attributes = []): string
    {
        // TODO 暫定措置
        // >>>
        return parent::radio($fieldName, $options, $attributes);
        // <<<

        $attributes['options'] = $options;
        $attributes = $this->_initInputField($fieldName, $attributes);
        unset($attributes['options']);

        $showEmpty = $this->_extractOption('empty', $attributes);
        if ($showEmpty) {
            $showEmpty = ($showEmpty === true)? __d('cake', 'empty') : $showEmpty;
            $options = ['' => $showEmpty] + $options;
        }
        unset($attributes['empty']);

        $legend = false;
        if (isset($attributes['legend'])) {
            $legend = $attributes['legend'];
            unset($attributes['legend']);
        } elseif (count($options) > 1) {
            $legend = Inflector::humanize($this->field());
        }

        $fieldsetAttrs = '';
        if (isset($attributes['fieldset'])) {
            $fieldsetAttrs = ['class' => $attributes['fieldset']];
            unset($attributes['fieldset']);
        }

        $label = true;
        if (isset($attributes['label'])) {
            $label = $attributes['label'];
            unset($attributes['label']);
        }

        $separator = null;
        if (isset($attributes['separator'])) {
            $separator = $attributes['separator'];
            unset($attributes['separator']);
        }

        $between = null;
        if (isset($attributes['between'])) {
            $between = $attributes['between'];
            unset($attributes['between']);
        }

        $value = null;
        if (isset($attributes['value'])) {
            $value = $attributes['value'];
        } else {
            $value = $this->getSourceValue($fieldName);
        }

        $disabled = [];
        if (isset($attributes['disabled'])) {
            $disabled = $attributes['disabled'];
        }

        $out = [];

        $hiddenField = isset($attributes['hiddenField'])? $attributes['hiddenField'] : true;
        unset($attributes['hiddenField']);

        if (isset($value) && is_bool($value)) {
            $value = $value? 1 : 0;
        }

        $div = null;
        if (!empty($attributes['div'])) {
            $div = $attributes['div'];
            unset($attributes['div']);
        }
        unset($label['label']);

        $this->_domIdSuffixes = [];
        foreach($options as $optValue => $optTitle) {
            $optionsHere = ['value' => $optValue, 'disabled' => false];
            if (is_array($optTitle)) {
                if (isset($optTitle['value'])) {
                    $optionsHere['value'] = $optTitle['value'];
                }

                $optionsHere += $optTitle;
                $optTitle = $optionsHere['name'];
                unset($optionsHere['name']);
            }

            if (isset($value) && strval($optValue) === strval($value)) {
                $optionsHere['checked'] = 'checked';
            }
            $isNumeric = is_numeric($optValue);
            if ($disabled && (!is_array($disabled) || in_array((string)$optValue, $disabled, !$isNumeric))) {
                $optionsHere['disabled'] = true;
            }
            $tagName = $attributes['id'] . $this->domIdSuffix($optValue);

            if ($label) {
                $labelOpts = is_array($label)? $label : [];
                $labelOpts += ['for' => $tagName];
                $optTitle = $this->label($tagName, $optTitle, $labelOpts);
            }

            if (is_array($between)) {
                $optTitle .= array_shift($between);
            }
            $allOptions = $optionsHere + $attributes;
            // CUSTOMIZE MODIFY 2018/09/14 ryuring
            // span タグで挟む
            // >>>
            /*$out[] = $this->Html->useTag('radio', $attributes['name'], $tagName,
                array_diff_key($allOptions, array('name' => null, 'type' => null, 'id' => null)),
                $optTitle
            );*/
            // ---
            $radio = $this->Html->useTag('radio', $attributes['name'], $tagName,
                array_diff_key($allOptions, ['name' => null, 'type' => null, 'id' => null]),
                $optTitle
            );
            if (isset($div)) {
                if ($div === false) {
                    $out[] = $radio;
                } elseif (is_array($div)) {
                    $divOptions = $div;
                    $tag = 'div';
                    if (!empty($divOptions['tag'])) {
                        $tag = $divOptions['tag'];
                    }
                    unset($divOptions['tag'], $divOptions['errorClass']);
                    $out[] = $this->Html->tag($tag, $radio, $divOptions);
                } else {
                    $out[] = $this->Html->div($attributes['class'], $radio);
                }
            } else {
                $out[] = $radio;
            }
            // <<<
        }
        $hidden = null;

        if ($hiddenField) {
            if (!isset($value) || $value === '') {
                $hidden = $this->hidden($fieldName, [
                    'form' => isset($attributes['form'])? $attributes['form'] : null,
                    'id' => $attributes['id'] . '_',
                    'value' => $hiddenField === true? '' : $hiddenField,
                    'name' => $attributes['name']
                ]);
            }
        }
        $out = $hidden . implode($separator, $out);

        if (is_array($between)) {
            $between = '';
        }

        if ($legend) {
            $out = $this->Html->useTag('legend', $legend) . $between . $out;
            $out = $this->Html->useTag('fieldset', $fieldsetAttrs, $out);
        }
        return $out;
    }

// CUSTOMIZE ADD 2014/07/02 ryuring

    /**
     * フォームのIDを作成する
     * BcForm::create より呼出される事が前提
     *
     * @param EntityInterface $context
     * @param array $options
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function createId($context, $options = [])
    {
        $request = $this->getView()->getRequest();
        if (!isset($options['id'])) {
            if (!empty($context)) {
                if (is_array($context)) {
                    // 複数$contextに設定されてる場合先頭のエンティティを優先
                    $context = array_shift($context);
                }
                [, $context] = pluginSplit($context->getSource());
            } else {
                $context = empty($request->getParam('controller')) ? false : $request->getParam('controller');
            }
            if ($domId = isset($options['url']['action'])? $options['url']['action'] : $request->getParam('action')) {
                $formId = Inflector::classify($context) . $request->getParam('prefix') . Inflector::camelize($domId) . 'Form' ;
            } else {
                $formId = null;
            }
        } else {
            $formId = $options['id'];
        }
        return $formId;
    }

    /**
     * フォームのIDを取得する
     *
     * BcFormHelper::create() の後に呼び出される事を前提とする
     *
     * @return string フォームID
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getId()
    {
        return $this->formId;
    }

    /**
     * フォームのIDを設定する
     *
     * BcFormHelper::create() の後に呼び出される事を前提とする
     * @param $id フォームID
     * @return string 新規フォームID
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setId($id)
    {
        return $this->formId = $id;
    }

    /**
     * CKEditorを出力する
     *
     * @param string $fieldName
     * @param array $options
     * @param array $editorOptions
     * @param array $styles
     * @return    string
     * @access    public
     */
    public function ckeditor($fieldName, $options = [])
    {

        $options = array_merge(['type' => 'textarea'], $options);
        return $this->BcCkeditor->editor($fieldName, $options);
    }

    /**
     * エディタを表示する
     *
     * @param string $fieldName
     * @param array $options
     * @return string
     */
    public function editor($fieldName, $options = [])
    {

        $options = array_merge([
            'editor' => 'BcCkeditor',
            'style' => 'width:99%;height:540px'
        ], $options);
        [$plugin, $editor] = pluginSplit($options['editor']);
        if (!empty($this->getView()->{$editor})) {
            return $this->getView()->{$editor}->editor($fieldName, $options);
        } elseif ($editor == 'none') {
            $_options = [];
            foreach($options as $key => $value) {
                if (!preg_match('/^editor/', $key)) {
                    $_options[$key] = $value;
                }
            }
            return $this->input($fieldName, array_merge(['type' => 'textarea'], $_options));
        } else {
            /** @var BcCkeditorHelper $bcCkeditor  */
            $bcCkeditor = $this->getView()->BcCkeditor;
            return $bcCkeditor->editor($fieldName, $options);
        }
    }

    /**
     * 都道府県用のSELECTタグを表示する
     *
     * @param string $fieldName Name attribute of the SELECT
     * @param mixed $selected Selected option
     * @param array $attributes Array of HTML options for the opening SELECT element
     * @param array $convertKey true value = "value" / false value = "key"
     * @return string 都道府県用のSELECTタグ
     */
    public function prefTag($fieldName, $selected = null, $attributes = [], $convertKey = false)
    {
        $prefs = $this->BcText->prefList();
        if ($convertKey) {
            $options = [];
            foreach($prefs as $key => $value) {
                if ($key) {
                    $options[$value] = $value;
                } else {
                    $options[$key] = $value;
                }
            }
        } else {
            $options = $prefs;
        }
        $attributes['value'] = $selected;
        $attributes['empty'] = false;
        return $this->select($fieldName, $options, $attributes);
    }

    /**
     * 和暦年
     *
     * @param string $fieldName Prefix name for the SELECT element
     * @param integer $minYear First year in sequence
     * @param integer $maxYear Last year in sequence
     * @param string $selected Option which is selected.
     * @param array $attributes Attribute array for the select elements.
     * @param boolean $showEmpty Show/hide the empty select option
     * @return string
     */
    public function wyear($fieldName, $minYear = null, $maxYear = null, $selected = null, $attributes = [], $showEmpty = true)
    {

        if ((empty($selected) || $selected === true) && $value = $this->getSourceValue($fieldName)) {
            if (is_array($value)) {
                if (isset($value['year'])) {
                    $selected = $value['year'];
                }
            } else {
                if (empty($value)) {
                    if (!$showEmpty && !$maxYear) {
                        $selected = 'now';
                    } elseif (!$showEmpty && $maxYear && !$selected) {
                        $selected = $maxYear;
                    }
                } else {
                    $selected = $value;
                }
            }
        }

        if (strlen($selected) > 4 || $selected === 'now') {

            $wareki = $this->BcTime->convertToWareki(date('Y-m-d', strtotime($selected)));
            if (!is_null($this->getSourceValue($fieldName))) {
                $wareki = $this->BcTime->convertToWareki($this->getSourceValue($fieldName));
            }

            $w = $this->BcTime->wareki($wareki);
            $wyear = $this->BcTime->wyear($wareki);
            $selected = $w . '-' . $wyear;

        } elseif ($selected === false) {
            $selected = null;
        } elseif (strpos($selected, '-') === false) {
            $wareki = $this->BcTime->convertToWareki($this->getSourceValue($fieldName));
            if ($wareki) {
                $w = $this->BcTime->wareki($wareki);
                $wyear = $this->BcTime->wyear($wareki);
                $selected = $w . '-' . $wyear;
            } else {
                $selected = null;
            }
        }
        $yearOptions = ['min' => $minYear, 'max' => $maxYear];
        $attributes = array_merge($attributes, [
            'value' => $selected,
            'empty' => $showEmpty
        ]);
        return $this->hidden($fieldName . ".wareki", ['value' => true]) .
            $this->select($fieldName . ".year", $this->_generateOptions('wyear', $yearOptions), $attributes);
    }

    /**
     * モデルよりリストを生成する
     *
     * @param string $modelName
     * @param mixed $conditions
     * @param mixed $fields
     * @param mixed $order
     * @return mixed リストまたは、false
     */
    public function generateList($modelName, $conditions = [], $fields = [], $order = [])
    {

        $model = ClassRegistry::init($modelName);
        if (!$model) {
            return 'aaa';
        }
        if ($fields) {
            [$idField, $displayField] = $fields;
        } else {
            return false;
        }

        $list = $model->find('all', ['conditions' => $conditions, 'fields' => $fields, 'order' => $order]);

        if ($list) {
            return Hash::combine($list, "{n}." . $modelName . "." . $idField, "{n}." . $modelName . "." . $displayField);
        } else {
            return null;
        }
    }

    /**
     * JsonList
     *
     * @param string $field フィールド文字列
     * @param string $attributes
     * @return array 属性
     */
    public function jsonList($field, $attributes)
    {

        am(["imgSrc" => "", "ajaxAddAction" => "", "ajaxDelAction" => ""], $attributes);
        // JsonDb用Hiddenタグ
        $out = $this->hidden('Json.' . $field . '.db');
        // 追加テキストボックス
        $out .= $this->text('Json.' . $field . '.name');
        // 追加ボタン
        $out .= $this->button(__d('baser', '追加'), ['id' => 'btnAdd' . $field]);
        // リスト表示用ビュー
        $out .= '<div id="Json' . $field . 'View"></div>';

        // javascript
        $out .= '<script type="text/javascript"><!--' . "\n" .
            'jQuery(function(){' . "\n" .
            'var json_List = new JsonList({"dbId":"Json' . $field . 'Db","viewId":"JsonTagView","addButtonId":"btnAdd' . $field . '",' . "\n" .
            '"deleteButtonType":"img","deleteButtonSrc":"' . $attributes['imgSrc'] . '","deleteButtonRollOver":true,' . "\n" .
            '"ajaxAddAction":"' . $attributes['ajaxAddAction'] . '",' . "\n" .
            '"ajaxDelAction":"' . $attributes['ajaxDelAction'] . '"});' . "\n" .
            'json_List.loadData();' . "\n" .
            '});' . "\n" .
            '//--></script>';

        return $out;
    }

    /**
     * 文字列保存用複数選択コントロール
     *
     * @param string $fieldName id,nameなどの名前
     * @param array $options optionタグの値
     * @param mixed $selected selectedを付与する要素
     * @param array $attributes htmlの属性
     * @param mixed $showEmpty 空要素の表示/非表示、初期値
     * @return string
     */
    public function selectText($fieldName, $options = [], $selected = null, $attributes = [], $showEmpty = '')
    {

        $_attributes = ['separator' => '<br />', 'quotes' => true];
        $attributes = Hash::merge($_attributes, $attributes);

        // $selected、$showEmptyをFormHelperのselect()に対応
        $attributes += [
            'value' => $selected,
            'empty' => $showEmpty
        ];

        $quotes = $attributes['quotes'];
        unset($attributes['quotes']);

        $_options = $this->_initInputField($fieldName, $options);
        if (empty($attributes['multiple']))
            $attributes['multiple'] = 'checkbox';
        $id = $_options['id'];
        $_id = $_options['id'] . '_';
        $name = $_options['name'];
        $out = '<div id="' . $_id . '">' . $this->select($fieldName . '_', $options, $attributes) . '</div>';
        $out .= $this->hidden($fieldName);
        $script = <<< DOC_END
$(function() {
    aryValue = $("#{$id}").val().replace(/\'/g,"").split(",");
    for(key in aryValue){
        var value = aryValue[key];
        $("#"+camelize("{$id}_"+value)).prop('checked',true);
    }
    $("#{$_id} input[type=checkbox]").change(function(){
        var aryValue = [];
        $("#{$_id} input[type=checkbox]").each(function(key,value){
            if($(this).prop('checked')){
                aryValue.push("'"+$(this).val()+"'");
            }
        });
        $("#{$id}").val(aryValue.join(','));
    });
});
DOC_END;
        $out .= $this->Js->buffer($script);
        return $out;
    }

    /**
     * ファイルインプットボックス出力
     *
     * 画像の場合は画像タグ、その他の場合はファイルへのリンク
     * そして削除用のチェックボックスを表示する
     *
     * 《オプション》
     * imgsize    画像のサイズを指定する
     * rel        A タグの rel 属性を指定
     * title    A タグの title 属性を指定
     * link        大きいサイズへの画像へのリンク有無
     * delCheck    削除用チェックボックスの利用可否
     * force    ファイルの存在有無に関わらず強制的に画像タグを表示するかどうか
     *
     * @param string $fieldName
     * @param array $options
     * @return string
     * @checked
     */
    public function file($fieldName, $options = []): string
    {

        $options = $this->_initInputField($fieldName, $options);
        // TODO: テーブル名を取得する
        $table = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        if (!$table->hasBehavior('BcUpload')) {
            return parent::file($fieldName, $options);
        }
        // $fieldName = implode('.', $entity);

        // NOTE: idが出力されなくなったため、以前のIDが出力されるよう変更
        // "Contact.upload" -> "ContactUpload"
        $id = implode(array_map(function($field) { return Inflector::camelize($field); }, explode('.', $fieldName)));
        $options = array_merge([
            'imgsize' => 'medium', // 画像サイズ
            'rel' => '', // rel属性
            'title' => '', // タイトル属性
            'link' => true, // 大きいサイズの画像へのリンク有無
            'delCheck' => true,
            'force' => false,
            'width' => '',
            'height' => '',
            'class' => '',
            'div' => false,
            'deleteSpan' => [],
            'deleteCheckbox' => [],
            'deleteLabel' => [],
            'figure' => [],
            'img' => ['class' => ''],
            'figcaption' => [],
            'id' => $id
        ], $options);

        $linkOptions = [
            'imgsize' => $options['imgsize'],
            'rel' => $options['rel'],
            'title' => $options['title'],
            'link' => $options['link'],
            'delCheck' => $options['delCheck'],
            'force' => $options['force'],
            'width' => $options['width'],
            'height' => $options['height'],
            'figure' => $options['figure'],
            'img' => $options['img'],
            'figcaption' => $options['figcaption']
        ];

        $deleteSpanOptions = $deleteCheckboxOptions = $deleteLabelOptions = [];
        if (!empty($options['deleteSpan'])) {
            $deleteSpanOptions = $options['deleteSpan'];
        }
        if (!empty($options['deleteCheckbox'])) {
            $deleteCheckboxOptions = $options['deleteCheckbox'];
        }
        if (!empty($options['deleteLabel'])) {
            $deleteLabelOptions = $options['deleteLabel'];
        }
        if (!empty($options['div'])) {
            $divOptions = $options['div'];
        }
        if (empty($options['class'])) {
            unset($options['class']);
        }
        unset($options['imgsize'], $options['rel'], $options['title'], $options['link']);
        unset($options['delCheck'], $options['force'], $options['width'], $options['height']);
        unset($options['deleteSpan'], $options['deleteCheckbox'], $options['deleteLabel']);
        unset($options['figure'], $options['img'], $options['figcaption'], $options['div']);

        $fileLinkTag = $this->BcUpload->fileLink($fieldName, $linkOptions);
        $fileTag = parent::file($fieldName, $options);

        if (empty($options['value'])) {
            $value = $this->getSourceValue($fieldName);
        } else {
            $value = $options['value'];
        }

        // PHP5.3対応のため、is_string($value) 判別を実行
        $delCheckTag = '';
        if ($fileLinkTag && $linkOptions['delCheck'] && (is_string($value) || empty($value['session_key']))) {
            $delCheckTag = $this->Html->tag('span', $this->checkbox($fieldName . '_delete', $deleteCheckboxOptions) . $this->label($fieldName . '_delete', __d('baser', '削除する'), $deleteLabelOptions), $deleteSpanOptions);
        }
        $hiddenValue = $this->getSourceValue($fieldName . '_');
        $fileValue = $this->getSourceValue($fieldName);

        $hiddenTag = '';
        if ($fileLinkTag) {
            if (is_array($fileValue) && empty($fileValue['tmp_name']) && $hiddenValue) {
                $hiddenTag = $this->hidden($fieldName . '_', ['value' => $hiddenValue]);
            } else {
                if (is_array($fileValue)) {
                    $fileValue = null;
                }
                $hiddenTag = $this->hidden($fieldName . '_', ['value' => $fileValue]);
            }
        }

        $out = $fileTag;

        if ($fileLinkTag) {
            $out .= '&nbsp;' . $delCheckTag . $hiddenTag . '<br />' . $fileLinkTag;
        }

        if (isset($divOptions)) {
            if ($divOptions === false) {
                return $out;
            } elseif (is_array($divOptions)) {
                $tag = 'div';
                if (!empty($divOptions['tag'])) {
                    $tag = $divOptions['tag'];
                }
                if (!empty($divOptions['class'])) {
                    $divOptions['class'] .= ' upload-file';
                } else {
                    $divOptions['class'] = 'upload-file';
                }
                unset($divOptions['tag'], $divOptions['errorClass']);
                return $this->Html->tag($tag, $out, $divOptions);
            } else {
                return $this->Html->div($options['class'], $out);
            }
        } else {
            return $out;
        }
    }

// <<<

}
