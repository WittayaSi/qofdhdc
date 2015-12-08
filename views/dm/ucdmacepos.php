
<?php

$this->title = 'DM';

use kartik\grid\GridView;
use miloschuman\highcharts\Highcharts;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

$this->params['breadcrumbs'][] = $this->title;
//$datas = $dataProvider->getModels();
?>
<div class="pull-left">   
        <a class="btn  btn-primary"
       href="<?= Url::to(['indexucdmacepos']) ?>">
        <i class="glyphicon glyphicon-chevron-left"> ย้อนกลับ</i>
    </a>

</div>
<?php Pjax::begin(); ?> 
<?php

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
//       [
//            'label'=>'HOSPCODE',
//            'attribute'=>'hospcode',
//            'format'=>'raw',
//            'value'=> function($model)use($hospcode){
//                return Html::a(Html::encode($model['hospcode']),[
//                    'dm/indivucdmacetra/',
//                    'hospcode'=>$model['hospcode'],                    
//                ]) ;
//            }            
//        ], 
    [
        'label' => 'รหัสสถานบริการ ',
        'attribute' => 'hospcode',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'label' => 'สถานบริการ ',
        'attribute' => 'hospname',
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'attribute' => 'target',
        'label' => 'Target',
        'format' => 'integer',
        'pageSummary' => true,
        'vAlign' => 'middle',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'class' => 'kartik\grid\DataColumn',
        'label' => 'Result',
        'attribute' => 'result',
        'format' => 'integer',
        'pageSummary' => true,
        'vAlign' => 'middle',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
    ],
    [
        'label' => 'สถานบริการ ',
        'attribute' => 'hospname',
        'headerOptions' => ['class' => 'text-center'],
    ],
];
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '-'],
    'columns' => $gridColumns,
    'responsive' => true,
    'hover' => true,
    'striped' => false,
    'floatHeader' => FALSE,
    'showPageSummary' => true,
    'panel' => [
        'type' => GridView::TYPE_SUCCESS,
        'heading' => 'DM สิทธิUC Microalbuminuria ได้รับยากลุ่ม ACEหรือARB(POSITIVE) ปีงบประมาณ 2559'
    
    ],
]);
?>
<?php Pjax::end(); ?> 