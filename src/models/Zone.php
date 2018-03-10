<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "zone".
 *
 * @property int $id
 * @property string $zoneName
 * @property int $version
 *
 * @property Domain[] $domains
 */
class Zone extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zone';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['zoneName'], 'required'],
            [['version'], 'integer'],
            [['zoneName'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'zoneName' => 'Zone Name',
            'version' => 'Version',
        ];
    }

    public function relations()
    {
        return [
            'domains' => [self::HAS_MANY, 'Domain'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDomains()
    {
        return $this->hasMany(Domain::className(), ['zone_id' => 'id']);
    }
}
