<?php
namespace CapacitySpace;

    class Capacity {
        public function getFromURL ($markValue, $bikeType) {
            $capacity = file_get_contents("https://www.louis.de/en/m/ajax/json/select-from-list?bike-selection-fieldset[manufacturer]=$markValue&bike-selection-fieldset[biketype]=$bikeType&bike-selection-fieldset[sortBySelect]=title&get=capacity");
            $capacity = json_decode($capacity, true);
            unset($capacity['options'][0], $capacity['options'][1]);
            return $capacity;
        }
    }