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

    const ZONE_TEMPLATE = '
$ttl 86400
@       IN      SOA     dns-hoge.com. root.dns-hoge.com. (
                        %s ;serial
                        600
                        3600
                        86499
                        38400 )
        IN      NS      dhs-hoge.com.
        IN      A       127.0.0.1
dns-hoge        IN      A       127.0.0.1
hoge    IN      CNAME   dns-hoge
%s';


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

    /**
     * @param int $id 更新対象のzoneID
     */
    public function updateZoneFile()
    {
        $template = '%s IN %s %s';

        $this->version = ++$this->version;
        $this->save();

        $serial = date('Ymd') . $this->version;

        $domainListString = '';
        foreach (Domain::find()->all() as $d) {
            $domainListString .= sprintf($template, $d->domainName, $d->recordType, $d->host) . PHP_EOL;
        }
        echo $domainListString;

        $zoneResource = sprintf(self::ZONE_TEMPLATE, $serial, $domainListString);

        $filePath = '/home/domainNameManager/src/runtime/hoge.zone';
        $backupPath = '/home/domainNameManager/src/runtime/hoge.zone.bk';

        try {
            if (file_exists($filePath)) {
                rename($filePath, $backupPath);
            }

            file_put_contents($filePath, $zoneResource);
            chmod($filePath, 0666);

            // 再起動コマンド
            system('rndc reload', $output);

        } catch (Exception $e) {
            rename($backupPath, $filePath);

            // 再起動コマンド
            system('rndc reload', $output);
        }

        if (file_exists($backupPath)) {
            unlink($backupPath);
        }
    }
}
