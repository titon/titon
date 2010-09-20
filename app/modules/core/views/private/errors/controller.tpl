
<h1>Missing Controller: <?php echo $class; ?></h1>

<p>The controller <b><?php echo $class; ?></b> could not be found or instantiated.<br />
Your problem may be resolved by following the checklist below:</p>

<ul>
    <li>The controller and file name must be camel cased: <i><?php echo $class; ?></i></li>
    <li>The controller must extend from the AppController: <i>\app\AppController</i></li>
    <li>The controller must use the correct container namespace (if in a container): <i><?php echo $namespace; ?>;</i></li>
</ul>

<p>Path: <?php echo $path; ?></p>

<pre>namespace <?php echo $namespace; ?>;

class <?php echo $class; ?> extends \app\AppController {

}</pre>
