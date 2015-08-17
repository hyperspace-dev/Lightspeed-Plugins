<?php

/**
 * XUploadAction
 * =============
 * Basic upload functionality for an action used by the xupload extension.
 *
 * XUploadAction is used together with XUpload and XUploadForm to provide file upload funcionality to any application
 *
 * You must configure properties of XUploadAction to customize the folders of the uploaded files.
 *
 * Using XUploadAction involves the following steps:
 *
 * 1. Override CController::actions() and register an action of class XUploadAction with ID 'upload', and configure its
 * properties:
 * ~~~
 * [php]
 * class MyController extends CController
 * {
 *     public function actions()
 *     {
 *         return array(
 *             'upload'=>array(
 *                 'class'=>'xupload.actions.XUploadAction',
 *                 'path' =>Yii::app() -> getBasePath() . "/../uploads",
 *                 'publicPath' => Yii::app() -> getBaseUrl() . "/uploads",
 *                 'subfolderVar' => "parent_id",
 *             ),
 *         );
 *     }
 * }
 *
 * 2. In the form model, declare an attribute to store the uploaded file data, and declare the attribute to be validated
 * by the 'file' validator.
 * 3. In the controller view, insert a XUpload widget.
 *
 * ###Resources
 * - [xupload](http://www.yiiframework.com/extension/xupload)
 *
 * @version 0.3
 * @author Asgaroth (http://www.yiiframework.com/user/1883/)
 */
class XUploadAction extends CAction {

    /**
     * XUploadForm (or subclass of it) to be used.  Defaults to XUploadForm
     * @see XUploadAction::init()
     * @var string
     * @since 0.5
     */
    public $formClass = 'application.extensions.addimgupload.models.XUploadForm';

    /**
     * Name of the model attribute referring to the uploaded file.
     * Defaults to 'file', the default value in XUploadForm
     * @var string
     * @since 0.5
     */
    public $fileAttribute = 'file';

    /**
     * Name of the model attribute used to store mimeType information.
     * Defaults to 'mime_type', the default value in XUploadForm
     * @var string
     * @since 0.5
     */
    public $mimeTypeAttribute = 'mime_type';

    /**
     * Name of the model attribute used to store file size.
     * Defaults to 'size', the default value in XUploadForm
     * @var string
     * @since 0.5
     */
    public $sizeAttribute = 'size';

    /**
     * Name of the model attribute used to store the file's display name.
     * Defaults to 'name', the default value in XUploadForm
     * @var string
     * @since 0.5
     */
    public $displayNameAttribute = 'name';

    /**
     * Name of the model attribute used to store the file filesystem name.
     * Defaults to 'filename', the default value in XUploadForm
     * @var string
     * @since 0.5
     */
    public $fileNameAttribute = 'filename';

    /**
     * The query string variable name where the subfolder name will be taken from.
     * If false, no subfolder will be used.
     * Defaults to null meaning the subfolder to be used will be the result of date("mdY").
     *
     * @see XUploadAction::init().
     * @var string
     * @since 0.2
     */
    public $subfolderVar;

    /**
     * Path of the main uploading folder.
     * @see XUploadAction::init()
     * @var string
     * @since 0.1
     */
    public $path;
    public $module;

    /**
     * Public path of the main uploading folder.
     * @see XUploadAction::init()
     * @var string
     * @since 0.1
     */
    public $publicPath;

    /**
     * @var boolean dictates whether to use sha1 to hash the file names
     * along with time and the user id to make it much harder for malicious users
     * to attempt to delete another user's file
     */
    public $secureFileNames = false;

    /**
     * Name of the state variable the file array is stored in
     * @see XUploadAction::init()
     * @var string
     * @since 0.5
     */
    public $stateVariable = 'xuploadFiles';

    /**
     * The resolved subfolder to upload the file to
     * @var string
     * @since 0.2
     */
    private $_subfolder = "";

    /**
     * The form model we'll be saving our files to
     * @var CModel (or subclass)
     * @since 0.5
     */
    private $_formModel;

