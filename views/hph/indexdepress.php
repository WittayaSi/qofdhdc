<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'QOF-DHDC';
?>
<div class="hph-indexanc12week">
    <center><p>
        <?= Html::a('<i class="glyphicon glyphicon-search"></i> ดูรายงาน', ['depress'], ['class' => 'btn btn-success']) ?>
       
    </p></center>

    <div class="text-center">     
        <?php
        echo Html::img('@web/images/depression.jpg',['class'=>'img-responsive']);
        ?>

        <p class="lead"></p>


    </div>
</div>