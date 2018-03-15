<?php
namespace backend\components;

use yii\base\Component;

class Validation extends Component
{
    public function csv($data, $rules, $tips) {
        $validationErrors = '';
        $numRow = 1;

        foreach ($data as $record) {
            foreach ($rules as $column => $pattern) {
                if (!$pattern) continue;
                
                if (!isset($record[$column]) or $record[$column] == '') {
                    $validationErrors .= 'Error: Row ' . $numRow . '. Column `' . $column. "` cannot be empty<br>";
                    continue;
                }
                $res = preg_match($pattern, strtolower($record[$column]));
                if (!$res) $validationErrors .= 'Error: Row ' . $numRow . '. Column `' . $column . '`. ' . $tips[$column] . "<br>";
            }
            $numRow++;
        }
        
        return $validationErrors;
    }
}