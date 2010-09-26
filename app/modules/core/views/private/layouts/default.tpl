
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $this->pageTitle(); ?></title>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $this->charset(); ?>">
	<meta name="description" content="Titon: The PHP 5.3 Micro Framework">
    <?php echo $this->Html->stylesheets(); ?>
</head>
<body>
    <?php echo $this->content(); ?>
	<?php echo $this->Html->scripts(); ?>
</body>
</html>
