
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
       href="<?= Url::to(['indexpredm']) ?>">
        <i class="glyphicon glyphicon-chevron-left"> ย้อนกลับ</i>
    </a>

</div>
<?php Pjax::begin();?> 
<?php
$gridColumns = [
    ['class'=>'kartik\grid\SerialColumn'], 
        
       [
            'label'=>'HOSPCODE',
            'attribute'=>'hospcode',
            'format'=>'raw',
            'value'=> function($model)use($hospcode){
                return Html::a(Html::encode($model['hospcode']),[
                    'dm/indivpredm/',
                    'hospcode'=>$model['hospcode'],
                    //'hospcode'=>$hospcode
                ]) ;
            }            
        ], 
//    [
//            'label'=>'รหัสสถานบริการ ',
//            'attribute'=>'hospcode',
//            'headerOptions' => ['class'=>'text-center'],
//            'contentOptions' => ['class'=>'text-center'],
//        ],
[
            'label'=>'สถานบริการ ',
            'attribute'=>'hospname',
            'headerOptions' => ['class'=>'text-center'],
            
        ],
        [
            'class' => 'kartik\grid\DataColumn',
            'attribute' => 'target',
            'label'=>'จำนวนPre DM(คน)',
            'format'=>'integer',
            'pageSummary' => true,
            'vAlign' => 'middle',
            'headerOptions' => ['class'=>'text-center'],
            'contentOptions' => ['class'=>'text-center'],
        ],
        [
            'class' => 'kartik\grid\DataColumn',
            'label'=>'จำนวนป่วย (คน)',
            'attribute' => 'result',
            'format'=>'integer',
            'pageSummary' => true,
            'vAlign' => 'middle',
            'headerOptions' => ['class'=>'text-center'],
            'contentOptions' => ['class'=>'text-center'],
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
                'heading' => 'DM รายใหม่จากกลุ่ม PreDM ปีงบประมาณ 2559'
                //'footer'=>'ประมวลผล ณ วันที่  : '.  date('Y-m-d',strtotime($datas[4]['sdate'])), 
                        ],
                    ]);
            ?>
<?php Pjax::end();?> 