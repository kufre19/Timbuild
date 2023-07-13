<?php

namespace App\Http\Controllers\BotAbilities;

use App\Http\Controllers\BotFunctions\GeneralFunctions;
use App\Traits\GeneralAbilities;

class LocateStore extends GeneralFunctions implements AbilityInterface{
    use GeneralAbilities;

    public $steps = ["begin_func", "CheckRegion", ""];


    public function begin_func()
    {
        $this->set_session_route("LocateStore");
        $message = <<<MSG
        Let us help you! We currently only have stores located in 5 Provinces and growing.
        Please select your region to make your store selection easier. Your need only reply with the corresponding number:
        MSG;
        $this->listRegion($message);
        $this->go_to_next_step();
        $this->ResponsedWith200();

        
    }


    public function CheckRegion()
    {
        $this->send_post_curl($this->make_text_message($this->user_message_original,$this->userphone));
        $this->ResponsedWith200();
    }

    function call_method($key)
    {
        $method_name = $this->steps[$key];
        $this->$method_name();
    }
}