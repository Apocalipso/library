<?php
namespace app\services;

use yii\httpclient\Client;
use Yii;
use yii\base\Component;

/**
 * Сервис для отправки SMS через smspilot.ru
 */
class SmsService extends Component
{
    public $apiKey;
    public $from = 'INFORM';
    public $apiUrl = 'https://smspilot.ru/api.php';

    public function send($phone, $message)
    {
        $client = new Client();
        
        try {
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl($this->apiUrl)
                ->setData([
                    'send' => $message,
                    'to' => $phone,
                    'from' => $this->from,
                    'apikey' => $this->apiKey,
                ])
                ->send();

            if ($response->isOk) {
                $data = $response->data;
                Yii::info("SMS отправлено на {$phone}: " . print_r($data, true), 'sms');
                return true;
            }
            
            Yii::error("Ошибка отправки SMS на {$phone}: " . $response->content, 'sms');
            return false;
        } catch (\Exception $e) {
            Yii::error("Исключение при отправке SMS на {$phone}: " . $e->getMessage(), 'sms');
            return false;
        }
    }

    public function sendNewBookNotification($phone, $authorName, $bookTitle)
    {
        $message = "Новая книга автора {$authorName}: {$bookTitle}";
        return $this->send($phone, $message);
    }
}

