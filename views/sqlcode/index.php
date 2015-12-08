<?php

use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use app\models\SysOnoffSql;
?>

<h4><button class="btn btn-success">คำสั่ง SQL  </button><?= $saved ?></h4>
<div class="alert alert-primary">*** วิธี Run คำสั่ง sql ให้ใส่เครื่องหมาย ; ปิดท้ายคำสั่งเสมอ ***</div>
<?php
$route = Yii::$app->urlManager->createUrl('sqlcode/result');
?>
<form method="POST" >
    <div style="margin-bottom: 3px">
        <textarea name="sql_code" id="sql_code" class="form-control" rows='10' style="background-color: wheat"><?= @$sql_code ?></textarea>
    </div>
    <div>
        <?php
        $onof = SysOnoffSql::findOne(1);
        if ($onof->status === 'on'):
            ?>
            <button class="btn btn-info"><i class="glyphicon glyphicon-refresh"></i> รันชุดคำสั่ง</button>
            <button name="save" value="yes" class="btn btn-success"><i class="glyphicon glyphicon-save-file"></i> จัดเก็บ</button>
            <a href="<?= yii\helpers\Url::to(['sqlscript/index']) ?>" class="btn btn-primary"><i class="glyphicon glyphicon-list"></i> คลัง script</a>
        <?php else: ?>
            <label> ผู้ดูแลระบบปิดใช้งาน </label>

        <?php endif; ?>
    </div>
</form>

<hr>
<?php if(isset($_POST['script_name'])): ?>
<div class="alert alert-success"> ชื่อ Script : <?=$_POST['script_name']?></div>
<?php endif; ?>
    <?php
    if (isset($dataProvider))
    //echo yii\grid\GridView::widget([
        echo \kartik\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'formatter'=>['class'=>'yii\i18n\Formatter','nullDisplay'=>'-'],
            'responsive' => TRUE,
            'striped'=>false,
            'hover' => true,
            'floatHeader' => true,
           
            'panel' => [
                'before' => '',
                'type' => \kartik\grid\GridView::TYPE_INFO

            //'after'=>''
            ],
        ]);
    ?>
<?php
$script = <<< JS
$(function(){
    $("label[title='Show all data']").hide();
});        

JS;
$this->registerJs($script);
?>

