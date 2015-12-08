<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sqlscript */

$this->title = 'Update Sqlscript: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Sqlscripts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sqlscript-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
