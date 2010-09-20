<h1>Missing Action: <?php echo $action; ?></h1>

<p>No such action exists within the controller <b><?php echo $controller; ?></b>.<br />
Your problem may be resolved by following the checklist below:</p>

<ul>
    <li>Add the method <b><?php echo $action; ?></b> to the controller <b><?php echo $controller; ?></b></li>
    <li>Action name may only be camel case or underscored; dashed action names will be converted to underscores</li>
    <li>Action may not start with an underscore ( _ )</li>
    <li>Action must have a visibility modifier of public</li>
</ul>

<pre>namespace <?php echo $namespace; ?>;

class <?php echo $controller; ?> extends \app\AppController {

    public function <?php echo $action; ?>() {
    }

}</pre>
