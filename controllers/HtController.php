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

class HtController extends Controller {

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
                'only'=> ['indivpreht','indivhtscreen','indivhtlipid','indivhtkedney','indivht2control',
                           'indivhtperyearnor','indivuchtipd','indivhtperyear'
                    ],
                'ruleConfig'=>[
                    'class'=>AccessRule::className()
                ],
                'rules'=>[
                    [
                        'actions'=>['indivpreht','indivhtscreen','indivhtlipid','indivhtkedney','indivht2control',
                           'indivhtperyearnor','indivuchtipd','indivhtperyear'
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
    
    public function actionIndexpreht(){
        return $this->render('indexpreht');        
    }
    public function actionIndexhtscreen(){
        return $this->render('indexhtscreen');        
    }
    public function actionIndexhtlipid(){
        return $this->render('indexhtlipid');        
    }
    public function actionIndexhtkedney(){
        return $this->render('indexhtkedney');        
    }
    public function actionIndexht2control(){
        return $this->render('indexht2control');        
    }
    public function actionIndexhtperyear(){
        return $this->render('indexhtperyear');        
    }
    public function actionIndexhtperyearnor(){
        return $this->render('indexhtperyearnor');        
    }
    public function actionIndexuchtipd(){
        return $this->render('indexuchtipd');        
    }

    public function actionPreht() {
        
        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
            
            
SELECT h.hoscode hospcode
	,h.hosname hospname
	,a.target
	,a.result
	,'0502' qof_code
FROM chospital_amp h
LEFT JOIN (
	SELECT p.hospcode
		,COUNT(DISTINCT IF(p.HT_target='NOHT',p.pid,NULL)) target
		,COUNT(DISTINCT IF(p.HT_work='NOHT',p.pid,NULL)) result
	FROM (
		SELECT p.hospcode, p.pid, p.cid ,p.hn
			,CONCAT(p.name,' ',p.lname) ptname
			,TIMESTAMPDIFF(YEAR,p.birth,'2015-10-01') age
			,p.typearea, p.nation
			,p.discharge, n.date_serv
			,n.bslevel, c.date_diag
			,IF(c.pid IS NOT NULL, 'NOHT', NULL) HT_work
			,IF(cc.pid IS NULL, 'NOHT', NULL) HT_target
		FROM person p
		LEFT JOIN ncdscreen n ON n.pid=p.pid AND n.hospcode=p.hospcode
		LEFT JOIN chronic c ON c.pid=p.pid AND c.hospcode=p.hospcode 
								AND c.chronic BETWEEN 'I10' AND 'I1599' 
								AND c.date_diag BETWEEN '2015-10-01' AND '2016-09-30'

		LEFT JOIN chronic cc ON cc.pid=p.pid AND cc.hospcode=p.hospcode 
								AND cc.chronic BETWEEN 'I10' AND 'I1599' 
								AND cc.date_diag < n.DATE_SERV

		WHERE TIMESTAMPDIFF(YEAR,p.birth,'2015-10-01')>=35
				AND p.typearea IN (1,3)
				AND p.nation='099'
				AND p.discharge='9'
				AND n.date_serv BETWEEN '2015-10-01' AND '2016-09-30'
				AND IF(n.sbp_2 > 0,n.sbp_2,n.sbp_1) BETWEEN 130 AND 139
				AND IF(n.dbp_2 > 0,n.dbp_2,n.dbp_1) BETWEEN 80 AND 89
	) p
	GROUP BY p.hospcode
) a ON a.hospcode=h.hoscode;

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
        return $this->render('preht', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivpreht($hospcode = null) {

        $sql = "
            select p.hospcode,p.pid,p.cid,p.hn,concat(p.name,' ',p.lname) ptname
        ,timestampdiff(year,p.birth,'2015-10-01') age,p.typearea,p.nation,p.discharge 
        ,n.date_serv,n.bslevel,cc.date_diag,if(cc.date_diag is not null,'Y',null) OK
        from person p
        left join ncdscreen n on n.pid=p.pid and n.hospcode=p.hospcode
        left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode and c.chronic between 'I10' and 'I1599' and c.date_diag<'2015-10-01'
        left join chronic cc on cc.pid=p.pid and cc.hospcode=p.hospcode and cc.chronic between 'I10' and 'I1599'
        where timestampdiff(year,p.birth,'2015-10-01')>=35
        and p.typearea in (1,3)
        and p.nation=099
        and p.discharge=9
        and n.date_serv between '2015-10-01' AND '2016-09-30' 
        and n.sbp_2 between 130 and 139
        and n.dbp_2 between 80 and 89
        and c.pid is null
        and p.hospcode='$hospcode'
        group by p.hospcode,p.pid;
            
                ";
        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_preht', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
    
    public function actionHtscreen() {
        
        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
            
SELECT a.hospcode
	,a.hospname 
	,b.target
	,a.result
	,'0402' qof_code
FROM (
	SELECT h.hoscode hospcode
		,h.hosname hospname
		,a.result
	FROM chospital_amp h
	INNER JOIN (
		SELECT p.hospcode
			,COUNT(DISTINCT IF(p.HT_result = 'NOHT',p.pid,NULL)) result

		FROM (
			SELECT p.hospcode
				,p.pid
				,IF(c.pid IS NULL, 'NOHT', NULL) HT_result
			FROM person p
			LEFT JOIN ncdscreen a ON a.pid=p.pid AND a.hospcode=p.hospcode 
			LEFT JOIN chronic c ON c.pid=a.pid AND c.hospcode=a.hospcode 
									AND c.chronic BETWEEN 'I10' AND 'I1599' 
									AND c.date_diag<='2015-10-01'
			WHERE TIMESTAMPDIFF(YEAR,p.birth,'2015-10-01')>=35
				AND p.typearea IN (1,3) AND p.NATION = '099'
				AND c.pid IS NULL
				AND a.height IS NOT NULL AND a.height > 0
				AND a.weight IS NOT NULL AND a.weight > 0
				AND a.sbp_1 IS NOT NULL AND a.sbp_1 > 0
				AND a.dbp_1 IS NOT NULL AND a.dbp_1 > 0
		) p
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
			,COUNT(DISTINCT IF(p.HT_target = 'NOHT',p.pid,NULL)) target
		FROM (
			SELECT p.hospcode
				,p.pid
				,IF(c.pid IS NULL, 'NOHT', NULL) HT_target
			FROM person p
			LEFT JOIN chronic c ON c.pid=p.pid AND c.hospcode=p.hospcode 
									AND c.chronic BETWEEN 'I10' AND 'I1599' 
									AND c.date_diag<='2015-10-01'
			WHERE TIMESTAMPDIFF(YEAR,p.birth,'2015-10-01')>=35
				AND p.typearea IN (1,3) AND p.NATION = '099'
				AND c.pid IS NULL
		) p
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
        return $this->render('htscreen', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivhtscreen($hospcode = null) {

        $sql = "
            select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
            ,cast(group_concat(distinct a.date_serv order by a.date_serv desc) as char(10)) date_serv
            ,if(a.weight>0 and a.height>0,'Y',null) OK
            from person p
            left join ncdscreen a on a.pid=p.pid and a.hospcode=p.hospcode 
            and a.date_serv between '2015-10-01' and '2016-09-30'
            left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode and c.chronic between 'I10' and 'I1599' 
            and c.date_diag<'2015-10-01'
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

        return $this->render('indiv_htscreen', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
    
    public function actionHtlipid() {
        
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
where c.chronic between 'I10' and 'I1599'
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
        return $this->render('htlipid', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivhtlipid($hospcode = null) {

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
where c.chronic between 'I10' and 'I1599'
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

        return $this->render('indiv_htlipid', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
    
    public function actionHtkedney() {
        
        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
           
select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct if(c.chronic between 'I10' and 'I1599','HT','DM')) clinic 
,group_concat(distinct if(dx.diagcode regexp 'I1(0|1|2|3|4|5)2',dx.diagcode,null) order by dx.diagcode) dx
,group_concat(distinct if(dx.diagcode regexp 'N18',dx.diagcode,null) order by dx.diagcode) cc

,if(group_concat(distinct if(dx.diagcode regexp 'I1(0|1|2|3|4|5)2','Y',null))='Y'
or group_concat(distinct if(dx.diagcode regexp 'N18','Y',null))='Y','Y',null) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode 
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_opd dx on dx.pid=p.pid and dx.hospcode=p.hospcode and dx.date_serv between '2015-10-01' and '2016-09-30'
where (c.chronic between 'E10' and 'E1499' or c.chronic between 'I10' and 'I1599')
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
group by p.hospcode,p.pid
having clinic='HT'
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
        return $this->render('htkedney', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivhtkedney($hospcode = null) {

        $sql = "            
            select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct if(c.chronic between 'I10' and 'I1599','HT','DM')) clinic 
,group_concat(distinct if(dx.diagcode regexp 'I1(0|1|2|3|4|5)2',dx.diagcode,null) order by dx.diagcode) dx
,group_concat(distinct if(dx.diagcode regexp 'N18',dx.diagcode,null) order by dx.diagcode) cc

,if(group_concat(distinct if(dx.diagcode regexp 'I1(0|1|2|3|4|5)2','Y',null))='Y'
or group_concat(distinct if(dx.diagcode regexp 'N18','Y',null))='Y','Y',null) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode 
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_opd dx on dx.pid=p.pid and dx.hospcode=p.hospcode and dx.date_serv between '2015-10-01' and '2016-09-30'
where (c.chronic between 'E10' and 'E1499' or c.chronic between 'I10' and 'I1599')
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
AND p.hospcode='$hospcode'
group by p.hospcode,p.pid
having clinic='HT'
order by result desc
            
                ";
        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_htkedney', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
    
    public function actionHt2control() {
        
        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
            select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct dx.diagcode order by dx.diagcode) dx
,getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),1) sbp1
,getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),2) sbp2
,getwordnum(group_concat(if(s.dbp>0,s.dbp,null) order by s.seq),1) dbp1
,getwordnum(group_concat(if(s.dbp>0,s.dbp,null) order by s.seq),2) dbp2
,if(getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),1)>0
and getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),1)<140
and getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),2)>0
and getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),2)<140
and getwordnum(group_concat(if(s.dbp>0,s.dbp,null) order by s.seq),1)<90
and getwordnum(group_concat(if(s.dbp>0,s.dbp,null) order by s.seq),2)<90,'Y',null) result

from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode and (c.chronic between 'E10' and 'E1499' or c.chronic between 'I10' and 'I1599')
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_opd dx on dx.pid=p.pid and dx.hospcode=p.hospcode and dx.date_serv between '2015-10-01' and '2016-09-30'
left join service s on s.seq=dx.seq and s.hospcode=dx.hospcode
where c.pid is null
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and dx.diagcode between 'I10' and 'I1599'
and dx.diagtype=1
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
        return $this->render('ht2control', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivht2control($hospcode = null) { ///เก่า

        $sql = "            
                
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct dx.diagcode order by dx.diagcode) dx
,getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),1) sbp1
,getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),2) sbp2
,getwordnum(group_concat(if(s.dbp>0,s.dbp,null) order by s.seq),1) dbp1
,getwordnum(group_concat(if(s.dbp>0,s.dbp,null) order by s.seq),2) dbp2
,if(getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),1)>0
and getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),1)<140
and getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),2)>0
and getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),2)<140
and getwordnum(group_concat(if(s.dbp>0,s.dbp,null) order by s.seq),1)<90
and getwordnum(group_concat(if(s.dbp>0,s.dbp,null) order by s.seq),2)<90,'Y',null) result

