
<h1>Missing Model: <?php echo $model; ?></h1>

<p>The model <b><?php echo $model; ?></b> could not be found.<br />
Your problem may be resolved by following the checklist below:</p>

<ul>
    <li>The model name and filename must be camel cased and singular: <i><?php echo $model; ?></i></li>
    <li>The model must extend from the AppModel: <i>\app\models\AppModel</i></li>
    <li>The model must use the correct namespace: <i>namespace app\models;</i></li>
</ul>

<p>Path: <?php echo $filepath; ?></p>

<pre>namespace app\models;

class <?php echo $model; ?> extends \app\models\AppModel {

}</pre>
