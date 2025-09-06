<?php

use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\App;

if(!function_exists('translate')) {
    function  translate($key)
    {
        // Always use English - multi-language functionality removed
        App::setLocale('en');
        
        $lang_array = include(base_path('resources/lang/en/messages.php'));
        $processed_key = ucfirst(str_replace('_', ' ', Helpers::remove_invalid_charcaters($key)));

        if (!array_key_exists($key, $lang_array)) {
            $lang_array[$key] = $processed_key;
            $str = "<?php return " . var_export($lang_array, true) . ";";
            file_put_contents(base_path('resources/lang/en/messages.php'), $str);
            $result = $processed_key;
        } else {
            $result = __('messages.' . $key);
        }
        return $result;
    }
}
