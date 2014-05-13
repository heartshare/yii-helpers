<!DOCTYPE html>
<html>
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap-responsive.min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/backend.css" />
	<?php Yii::app()->clientScript->registerPackage('jquery'); ?>
	<!--<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>-->
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
	<div id="wrap" class='row-fluid'>
		<div class="navbar navbar-inverse navbar-fixed-top">
	      <div class="navbar-inner">
	       <div class="container">
	          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	          </button>
	          <a class="brand" href="<?php echo $this->createUrl('site/index'); ?>"><?php echo Yii::app()->name;?></a>
	          <div class="nav-collapse collapse">
	            <?php $this->widget('zii.widgets.CMenu',array(
					'items'=>array(
						array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
						array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
					),
					'htmlOptions'=>array(
						'class'=>'nav'
					)
				)); ?>
	          </div><!--/.nav-collapse -->
	        </div>
	      </div>
	    </div>
	     <div id="main" class="container clear-top">
			<?php echo $content; ?>
		</div>
	</div>
	<footer class='footer'>
		<div class='container'>
			<div class="row-fluid">
				<div class='span12'>
					<p>
						Copyright &copy; <?php echo date('Y'); ?> by My Company.<br/>
						All Rights Reserved.<br/>
						<?php echo Yii::powered(); ?>
					</p>
				</div>
			</div>
	</div>
	</footer><!-- footer -->

</body>
</html>