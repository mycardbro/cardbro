<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Products;

/**
 * ProductsSearch represents the model behind the search form about `backend\models\Products`.
 */
class ProductsSearch extends Products
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'ORDERS_ACCESS_LEVEL'], 'integer'],
            [['PRICE'], 'number'],
            [['NAME', 'GPS_CRDPRODUCT', 'GPS_DESIGNREF', 'GPS_CURRCODE', 'GPS_AMTLOAD', 'GPS_IMAGEID', 'GPS_LIMITSGROUP', 'GPS_PERMSGROUP', 'GPS_FEESGROUP', 'GPS_CARRIERREF', 'GPS_ACTION', 'PARTNER_NAME', 'PARTNER_COMPANY', 'PARTNER_ADDRL1', 'PARTNER_ADDRL2', 'PARTNER_ADDRL3', 'PARTNER_VAT', 'URL_CARDIMAGE', 'URL_TOS', 'STOCK_ID'], 'safe'],
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
        $query = Products::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['NAME'] = 'asc';

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ID' => $this->ID,
            'PRICE' => $this->PRICE,
            'ORDERS_ACCESS_LEVEL' => $this->ORDERS_ACCESS_LEVEL,
        ]);

        $query->andFilterWhere(['like', 'NAME', $this->NAME])
            ->andFilterWhere(['like', 'GPS_CRDPRODUCT', $this->GPS_CRDPRODUCT])
            ->andFilterWhere(['like', 'GPS_DESIGNREF', $this->GPS_DESIGNREF])
            ->andFilterWhere(['like', 'GPS_CURRCODE', $this->GPS_CURRCODE])
            ->andFilterWhere(['like', 'GPS_AMTLOAD', $this->GPS_AMTLOAD])
            ->andFilterWhere(['like', 'GPS_IMAGEID', $this->GPS_IMAGEID])
            ->andFilterWhere(['like', 'GPS_LIMITSGROUP', $this->GPS_LIMITSGROUP])
            ->andFilterWhere(['like', 'GPS_PERMSGROUP', $this->GPS_PERMSGROUP])
            ->andFilterWhere(['like', 'GPS_FEESGROUP', $this->GPS_FEESGROUP])
            ->andFilterWhere(['like', 'GPS_CARRIERREF', $this->GPS_CARRIERREF])
            ->andFilterWhere(['like', 'GPS_ACTION', $this->GPS_ACTION])
            ->andFilterWhere(['like', 'PARTNER_NAME', $this->PARTNER_NAME])
            ->andFilterWhere(['like', 'PARTNER_COMPANY', $this->PARTNER_COMPANY])
            ->andFilterWhere(['like', 'PARTNER_ADDRL1', $this->PARTNER_ADDRL1])
            ->andFilterWhere(['like', 'PARTNER_ADDRL2', $this->PARTNER_ADDRL2])
            ->andFilterWhere(['like', 'PARTNER_ADDRL3', $this->PARTNER_ADDRL3])
            ->andFilterWhere(['like', 'PARTNER_VAT', $this->PARTNER_VAT])
            ->andFilterWhere(['like', 'URL_CARDIMAGE', $this->URL_CARDIMAGE])
            ->andFilterWhere(['like', 'URL_TOS', $this->URL_TOS])
            ->andFilterWhere(['like', 'STOCK_ID', $this->STOCK_ID]);

        return $dataProvider;
    }
}
