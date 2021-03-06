<?php

class SceneController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';
    public $defaultAction = 'admin';

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $model = new Scene;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Scene'])) {
            $model->attributes = $_POST['Scene'];
            $image = CUploadedFile::getInstance($model, 'image');
            if ($model->save()){
                if($image){
                    $model->image = "img_{$model->id}.{$image->extensionName}";
                    if($image->saveAs(Yii::app()->basePath.'/../images/scenes/'.$model->image)){
                        $model->save();
                    }
                }
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Scene'])) {
            $mimg = $model->image;
            $model->attributes = $_POST['Scene'];
            $image = CUploadedFile::getInstance($model, 'image');
            if(!$image)
                $model->image = $mimg;
            if ($model->save()){
                if($image){
                    $model->image = "img_{$model->id}.{$image->extensionName}";
                    if($image->saveAs(Yii::app()->basePath.'/../images/scenes/'.$model->image)){
                        $model->save();
                    }
                }
                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        
        
        $criteria = new CDbCriteria;
        $criteria->condition = 'type<:type and parentID is not null';
        $criteria->params = array(':type'=>DEV_ARDUINO);
        //$criteria->addInCondition('type', array());
        $executiveDevices = Device::model()->findAll($criteria);
        
        $sceneWidgetModel = new SceneWidget;
        $sceneWidgetModel->sceneID = $id;
        
        $deviceImages = array();
        foreach(glob(Yii::app()->basePath.'/../images/icons/*.*') as $fname){
            $fname = basename($fname);
            $deviceImages[] = $fname;
        }
        
        
        
        $this->render('update', array(
            'model' => $model,
            'executiveDevices' => $executiveDevices,
            'sceneWidgetModel' => $sceneWidgetModel,
            'deviceImages' => $deviceImages,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new Scene('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Scene']))
            $model->attributes = $_GET['Scene'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Scene the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = Scene::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Scene $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'scene-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
