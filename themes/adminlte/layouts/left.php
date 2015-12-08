<?php
use yii\bootstrap\Nav;
use yii\helpers\Url;
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?php echo Yii::$app->session["Username"] . '<br>' . Yii::$app->session["Department"]; ?></p>
<!--                <small><?php echo Yii::$app->session["Department"]; ?> </small>
                <br>-->
                <a href="index.php?r=stmusername/logout"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
<!--        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>-->
        <!-- /.search form -->

    <ul class="sidebar-menu">


            <li class="header"><h5><div class="label label-default"> เมนู</div></h5></li>
             <li><a href="<?php echo Url::to(['occupation/index']); ?>"><i class="fa fa-circle text-green"></i> <span> บันทึกMED-ERROR</span> <small class="label pull-right bg-blue"></small></a> </li>
            <li><a href="<?php echo Url::to(['disease/index']); ?>"><i class="fa fa-circle text-aqua"></i> <span> รายการMED-ERROR</span><small class="label pull-right bg-red"></small></a></li>           
            <li><a href="<?php echo Url::to(['sex/index']); ?>"><i class="fa fa-circle text-orange"></i> <span> ระบบรายงาน</span><small class="label pull-right bg-aqua"></small></a></li>
<!--            <li><a href="<?php echo Url::to(['year/index']); ?>"><i class="fa fa-circle text-blue"></i> <span> แยกตามปี</span><small class="label pull-right bg-orange"></small></a></li>-->
            
             


        </ul>

    </section>

</aside>
