<?php

use App\Http\Controllers\BotFunctions\TextMenuSelection;
use App\Models\Region;

trait GeneralAbilities
{


    // this will house methods that will be general in any project

    public function listRegion($message)
    {
        $region_model = new Region();
        $regions = $region_model->get();
        $txt_menu = new TextMenuSelection($regions->region);
       
        $txt_menu->send_menu_to_user($message);

    }
}
