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

class DmController extends Controller {

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
                'only'=> ['indivpredm','indivkedneydm','indivldldm','indivscreendm','indivucdmhba1c',
                           'indivucdm15uphba1c','indivucdmlipid','indivucdmmicroal','indivucdmfoot' ,
                           'indivucdmserfoot','indivucdmeye','indivdmperyear','indivucdm15uphba1ccontrol',
                           'indivucdm65uphba1ccontrol','indivucdm65downhba1ccontrol','indivucdmseripd',
                           'indivucdmadmit','indivucdmleg'
                    ],
                'ruleConfig'=>[
                    'class'=>AccessRule::className()
                ],
                'rules'=>[
                    [
                        'actions'=>['indivpredm','indivkedneydm','indivldldm','indivscreendm','indivucdmhba1c',
                           'indivucdm15uphba1c','indivucdmlipid','indivucdmmicroal','indivucdmfoot' ,
                           'indivucdmserfoot','indivucdmeye','indivdmperyear','indivucdm15uphba1ccontrol',
                           'indivucdm65uphba1ccontrol','indivucdm65downhba1ccontrol','indivucdmseripd',
                            'indivucdmadmit','indivucdmleg'
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
    
    public function actionIndexpredm(){
        return $this->render('indexpredm');        
    }
     public function actionIndexkedneydm(){
        return $this->render('indexkedneydm');        
    }
    public function actionIndexldldm(){
        return $this->render('indexldldm');        
    }
    public function actionIndexucdmlipid(){
        return $this->render('indexucdmlipid');        
    }
    public function actionIndexucdmhba1c(){
        return $this->render('indexucdmhba1c');        
    }
    public function actionIndexucdmmicroal(){
        return $this->render('indexucdmmicroal');        
    }
    public function actionIndexucdmserfoot(){
        return $this->render('indexucdmserfoot');        
    }
    public function actionIndexucdmfoot(){
        return $this->render('indexucdmfoot');        
    }
    public function actionIndexucdmeye(){
        return $this->render('indexucdmeye');        
    }
     public function actionIndexucdmacetra(){
        return $this->render('indexucdmacetra');        
    }
    public function actionIndexucdmacepos(){
        return $this->render('indexucdmacepos');        
    }
     public function actionIndexucdm65downhba1ccontrol(){
        return $this->render('indexucdm65downhba1ccontrol');        
    }
    public function actionIndexucdm65uphba1ccontrol(){
        return $this->render('indexucdm65uphba1ccontrol');        
    }
    public function actionIndexdmperyear(){
        return $this->render('indexdmperyear');        
    }
     public function actionIndexscreendm(){
        return $this->render('indexscreendm');        
    }
    public function actionIndexdmkedneymicro(){
        return $this->render('indexdmkedneymicro');        
    }
    public function actionIndexucdmseripd(){
        return $this->render('indexucdmseripd');        
    }
     public function actionIndexucdmadmit(){
        return $this->render('indexucdmadmit');        
    }
    public function actionIndexucdmleg(){
        return $this->render('indexucdmleg');        
    }
    

    public function actionPredm() {


        $connection = Yii::$app->db2;
        $data = $connection->createCommand("       

SELECT h.hoscode hospcode
	,h.hosname hospname
	,a.target
	,a.result
	,'0501' qof_code
FROM chospital_amp h
LEFT JOIN (
	SELECT p.hospcode
				,COUNT(DISTINCT IF(p.DM_target='NODM',p.pid,NULL)) target
				,COUNT(DISTINCT IF(p.DM_work='NODM',p.pid,NULL)) result
	FROM(
		SELECT p.hospcode
					,p.pid
					,IF(c.pid IS NOT NULL, 'NODM', NULL) DM_work #edit from (c.pid is null)  to (c.pid is not null)
					,IF(cc.pid IS NULL, 'NODM', NULL) DM_target
					/*,c.DATE_DIAG
					,p.cid
					,p.hn
					,concat(p.name,' ',p.lname) ptname
					,timestampdiff(year,p.birth,'2015-10-01') age
					,p.typearea
					,p.nation
					,p.discharge 
					,n.date_serv
					,n.bslevel*/
		FROM person p
		LEFT JOIN ncdscreen n ON n.pid=p.pid AND n.hospcode=p.hospcode
		LEFT JOIN chronic c ON c.pid=p.pid AND c.hospcode=p.hospcode 
								AND c.chronic BETWEEN 'E10' AND 'E1499' 
								AND c.DATE_DIAG BETWEEN '2015-10-01' AND '2016-09-30'

		LEFT JOIN chronic cc ON cc.pid=p.pid AND cc.hospcode=p.hospcode AND cc.chronic BETWEEN 'E10' AND 'E1499' 
													AND c.DATE_DIAG < n.date_serv

		WHERE TIMESTAMPDIFF(YEAR,p.birth,'2015-10-01')>=35
			AND p.typearea IN (1,3)
			AND p.nation='099'
			AND p.discharge='9'
			AND n.date_serv BETWEEN '2015-10-01' AND '2016-09-30'
			AND n.bslevel BETWEEN 100 AND 125
	) p
	GROUP BY p.hospcode
) a ON a.hospcode=h.hoscode
                        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('predm', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }

    public function actionIndivpredm($hospcode = null) {

        $sql = "
            select p.hospcode,p.pid,p.cid,p.hn,concat(p.name,' ',p.lname) ptname
            ,timestampdiff(year,p.birth,'2015-10-01') age,p.typearea,p.nation,p.discharge 
            ,n.date_serv,n.bslevel,cc.date_diag,if(cc.date_diag is not null,'Y',null) OK
            from person p
            left join ncdscreen n on n.pid=p.pid and n.hospcode=p.hospcode
            left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode and c.chronic between 'E10' and 'E1499' and c.date_diag<'2015-10-01'
            left join chronic cc on cc.pid=p.pid and cc.hospcode=p.hospcode and cc.chronic between 'E10' and 'E1499'
            where timestampdiff(year,p.birth,'2015-10-01')>=35
            and p.typearea in (1,3)
            and p.nation=099
            and p.discharge=9
            and n.date_serv BETWEEN '2015-10-01' AND '2016-09-30'
            and n.bslevel between 100 and 125
            and c.pid is null
            and p.hospcode='$hospcode'
            group by p.hospcode,p.pid;
            
                ";


        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_predm', [
                    'rawData' => $rawData,
                    'sql' => $sql,
                        //'hospcode' => $hospcode,
        ]);
    }

    public function actionKedneydm() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand("  
         select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct if(dx.diagcode regexp 'E1(0|1|2|3|4)2',dx.diagcode,null) order by dx.diagcode) dx
,group_concat(distinct if(dx.diagcode regexp 'N18',dx.diagcode,null) order by dx.diagcode) cc

,if(group_concat(distinct if(dx.diagcode regexp 'E1(0|1|2|3|4)2','Y',null))='Y'
or group_concat(distinct if(dx.diagcode regexp 'N18','Y',null))='Y','Y',null) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode 
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_opd dx on dx.pid=p.pid and dx.hospcode=p.hospcode and dx.date_serv between '2015-10-01' and '2016-09-30'
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
group by p.hospcode,p.pid
order by result desc
) a on a.hospcode=h.hoscode
group by a.hospcode
        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('kedneydm', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    public function actionIndivkedneydm($hospcode = null) {

        $sql = "
           select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct if(dx.diagcode regexp 'E1(0|1|2|3|4)2',dx.diagcode,null) order by dx.diagcode) dx
,group_concat(distinct if(dx.diagcode regexp 'N18',dx.diagcode,null) order by dx.diagcode) cc

,if(group_concat(distinct if(dx.diagcode regexp 'E1(0|1|2|3|4)2','Y',null))='Y'
or group_concat(distinct if(dx.diagcode regexp 'N18','Y',null))='Y','Y',null) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode 
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_opd dx on dx.pid=p.pid and dx.hospcode=p.hospcode and dx.date_serv between '2015-10-01' and '2016-09-30'
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and p.hospcode='$hospcode'
group by p.hospcode,p.pid
order by result desc

            
                ";


        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_kedneydm', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
    
     public function actionLdldm() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand("  
            select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(if(s.labresult>0,s.labresult,null) order by s.date_serv desc) labresult
,group_concat(distinct if(s.labresult<100,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode 
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and s.labtest=09 
and s.labresult>0
and s.date_serv between '2015-10-01' and '2016-09-30'
group by p.hospcode,p.pid
order by result desc
) a on a.hospcode=h.hoscode
group by a.hospcode

        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ldldm', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivldldm($hospcode = null) {

        $sql = "
            select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(if(s.labresult>0,s.labresult,null) order by s.date_serv desc) labresult
,group_concat(distinct if(s.labresult<100,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode 
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and s.labtest=09 
and s.labresult>0
and s.date_serv between '2015-10-01' and '2016-09-30'
and p.hospcode='$hospcode'
group by p.hospcode,p.pid
order by result desc

            
                ";


        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_ldldm', [
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
    
    public function actionScreendm() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand("  
            
SELECT a.hospcode
	,a.hospname 
	,b.target
	,a.result
	,'0401' qof_code
FROM (
	SELECT h.hoscode hospcode
		,h.hosname hospname
		,a.result
	FROM chospital_amp h
	LEFT JOIN (

		SELECT p.hospcode
			,COUNT(DISTINCT IF(p.DM_result = 'NODM',p.pid,NULL)) result
		FROM (
			SELECT p.hospcode
				,p.pid
				,IF(c.pid IS NULL, 'NODM', NULL) DM_result
			FROM person p
			LEFT JOIN ncdscreen a ON a.pid=p.pid AND a.hospcode=p.hospcode 
			LEFT JOIN chronic c ON c.pid=a.pid AND c.hospcode=a.hospcode 
									AND c.chronic BETWEEN 'E10' AND 'E1499' 
									AND c.date_diag<='2015-04-01'

			WHERE TIMESTAMPDIFF(YEAR,p.birth,'2015-04-01')>=35
					AND p.typearea IN (1,3) AND p.NATION = '099'
					AND c.pid IS NULL
					AND a.height IS NOT NULL AND a.height > 0
					AND a.weight IS NOT NULL AND a.weight > 0
					AND a.sbp_1 IS NOT NULL AND a.sbp_1 > 0
					AND a.dbp_1 IS NOT NULL AND a.dbp_1 > 0
					AND a.bslevel IS NOT NULL AND a.bslevel > 0
		) p GROUP BY p.hospcode
	) a ON a.hospcode=h.hoscode where h.hoscode <> '11241'
) a

LEFT JOIN (
	SELECT h.hoscode hospcode
		,h.hosname hospname
		,a.target
	FROM chospital_amp h
	LEFT JOIN (

		SELECT p.hospcode
			,COUNT(DISTINCT IF(p.DM_target = 'NODM',p.pid,NULL)) target
		FROM (
			SELECT p.hospcode
				,p.pid
				,IF(c.pid IS NULL, 'NODM', NULL) DM_target
			FROM person p
			LEFT JOIN chronic c ON c.pid=p.pid AND c.hospcode=p.hospcode 
									AND c.chronic BETWEEN 'E10' AND 'E1499' 
									AND c.date_diag<='2015-04-01'

			WHERE TIMESTAMPDIFF(YEAR,p.birth,'2015-04-01')>=35
					AND p.typearea IN (1,3) AND p.NATION = '099'
					AND c.pid IS NULL
		) p GROUP BY p.hospcode
	) a ON a.hospcode=h.hoscode where h.hoscode <> '11241'

) b ON a.hospcode = b.hospcode;

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
        return $this->render('screendm', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
                    'data' => $data,
        ]);
    }
    
    public function actionIndivscreendm($hospcode = null) {

        $sql = "
           select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
            ,cast(group_concat(distinct a.date_serv order by a.date_serv desc) as char(10)) date_serv
            ,if(a.weight>0 and a.height>0,'Y',null) OK
            from person p
            left join ncdscreen a on a.pid=p.pid and a.hospcode=p.hospcode and a.date_serv between '2015-10-01' and '2016-09-30'
            left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode and c.chronic between 'I10' and 'I1599' and c.date_diag<'2015-10-01'
            where timestampdiff(year,p.birth,'2015-10-01')>=35
            and p.typearea in (1,3)
            and c.pid is null
            and p.hospcode='$hospcode'
            
                ";


        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_screendm', [
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
    
    public function actionUcdmhba1c() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand("  
            
SELECT a.hospcode
	,a.hospname
	,b.target
	,a.result
	,'0801' qof_code
FROM (

	SELECT h.hoscode hospcode
		,h.hosname hospname
		,a.result
	FROM chospital_amp h
	INNER JOIN (
		SELECT p.hospcode
			,COUNT(DISTINCT IF(l.labresult>0,l.pid,NULL)) result
		FROM person p
		LEFT JOIN chronic c ON c.pid=p.pid AND c.hospcode=p.hospcode
		LEFT JOIN service s ON s.pid=p.pid AND s.hospcode=p.hospcode
		LEFT JOIN labfu l ON l.pid=p.pid AND l.hospcode=p.hospcode 
							AND l.labtest=05 
							AND l.date_serv BETWEEN '2015-10-01' AND '2016-09-30'
		WHERE c.chronic BETWEEN 'E10' AND 'E1499'
			AND s.instype='0100'
			AND p.typearea IN (1,3) AND p.NATION = '099'
			AND p.discharge = 9 
		GROUP BY p.hospcode
	) a ON a.hospcode=h.hoscode
) a

LEFT JOIN (	
	SELECT h.hoscode hospcode
		,h.hosname hospname
		,a.target
	FROM chospital_amp h
	INNER JOIN (
		SELECT p.hospcode
			,COUNT(DISTINCT p.pid) target
		FROM person p
		LEFT JOIN chronic c ON c.pid=p.pid AND c.hospcode=p.hospcode
		LEFT JOIN card cc ON cc.pid=p.pid AND cc.hospcode=p.hospcode
		WHERE c.chronic BETWEEN 'E10' AND 'E1499'
			AND cc.instype_new='0100'
			AND p.typearea IN (1,3) AND p.NATION = '099'
			AND p.discharge = 9
		GROUP BY p.hospcode
	) a ON a.hospcode=h.hoscode
) b ON a.hospcode = b.hospcode;

        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ucdmhba1c', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivucdmhba1c($hospcode = null) {

        $sql = "
           select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct s.date_serv) date_serv
,group_concat(if(s.labresult>0,s.labresult,null)) labresult
,group_concat(distinct if(s.labresult>0,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode and s.labtest=05 and s.date_serv between '2015-10-01' and '2016-09-30'
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and p.hospcode='$hospcode'
group by p.hospcode,p.pid
order by result desc
            
                ";


        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_ucdmhba1c', [
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
    
    public function actionUcdm15uphba1c() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand("  
            
        SELECT a.hospcode
	,a.hospname
	,b.target
	,a.result
	,'0802' qof_code
FROM (
	
	SELECT h.hoscode hospcode
		,h.hosname hospname
		,a.result
	FROM chospital_amp h
	INNER JOIN (
		SELECT p.hospcode
			,COUNT(DISTINCT IF(lab.labresult>0,lab.cid,NULL)) result
		FROM person p
		LEFT JOIN chronic c ON c.pid=p.pid AND c.hospcode=p.hospcode
		LEFT JOIN service s ON s.pid=p.pid AND s.hospcode=p.hospcode
		LEFT JOIN (SELECT p.cid,l.* 
				FROM labfu l 
				INNER JOIN person p ON p.hospcode = l.hospcode AND p.pid = l.pid 
				WHERE l.labtest=05 AND l.date_serv BETWEEN '2015-10-01' AND '2015-09-30') lab ON lab.cid = p.cid
		WHERE TIMESTAMPDIFF(YEAR,p.birth,'2015-10-01')>=15
			AND c.chronic BETWEEN 'E10' AND 'E1499'
			AND s.instype='0100' AND s.date_serv BETWEEN '2015-10-01' AND '2016-09-30'
			AND p.typearea IN ('1','3') AND p.NATION = '099'
			AND p.discharge = '9' 
		GROUP BY p.hospcode
	) a ON a.hospcode=h.hoscode
) a

LEFT JOIN (
	
	SELECT h.hoscode hospcode
		,h.hosname hospname
		,a.target
	FROM chospital_amp h
	INNER JOIN (
		SELECT p.hospcode
			,COUNT(DISTINCT p.pid) target
		FROM person p
		LEFT JOIN chronic c ON c.pid=p.pid AND c.hospcode=p.hospcode
		LEFT JOIN card cc ON cc.pid=p.pid AND cc.hospcode=p.hospcode
		WHERE TIMESTAMPDIFF(YEAR,p.birth,'2015-10-01')>=15
			AND c.chronic BETWEEN 'E10' AND 'E1499'
			AND cc.instype_new='0100'
			AND p.typearea IN ('1','3') AND p.NATION = '099'
			AND p.discharge = '9' 
		GROUP BY p.hospcode
	) a ON a.hospcode=h.hoscode
) b ON a.hospcode = b.hospcode;

        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ucdm15uphba1c', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivucdm15uphba1c($hospcode = null) {

        $sql = "
           select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
            ,timestampdiff(year,p.birth,'2015-10-01') age
            ,instype_new,c.chronic,s.date_serv,s.labresult,if(s.labresult>0,'Y',null) OK
            from person p
            left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
            left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
            left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode and s.labtest=05 
            and s.date_serv between '2015-10-01' and '2016-09-30'
            where timestampdiff(year,p.birth,'2015-10-01')>=15
            and c.chronic between 'E10' and 'E1499'
            and cc.instype_new=0100
            and p.typearea in (1,3)
            and p.hospcode='$hospcode'
            group by p.hospcode,p.pid;
            
                ";


        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_ucdm15uphba1c', [
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
    
    public function actionUcdmlipid() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand("  
     
select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(if(s1.labresult>0,s1.labresult,null)) TG
,group_concat(if(s2.labresult>0,s2.labresult,null)) Ch 
,group_concat(if(s3.labresult>0,s3.labresult,null)) HDL
,group_concat(if(s4.labresult>0,s4.labresult,null)) LDL
,group_concat(distinct if(s1.labresult>0 and s2.labresult>0 and s3.labresult>0 and s4.labresult>0,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s1 on s1.pid=p.pid and s1.hospcode=p.hospcode and s1.labtest=06 and s1.date_serv between '2015-10-01' and '2016-09-30'
left join labfu s2 on s2.pid=p.pid and s2.hospcode=p.hospcode and s2.labtest=07 and s2.date_serv between '2015-10-01' and '2016-09-30'
left join labfu s3 on s3.pid=p.pid and s3.hospcode=p.hospcode and s3.labtest=08 and s3.date_serv between '2015-10-01' and '2016-09-30'
left join labfu s4 on s4.pid=p.pid and s4.hospcode=p.hospcode and s4.labtest=09 and s4.date_serv between '2015-10-01' and '2016-09-30'
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
group by p.hospcode,p.pid
order by result desc
) a on a.hospcode=h.hoscode
group by a.hospcode

        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ucdmlipid', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivucdmlipid($hospcode = null) {

        $sql = "          
       select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(if(s1.labresult>0,s1.labresult,null)) TG
,group_concat(if(s2.labresult>0,s2.labresult,null)) Ch 
,group_concat(if(s3.labresult>0,s3.labresult,null)) HDL
,group_concat(if(s4.labresult>0,s4.labresult,null)) LDL
,group_concat(distinct if(s1.labresult>0 and s2.labresult>0 and s3.labresult>0 and s4.labresult>0,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s1 on s1.pid=p.pid and s1.hospcode=p.hospcode and s1.labtest=06 and s1.date_serv between '2015-10-01' and '2016-09-30'
left join labfu s2 on s2.pid=p.pid and s2.hospcode=p.hospcode and s2.labtest=07 and s2.date_serv between '2015-10-01' and '2016-09-30'
left join labfu s3 on s3.pid=p.pid and s3.hospcode=p.hospcode and s3.labtest=08 and s3.date_serv between '2015-10-01' and '2016-09-30'
left join labfu s4 on s4.pid=p.pid and s4.hospcode=p.hospcode and s4.labtest=09 and s4.date_serv between '2015-10-01' and '2016-09-30'
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and p.hospcode='$hospcode'
group by p.hospcode,p.pid
order by result desc

            
                ";

        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_ucdmlipid', [                    
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
    
    public function actionUcdmmicroal() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand("  
       SELECT a.hospcode
	,a.hospname
	,b.target
	,a.result
	,'1000' qof_code
FROM (
	
	SELECT h.hoscode hospcode
		,h.hosname hospname
		,a.result
	FROM chospital_amp h
	INNER JOIN (
		SELECT p.hospcode
			,COUNT(DISTINCT IF(l.labresult IS NOT NULL,l.pid,NULL)) result
		FROM person p
		LEFT JOIN chronic c ON c.pid=p.pid AND c.hospcode=p.hospcode
		LEFT JOIN service s ON s.pid=p.pid AND s.hospcode=p.hospcode
		LEFT JOIN labfu l ON l.pid=p.pid AND l.hospcode=p.hospcode 
							AND l.labtest='12' 
							AND l.date_serv BETWEEN '2015-10-01' AND '2016-09-30'
		WHERE c.chronic BETWEEN 'E10' AND 'E1499'
			AND s.instype='0100'
			AND p.typearea IN (1,3) AND p.NATION = '099'
		GROUP BY p.hospcode
	) a ON a.hospcode=h.hoscode
) a

LEFT JOIN (
	
	SELECT h.hoscode hospcode
		,h.hosname hospname
		,a.target
	FROM chospital_amp h
	INNER JOIN (
		SELECT p.hospcode
			,COUNT(DISTINCT p.pid) target
		FROM person p
		LEFT JOIN chronic c ON c.pid=p.pid AND c.hospcode=p.hospcode
		LEFT JOIN card cc ON cc.pid=p.pid AND cc.hospcode=p.hospcode
		WHERE c.chronic BETWEEN 'E10' AND 'E1499'
			AND cc.instype_new='0100'
			AND p.typearea IN (1,3) AND p.NATION = '099'
		GROUP BY p.hospcode
	) a ON a.hospcode=h.hoscode
) b ON a.hospcode = b.hospcode;

        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ucdmmicroal', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivucdmmicroal($hospcode = null) {

        $sql = "          
                select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
                ,instype_new,c.chronic,s.date_serv,s.labresult,if(s.labresult is not null,'Y',null) OK
                from person p
                left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
                left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
                left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode and s.labtest=12 
                and s.date_serv between '2015-10-01' AND '2016-09-30'
                where c.chronic between 'E10' and 'E1499'
                and cc.instype_new=0100
                and p.typearea in (1,3)
                and p.hospcode='$hospcode'
                group by p.hospcode,p.pid;            
                ";

        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_ucdmmicroal', [                    
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
    
    public function actionUcdmfoot() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand("  
select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct 
if(dx.diagcode regexp 'E1(0|1|2|3|4)5'
or dx.diagcode regexp 'E1(0|1|2|3|4)6'
or dx.diagcode regexp 'E1(0|1|2|3|4)7'
or dx.diagcode regexp 'E1(0|1|2|3|4)9'
or dx.diagcode regexp 'L030|L031|L024|M884|M886|I792|M142|M146'
,dx.diagcode,null) order by dx.diagcode) dx
,group_concat(distinct 
if(dx.diagcode regexp 'E1(0|1|2|3|4)5'
or dx.diagcode regexp 'E1(0|1|2|3|4)6'
or dx.diagcode regexp 'E1(0|1|2|3|4)7'
or dx.diagcode regexp 'E1(0|1|2|3|4)9'
or dx.diagcode regexp 'L030|L031|L024|M884|M886|I792|M142|M146'
,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_opd dx on dx.pid=c.pid and dx.hospcode=c.hospcode and dx.date_serv between '2015-10-01' and '2016-09-30'
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
group by p.hospcode,p.pid
order by dx desc
) a on a.hospcode=h.hoscode
group by a.hospcode

        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ucdmfoot', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivucdmfoot($hospcode = null) {

        $sql = "        
                  select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct 
if(dx.diagcode regexp 'E1(0|1|2|3|4)5'
or dx.diagcode regexp 'E1(0|1|2|3|4)6'
or dx.diagcode regexp 'E1(0|1|2|3|4)7'
or dx.diagcode regexp 'E1(0|1|2|3|4)9'
or dx.diagcode regexp 'L030|L031|L024|M884|M886|I792|M142|M146'
,dx.diagcode,null) order by dx.diagcode) dx
,group_concat(distinct 
if(dx.diagcode regexp 'E1(0|1|2|3|4)5'
or dx.diagcode regexp 'E1(0|1|2|3|4)6'
or dx.diagcode regexp 'E1(0|1|2|3|4)7'
or dx.diagcode regexp 'E1(0|1|2|3|4)9'
or dx.diagcode regexp 'L030|L031|L024|M884|M886|I792|M142|M146'
,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_opd dx on dx.pid=c.pid and dx.hospcode=c.hospcode and dx.date_serv between '2015-10-01' and '2016-09-30'
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and p.hospcode='$hospcode'
group by p.hospcode,p.pid
order by dx desc

                
                ";

        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_ucdmfoot', [                    
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
    
    public function actionUcdmserfoot() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand("  
         select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(if(s.foot is not null,s.foot,null) order by date_serv) foot
,group_concat(distinct if(s.foot in (1,3),'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join chronicfu s on s.pid=p.pid and s.hospcode=p.hospcode and s.date_serv between '2015-10-01' and '2016-09-30'
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
group by p.hospcode,p.pid
order by result desc,foot desc
) a on a.hospcode=h.hoscode
group by a.hospcode

        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ucdmserfoot', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivucdmserfoot($hospcode = null) {

        $sql = "        
                    select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(if(s.foot is not null,s.foot,null) order by date_serv) foot
,group_concat(distinct if(s.foot in (1,3),'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join chronicfu s on s.pid=p.pid and s.hospcode=p.hospcode and s.date_serv between '2015-10-01' and '2016-09-30'
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and p.hospcode='$hospcode'
group by p.hospcode,p.pid
order by result desc,foot desc

                ";

        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_ucdmserfoot', [                    
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
    
     public function actionUcdmeye() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand("  
            

select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(if(s.retina is not null,s.retina,null) order by date_serv) retina
,group_concat(distinct if(s.retina in (1,2,3,4),'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join chronicfu s on s.pid=p.pid and s.hospcode=p.hospcode and s.date_serv between '2015-10-01' and '2016-09-30'
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
group by p.hospcode,p.pid
order by result desc,retina desc
) a on a.hospcode=h.hoscode
group by a.hospcode

        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ucdmeye', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivucdmeye($hospcode = null) {

        $sql = " 
            select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(if(s.retina is not null,s.retina,null) order by date_serv) retina
,group_concat(distinct if(s.retina in (1,2,3,4),'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join chronicfu s on s.pid=p.pid and s.hospcode=p.hospcode and s.date_serv between '2015-10-01' and '2016-09-30'
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and p.hospcode='$hospcode'
group by p.hospcode,p.pid
order by result desc,retina desc

                ";

        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_ucdmeye', [                    
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
    
    public function actionDmperyear() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand("  
       
SELECT a.hospcode
		,a.hospname
		,b.target
		,a.result
		,'2100' qof_code
FROM (
	SELECT h.hoscode hospcode
		,h.hosname hospname
		,a.result
	FROM chospital_amp h
	LEFT JOIN (
		SELECT p.hospcode
			,COUNT(DISTINCT IF((
					(l.labtest = '12' AND l.labresult IS NOT NULL) 
					OR (l.labtest <> '12' AND l.labresult IS NOT NULL AND l.labresult > 0)
					) 
					OR cf.pid IS NOT NULL, p.pid, NULL)) result
		FROM person p
		LEFT JOIN service s ON s.pid=p.pid AND s.hospcode=p.hospcode
		LEFT JOIN chronic c ON c.pid = p.pid AND c.hospcode = p.hospcode
		LEFT JOIN labfu l ON l.pid = p.pid AND l.hospcode = p.hospcode
							AND l.labtest IN ('05','06','07','08','12')
							AND l.date_serv BETWEEN '2015-10-01' AND '2016-09-30'
		LEFT JOIN chronicfu cf ON cf.pid = p.pid AND cf.hospcode = p.hospcode
								AND (cf.retina IN ('1','2','3','4') OR cf.foot IN ('1','3'))
								AND cf.date_serv BETWEEN '2015-10-01' AND '2016-09-30'

		WHERE c.chronic BETWEEN 'E10' AND 'E1499'
			AND s.instype='0100'
			AND p.typearea IN (1,3) AND p.nation = '099'
		GROUP BY p.hospcode
	) a ON a.hospcode=h.hoscode
) a

LEFT JOIN (
	SELECT h.hoscode hospcode
		,h.hosname hospname
		,a.target
	FROM chospital_amp h
	LEFT JOIN (
		SELECT p.hospcode
				,COUNT(DISTINCT p.pid) target
		FROM person p
		LEFT JOIN card s ON s.pid=p.pid AND s.hospcode=p.hospcode
		LEFT JOIN chronic c ON c.pid = p.pid AND c.hospcode = p.hospcode

		WHERE c.chronic BETWEEN 'E10' AND 'E1499'
			AND s.instype_new='0100'
			AND p.typearea IN (1,3) AND p.nation = '099'
		GROUP BY p.hospcode
	) a ON a.hospcode=h.hoscode
) b ON b.hospcode = a.hospcode;

        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('dmperyear', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivdmperyear($hospcode = null) {

        $sql = "        
                    select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,instype_new,c.chronic,s1.labresult Lab05,s2.labresult Lab06,s3.labresult Lab07,s4.labresult Lab08,s5.labresult Lab12
,if(s1.labresult>0 and s2.labresult>0 and s3.labresult>0 and s4.labresult>0 and s5.labresult is not null,'Y',null) OK
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s1 on s1.pid=p.pid and s1.hospcode=p.hospcode and s1.labtest=05 and s1.date_serv between '2015-10-01' AND '2016-09-30'
left join labfu s2 on s2.pid=p.pid and s2.hospcode=p.hospcode and s2.labtest=06 and s2.date_serv between '2015-10-01' AND '2016-09-30'
left join labfu s3 on s3.pid=p.pid and s3.hospcode=p.hospcode and s3.labtest=07 and s3.date_serv between '2015-10-01' AND '2016-09-30'
left join labfu s4 on s4.pid=p.pid and s4.hospcode=p.hospcode and s4.labtest=08 and s4.date_serv between '2015-10-01' AND '2016-09-30'
left join labfu s5 on s5.pid=p.pid and s5.hospcode=p.hospcode and s5.labtest=12 and s5.date_serv between '2015-10-01' AND '2016-09-30'
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.hospcode='$hospcode'
group by p.hospcode,p.pid;
                ";

        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_dmperyear', [                    
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
    
     public function actionUcdmacetra() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 

SELECT a.hospcode
	,a.hospname
	,b.target
	,a.result
	,'1301' qof_code
FROM (	
	SELECT h.hoscode hospcode
		,h.hosname hospname
		,a.result
	FROM chospital_amp h
	LEFT JOIN (
		SELECT p.hospcode
			,COUNT(DISTINCT IF(didstd.trade_name IS NOT NULL AND l.labresult IS NOT NULL AND (l.labresult = 1 OR l.labresult BETWEEN 30 AND 299), p.pid, NULL)) result
		
		FROM person p
		LEFT JOIN chronic c ON c.pid=p.pid AND c.hospcode=p.hospcode
		LEFT JOIN service s ON s.pid=p.pid AND s.hospcode=p.hospcode
		LEFT JOIN labfu l ON l.pid=p.pid AND l.hospcode=p.hospcode 
																		AND l.date_serv BETWEEN '2015-10-01' AND '2016-09-30' 
		LEFT JOIN drug_opd d ON d.pid = p.pid
												AND d.hospcode = p.hospcode
												AND d.date_serv BETWEEN '2015-10-01' AND '2016-09-30'
		LEFT JOIN l_drug_didstd didstd ON didstd.std_code = d.DIDSTD
										
		WHERE c.chronic BETWEEN 'E10' AND 'E1499'
			AND l.labtest = '12'
			AND s.instype='0100'
			AND p.typearea IN (1,3) AND p.NATION = '099'
		GROUP BY p.hospcode
	) a ON a.hospcode=h.hoscode
) a

LEFT JOIN (

	SELECT h.hoscode hospcode
		,h.hosname hospname
		,target
	FROM chospital_amp h
	LEFT JOIN (
		SELECT p.hospcode
			,COUNT(DISTINCT IF(l.labresult IS NOT NULL AND (l.labresult = 1 OR l.labresult BETWEEN 30 AND 299), p.pid, NULL)) target
		FROM person p
		LEFT JOIN chronic c ON c.pid=p.pid AND c.hospcode=p.hospcode
		LEFT JOIN card cc ON cc.pid=p.pid AND cc.hospcode=p.hospcode
		LEFT JOIN labfu l ON l.pid=p.pid AND l.hospcode=p.hospcode AND l.date_serv BETWEEN '2015-10-01' AND '2016-09-30'
		WHERE c.chronic BETWEEN 'E10' AND 'E1499'
			AND cc.instype_new='0100'
			AND l.labtest = '12'
			AND p.typearea IN (1,3) AND p.NATION = '099'
		GROUP BY p.hospcode
	) a ON a.hospcode=h.hoscode
) b ON a.hospcode = b.hospcode;

                        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ucdmacetra', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionUcdmacepos() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
                

SELECT a.hospcode
	,a.hospname
	,b.target
	,a.result
	,'1302' qof_code
FROM (
	
	SELECT h.hoscode hospcode
		,h.hosname hospname
		,a.result
	FROM chospital_amp h
	LEFT JOIN (
		SELECT p.hospcode
			,COUNT(DISTINCT IF(didstd.trade_name IS NOT NULL AND l.labresult IS NOT NULL AND (l.labresult = 2 OR l.labresult >= 300), p.pid, NULL)) result
		
		FROM person p
		LEFT JOIN chronic c ON c.pid=p.pid AND c.hospcode=p.hospcode
		LEFT JOIN service s ON s.pid=p.pid AND s.hospcode=p.hospcode
		LEFT JOIN labfu l ON l.pid=p.pid AND l.hospcode=p.hospcode 
																		AND l.date_serv BETWEEN '2015-10-01' AND '2016-09-30' 
		LEFT JOIN drug_opd d ON d.pid = p.pid
												AND d.hospcode = p.hospcode
												AND d.date_serv BETWEEN '2015-10-01' AND '2016-09-30'
		LEFT JOIN l_drug_didstd didstd ON didstd.std_code = d.DIDSTD
										
		WHERE c.chronic BETWEEN 'E10' AND 'E1499'
			AND l.labtest = '12'
			AND s.instype='0100'
			AND p.typearea IN (1,3) AND p.NATION = '099'
		GROUP BY p.hospcode
	) a ON a.hospcode=h.hoscode
) a

LEFT JOIN (
	
	SELECT h.hoscode hospcode
		,h.hosname hospname
		,a.target
	FROM chospital_amp h
	LEFT JOIN (
		SELECT p.hospcode
			,COUNT(DISTINCT IF(l.labresult IS NOT NULL AND (l.labresult = 2 OR l.labresult >= 300), p.pid, NULL)) target
		FROM person p
		LEFT JOIN chronic c ON c.pid=p.pid AND c.hospcode=p.hospcode
		LEFT JOIN labfu l ON l.pid=p.pid AND l.hospcode=p.hospcode 
																		AND l.date_serv BETWEEN '2015-10-01' AND '2016-09-30'
		LEFT JOIN card cc ON cc.pid=p.pid AND cc.hospcode=p.hospcode
		WHERE c.chronic BETWEEN 'E10' AND 'E1499'
			AND cc.instype_new='0100'
			AND l.labtest = '12'
			AND p.typearea IN (1,3) AND p.NATION = '099'
		GROUP BY p.hospcode
	) a ON a.hospcode=h.hoscode
) b ON a.hospcode = b.hospcode;

                        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ucdmacepos', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }

    public function actionUcdm15uphba1ccontrol() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 

            
drop table if exists tmp_lab_all;
create table if not exists tmp_lab_all(
	cid varchar(13)
	,labtest varchar(7)
	,labresult decimal(6,2)
	,pid varchar(15)
	,date_serv date
	,index idx(cid, labtest, labresult, date_serv)
);

insert into tmp_lab_all
SELECT p.cid
	,l.labtest
	,l.labresult
	,l.pid
	,l.date_serv
FROM labfu l 
INNER JOIN person p ON p.hospcode = l.hospcode AND p.pid = l.pid 
WHERE l.labtest IN ('05', '01', '03')  AND l.date_serv BETWEEN '2015-10-01' AND '2016-09-30';

SELECT 
  h.hoscode hospcode,
  h.hosname hospname,
  a.target,
  a.result,
  '1503' qof_code
  
FROM chospital_amp h 
  LEFT JOIN 
    (
	SELECT  p2.hospcode,
		      COUNT(DISTINCT p.cid) target,
		      COUNT( DISTINCT IF(hba1c = 'Y' 
							 , IF(getwordnum(hba1c_lab_result, 1) <= 7,p.cid,NULL)
							 , IF(getwordnum (other_result, 1) BETWEEN 70  AND 130   
								AND getwordnum (other_result, 2) BETWEEN 70  AND 130, p.cid,NULL)
						)
				) result 

	  FROM
	      (
			SELECT  p.cid,
				GROUP_CONCAT(IF(s.labtest = '05', 'Y', NULL)) hba1c,
				GROUP_CONCAT(IF(s.labtest = '05', s.labresult, NULL) ORDER BY s.date_serv DESC ) hba1c_lab_result,
				GROUP_CONCAT(IF(s.labtest = '01', 'FPG', IF(s.labtest = '03', 'DTX', NULL)) ORDER BY s.date_serv DESC) other_lab,
				GROUP_CONCAT(IF(s.labtest = '01', s.labresult, IF(s.labtest = '03', s.labresult, NULL)) ORDER BY s.date_serv DESC ) other_result 
			from (
				 select distinct p.cid
				 FROM person p 
			         LEFT JOIN chronic c ON c.pid = p.pid  AND c.hospcode = p.hospcode 
			         LEFT JOIN service cc ON cc.pid = p.pid AND cc.hospcode = p.hospcode 
				 WHERE TIMESTAMPDIFF(YEAR, p.birth, '2015-10-01') > 15 
									AND c.chronic BETWEEN 'E10'  AND 'E1499' 
									AND cc.instype = '0100' 
									AND p.typearea IN ('1', '3') 
			) p
			LEFT JOIN tmp_lab_all s on s.cid = p.cid
			GROUP BY p.cid
	     ) p 
	     left join person p2 on p2.cid = p.cid

	    GROUP BY p2.hospcode
    ) a 
ON a.hospcode = h.hoscode ;

drop table if exists tmp_lab_all;

                        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ucdm15uphba1ccontrol', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivucdm15uphba1ccontrol($hospcode = null) {

        $sql = "        
            select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
            ,timestampdiff(year,p.birth,'2015-10-01') age
            ,instype_new,c.chronic,s.date_serv,s.labresult,if(s.labresult<7,'Y',null) OK
            from person p
            left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
            left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
            left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode 
            and s.labtest=05 and s.date_serv between '2015-10-01' AND '2016-09-30'
            where timestampdiff(year,p.birth,'2015-10-01')>=15
            and c.chronic between 'E10' and 'E1499'
            and cc.instype_new=0100
            and p.typearea in (1,3)
            and p.hospcode='$hospcode'
            group by p.hospcode,p.pid;
                ";

        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_ucdm15uphba1ccontrol', [                    
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
    
    public function actionUcdm65uphba1ccontrol() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
            
select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,timestampdiff(year,p.birth,'2015-10-01') age
,group_concat(if(s.labresult>0,s.labresult,null) order by s.date_serv desc) labresult
,group_concat(distinct if(s.labresult<=8.5,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode 
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and s.labtest=05 
and s.labresult>0
and s.date_serv between '2015-10-01' and '2016-09-30'
and timestampdiff(year,p.birth,'2015-10-01')>65
group by p.hospcode,p.pid
order by result desc
) a on a.hospcode=h.hoscode
group by a.hospcode

                        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ucdm65uphba1ccontrol', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivucdm65uphba1ccontrol($hospcode = null) {

        $sql = "        
            

select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,timestampdiff(year,p.birth,'2015-10-01') age
,group_concat(if(s.labresult>0,s.labresult,null) order by s.date_serv desc) labresult
,group_concat(distinct if(s.labresult<=8.5,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode 
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and s.labtest=05 
and s.labresult>0
and s.date_serv between '2015-10-01' and '2016-09-30'
and timestampdiff(year,p.birth,'2015-10-01')>65
and p.hospcode='$hospcode' 
group by p.hospcode,p.pid
order by result desc

                ";

        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_ucdm65uphba1ccontrol', [                    
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
    
   public function actionUcdm65downhba1ccontrol() {
        
        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
          select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,timestampdiff(year,p.birth,'2015-10-01') age
,group_concat(if(s.labresult>0,s.labresult,null) order by s.date_serv desc) labresult
,group_concat(distinct if(s.labresult<=8.5,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode 
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and s.labtest=05 
and s.labresult>0
and s.date_serv between '2015-10-01' and '2016-09-30'
and timestampdiff(year,p.birth,'2015-10-01')>65
group by p.hospcode,p.pid
order by result desc
) a on a.hospcode=h.hoscode
group by a.hospcode


            ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ucdm65downhba1ccontrol', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivucdm65downhba1ccontrol($hospcode = null) {

        $sql = "
            
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,timestampdiff(year,p.birth,'2015-10-01') age
,group_concat(if(s.labresult>0,s.labresult,null) order by s.date_serv desc) labresult
,group_concat(distinct if(s.labresult<=8.5,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode 
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and s.labtest=05 
and s.labresult>0
and s.date_serv between '2015-10-01' and '2016-09-30'
and timestampdiff(year,p.birth,'2015-10-01')>65
and p.hospcode='$hospcode'
group by p.hospcode,p.pid
order by result desc

            
                ";
        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_ucdm65downhba1ccontrol', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
    
    public function actionDmkedneymicro() {
        
        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
            select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(if(s.labresult is not null,s.labresult,null)) labresult
,group_concat(distinct if(s.labresult is not null,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode and s.labtest=12 and s.date_serv between '2015-10-01' and '2016-09-30'
where c.chronic between 'E10' and 'E1499'
and c.chronic not regexp 'E1(0|1|2|3|4)2'
and c.chronic not regexp 'N18'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
group by p.hospcode,p.pid
order by result desc
) a on a.hospcode=h.hoscode
group by a.hospcode
            ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('dmkedneymicro', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivdmkedneymicro($hospcode = null) {

        $sql = "
            select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(if(s.labresult is not null,s.labresult,null)) labresult
,group_concat(distinct if(s.labresult is not null,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode and s.labtest=12 and s.date_serv between '2015-10-01' and '2016-09-30'
where c.chronic between 'E10' and 'E1499'
and c.chronic not regexp 'E1(0|1|2|3|4)2'
and c.chronic not regexp 'N18'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and p.hospcode='$hospcode'
group by p.hospcode,p.pid
order by result desc
            
                ";
        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_dmkedneymicro', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
    
    public function actionUcdmseripd() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
            select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct if(a.diagcode regexp 'E1(1|2|3|4)0|E1(1|2|3|4)1',a.diagcode,null) order by a.diagcode) pdx
,if(group_concat(distinct if(a.diagcode regexp 'E1(1|2|3|4)0|E1(1|2|3|4)1','Y',null))='Y','Y',null) result 
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_ipd a on a.pid=p.pid and a.hospcode=p.hospcode and date(a.datetime_admit) between '2015-10-01' and '2016-09-30' and a.diagtype=1
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
group by p.hospcode,p.pid
order by result desc
) a on a.hospcode=h.hoscode
group by a.hospcode
                        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ucdmseripd', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivucdmseripd($hospcode = null) {

        $sql = "        
           select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct if(a.diagcode regexp 'E1(1|2|3|4)0|E1(1|2|3|4)1',a.diagcode,null) order by a.diagcode) pdx
,if(group_concat(distinct if(a.diagcode regexp 'E1(1|2|3|4)0|E1(1|2|3|4)1','Y',null))='Y','Y',null) result 
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_ipd a on a.pid=p.pid and a.hospcode=p.hospcode and date(a.datetime_admit) between '2015-10-01' and '2016-09-30' and a.diagtype=1
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and p.hospcode='$hospcode'
group by p.hospcode,p.pid
order by result desc
                ";

        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_ucdmseripd', [                    
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
    
    public function actionUcdmadmit() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
            select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct if(a.diagcode regexp 'E1(0|1|2|3|4)2',a.diagcode,null) order by a.diagcode) pdx
,if(group_concat(distinct if(a.diagcode regexp 'E1(0|1|2|3|4)2','Y',null))='Y','Y',null) result 
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_ipd a on a.pid=p.pid and a.hospcode=p.hospcode and date(a.datetime_admit) between '2015-10-01' and '2016-09-30' and a.diagtype=1
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
group by p.hospcode,p.pid
order by result desc
) a on a.hospcode=h.hoscode
group by a.hospcode
                        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ucdmadmit', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivucdmadmit($hospcode = null) {

        $sql = "        
           select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct if(a.diagcode regexp 'E1(0|1|2|3|4)2',a.diagcode,null) order by a.diagcode) pdx
,if(group_concat(distinct if(a.diagcode regexp 'E1(0|1|2|3|4)2','Y',null))='Y','Y',null) result 
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_ipd a on a.pid=p.pid and a.hospcode=p.hospcode and date(a.datetime_admit) between '2015-10-01' and '2016-09-30' and a.diagtype=1
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and p.hospcode='$hospcode'
group by p.hospcode,p.pid
order by result desc

                ";

        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_ucdmadmit', [                    
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
    
    public function actionUcdmleg() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
            select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct if(a.diagcode regexp 'E1(0|1|2|3|4)4|E1(0|1|2|3|4)5',a.diagcode,null) order by a.diagcode) pdx
,group_concat(distinct if(b.procedcode regexp '841',b.procedcode,null) order by b.procedcode) op 

,if(group_concat(distinct if(a.diagcode regexp 'E1(0|1|2|3|4)4|E1(0|1|2|3|4)5','Y',null))='Y' 
and group_concat(distinct if(b.procedcode regexp '841','Y',null))='Y','Y',null) result

from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_ipd a on a.pid=p.pid and a.hospcode=p.hospcode and date(a.datetime_admit) between '2015-10-01' and '2016-09-30' and a.diagtype=1
left join procedure_ipd b on a.an=b.an and a.hospcode=b.hospcode
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
group by p.hospcode,p.pid
order by result desc
) a on a.hospcode=h.hoscode
group by a.hospcode
                        ")->queryAll();

        for ($i = 0; $i < sizeof($data); $i++) {
            $hospcode[] = $data[$i]['hospcode'];
            $target[] = $data[$i]['target'] * 1;
            $result[] = $data[$i]['result'] * 1;
            $hospname[] = $data[$i]['hospname'] * 1;
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        return $this->render('ucdmleg', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivucdmleg($hospcode = null) {

        $sql = "        
           select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct if(a.diagcode regexp 'E1(0|1|2|3|4)4|E1(0|1|2|3|4)5',a.diagcode,null) order by a.diagcode) pdx
,group_concat(distinct if(b.procedcode regexp '841',b.procedcode,null) order by b.procedcode) op 

,if(group_concat(distinct if(a.diagcode regexp 'E1(0|1|2|3|4)4|E1(0|1|2|3|4)5','Y',null))='Y' 
and group_concat(distinct if(b.procedcode regexp '841','Y',null))='Y','Y',null) result

from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_ipd a on a.pid=p.pid and a.hospcode=p.hospcode and date(a.datetime_admit) between '2015-10-01' and '2016-09-30' and a.diagtype=1
left join procedure_ipd b on a.an=b.an and a.hospcode=b.hospcode
where c.chronic between 'E10' and 'E1499'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and p.hospcode='$hospcode'
group by p.hospcode,p.pid
order by result desc

                ";

        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_ucdmleg', [                    
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
    
    
}
