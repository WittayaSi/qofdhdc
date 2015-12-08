<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use app\components\AccessRule;
use dektrium\user\models\User;

class HphController extends Controller {

    public $enableCsrfValidation = false;
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            
            'access'=>[
                'class'=>AccessControl::className(),
                'only'=> ['indivanc5','indivanc12week','indivnb2500','indivpap','indivdepress',
                           'indivdtp5','indivmmr1'
                    ],
                'ruleConfig'=>[
                    'class'=>AccessRule::className()
                ],
                'rules'=>[
                    [
                        'actions'=>['indivanc5','indivanc12week','indivnb2500','indivpap','indivdepress',
                           'indivdtp5','indivmmr1'
                    ],
                        'allow'=> true,
                        'roles'=>[
                            User::ROLE_USER,                            
                            User::ROLE_ADMIN

                        ]
                    ],                                     
                ]
            ]
        ];
    }
    public function actionIndexanc12week(){
        return $this->render('indexanc12week');
        
    }
     public function actionIndexanc5(){
        return $this->render('indexanc5');
        
    }
    public function actionIndexmmr1(){
        return $this->render('indexmmr1');
    }
    public function actionIndexdtp5(){
        return $this->render('indexdtp5');
        
    }
    public function actionIndexdepress(){
        return $this->render('indexdepress');
        
    }
    public function actionIndexpap(){
        return $this->render('indexpap');
        
    }
    public function actionIndexnb2500(){
        return $this->render('indexnb2500');
        
    }
    

    public function actionAnc5() {
    
        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
            
SELECT h.hoscode hospcode
	,h.hosname hospname
	,a.target
	,a.result
	,'0200' qof_code
FROM chospital_amp h
LEFT JOIN (
	SELECT l.hospcode
		,COUNT(DISTINCT l.pid) target
		,COUNT(DISTINCT IF(a1.ga<=14 
				AND (a2.ga between 15 AND 20)
				AND (a3.ga between 21 AND 28)
				AND (a4.ga between 29 AND 34)
				AND (a5.ga between 35 AND 42),l.pid,NULL)) result
	FROM labor l
	LEFT JOIN person p ON p.pid=l.pid AND l.hospcode=p.hospcode
	LEFT JOIN anc a1 ON a1.pid=l.pid AND a1.hospcode=l.hospcode AND a1.ga <=14 AND a1.gravida = l.gravida and a1.ancno = 1
	LEFT JOIN anc a2 ON a2.pid=l.pid AND a2.hospcode=l.hospcode AND (a2.ga between 15 and 20) AND a2.gravida = l.gravida and a2.ancno = 2
	LEFT JOIN anc a3 ON a3.pid=l.pid AND a3.hospcode=l.hospcode AND (a3.ga between 21 and 28) AND a3.gravida = l.gravida and a3.ancno = 3
	LEFT JOIN anc a4 ON a4.pid=l.pid AND a4.hospcode=l.hospcode AND (a4.ga between 29 and 34) AND a4.gravida = l.gravida and a4.ancno = 4
	LEFT JOIN anc a5 ON a5.pid=l.pid AND a5.hospcode=l.hospcode AND (a5.ga between 35 and 42) AND a5.gravida = l.gravida and a5.ancno = 5
	WHERE l.bdate BETWEEN '2015-04-01' AND '2016-03-31'
                and l.btype in (1,2,3,4,5)
				AND p.typearea IN (1,3)
				AND p.nation='099' 
	GROUP BY l.hospcode
) a ON a.hospcode=h.hoscode where h.hoscode <> '11241';

            ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'];
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pagesize' => false
            ]
        ]);
        return $this->render('anc5', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
                    'data' => $data
        ]);
    }
    
    public function actionIndivanc5($hospcode = null) {

        $sql = "
            select l.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
            ,a1.ga GA1,a2.ga GA2,a3.ga GA3,a4.ga GA4,a5.ga GA5
            ,if(a1.ga<=14 
            and a2.ga between 15 and 20
            and a3.ga between 21 and 28
            and a4.ga between 29 and 34
            and a5.ga between 35 and 42
            ,'Y',null) OK
            from labor l
            left join person p on p.pid=l.pid and l.hospcode=p.hospcode
            left join anc a1 on a1.pid=l.pid and a1.hospcode=l.hospcode and a1.ancno=1
            left join anc a2 on a2.pid=l.pid and a2.hospcode=l.hospcode and a2.ancno=2
            left join anc a3 on a3.pid=l.pid and a3.hospcode=l.hospcode and a3.ancno=3
            left join anc a4 on a4.pid=l.pid and a4.hospcode=l.hospcode and a4.ancno=4
            left join anc a5 on a5.pid=l.pid and a5.hospcode=l.hospcode and a5.ancno=5
            where l.bdate between '2015-04-01' AND '2016-03-31'
            and l.btype in (1,2,3,4,5)
            and p.typearea in (1,3)
            and p.nation=099
            and p.hospcode='$hospcode'
            group by l.hospcode,l.pid;
            ORDER BY OK ASC            
                ";
        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_anc5', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
    
    public function actionAnc12week() {
        
        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
            SELECT h.hoscode hospcode
	,h.hosname hospname
	,a.target
	,a.result
	,'0100' qof_code
FROM chospital_amp h
LEFT JOIN (
	SELECT l.hospcode
		,COUNT(DISTINCT l.pid) target
		,COUNT(DISTINCT IF(a.ga<=12,a.pid,NULL)) result
	FROM labor l
	LEFT JOIN person p ON p.pid=l.pid AND l.hospcode=p.hospcode
	LEFT JOIN anc a ON a.pid=l.pid AND a.hospcode=l.hospcode 
				AND a.gravida = l.gravida
				AND a.ancno=1
	WHERE l.bdate BETWEEN '2015-04-01' AND '2016-03-31'
			AND p.typearea IN (1,3) AND p.NATION = '099'
	GROUP BY l.hospcode
) a ON a.hospcode=h.hoscode;

            ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'];
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' =>[
                'pagesize'=>false
            ],
        ]);
        return $this->render('anc12week', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
                    'data' => $data,
        ]);
    }
    
    public function actionIndivanc12week($hospcode = null) {

        $sql = "
        select l.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
        ,a.ga,if(a.ga<=12,'Y',null) OK
        from labor l
        left join person p on p.pid=l.pid and l.hospcode=p.hospcode
        left join anc a on a.pid=l.pid and a.hospcode=l.hospcode and a.ancno=1 AND a.gravida = l.gravida
        where l.bdate between '2015-04-01' AND '2016-03-31'
        and p.typearea in (1,3) and p.nation = '099'
        and p.hospcode='$hospcode'
        group by l.hospcode,l.pid;
                ";
        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_anc12week', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
    
    public function actionNb2500() {
        
        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
            SELECT h.hoscode hospcode
	,h.hosname hospname
	,a.target
	,a.result
	#,'0700' qof_code
FROM chospital_amp h
LEFT JOIN (
	SELECT p.hospcode
		,COUNT(DISTINCT p.pid) target
		,COUNT(DISTINCT IF(a.bweight<2500,a.pid,NULL)) result
	FROM person p
	LEFT JOIN newborn a ON a.pid=p.pid AND a.hospcode=p.hospcode
	WHERE a.bdate BETWEEN '2015-04-01' AND '2016-03-31'
			AND p.typearea IN (1,3) AND p.NATION = '099' and p.hospcode <> '11241'
	GROUP BY p.hospcode
) a ON a.hospcode=h.hoscode where h.hoscode <> '11241';

            ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'];
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pagesize' => false,
            ]
        ]);
        return $this->render('nb2500', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
                    'data' => $data,
        ]);
    }
    
     public function actionIndivnb2500($hospcode = null) {

        $sql = "
            select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
        ,a.bweight
        ,if(a.bweight<2500,'Y',NULL) 'OK'
        from person p
        left join newborn a on a.pid=p.pid and a.hospcode=p.hospcode
        where a.bdate between '2015-04-01' AND '2016-03-31'
        and p.typearea in (1,3)
        and p.nation = '099'
        and p.hospcode='$hospcode'
        group by p.hospcode,p.pid;
                        ";
        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_nb2500', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
    
    public function actionPap() {
        
        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
            select h.hoscode hospcode,h.hosname hospname
,a.target,a.result
from chospital_amp h
left join (
select p.hospcode
,count(distinct p.pid) target
,count(distinct if(a.diagcode in ('Z014','Z124'),a.seq,null)) result
from person p
left join diagnosis_opd a on (a.pid=p.pid and a.hospcode=p.hospcode) 
     and a.date_serv between '2015-04-01' AND '2016-03-31'
where timestampdiff(YEAR,p.birth,'2015-04-01') between 30 and 60
and p.sex=2
and p.typearea in (1,3)
and p.nation = '099'
group by p.hospcode) a on a.hospcode=h.hoscode where h.hoscode <> '11241';

            ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'];
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pagesize' => false,
            ]
        ]);
        return $this->render('pap', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
                    'data' => $data,
        ]);
    }
    
    public function actionIndivpap($hospcode = null) {

        $sql = "
            select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
        ,if(a.diagcode in ('Z014','Z124'),'Y',null) 'OK'
        from person p
        left join diagnosis_opd a on a.pid=p.pid and a.hospcode=p.hospcode 
        and a.date_serv between '2015-04-01' AND '2016-03-31'
        where timestampdiff(year,p.birth,'2015-04-01') between 30 and 60
        and p.sex=2
        and p.typearea in (1,3)
        and p.nation = '099'
        and p.hospcode='$hospcode'
        group by p.hospcode,p.pid;
                                ";
        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_pap', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
    
    public function actionDepress() {
        
        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
                       
            select h.hoscode hospcode,h.hosname hospname
            ,a.target,a.result
            from chospital_amp h
            left join (
            select p.hospcode,count(distinct p.pid) target
            ,count(distinct if(left(a.diagcode,3) in ('F32','F33','F38','F39') 
            or a.diagcode in ('F341'),a.pid,null)) result
            from person p
            left join diagnosis_opd a on a.pid=p.pid and a.hospcode=p.hospcode
            where timestampdiff(year,p.birth,'2015-04-01')>15
            and p.typearea in (1,3)
            and p.nation ='099'
            group by p.hospcode) a on a.hospcode=h.hoscode;

            ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'];
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pagesize' => false
            ]
        ]);
        return $this->render('depress', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
                    'data' => $data,
        ]);
    }
    
    
    public function actionIndivdepress($hospcode = null) {

        $sql = "
            select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
            ,if(left(a.diagcode,3) in ('F32','F33','F38','F39') or a.diagcode in ('F341'),'Y',null) 'OK'
            from person p
            left join diagnosis_opd a on a.pid=p.pid and a.hospcode=p.hospcode
            where timestampdiff(year,p.birth,'2015-04-01')>15
            and p.typearea in (1,3)
            and p.nation = '099'
            and p.hospcode='$hospcode'
            group by p.hospcode,p.pid;
            
                ";
        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_depress', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }

    public function actionMmr1(){
        $connection = Yii::$app->db2;
        $data = $connection->createCommand("
            select h.hoscode hospcode
                ,h.hosname hospname
                ,a.target
                ,a.result
            from chospital_amp h
            left join (
                select p.hospcode
                    ,count(distinct p.pid) target
                    ,count(distinct if(e.date_serv between '2015-04-01' and '2016-03-31',e.pid,null)) result
                from person p
                left join epi e on (e.pid = p.pid and e.hospcode = p.hospcode) and e.vaccinetype in ('061','073','074','076')
                where p.birth between '2014-04-01' and '2015-03-31'
                    and p.discharge = '9' 
                    and p.typearea in (1,3)
                    and p.nation = '099'
                group by p.hospcode
            ) a on (a.hospcode = h.hoscode) where h.hoscode <> '11241';
        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++){
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'];
        }
        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pagesize' => false,
            ]
        ]);
        return $this->render('mmr1', [
            'dataProvider' => $dataProvider,
            'hospcode' => $hospcode,
            'hospname' => $hospname,
            'target' => $target,
            'result' => $result,
            'data' => $data,
        ]);

    }

    public function actionIndivmmr1($hospcode = null){
        $sql = "
            select p.hospcode,p.cid,p.pid,concat(p.name,' ',p.lname) ptname
            ,e.vaccinetype,p.birth,e.date_serv,if((e.date_serv between '2015-04-01' and '2016-03-31'),'Y',null) OK
            from person p
            left join epi e on (e.pid=p.pid and e.hospcode=p.hospcode) and e.vaccinetype in ('061','073','074','076')
            where (p.birth between '2014-04-01' and '2015-03-31')
                    and p.discharge = '9' 
                    and p.typearea in (1,3)
                    and p.nation = '099'
                    and p.hospcode = '$hospcode'
            group by p.hospcode,p.pid;
            
                ";
        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_mmr1', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
    
    public function actionDtp5() {
        
        $connection = Yii::$app->db2;
        $data = $connection->createCommand("                        
            SELECT h.hoscode hospcode
	,h.hosname hospname
	,a.target
	,a.result
FROM chospital_amp h
LEFT JOIN (
	SELECT p.hospcode
		,COUNT(DISTINCT p.pid) target
		,COUNT(DISTINCT IF(TIMESTAMPDIFF(YEAR,p.birth,a.date_serv)<=5,a.pid,NULL)) result
	FROM person p
	LEFT JOIN epi a ON a.pid=p.pid AND a.hospcode=p.hospcode AND a.vaccinetype='035'
	WHERE p.birth BETWEEN '2010-04-01' AND '2011-03-31' AND p.discharge = '9'
			AND p.typearea IN (1,3) AND p.NATION = '099'
	GROUP BY p.hospcode
) a ON a.hospcode=h.hoscode where h.hoscode <> '11241';

            ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'];
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' =>[
                'pagesize' => false,
            ]
        ]);
        return $this->render('dtp5', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
                    'data' => $data
        ]);
    }
    
    public function actionIndivdtp5($hospcode = null) {

        $sql = "
            select p.hospcode,p.cid,p.pid,concat(p.name,' ',p.lname) ptname
            ,a.vaccinetype,p.birth,a.date_serv,if(timestampdiff(year,p.birth,a.date_serv)<=5,'Y',null) OK
            from person p
            left join epi a on a.pid=p.pid and a.hospcode=p.hospcode and a.vaccinetype='035'
            where p.birth between '2010-04-01' AND '2011-03-31' AND p.discharge = '9'
            and p.typearea in (1,3) AND p.NATION = '099'
            and p.hospcode='$hospcode' and p.hospcode <> '11241'
            group by p.hospcode,p.pid;
            
                ";
        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_dtp5', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
    }

