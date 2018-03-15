<?php
namespace backend\models;

use yii\data\ActiveDataProvider;
use Yii;

class CompanySearch extends Company
{
    public $input_search;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','name','email','vat','vat_id','address','postal_code','country','city','region', 'input_search'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Company::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => Yii::$app->session->get('rows') ],
            'sort'=> [
                'defaultOrder' => ['updated_at'=>SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->orFilterWhere(['like', 'name', $this->input_search]);
        $query->orFilterWhere(['like', 'email', $this->input_search]);


        return $dataProvider;
    }
}