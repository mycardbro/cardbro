<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class InvoiceSearch extends Invoice
{
    public $input_search;
    public $product;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'user_id'], 'number'],
            [['bill_amount', 'paid_amount'], 'number'],
            [['created_at', 'updated_at', 'input_search', 'product', 'id'], 'safe'],
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
        $query = Invoice::find()->where(['>', 'bill_amount', 0]);

        $query->joinWith(['product']);

        if (Yii::$app->user->can('partner') && !Yii::$app->user->can('admin')){
            $query->where('user_id = ' . Yii::$app->user->id);
        }
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => Yii::$app->session->get('rows') ],
            'sort'=> [
                'defaultOrder' => ['updated_at'=>SORT_DESC],
            ],
        ]);

        $dataProvider->sort->attributes['product'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['product.name' => SORT_ASC],
            'desc' => ['product.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        /*$query->andFilterWhere([
            'id' => $this->id,
        ]);*/
        // grid filtering conditions
        /*$query->andFilterWhere([
            'bill_amount' => $this->bill_amount,
            'paid_amount' => $this->paid_amount,
        ]);

        $query->orFilterWhere(['like', 'invoice.id', $this->input_search]);*/
        $query->andFilterWhere(['or',
            ['like', 'product.name', $this->input_search],
            ['like', 'invoice.id', $this->input_search]
        ]);
        /*$query->andFilterWhere(['like', 'product.name', $this->input_search]);
        $query->orFilterWhere(['like', 'invoice.id', $this->input_search]);*/
        

        return $dataProvider;
    }
}