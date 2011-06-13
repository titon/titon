
<?php echo $this->html->docType(); ?>
<html>
<head>
    <title><?php echo $this->html->pageTitle(); ?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="description" content="Titon: The PHP 5.3 Micro Framework">
    <?php echo $this->asset->stylesheets(); ?>
</head>
<body>
    <?php echo $this->content(); ?>
	<?php echo $this->asset->scripsts(); ?>
</body>
</html>
