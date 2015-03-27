<?php

class DeviceController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
    
    public $defaultAction = 'admin';

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Device;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Device']))
		{
			$model->attributes=$_POST['Device'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Device']))
		{
			$model->attributes=$_POST['Device'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Device('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Device']))
			$model->attributes=$_GET['Device'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

    
    
    
    function htmlFormatParams($params, $level=0){
        $ret = '';

        if(is_array($params)){
            foreach($params as $name=>$val){
                for($i=0; $i<$level; $i++){
                    $ret .= "--";
                }
                if($level > 0) $ret .= " ";
                $ret .= "<b>{$name}</b>" .": ";
                if(!is_array($val)){
                    $ret .= $val;
                } else{
                    $ret .= "<br/>\n";
                    $ret .= $this->htmlFormatParams($val, $level+1);
                }
                $ret .= "<br/>\n";
            }
        }

        return $ret;
    }    
    
    
    
    
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Device the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Device::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Device $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='device-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
