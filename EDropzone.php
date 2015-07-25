<?php
/**
 *
 * Yii extension to the drag n drop HTML5 file upload Dropzone.js
 * For more info, see @link http://www.dropzonejs.com/
 *
 * @link https://github.com/subdee/yii-dropzone
 *
 * @author Konstantinos Thermos
 *
 * @copyright
 * Copyright (c) 2013 Konstantinos Thermos
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial
 * portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT
 * LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
 * NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */
class EDropzone extends CWidget {
	CONST VER_3_10_2 = '3.10.2';
	CONST VER_4_0_1 = '4.0.1';

	/**
	 * @var string The name of the file field
	 */
	public $name = false;
	/**
	 * @var CModel The model for the file field
	 */
	public $model = false;
	/**
	 * @var string The attribute of the model
	 */
	public $attribute = false;
	/**
	 * @var array An array of options that are supported by Dropzone
	 */
	public $options = array();
	/**
	 * @var string The URL that handles the file upload
	 */
	public $url = false;
	/**
	 * @var array An array of supported MIME types. Eg.: image/*,application/pdf,.psd
	 */
	public $mimeTypes = array();
	/**
	 * @var array The Javascript to be called on any event
	 */
	public $events = array();

	/**
	 * @var array The HTML options using in the tag div
	 */
	public $htmlOptions = array();

	/**
	 * @var string The path to custom css file
	 */
	public $customStyle = false;

	/** @var string  */
	public $assetsVersion = self::VER_4_0_1;

	public $enableTranslate = false;

	public function init() {
		if (!$this->url)
			$this->url = Yii::app()->createUrl('site/upload');

		if (!$this->name && $this->model instanceof CModel && $this->attribute)
			$this->name = CHtml::activeName($this->model, $this->attribute);

		if ( empty($this->htmlOptions['id']) ) {
			$this->htmlOptions['id'] = $this->id;
		} else {
			$this->id = $this->htmlOptions['id'];
		}

		if ( $this->enableTranslate ) {
			$this->initTranslate();
		}
	}

	/**
	 * Create a div and the appropriate Javascript to make the div into the file upload area
	 */
	public function run() {
		$this->registerAssets();
		$this->jsOptions();
		$this->renderHtml();
	}

	/**
	 * I prefer to render HTML from view file. But if you override Widget you must to override all view's.
	 * review: you need to add style manually into your project css:
	 * .dz-browser-not-supported .fallback {display:none !important}
	 */
	protected function renderHtml() {
		$htmlOptions = CMap::mergeArray(array('class' => 'dropzone', 'enctype'=> 'multipart/form-data'), $this->htmlOptions);
		echo CHtml::beginForm($this->url, 'post', $htmlOptions);
		echo '
        <div class="fallback" style="display:none;">
            <input name="' . $this->name . '" type="file" multiple />
        </div>
        ';
		echo CHtml::endForm();
	}

	protected function registerAssets() {
		if ( $this->assetsVersion == self::VER_3_10_2 ) {
			$basePath = dirname(__FILE__) . '/assets/';
			$js = '/js/dropzone.js';
			$css = '/css/dropzone.css';
		} else {
			$min = '';
			$basePath = dirname(__FILE__) . "/assets/versions/{$this->assetsVersion}/dist/";
			if ( YII_DEBUG ) {
				$basePath .= 'min/';
				$min = '.min';
			}
			$js = "/dropzone$min.js";
			$css = "/dropzone$min.css";
		}

		$baseUrl = Yii::app()->getAssetManager()->publish($basePath, false, 1, YII_DEBUG);
		Yii::app()->getClientScript()
			->registerScriptFile($baseUrl . $js, CClientScript::POS_BEGIN)
			->registerCssFile($baseUrl . $css);

		if($this->customStyle)
			Yii::app()->getClientScript()->registerCssFile($this->customStyle);
	}

	protected function jsOptions() {
		$onEvents = '';
		foreach($this->events as $event => $func){
			$onEvents .= "this.on('$event', function(param, param2, param3){ $func });";
		}

		$options = CMap::mergeArray(array(
			'url' => $this->url,
			'parallelUploads' => 5,
			'paramName' => $this->name,
			//'accept' => "js:function(file, done){if({$this->mimeTypes}.indexOf(file.type) == -1 ){done('File type not allowed.');}else{done();}}", //review There are many fixes in v 4.0.1. And 'acceptedFiles' + translation now work properly. So this code is deprecated i think
			'acceptedFiles' => join(',', $this->mimeTypes),
			'init' => "js:function(){ $onEvents }"
		), $this->options);

		$options = CJavaScript::encode($options);
		$script = "Dropzone.options.{$this->id} = $options";
		Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $this->getId(), $script, CClientScript::POS_BEGIN);
	}

	protected function initTranslate() {
		$dict = array(
			'dictDefaultMessage'=>Yii::t('EDropzone.dropzone','<b>Drop files</b> here to upload <span>(or click)</span>'),
			'dictFallbackMessage'=>Yii::t('EDropzone.dropzone',"Your browser does not support drag'n'drop file uploads."),
			'dictFallbackText'=>Yii::t('EDropzone.dropzone','Please use the fallback form below to upload your files like in the olden days.'),
			'dictInvalidFileType'=>Yii::t('EDropzone.dropzone',"Wrong type. Allowed types are: \n{types}", array('{types}'=>join('; ', $this->mimeTypes))),
			'dictFileTooBig'=>Yii::t('EDropzone.dropzone','Size is too big. Allowed size is {{maxFilesize}}. Your file is {{filesize}}'),
		);
		$this->options = CMap::mergeArray($this->options, $dict);
	}
}