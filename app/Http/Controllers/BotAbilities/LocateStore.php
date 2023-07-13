<?php

namespace App\Http\Controllers\BotAbilities;

use App\Http\Controllers\BotFunctions\GeneralFunctions;
use App\Traits\GeneralAbilities;

class LocateStore extends GeneralFunctions implements AbilityInterface
{
    use GeneralAbilities;

    public $steps = ["begin_func", "getStoreLocation", ""];
    public const USER_REGION = "user_region";


    public function begin_func()
    {
        $this->set_session_route("LocateStore");
        $message = <<<MSG
        Let us help you! We currently only have stores located in 5 Provinces and growing.
        Please select your region to make your store selection easier. Your need only reply with the corresponding number:
        MSG;
        $this->listRegion($message);

        $this->returnHomeMessage();

        $this->go_to_next_step();
        $this->ResponsedWith200();
    }


    public function getStoreLocation()
    {
        // $this->send_post_curl($this->make_text_message($this->user_message_original,$this->userphone));
        // $this->ResponsedWith200();

        // check first for selection to be correct
        $user_selected = $this->user_message_original;
        $this->CheckRegionSelected($user_selected);

      
        // second check for selection of others
        if ($user_selected == "6" || $this->user_message_lowered == "others") {
            $message = <<<MSG
            We’re sorry you’re located in a province we currently do not have any stores, but we’re positive one will be opening soon.
            MSG;
            $this->send_post_curl($this->make_text_message($message, $this->userphone));
            $this->returnHomeMessage();
            $this->ResponsedWith200();
        }

          // store collected region
          $this->storeAnswerToSession(self::USER_REGION);

          $store_menu = $this->listStoreInRegion($user_selected);
          $message = <<<MSG
          Fantastic. Here is a list of our stores located in your chosen province.
          MSG;
          $this->sendstoreMenu($store_menu,$message);
          $this->returnHomeMessage();
          $this->ResponsedWith200();



    }

    function call_method($key)
    {
        $method_name = $this->steps[$key];
        $this->$method_name();
    }
}
