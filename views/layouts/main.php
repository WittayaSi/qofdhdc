<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\assets\MaterialAsset;

/* @var $this \yii\web\View */
/* @var $content string */

//AppAsset::register($this);
MaterialAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>

        <div class="wrap">
            <?php
            NavBar::begin([
                'brandLabel' => 'QOF59-THASONGYANG',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'encodeLabels' => FALSE,
                'items' => [
                    ['label' => '<i class="glyphicon glyphicon-home"></i>', 'url' => ['/site/index']],
//            ['label' => 'About', 'url' => ['/site/about']],
//            ['label' => 'Contact', 'url' => ['/site/contact']],
                    ['label' => '<i class="glyphicon glyphicon-list"></i> เบาหวาน',
                        'items' => [
                            ['label' => 'DM รายใหม่จากกลุ่ม', 'url' => ['/dm/indexpredm']],
                            ['label' => 'DM พบภาวะแทรกซ้อนทางไต', 'url' => ['/dm/indexkedneydm']],
                            ['label' => 'DM มีผล LDL น้อยกว่า 100', 'url' => ['/dm/indexldldm']],
                            ['label' => 'DM คัดกรองDM อายุ>=35ปี', 'url' => ['/dm/indexscreendm']],
                            ['label' => 'DM สิทธิUC ได้รับการตรวจ HbA1c', 'url' => ['/dm/indexucdmhba1c']],
                            //['label' => 'DM สิทธิUC อายุ15ปีขึ้นไป ได้รับการตรวจ HbA1c', 'url' => ['/dm/ucdm15uphba1c']],
                            ['label' => 'DM สิทธิUC ได้รับการตรวจ Lipid Profile', 'url' => ['/dm/indexucdmlipid']],
                            ['label' => 'DM สิทธิUC ได้รับการตรวจ Microalbumin', 'url' => ['/dm/indexucdmmicroal']],
                            ['label' => 'DM สิทธิUC มีแผลที่เท้า', 'url' => ['/dm/indexucdmfoot']],
                            ['label' => 'DM สิทธิUC ได้รับการตรวจเท้า', 'url' => ['/dm/indexucdmserfoot']],
                            ['label' => 'DM สิทธิUC ได้รับการตรวจจอประสาทตา', 'url' => ['/dm/indexucdmeye']],
                            ['label' => 'DM สิทธิUC Microalbuminuria ได้รับยากลุ่ม ACEหรือARB(TRACE)', 'url' => ['/dm/indexucdmacetra']],
                            ['label' => 'DM สิทธิUC Microalbuminuria ได้รับยากลุ่ม ACEหรือARB(POSITIVE)', 'url' => ['/dm/indexucdmacepos']],
                            //['label' => 'DM สิทธิUC อายุ15ปีขึ้นไปคุมระดับ HbA1cได้', 'url' => ['/dm/ucdm15uphba1ccontrol']],
                            ['label' => 'DM สิทธิUC อายุน้อยว่า<65ปีคุมระดับ HbA1cได้', 'url' => ['/dm/indexucdm65downhba1ccontrol']], //***ไม่มีข้อมูล
                            ['label' => 'DM สิทธิUC อายุ>=65ปีขึ้นไปคุมระดับ HbA1cได้', 'url' => ['/dm/indexucdm65uphba1ccontrol']],
                            ['label' => 'DM ได้รับการตรวจภาวะแทรกซ้อนอย่างน้อย1ครั้งต่อปี', 'url' => ['/dm/indexdmperyear']],
                            ['label' => 'DM ไม่มีภาวะแทรกซ้อนทางไต และได้รับการตรวจ Microalอย่างน้อย 1 ครั้งต่อปี และต้องมีผล LAB', 'url' => ['/dm/indexdmkedneymicro']],
                            ['label' => 'DM สิทธิUC อัตราการใช้บริการของผู้ป่วยใน ที่มีภาวะแทรกซ้อนระยะสั้น', 'url' => ['/dm/indexucdmseripd']],
                            ['label' => 'DM สิทธิUC อัตราการรับไว้รักษาในโรงพยาบาล ที่มีภาวะแทรกซ้อนทางไต', 'url' => ['/dm/indexucdmadmit']],
                            ['label' => 'DM สิทธิUC อัตราการรับไว้รักษาในโรงพยาบาล ผู้ป่วยตัดขาจากภาวะแทรกซ้อน', 'url' => ['/dm/indexucdmleg']],
                        ]
                    ],
                    ['label' => '<i class="glyphicon glyphicon-list"></i> ความดัน',
                        'items' => [
                            ['label' => 'HT รายใหม่จากกลุ่ม PreHT', 'url' => ['/ht/indexpreht']],
                            ['label' => 'HT การคัดกรอง', 'url' => ['/ht/indexhtscreen']],
                            ['label' => 'HT สิทธิUC ได้รับการตรวจ Lipid Profile', 'url' => ['/ht/indexhtlipid']],
                            ['label' => 'HT ที่พบภาวะแทรกซ้อนทางไต', 'url' => ['/ht/indexhtkedney']],
                            ['label' => 'HT-(อย่างเดียว)ที่ควบคุมได้2ครั้งสุดท้าย', 'url' => ['/ht/indexht2control']],
                            ['label' => 'HT ที่ได้รับการตรวจภาวะแทรกซ้อนอย่างน้อย1ครั้งต่อปี(FGP)-กองทุน', 'url' => ['/ht/indexhtperyear']],
                            ['label' => 'HT ที่ได้รับการตรวจภาวะแทรกซ้อนอย่างน้อย1ครั้งต่อปี', 'url' => ['/ht/indexhtperyearnor']],
                            ['label' => 'HT อัตราการรับไว้รักษาในโรงพยาบาล ด้วยโรคความดันโลหิตสูงหรือภาวะแทรกซ้อนของความดันโลหิตสูง ', 'url' => ['/ht/indexuchtipd']],
                        ]
                    ],
                    ['label' => '<i class="glyphicon glyphicon-list"></i> PP',
                        'items' => [
                            ['label' => 'PP 5ครั้ง', 'url' => ['/hph/indexanc5']],
                            ['label' => 'PP-12สัปดาห์', 'url' => ['/hph/indexanc12week']],
                            ['label' => 'PP-NB น้ำหนักเด็กแรกเกิดน้อยกว่า2500กรัม', 'url' => ['/hph/indexnb2500']],
                            ['label' => 'PP-PAPSMEAR ', 'url' => ['/hph/indexpap']],
                            ['label' => 'PP-Depression', 'url' => ['/hph/indexdepress']],
                            ['label' => 'PP-MMR1', 'url' => ['/hph/indexmmr1']],
                            ['label' => 'PP-DTP5', 'url' => ['/hph/indexdtp5']],
                        ]
                    ],
                    /*['label' => '<i class="glyphicon glyphicon-refresh"></i> Run SQL',
                        'items' => [
                            ['label' => '<i class="glyphicon glyphicon-refresh"></i> Run SQL', 'url' => ['/sqlcode/index']],
                        ]
                    ],*/
                    Yii::$app->user->isGuest ?
                            ['label' => 'เข้าสู่ระบบ', 'url' => ['/user/security/login']] :
                            ['label' => '<i class="glyphicon glyphicon-user"></i> (' . Yii::$app->user->identity->username . ')', 'items' => [
                            ['label' => 'ข้อมูลส่วนตัว', 'url' => ['/user/settings/profile']],
                            //['label' => 'Account', 'url' => ['/user/settings/account']],
                            ['label' => 'ออกจากระบบ', 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']],
                        ]],
                    ['label' => '<i class="glyphicon glyphicon-exclamation-sign"></i> เพิ่มผู้ใช้', 'url' => ['/user/registration/register'], 'visible' => !Yii::$app->user->isGuest],
                ],
            ]);
            NavBar::end();
            ?>

            <div class="container" style="margin-top: 80px;">
            <?=
            Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ])
            ?>
            <?= $content ?>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
        <!--        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>
        
                <p class="pull-right"><?= Yii::powered() ?></p>-->
            </div>
        </footer>

<?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
