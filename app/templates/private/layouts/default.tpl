
<?php echo $this->Html->docType('html', null, 5); ?>
<html>
<head>
    <title><?php echo $this->pageTitle(); ?></title>
    <?php // Meta
    echo $this->Html->meta('content-type');
    echo $this->Html->meta('author', 'Titan PHP Framework - Miles Johnson');
    echo $this->Html->meta('description', 'Titan - The lightweight modular PHP 5.3 micro framework!');
	echo $this->stylesheets(); ?>
</head>
<body>
    <?php echo $this->content(); ?>
	<?php echo $this->scripts(); ?>
</body>
</html>
