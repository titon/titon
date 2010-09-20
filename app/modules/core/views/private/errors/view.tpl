
<h1>Missing View: <?php echo $view; ?></h1>

<p>The view was not found for the <?php echo $controller; ?>::<?php echo $action; ?>() action.<br />
Your problem may be resolved by following the checklist below:</p>

<ul>
    <li>The view template should have an extension of <i>.tpl</i></li>
    <li>The view must be lowercase and underscored</li>
    <li>The view must be located in the correct controller and container path</li>
</ul>

<p>Path: <?php echo $path; ?></p>