    /**
     * Initialize the propeties of pthis action, if they are not set.
     *
     * @since 0.1
     */
    public function init( ) {

        if( !isset( $this->path ) ) {
            $this->path = realpath( Yii::app( )->getBasePath( )."/../uploads" );
        }

        if( !is_dir( $this->path ) ) {
            mkdir( $this->path, 0777, true );
            chmod ( $this->path , 0777 );
            //throw new CHttpException(500, "{$this->path} does not exists.");
        } else if( !is_writable( $this->path ) ) {
            chmod( $this->path, 0777 );
            //throw new CHttpException(500, "{$this->path} is not writable.");
        }
        $this->_subfolder=''; // Set subfolder
        if( !isset($this->_formModel)) {
            $this->formModel = Yii::createComponent(array('class'=>$this->formClass));
        }

        if($this->secureFileNames) {
            $this->formModel->secureFileNames = true;
        }
    }

    /**
     * The main action that handles the file upload request.
     * @since 0.1
     * @author Asgaroth
     */
    public function run( ) {
        $this->sendHeaders();

        $this->handleDeleting() or $this->handleUploading();
    }
    protected function sendHeaders()
    {
        header('Vary: Accept');
        if (isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }
    }
    /**
     * Removes temporary file from its directory and from the session
     *
     * @return bool Whether deleting was meant by request
     */
    protected function handleDeleting()
    {
        if (isset($_GET["_method"]) && $_GET["_method"] == "delete") {
            $success = false;
			$code=Yii::app()->getRequest()->getQuery('code');
			$row = Yii::app()->db->createCommand()->from('xlsws_mod_addimglist')->where('code="'.$code.'" AND name="'.$_GET["file"].'"')->queryRow(); // Get row will delete to check level
			$delRow = Yii::app()->db->createCommand()->delete('xlsws_mod_addimglist', 'code="'.$code.'" AND name="'.$_GET["file"].'"'); // Delete row when del image
			if($row['level']==1){
				$rowUpdate = Yii::app()->db->createCommand()->from('xlsws_mod_addimglist')->where('code="'.$code.'"')->queryRow(); // Get a row default
				if($rowUpdate){
					$update = Yii::app()->db->createCommand()->update('xlsws_mod_addimglist',array('level'=>1),'code="'.$rowUpdate['code'].'"'); // Update row default
				}
			}
			$pathfile=Yii::getPathOfAlias('webroot').'/images/addimglist/'.$_GET["file"];	// Create path
            if (is_file($pathfile)){
				$success = unlink($pathfile);
			}
            echo json_encode($success);
            return true;
        }
        return false;
    }

