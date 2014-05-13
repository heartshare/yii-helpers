<?php $this->beginContent('//layouts/main'); ?>
<div class="row-fluid">
	<div class="span9">
			<?php echo $content; ?>
	</div>
	<div class="span3">
		<?php
			$this->beginWidget('zii.widgets.CPortlet', array(
				'title'=>'',
			));
			$this->widget('zii.widgets.CMenu', array(
				'items'=>$this->menu,
				'htmlOptions'=>array('class'=>'nav nav-list bs-sidenav'),
			));
			$this->endWidget();
		?>
	</div>
</div>
<?php $this->endContent(); ?>