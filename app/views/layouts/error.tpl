
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $this->pageTitle(); ?></title>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $this->charset(); ?>">
	<meta name="description" content="Titon: The PHP 5.3 Micro Framework">
</head>
<body>
    <?php echo $this->content(); ?>

    <?php if (!empty($benchmarks)) { ?>
		<table cellpadding="5" cellspacing="0">
		<tr>
			<th>Benchmark</th>
			<th>Time</th>
			<th>Memory Usage</th>
			<th>Peak Memory</th>
		</tr>
			<?php foreach ($benchmarks as $mark => $data) { ?>
			<tr>
				<td><?php echo $mark; ?></td>
				<td><?php echo (($data['avgTime'] != null) ? $data['avgTime'] : 'Interrupted'); ?></td>
				<td><?php echo (($data['avgMemory'] != null) ? $data['avgMemory'] : 'Interrupted'); ?></td>
				<td><?php echo $data['peakMemory']; ?></td>
			</tr>
			<?php } ?>
		</table>
    <?php } ?>
</body>
</html>
