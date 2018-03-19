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
    // TODO 環境設定ファイルに書き出したい
    const ALLOWED_IP_RANGE = '192.168.0.0/24';

    const REGEX = '/^[a-zA-Z0-9][a-zA-Z0-9\-]{1,61}[A-Za-z0-9]$/';

    public static $allowedRecordType = [
        'A' => 'A',
        // 'MX' => 'MX',
        // 'CNAME' => 'CNAME',
        // 'SOA' => 'SOA',
    ];

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
            [['host'], 'ip', 'ranges' => [self::ALLOWED_IP_RANGE]],
            [['host'], 'hostValidator'],
            [['domainName'], 'match', 'pattern' => self::REGEX],
            [['updatedAt', 'createdAt'], 'safe'],
            [['zone_id'], 'integer'],
            [['recordType', 'domainName', 'host'], 'string', 'max' => 255],
            [['zone_id'], 'exist', 'skipOnError' => true, 'targetClass' => Zone::className(), 'targetAttribute' => ['zone_id' => 'id']],
        ];
    }

    public function relations()
    {
        return [
            'zone' => [self::BELONGS_TO, 'Zone', 'zone_id'],
        ];
    }

    public function hostValidator($attribute, $params)
    {
        $ip = explode('.', $this->$attribute);

        if ($ip[3] < 11) {
            $this->addError($attribute, 'This IP is not allowed.');
            return;
        }

        // 重複チェック
        $domain = Domain::find()->where([$attribute => $this->$attribute]);

        if (!$this->isNewRecord) {
            $domain->andWhere("id<>{$this->id}");
        }

        if ($domain->exists()) {
            $this->addError($attribute, 'This IP is already used.');
            return;
        }
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

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->createdAt = date('Y-m-d H:i:s');
        }
        $this->updatedAt = date('Y-m-d H:i:s');
        return true;
    }
}
