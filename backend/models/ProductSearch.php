<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Product;

/**
 * ProductsSearch represents the model behind the search form about `backend\models\Products`.
 */
class ProductSearch extends Product
{
    public $num_rows;

    public $input_search;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'company_id'], 'integer'],
            [['price'], 'number'],
            [['name', 'crdproduct', 'designref', 'currcode', 'amtload', 'imageid', 'limitsgroup', 'permsgroup',
                'feesgroup', 'carrierref', 'action','created_at', 'updated_at', 'input_search', 'num_rows'], 'safe'],
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
        $query = Product::find();
        $subQuery = Orders::find()
            ->select('invoice.product_id, count(*) as num_rows')
            ->join('LEFT JOIN', 'invoice', 'card.invoice_id = invoice.id')
            ->groupBy('invoice.product_id');
        $query->leftJoin(['num_rows' => $subQuery], 'product_id = id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => Yii::$app->session->get('rows') ],
            'sort'=> [
                'defaultOrder' => ['updated_at'=>SORT_DESC],
            ],
        ]);

        $dataProvider->sort->attributes['num_rows'] = [
            'asc' => ['num_rows' => SORT_ASC],
            'desc' => ['num_rows' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->orFilterWhere(['like', 'name', $this->input_search]);
        
        return $dataProvider;
    }
}