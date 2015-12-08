<?php

$this->params['breadcrumbs'][]=$this->title;
//use yii\grid\GridView;
use miloschuman\highcharts\Highcharts;
use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
?>
<div class="pull-left">
    <h4>
        <span style="background-color:#00A2E8; color: white;padding: 5px">ปีงบประมาณ 2559</span>
    </h4>
        <a class="btn  btn-success"
       href="<?= Url::to(['uchtipd']) ?>">
        <i class="glyphicon glyphicon-chevron-left"> ย้อนกลับ</i>
    </a>

</div>
<?php 
function filter($col) {
    $filterresult = Yii::$app->request->getQueryParam('filterresult', '');
    if (strlen($filterresult) > 0) {
        if (strpos($col['result'], $filterresult) !== false) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

$filteredData = array_filter($rawData, 'filter');
$searchModel = ['result' => Yii::$app->request->getQueryParam('$filterresult', '')];

$dataProvider = new ArrayDataProvider([

    'allModels' => $filteredData,
    'pagination' =>[
                'pagesize'=>15
            ],
    'sort' => [
        //'attributes' => count($rawData[0]) > 0 ? array_keys($rawData[0]) : array()
        ]]);


    
    $gridColumns = [
    ['class'=>'kartik\grid\SerialColumn'],        
            
        [
            'label'=>'HOSPCODE ',
            'attribute'=>'hospcode',
            'headerOptions' => ['class'=>'text-center'],
            'contentOptions' => ['class'=>'text-center'],
        ],
        [
            'label'=>'CID',
            'attribute'=>'cid',
            'headerOptions' => ['class'=>'text-center'],
            'contentOptions' => ['class'=>'text-center'],
        ],
        [
            'label'=>'HN',
            'attribute'=>'hn',
            'headerOptions' => ['class'=>'text-center'],
            'contentOptions' => ['class'=>'text-center'],
        ],
         [
            'label'=>'PID',
            'attribute'=>'pid',
            'headerOptions' => ['class'=>'text-center'],            
        ],        
        
        [
            'label'=>'ชื่อ-สกุล',
            'attribute'=>'ptname',
            'headerOptions' => ['class'=>'text-center'],            
        ],        
        [
            'label'=>'Pdx',
            'attribute'=>'Pdx',
            'headerOptions' => ['class'=>'text-center'],
            'contentOptions' => ['class'=>'text-center'],
        ],
        [
            'label'=>'Sdx',
            'attribute'=>'Sdx',
            'headerOptions' => ['class'=>'text-center'],
            'contentOptions' => ['class'=>'text-center'],
        ],
        [
            'label'=>'Pdx1',
            'attribute'=>'Pdx1',
            'headerOptions' => ['class'=>'text-center'],
            'contentOptions' => ['class'=>'text-center'],
        ],
        [
            'label'=>'Pdx2',
            'attribute'=>'Pdx2',
            'headerOptions' => ['class'=>'text-center'],
            'contentOptions' => ['class'=>'text-center'],
        ],
        [
            'label'=>'Pdx3',
            'attribute'=>'Pdx3',
            'headerOptions' => ['class'=>'text-center'],
            'contentOptions' => ['class'=>'text-center'],
        ],
        [
            'label'=>'Sdx1',
            'attribute'=>'Sdx1',
            'headerOptions' => ['class'=>'text-center'],
            'contentOptions' => ['class'=>'text-center'],
        ],
        [
            'label'=>'Sdx2',
            'attribute'=>'Sdx2',
            'headerOptions' => ['class'=>'text-center'],
            'contentOptions' => ['class'=>'text-center'],
        ],
        [
            'label'=>'Sdx3',
            'attribute'=>'Sdx3',
            'headerOptions' => ['class'=>'text-center'],
            'contentOptions' => ['class'=>'text-center'],
        ],
        [
            'label'=>'Result',
            'attribute'=>'result',
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
            'floatHeader' => FALSE,        
           //'showPageSummary' => true,
            'panel' => [           
                'type' => GridView::TYPE_INFO,
                'heading' => 'HT รายชื่อสิทธิUC-(กองทุน) อัตราการรับไว้รักษาในโรงพยาบาล ด้วยโรคความดันโลหิตสูงหรือภาวะแทรกซ้อนของความดันโลหิตสูง ปีงบประมาณ 2559',
                
                        ],
                    ]);
            ?>