    /**
     * Uploads file to temporary directory
     *
     * @throws CHttpException
     */
    protected function handleUploading()
    {
        $this->init();
        $model = $this->formModel;
        $model->{$this->fileAttribute} = CUploadedFile::getInstance($model, $this->fileAttribute);
        if ($model->{$this->fileAttribute} !== null) {
            $model->{$this->mimeTypeAttribute} = $model->{$this->fileAttribute}->getType();
            $model->{$this->sizeAttribute} = $model->{$this->fileAttribute}->getSize();
            $model->{$this->displayNameAttribute} = $model->{$this->fileAttribute}->getName();
            $model->{$this->fileNameAttribute} = $model->{$this->displayNameAttribute};

            if ($model->validate()) {

                $path = $this->getPath();

                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                    chmod($path, 0777);
                }

                $model->{$this->fileAttribute}->saveAs($path . $model->{$this->fileNameAttribute});
                chmod($path . $model->{$this->fileNameAttribute}, 0777);

                $returnValue = $this->beforeReturn();
                if ($returnValue === true) {
                    echo json_encode(array(array(
                        "name" => $model->{$this->displayNameAttribute},
                        "type" => $model->{$this->mimeTypeAttribute},
                        "size" => $model->{$this->sizeAttribute},
                        "url" => $this->getFileUrl($model->{$this->fileNameAttribute}),
                        "thumbnail_url" => $model->getThumbnailUrl($this->getPublicPath()),
                        "delete_url" => $this->getController()->createUrl($this->getId(), array(
                            "_method" => "delete",
                            "file" => $model->{$this->fileNameAttribute},
                            "code" => Yii::app()->getRequest()->getQuery("code"),
                        )),
                        "delete_type" => "POST"
                    )));
					$code=Yii::app()->getRequest()->getQuery('code');
					$row = Yii::app()->db->createCommand()->from('xlsws_mod_addimglist')->where('code="'.$code.'"')->queryRow(); // Get row will delete to check level
					if($row)
					{
					$delRow = Yii::app()->db->createCommand()->delete('xlsws_mod_addimglist', 'code="'.$code.'"'); // Delete row when del image
					$pathfile=Yii::getPathOfAlias('webroot').'/images'.$row['url'];	// Create path
						if (is_file($pathfile)){
							$success = unlink($pathfile);
						}
					}
					$insert = Yii::app()->db->createCommand()->insert('xlsws_mod_addimglist',array('code'=>$code,'name'=>$model->{$this->fileNameAttribute},'url'=>'/addimglist/'.$model->{$this->fileNameAttribute},'size'=>$model->size,'type'=>0,'level'=>1));
				} else {
                    echo json_encode(array(array("error" => $returnValue,)));
                    Yii::log("XUploadAction: " . $returnValue, CLogger::LEVEL_ERROR, "application.extensions.addimgupload.actions.XUploadAction");
                }
            } else {
                echo json_encode(array(array("error" => $model->getErrors($this->fileAttribute),)));
                Yii::log("XUploadAction: " . CVarDumper::dumpAsString($model->getErrors()), CLogger::LEVEL_ERROR, "application.extensions.addimgupload.actions.XUploadAction");
            }
        } else {
            throw new CHttpException(500, "Could not upload file");
        }
    }

    /**
     * We store info in session to make sure we only delete files we intended to
     * Other code can override this though to do other things with state, thumbnail generation, etc.
     * @since 0.5
     * @author acorncom
     * @return boolean|string Returns a boolean unless there is an error, in which case it returns the error message
     */
    protected function beforeReturn() {
        $path = $this->getPath();

        // Now we need to save our file info to the user's session
        $userFiles = Yii::app( )->user->getState( $this->stateVariable, array());

        $userFiles[$this->formModel->{$this->fileNameAttribute}] = array(
            "path" => $path.$this->formModel->{$this->fileNameAttribute},
            //the same file or a thumb version that you generated
            "thumb" => $path.$this->formModel->{$this->fileNameAttribute},
            "filename" => $this->formModel->{$this->fileNameAttribute},
            'size' => $this->formModel->{$this->sizeAttribute},
            'mime' => $this->formModel->{$this->mimeTypeAttribute},
            'name' => $this->formModel->{$this->displayNameAttribute},
        );
        Yii::app( )->user->setState( $this->stateVariable, $userFiles );

        return true;
    }

    /**
     * Returns the file URL for our file
     * @param $fileName
     * @return string
     */
    protected function getFileUrl($fileName) {
        return $this->getPublicPath().$fileName;
    }

    /**
     * Returns the file's path on the filesystem
     * @return string
     */
    protected function getPath() {
        $path = ($this->_subfolder != "") ? "{$this->path}/{$this->_subfolder}/" : "{$this->path}/";
        return $path;
    }

    /**
     * Returns the file's relative URL path
     * @return string
     */
    protected function getPublicPath() {
        return ($this->_subfolder != "") ? "{$this->publicPath}/{$this->_subfolder}/" : "{$this->publicPath}/";
    }



    /**
     * Our form model setter.  Allows us to pass in a instantiated form model with options set
     * @param $model
     */
    public function setFormModel($model) {
        $this->_formModel = $model;
    }

    public function getFormModel() {
        return $this->_formModel;
    }


}
