<?php

class Mundipagg_Paymentmodule_Model_BankCodes
{
    public function toOptionArray() 
    {
        $url = "https://dashboard.mundipagg.com/emb/bank_info.json";
        $banks = json_decode(file_get_contents($url), true);
        $combo[0] = "Select";
        if (!empty($banks['banks'])) {
            foreach ($banks['banks'] as $key => $bank) {
                $combo[$key] =  $bank['name'];
            }
            return $combo;
        }
    }
}
