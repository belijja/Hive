<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 02.06.2017
 * Time: 11:25
 */
declare(strict_types = 1);

namespace BackOffice;

use Helpers\ConfigHelpers\ConfigManager;
use Helpers\ConfigHelpers\Db;

class Bonus
{
    public function getWagerCampaignDetails()
    {
        $query = Db::getInstance(ConfigManager::getDb('database', false))->prepare("SELECT c.id, c.bonus_amount, c.wagering_multiplier, DATE_ADD(c.end_date, interval + wagering_days day) as wagering_expiry_days, c.wagering_milestone, c.wagering_weekdays, c.bonus_max_amount
                FROM campaigns c 
                WHERE c.start_date <= NOW() 
                AND c.end_date >= NOW() 
                AND c.type = 3 
                AND c.status = 2 LIMIT 1");//fetching one wagering campaign because there can be only one active at the moment
        if ($query->execute() && $query->rowCount() > 0) {
            $wageredCampaign = $query->fetch(\PDO::FETCH_ASSOC);
        } else {
            error_log("Query failed! " . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__);
            throw new \SoapFault('0', 'Unspecified error.');
        }
        return $wageredCampaign;//type 1 = fun campaign, type 2 = real campaign and type 3 = wagering campaign
    }

}