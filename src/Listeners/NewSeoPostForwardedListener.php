<?php

namespace Brucelwayne\SEO\Listeners;

use Brucelwayne\SEO\Events\NewSeoPostForwardedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewSeoPostForwardedListener implements ShouldQueue
{
    /*
     * 需要翻译的语言
     {
     "id": {
       "name": "Indonesian",
       "script": "Latn",
       "native": "Bahasa Indonesia",
       "regional": "id_ID"
     },
     "nb": {
       "name": "Norwegian Bokmål",
       "script": "Latn",
       "native": "Bokmål",
       "regional": "nb_NO"
     },
     "da": {
       "name": "Danish",
       "script": "Latn",
       "native": "dansk",
       "regional": "da_DK"
     },
     "de": {
       "name": "German",
       "script": "Latn",
       "native": "Deutsch",
       "regional": "de_DE"
     },
     "en": {
       "name": "English",
       "script": "Latn",
       "native": "English",
       "regional": "en_GB"
     },
     "es": {
       "name": "Spanish",
       "script": "Latn",
       "native": "español",
       "regional": "es_ES"
     },
     "fr": {
       "name": "French",
       "script": "Latn",
       "native": "français",
       "regional": "fr_FR"
     },
     "it": {
       "name": "Italian",
       "script": "Latn",
       "native": "italiano",
       "regional": "it_IT"
     },
     "pt": {
       "name": "Portuguese",
       "script": "Latn",
       "native": "português",
       "regional": "pt_PT"
     },
     "ru": {
       "name": "Russian",
       "script": "Cyrl",
       "native": "русский",
       "regional": "ru_RU"
     },
     "ar": {
       "name": "Arabic",
       "script": "Arab",
       "native": "العربية",
       "regional": "ar_AE"
     },
     "th": {
       "name": "Thai",
       "script": "Thai",
       "native": "ไทย",
       "regional": "th_TH"
     },
     "ko": {
       "name": "Korean",
       "script": "Hang",
       "native": "한국어",
       "regional": "ko_KR"
     },
     "ja": {
       "name": "Japanese",
       "script": "Jpan",
       "native": "日本語",
       "regional": "ja_JP"
     },
     "zh": {
       "name": "Chinese (Simplified)",
       "script": "Hans",
       "native": "简体中文",
       "regional": "zh_CN"
     },
     "zh-Hant": {
       "name": "Chinese (Traditional)",
       "script": "Hant",
       "native": "繁體中文",
       "regional": "zh_CN"
     }
    }
            */
    public function handle(NewSeoPostForwardedEvent $event)
    {

    }
}