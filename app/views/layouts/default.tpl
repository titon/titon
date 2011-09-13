
<?php echo $this->html->docType(); ?>
<html>
<head>
    <title><?php echo $this->html->title(); ?></title>
	<?php
	echo $this->html->meta('content-type');
	echo $this->html->meta('description', 'Titon: The PHP 5.3 Micro Framework');
	echo $this->html->meta('rss', 'http://titon/feed.rss');
	echo $this->asset->stylesheets(); ?>
</head>
<body>
    <?php echo $this->content(); ?>
	<?php echo $this->asset->scripts(); ?>
</body>
</html>
