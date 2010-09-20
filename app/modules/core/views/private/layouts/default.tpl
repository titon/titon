
<?php echo $view->Html->docType('html', null, 5); ?>
<html>
<head>
    <title><?php echo $view->pageTitle(); ?></title>
    <?php // Meta
    echo $view->Html->meta('content-type');
    echo $view->Html->meta('author', 'Titan PHP Framework - Miles Johnson');
    echo $view->Html->meta('description', 'Titan - The lightweight modular PHP 5.3 micro framework!');
	echo $view->stylesheets(); ?>
</head>
<body>
    <?php echo $this->content(); ?>
	<?php echo $view->scripts(); ?>
</body>
</html>
