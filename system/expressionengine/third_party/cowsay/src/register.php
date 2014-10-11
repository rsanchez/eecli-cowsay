<?php

if (php_sapi_name() === 'cli') {
    eecli\Application::registerGlobalCommand('\\eecli\\Cowsay\\CowsayCommand');
}
