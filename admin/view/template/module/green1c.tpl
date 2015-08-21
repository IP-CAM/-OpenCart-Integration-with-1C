<?php echo $header; ?>
<div id="container">
	<div id="content">
		<div class="breadcrumb">
	    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
	   		<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
	    <?php } ?>
	  	</div>
	  	<div class="box">
		  	<div class="heading">
		    	<h1 style="color:#003A88;"><?php echo $heading_title; ?></h1>
		      	<div class="buttons"><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_back;?></span></a>
      			</div>
		    </div>
		    <div class="content">
		    	<div id="tabs" class="htabs">
		        	<a href="#general"><?php echo $tab1; ?></a>
		        	<a href="#upload"><?php echo $tab2; ?></a>
		    	</div>
		    	 <div id="general">
         		<?php $action1='index.php?route=module/green1c/importData&token='.$token; ?>
         			<form action="<?php echo $action1; ?>" method="post">
						<input type="submit" class="button" name="import" value="<?php echo $send; ?>">
					</form>
					<p><?php echo $success; ?></p>
					<p><?php echo $error_imp; ?></p>
        		</div>
        		<div id="upload">
         		<?php $action2='index.php?route=module/green1c/upload&token='.$token; ?>
         			<form action="<?php echo $action2; ?>" method="post" enctype="multipart/form-data">
         				<input type="file" name="file_name">
						<input type="submit" class="button" name="upload" value="<?php echo $upload; ?>">
					</form>
					<p><?php echo $upload_message; ?></p>
					<p><?php echo $upload_error;?></p>
        		</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$('#tabs a').tabs();
</script>
<?php echo $footer; ?>