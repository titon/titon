
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title><?php echo $this->pageTitle(); ?></title>
</head>
<body>
    <?php echo $this->content(); ?>

    <?php // Show benchmarks
	if (!empty($benchmarks)) { ?>
    <table cellpadding="5" cellspacing="0">
    <tr>
        <th>Benchmark</th>
        <th>Timeage</th>
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
