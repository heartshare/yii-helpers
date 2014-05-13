<?php
/**
* 
* Version 1.0.1
* 
* Author: Roberto Serra - obi.serra@gmail.com
* 
*
* This class handle the order 
*
*
* 
* -- Usage: --
*
*
* 
* public function behaviors(){
*		return array(
*			'OrderHandler'=>array(
*				'class'=>'application.components.OrderHandler',
*				'orderColumn'=> 'colsName', // the field that contains the order val
*				'dependColumns'=>array('depColsName'), // dependency field, for example category
*			)
*		);
*	}
*
* In the views/admin.php file, to change the order value
*
* array(
*		'class'=>'CButtonColumn',
*		'template'=>'{delete}{view}{up}{down}',
*		'buttons'=>array(
*			'up' => array(
*				'label'=>'Up',
*				'imageUrl'=>Yii::app()->baseUrl."/images/be/up.png", // you should upload this image
*				'url'=>'Yii::app()->createUrl("controllerName/up", array("id"=>$data->ID))',
*			),
*			'down' => array(
*				'label'=>'Down',
*				'imageUrl'=>Yii::app()->baseUrl."/images/be/down.png", // and this too
*				'url'=>'Yii::app()->createUrl("controllerName/down", array("id"=>$data->ID))',
*			),
*		),
*	),
*
*
* In the controller file
*
* public function actionUp($id){
*		$model = $this->loadModel($id);
*		$model->up();
*		$this->redirect(array('admin','crt'=>$model->id_cartella));
*	}
*	public function actionDown($id){
*		$model = $this->loadModel($id);
*		$model->down();
*		$this->redirect(array('admin','crt'=>$model->id_cartella));
*	}
*
**/

class OrderHandler extends CActiveRecordBehavior {

	public $orderColumn = '';
	public $dependColumns = array();


	public $nextOrder;



	private function getMaxOrder(){
		$command = Yii::app()->db->createCommand()
				->select('MAX('.$this->orderColumn.')')
				->from($this->Owner->tableName());
		if(!empty($this->dependColumns)){
			$condition = array('AND');
			foreach ($this->dependColumns as $value) {
				$condition[] = $value.'='.$this->Owner->$value;
			}
			$command->where($condition);
		}
		//echo $command->getText();
		$order = $command->queryScalar();
		return $order;
	}

	public function getOrder(){
		$col = $this->orderColumn;
		$order = 0;
		if($this->Owner->$col){
			$order = $this->Owner->$col;
		} else {
			$order = $this->getMaxOrder();
			$order++;
		}
		return $order;
	}

	private function normalizeOrder(){
		$connection=Yii::app()->db;
		$transaction = $connection->beginTransaction();
		$sql1 = 'CREATE TEMPORARY TABLE IF NOT EXISTS virtual_order (id varchar(255), new_order varchar(255))';
			$sql2 = 'INSERT INTO virtual_order (id, new_order) SELECT ID , @rownum:=@rownum+1 FROM '.$this->Owner->tableName().', (SELECT @rownum:=0) AS x ';
			if(!empty($this->dependColumns)){
				$conds = array();
				foreach ($this->dependColumns as $value) {
					$conds[] = $value.' ='.$this->Owner->$value;
				}
				$sql2 .= 'WHERE '.implode('AND', $conds);
			}
			$sql2 .= ' ORDER BY '.$this->orderColumn.' ASC';
			$sql3 = 'UPDATE '.$this->Owner->tableName().' t INNER JOIN virtual_order v ON t.ID = v.id SET t.'.$this->orderColumn.' = v.new_order';
		try
		{
			
			$connection->createCommand($sql1)->execute();
			$connection->createCommand($sql2)->execute();
			$connection->createCommand($sql3)->execute();
			$transaction->commit();
		}
		catch(Exception $e){
			
			//print_r($e->errorInfo);
			$transaction->rollBack();
		}
	}

	/**
	 * Fa salire di una posizione la riga  
	 */
	public function up(){
		$col = $this->orderColumn;
		if($this->Owner->$col >= 1)
			$this->Owner->$col--;

		$condition = $col.' >='.$this->Owner->$col;
		if(!empty($this->dependColumns)){
				$conds = array();
				foreach ($this->dependColumns as $value) {
					$conds[] = $value.' ='.$this->Owner->$value;
				}
				$condition .= ' AND '.implode('AND', $conds).' AND ID !='.$this->Owner->ID;
			}
		$criteria=new CDbCriteria(array(
				'condition'=>$condition,
				));
		$this->Owner->updateAll(
				array($col=>new CDbExpression($col.'+1')),
				$criteria
				);
		$this->Owner->update();
		$this->normalizeOrder();
	}
	/**
	 * Fa scendere di una posizione la riga  
	 */
	public function down(){
		$col = $this->orderColumn;
		if($this->Owner->$col >= 1)
			$this->Owner->$col++;

		$condition = $col.' <='.$this->Owner->$col;
		if(!empty($this->dependColumns)){
				$conds = array();
				foreach ($this->dependColumns as $value) {
					$conds[] = $value.' ='.$this->Owner->$value;
				}
				$condition .= ' AND '.implode('AND', $conds).' AND ID !='.$this->Owner->ID;
			}
		$criteria=new CDbCriteria(array(
				'condition'=>$condition,
				));
		$this->Owner->updateAll(
				array($col=>new CDbExpression($col.'-1')),
				$criteria
				);
		$this->Owner->update();
		$this->normalizeOrder();
	}


	public function afterSave($event){
		$this->normalizeOrder();
		return parent::afterSave($event);
	}
	public function afterDelete($event){
		$this->normalizeOrder();
		return parent::afterDelete($event);
	}

}
?>
