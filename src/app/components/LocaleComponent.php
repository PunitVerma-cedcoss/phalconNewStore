<?php

namespace App\Components;

use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;
use Phalcon\Di\Injectable;

class LocaleComponent extends injectable
{
    public function getTranslator($language): NativeArray
    {
        // $language = $this->request->getBestLanguage();
        $messages = [];
        $translationFile = '/languages/' . $language . '.php';
        if (!file_exists(APP_PATH . $translationFile)) {
            $translationFile = '/languages/en.php';
        }
        require APP_PATH . $translationFile;
        $interpolator = new InterpolatorFactory();
        $factory = new TranslateFactory($interpolator);

        return $factory->newInstance(
            'array',
            [
                'content' => $messages,
            ]
        );
    }
}
