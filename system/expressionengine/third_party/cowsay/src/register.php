<?php

if (php_sapi_name() !== 'cli') {
    exit;
}

eecli\Application::registerGlobalCommand('\\eecli\\Cowsay\\CowsayCommand');
