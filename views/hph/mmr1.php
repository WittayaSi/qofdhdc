
<?php

$this->title = 'MMR1';

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


<?php

for($i=0;$i<sizeof($data);$i++){
    $categories[] = $hospname[$i];
}
$js_categories = implode("','", $categories);

for($i=0;$i<sizeof($data);$i++){
    if($target[$i] == null){
        $datas[] = 0;
    }else{
        $datas[] = number_format($result[$i]/$target[$i]*100,2,'.','');
    }
}
$js_datas = implode(',', $datas);

    $this->registerJs("
        var categories = ['$js_categories'];
        $('#graph').highcharts({
            chart: {
                type: 'column',
                height: 600
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'เปอร์เซ็นต์ MMR1'
            },
            subtitle: {
                text: 'ข้อมูลจาก : 43 แฟ้ม'
            },
            xAxis: {
                categories: categories,
                labels: {
                    rotation : -45
                },
                reversed: false,
            },
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: 'เปอร์เซ็นต์'
                }
            },
            series: [{
                name: 'percent',
                data: [$js_datas]
            }]
        });
    ");
?>

<div class="row">
    <div style="display: none">
        <?=
        Highcharts::widget([
            'scripts' => [
                'highcharts-more', // enables supplementary chart types (gauge, arearange, columnrange, etc.)
                //'modules/exporting', // adds Exporting button/menu to chart
                'themes/grid'        // applies global 'grid' theme to all charts
            ]
        ]);
        ?>
    </div>
    <div id="graph"></div>
</div>
<br>

<div class="pull-left">  
    <h4>
        <span style="color: white;padding: 5px;">MMR1 ปีงบประมาณ 2559</span>
    </h4>  
    <a class="btn  btn-success"
       href="<?= Url::to(['indexmmr1']) ?>">
        <i class="glyphicon glyphicon-chevron-left"> ย้อนกลับ</i>
    </a>

</div>
<?php Pjax::begin(); ?> 
<?php

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
        [
            'label' => 'HOSPCODE',
            'attribute' => 'hospcode',
            'format' => 'raw',
            'value' => function($model)use($hospcode) {
                return Html::a(Html::encode($model['hospcode']), [
                            'hph/indivmmr1/',
                            'hospcode' => $model['hospcode'],
                        ]);
            }
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
                'class' => 'kartik\grid\DataColumn',
                'label' => 'เปอร์เซ็นต์',
                'format' => 'raw',
                'value' => function($model) {
                    if(!empty($model['target'])){
                        return ($model['result']/$model['target'])*100;
                    }
                },
                'format'=>['decimal',2],
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
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
                'type' => GridView::TYPE_PRIMARY,
                //'heading' => 'DTP5 ปีงบประมาณ 2559'            
            ],
        ]);
        ?>
        <?php Pjax::end(); ?> 