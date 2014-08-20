yii-dropzone
============

A Yii extension for Dropzone.js

Now you can use all of the events and set a custom CSS file. See the code for more details.

	$dict = array(
		'dictDefaultMessage'=>'Arraste seus arquivos aqui para realizar o Upload!',
		'dictFallbackMessage'=>'Seu browser não suporta esse recurso. :(',
		'dictFallbackText'=>'Por favor, use o formulário de upload abaixo.',
		'dictInvalidFileType'=>'Tipo de Arquivo Inválido!',
		'dictFileTooBig'=>'O arquivo é muito grande!',
		'dictResponseError'=>'Oops! Não foi possível fazer o upload!',
		'dictCancelUpload'=>'Cancelar',
		'dictCancelUploadConfirmation'=>'Deseja cancelar o upload?',
		'dictRemoveFile'=>'Deletar',
		'dictMaxFilesExceeded'=>'Número máximo de arquivos excedeu!',
	);
	
	$options = array(
	    'addRemoveLinks'=>true,
	);
	
	$events = array(
	    'success' => 'successUp(this, param, param2, param3)',
	    'totaluploadprogress'=>'incProgress(this, param, param2, param3)',
	    'queuecomplete'=>'complete()'
	);

	$this->widget('wcext.dropzone.EDropzone', array(
	    //'model' => $model,
	    //'attribute' => 'file',
	    'name'=>'upload',
	    'url' => $this->createUrl('controller/action'),
	    'mimeTypes' => array('image/jpeg', 'image/png', 'video/mp4'),
	    'events' => $events,
	    'options' => CMap::mergeArray($options, $dict ),
	    'htmlOptions' => array('style'=>'height:95%; overflow: hidden;'),
	    'customStyle'=> $this->module->assetsPath.'/css/customdropzone.css'
	));
