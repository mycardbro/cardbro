<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Invoices;

/**
 * InvoicesSearch represents the model behind the search form about `backend\models\Invoices`.
 */
class InvoicesSearch extends Invoices
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID'], 'integer'],
            [['INVOICE_ID'], 'safe'],
            [['BILLAMOUNT', 'PAIDAMOUNT'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Invoices::find();

        // add conditions that should always apply here
        if (Yii::$app->user->can('partner') && !Yii::$app->user->can('admin')){
            $query->where('USER_ID = ' . Yii::$app->user->id);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 10 ],
            'sort'=> [
                'defaultOrder' => ['created_at'=>SORT_DESC],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ID' => $this->ID,
            'BILLAMOUNT' => $this->BILLAMOUNT,
            'PAIDAMOUNT' => $this->PAIDAMOUNT,
        ]);

        $query->andFilterWhere(['like', 'INVOICE_ID', $this->INVOICE_ID]);

        return $dataProvider;
    }
}
