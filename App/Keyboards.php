<?php

namespace app\App;

class Keyboards
{
    public static function lang_keyboard($lang)
    {
        return [
            [
                ['text' => $lang === 'en' ? '🔸 en 🔸' : 'en', 'callback_data' => 'en'],
                ['text' => $lang === 'ru' ? '🔸 ru 🔸' : 'ru', 'callback_data' => 'ru'],
            ]
        ];
    }
}





