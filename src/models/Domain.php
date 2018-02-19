<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "domain".
 *
 * @property int $id
 * @property string $recordType
 * @property string $domainName
 * @property string $host
 * @property string $updatedAt
 * @property string $createdAt
 * @property int $zone_id
 *
 * @property Zone $zone
 */
class Domain extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'domain';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['recordType', 'domainName', 'host', 'zone_id'], 'required'],
            [['updatedAt', 'createdAt'], 'safe'],
            [['zone_id'], 'integer'],
            [['recordType', 'domainName', 'host'], 'string', 'max' => 255],
            [['zone_id'], 'exist', 'skipOnError' => true, 'targetClass' => Zone::className(), 'targetAttribute' => ['zone_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'recordType' => 'Record Type',
            'domainName' => 'Domain Name',
            'host' => 'Host',
            'updatedAt' => 'Updated At',
            'createdAt' => 'Created At',
            'zone_id' => 'Zone ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZone()
    {
        return $this->hasOne(Zone::className(), ['id' => 'zone_id']);
    }
}
