<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'อ๊ะ อ๊ะ ล็อคอินก่อนใช้งานในส่วนนี้';
?>
<div class="jumbotron">
      <h2><?=Html::img(Url::base().'/images/fg.png')?><?= Html::encode($this->title) ?></h2>
      <p class="text-danger"><?= nl2br(Html::encode($message)) ?></p>
     
</div>
