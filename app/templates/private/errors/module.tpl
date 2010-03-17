
<h1>Missing Module: <?php echo $class; ?> <?php echo $type; ?></h1>

<p>The application module could not be located.<br />
Your problem may be resolved by following the checklist below:</p>

<ul>
    <li>The module and file name must be camel cased: <i><?php echo $class; ?></i></li>
    <li>The module must extend from the correct interface (inherited from parent module): <i><?php echo $interface; ?></i></li>
    <li>The module must use the correct namespace: <i><?php echo $namespace; ?></i></li>
</ul>

<p>Path: <?php echo $path; ?></p>

<pre>namespace <?php echo $namespace; ?>;

class <?php echo $class; ?> extends <?php echo $parent; ?> {

}</pre>
