<?php


namespace app\App;

use Dejurin\GoogleTranslateForFree;

class Response
{
    public Methods $methods;
    public string $lang = 'en';

    public function __construct()
    {
        $this->methods = new Methods();
    }

    public function translate($message)
    {
        if(preg_match('#[a-z]+#i', $message)){
            $source = 'en';
            $target = 'ru';
        }else{
            $source = 'ru';
            $target = 'en';
        }
        $attempts = 5;
        $tr = new GoogleTranslateForFree();
        return ['text' => $tr->translate($source, $target, $message, $attempts), 'target' => $target];
    }

    public function getResponse($message, $chatId, $message_id, $document)
    {
        if($message === '/start'){
            $this->methods->sendMessage($chatId, 'Привет! Я бот-переводчик и я помогу вам перевести с английского на русский или обратно. Просто отправьте текст или файл.');
        }

        elseif(!empty($document)){
            $result = ' ';
            $handle = fopen(__DIR__ . '/user_files/' .$document, "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $tmp = $this->translate($line);
                    $result .= $tmp['text'];
                }
                file_put_contents(__DIR__ . '/bot_files/' . $document, print_r($result, 1), FILE_APPEND);
                fclose($handle);
                $resp = $this->methods->sendDocument($chatId, __DIR__ . '/bot_files/' . $document);
                if($resp['ok']){
                    unlink(__DIR__ . '/bot_files/' . $document);
                    unlink(__DIR__ . '/user_files/' . $document);
                }
            }
        }

        elseif(!empty($message)){
            $result = $this->translate($message);

            if($result['text'] && $result['target'] === 'ru'){
                $this->methods->sendMessage($chatId, 'Русский: '.$result['text']);
            }elseif($result['text'] && $result['target'] === 'en'){
                $this->methods->sendMessage($chatId, 'English: '.$result['text']);
            }else{
                $this->methods->sendMessage($chatId, 'Не удалось перевести... Попробуйте еще раз.');
            }
        }

        else{
            $this->methods->sendMessage($chatId, "Это бот-переводчик, он ожидает от вас текст или текстовый файл для перевода");
        }
    }

}
