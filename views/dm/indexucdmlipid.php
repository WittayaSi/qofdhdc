<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'QOF-DHDC';
?>
<div class="hph-indexanc12week">
    <p>
        <?= Html::a('<i class="glyphicon glyphicon-search"></i> เข้าสู่รายงาน', ['ucdmlipid'], ['class' => 'btn btn-success']) ?>
       
    </p>

    <div class="text-center">     
        <?php
        echo Html::img('@web/images/ucdmlipid.jpg',['class'=>'img-responsive']);
        ?>

        <p class="lead"></p>


    </div>
</div>