from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode and (c.chronic between 'E10' and 'E1499' or c.chronic between 'I10' and 'I1599')
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_opd dx on dx.pid=p.pid and dx.hospcode=p.hospcode and dx.date_serv between '2015-10-01' and '2016-09-30'
left join service s on s.seq=dx.seq and s.hospcode=dx.hospcode
where c.pid is null
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and dx.diagcode between 'I10' and 'I1599'
and dx.diagtype=1
and p.hospcode='$hospcode'
group by p.hospcode,p.pid
order by result desc

                ";
        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_ht2control', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
//    public function actionIndivht2control($hospcode = null) { ///ใหม่
//
//        $sql = "    
//
//select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
//,group_concat(distinct dx.diagcode order by dx.diagcode) dx
//,getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),1) sbp1
//,getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),2) sbp2
//,getwordnum(group_concat(if(s.dbp>0,s.dbp,null) order by s.seq),1) dbp1
//,getwordnum(group_concat(if(s.dbp>0,s.dbp,null) order by s.seq),2) dbp2
//,if(getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),1)>0
//and getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),1)<140
//and getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),2)>0
//and getwordnum(group_concat(if(s.sbp>0,s.sbp,null) order by s.seq),2)<140
//and getwordnum(group_concat(if(s.dbp>0,s.dbp,null) order by s.seq),1)<90
//and getwordnum(group_concat(if(s.dbp>0,s.dbp,null) order by s.seq),2)<90,'Y',null) result
//
//from person p
//left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode and (c.chronic between 'E10' and 'E1499' or c.chronic between 'I10' and 'I1599')
//left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
//left join diagnosis_opd dx on dx.pid=p.pid and dx.hospcode=p.hospcode and dx.date_serv between '2015-10-01' and '2016-09-30'
//left join service s on s.seq=dx.seq and s.hospcode=dx.hospcode
//where c.pid is null
//and cc.instype_new=0100
//and p.typearea in (1,3)
//and p.nation=099
//and p.discharge=9
//and dx.diagcode between 'I10' and 'I1599'
//and dx.diagtype=1
//AND p.hospcode='$hospcode'
//group by p.hospcode,p.pid
//order by result desc
//
//                ";
//        try {
//            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
//        } catch (\yii\db\Exception $e) {
//            throw new \yii\web\ConflictHttpException('sql error');
//        }
//
//        return $this->render('indiv_ht2control', [
//                    'rawData' => $rawData,
//                    'sql' => $sql,                        
//        ]);
//    }
    public function actionHtperyear() {
        
        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
            
select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct if(c.chronic between 'I10' and 'I1599','HT','DM')) clinic
,group_concat(if(s.labresult is not null,concat('LabTest',s.labtest,'#',s.labresult),null)) LabResult
,group_concat(distinct if(s.labresult is not null,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode and s.labtest in (01,03) and s.date_serv between '2015-10-01' and '2016-09-30'
where (c.chronic between 'I10' and 'I1599' or c.chronic between 'E10' and 'E1499')
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
group by p.hospcode,p.pid
having clinic='HT'
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
        return $this->render('htperyear', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivhtperyear($hospcode = null) { 

        $sql = "            
            
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct if(c.chronic between 'I10' and 'I1599','HT','DM')) clinic
,group_concat(if(s.labresult is not null,concat('LabTest',s.labtest,'#',s.labresult),null)) LabResult
,group_concat(distinct if(s.labresult is not null,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode and s.labtest in (01,03) and s.date_serv between '2015-10-01' and '2016-09-30'
where (c.chronic between 'I10' and 'I1599' or c.chronic between 'E10' and 'E1499')
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
AND p.hospcode='$hospcode'
group by p.hospcode,p.pid
having clinic='HT'
order by result desc

                ";
        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_htperyear', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
    
    public function actionHtperyearnor() {
        
        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
            

select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct if(c.chronic between 'I10' and 'I1599','HT','DM')) clinic
,group_concat(if(s.labresult is not null,s.labresult,null)) LabResult
,group_concat(distinct if(s.labresult is not null,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode and s.labtest=11 and s.date_serv between '2015-10-01' and '2016-09-30'
where (c.chronic between 'I10' and 'I1599' or c.chronic between 'E10' and 'E1499')
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
group by p.hospcode,p.pid
having clinic='HT'
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
        return $this->render('htperyearnor', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivhtperyearnor($hospcode = null) { 

        $sql = "            
            

select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct if(c.chronic between 'I10' and 'I1599','HT','DM')) clinic
,group_concat(if(s.labresult is not null,s.labresult,null)) LabResult
,group_concat(distinct if(s.labresult is not null,'Y',null)) result
from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join labfu s on s.pid=p.pid and s.hospcode=p.hospcode and s.labtest=11 and s.date_serv between '2015-10-01' and '2016-09-30'
where (c.chronic between 'I10' and 'I1599' or c.chronic between 'E10' and 'E1499')
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and p.hospcode='$hospcode'
group by p.hospcode,p.pid
having clinic='HT'
order by result desc


                ";
        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }

        return $this->render('indiv_htperyearnor', [
                    'rawData' => $rawData,
                    'sql' => $sql,                        
        ]);
    }
    
    public function actionUchtipd() {

        $connection = Yii::$app->db2;
        $data = $connection->createCommand(" 
           
select h.hoscode hospcode,cast(h.hosname as char(200)) hospname,count(*) target,sum(result='Y') result,sum(result='Y')/count(*)*100 percent
from chospital_amp h
left join (
select a.*
,if(pdx1 is not null
or (pdx2 is not null and sdx2 is not null)
or (pdx3 is not null and sdx3 is not null),'Y',null) result
from (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct case 
when a.diagcode regexp 'I1(0|1|2|3|4|5)|I674' then '1'
when a.diagcode regexp 'I60|I61|I62' then '2'
when a.diagcode regexp 'H350' then '3' end) Pdx
,group_concat(distinct case 
when b.diagcode regexp 'I1(0|1|2|3|4|5)|I674' and a.diagcode not between 'S00' and 'T9999' then '2'
when b.diagcode regexp 'I1(0|1|2|3|4|5)|I674' then '3' end) Sdx

,group_concat(distinct if(a.diagcode regexp 'I1(0|1|2|3|4|5)|I674',a.diagcode,null)) Pdx1
,group_concat(distinct if(a.diagcode regexp 'I60|I61|I62',a.diagcode,null)) Pdx2
,group_concat(distinct if(a.diagcode regexp 'H350',a.diagcode,null)) Pdx3

,null Sdx1
,group_concat(distinct if(b.diagcode regexp 'I1(0|1|2|3|4|5)|I674' and a.diagcode not between 'S00' and 'T9999',b.diagcode,null)) Sdx2
,group_concat(distinct if(b.diagcode regexp 'I1(0|1|2|3|4|5)|I674',b.diagcode,null)) Sdx3

from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_ipd a on a.pid=p.pid and a.hospcode=p.hospcode and date(a.datetime_admit) between '2015-10-01' and '2016-09-30' and a.diagtype=1
left join diagnosis_ipd b on b.pid=p.pid and b.hospcode=p.hospcode and date(b.datetime_admit) between '2015-10-01' and '2016-09-30' and a.diagtype<>1
where c.chronic between 'I10' and 'I1599'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
group by p.hospcode,p.pid) a
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
        return $this->render('uchtipd', [
                    'dataProvider' => $dataProvider,
                    'hospcode' => $hospcode,
                    'target' => $target,
                    'result' => $result,
                    'hospname' => $hospname,
        ]);
    }
    
    public function actionIndivuchtipd($hospcode = null) {

        $sql = "        
          select a.*
,if(pdx1 is not null
or (pdx2 is not null and sdx2 is not null)
or (pdx3 is not null and sdx3 is not null),'Y',null) result
from (
select p.hospcode,p.cid,p.hn,p.pid,concat(p.name,' ',p.lname) ptname
,group_concat(distinct case 
when a.diagcode regexp 'I1(0|1|2|3|4|5)|I674' then '1'
when a.diagcode regexp 'I60|I61|I62' then '2'
when a.diagcode regexp 'H350' then '3' end) Pdx
,group_concat(distinct case 
when b.diagcode regexp 'I1(0|1|2|3|4|5)|I674' and a.diagcode not between 'S00' and 'T9999' then '2'
when b.diagcode regexp 'I1(0|1|2|3|4|5)|I674' then '3' end) Sdx

,group_concat(distinct if(a.diagcode regexp 'I1(0|1|2|3|4|5)|I674',a.diagcode,null)) Pdx1
,group_concat(distinct if(a.diagcode regexp 'I60|I61|I62',a.diagcode,null)) Pdx2
,group_concat(distinct if(a.diagcode regexp 'H350',a.diagcode,null)) Pdx3

,null Sdx1
,group_concat(distinct if(b.diagcode regexp 'I1(0|1|2|3|4|5)|I674' and a.diagcode not between 'S00' and 'T9999',b.diagcode,null)) Sdx2
,group_concat(distinct if(b.diagcode regexp 'I1(0|1|2|3|4|5)|I674',b.diagcode,null)) Sdx3

from person p
left join chronic c on c.pid=p.pid and c.hospcode=p.hospcode
left join card cc on cc.pid=p.pid and cc.hospcode=p.hospcode
left join diagnosis_ipd a on a.pid=p.pid and a.hospcode=p.hospcode and date(a.datetime_admit) between '2015-10-01' and '2016-09-30' and a.diagtype=1
left join diagnosis_ipd b on b.pid=p.pid and b.hospcode=p.hospcode and date(b.datetime_admit) between '2015-10-01' and '2016-09-30' and a.diagtype<>1
where c.chronic between 'I10' and 'I1599'
and cc.instype_new=0100
and p.typearea in (1,3)
and p.nation=099
and p.discharge=9
and p.hospcode='$hospcode'
group by p.hospcode,p.pid) a
order by result desc

                ";

        try {
            $rawData = \Yii::$app->db2->createCommand($sql)->queryAll();
        } catch (\yii\db\Exception $e) {
            throw new \yii\web\ConflictHttpException('sql error');
        }
        return $this->render('indiv_uchtipd', [                    
                    'rawData' => $rawData,
                    'sql' => $sql,                     
        ]);
    }
}