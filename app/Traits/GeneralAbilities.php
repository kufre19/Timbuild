<?php
namespace App\Traits;

use App\Http\Controllers\BotFunctions\TextMenuSelection;
use App\Models\Region;

trait GeneralAbilities
{


    // this will house methods that will be general in any project

    public function listRegion($message)
    {
        $regions_Arr = [];
        $region_model = new Region();
        $regions = $region_model->select("region")->get();
        foreach ($regions as $region => $value) {
            array_push($regions_Arr,$value['region']);
        }
        $region_obj = $this->MenuArrayToObj($regions_Arr);
        $txt_menu = new TextMenuSelection($region_obj);
       
        $txt_menu->send_menu_to_user($message);

    }
}
