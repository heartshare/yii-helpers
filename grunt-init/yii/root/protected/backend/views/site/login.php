<?php
$this->pageTitle=Yii::app()->name . ' - Login';
?>
<br><br>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
	'htmlOptions'=>array(
		'class'=>'form-signin'
	),
	'errorMessageCssClass'=>'text-error'
)); ?>
	<h2 class="form-signin-heading">Please sign in</h2>
	

		<?php echo $form->textField($model,'username',array('class'=>'input-block-level','placeholder'=>'Username')); ?>
		<?php echo $form->error($model,'username'); ?>

		<?php echo $form->passwordField($model,'password',array('class'=>'input-block-level','placeholder'=>'Password')); ?>
		<?php echo $form->error($model,'password'); ?>
		
		<label class='checkbox'>
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		Remember me
		</label>
		<?php echo $form->error($model,'rememberMe'); ?>
		<br>
		<?php echo CHtml::submitButton('Login',array('class'=>'btn btn-large btn-primary')); ?>
	

<?php $this->endWidget(); ?>
</div><!-- form -->
