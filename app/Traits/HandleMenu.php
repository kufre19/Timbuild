<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait HandleMenu {
    use SendMessage;

    public function menu_index()
    {
       
        
    }



    public function determin_menu()
    {
        
    }

    public function get_command_and_value_menu()
    {
        $data = explode(":",$this->menu_item_id);
        return $data;
    }
    
}