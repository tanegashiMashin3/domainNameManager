<?php

namespace app\controllers;

use Yii;
use app\models\Domain;
use app\models\Zone;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DomainController implements the CRUD actions for Domain model.
 */
class DomainController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Domain models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Domain::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Domain model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Domain model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Domain();
        if (Yii::$app->request->isGet) {
            return $this->render('create', [
                'model' => $model,
            ]);
        }

        // FIXME 増えることはおそらくないので、固定zoneを指定
        $model->load(Yii::$app->request->post());
        $model->zone_id = 1;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save()) {
                $transaction->rollBack();
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            $this->saveWithDnsReload($model);
            $transaction->commit(); 
            return $this->redirect(['view', 'id' => $model->id]);

        } catch (Exception $e) {
            $transaction->rollback();
            // TODO エラーを追加する
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Domain model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {

                $this->saveWithDnsReload($model);
                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } catch (Exception $e) {
            $transaction->rollback();
            $model->addError('host', 'DB更新に失敗しました。');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 保存後、BINDに即時反映する
     */
    private function saveWithDnsReload(Domain $domain)
    {
        $domain->zone->updateZoneFile();

        return true;
    }

    /**
     * Deletes an existing Domain model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Domain model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Domain the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Domain::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
