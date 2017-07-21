<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 02.06.2017
 * Time: 11:25
 */
declare(strict_types = 1);

namespace BackOffice;

use Containers\ServiceContainer;

class Bonus
{
    use ServiceContainer;

    /**
     * @return array
     */
    public function getWagerCampaignDetails(): array
    {
        $wageredCampaign = [];
        $query = $this->container->get('Db')->getDb(false)->prepare("SELECT c.id, c.bonus_amount, c.wagering_multiplier, DATE_ADD(NOW(), interval + wagering_days day) as wagering_expiry_days, c.wagering_milestone, c.wagering_weekdays, c.bonus_max_amount
                FROM campaigns c 
                WHERE c.start_date <= NOW() 
                AND c.end_date >= NOW() 
                AND c.type = 3 
                AND c.status = 2 LIMIT 1");//fetching one wagering campaign because there can be only one active at the moment
        if ($query->execute() && $query->rowCount() > 0) {
            $wageredCampaign = $query->fetch(\PDO::FETCH_ASSOC);
        } else {
            $this->container->get('Logger')->log('error', true, "Query failed! " . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__);
            return $wageredCampaign;
        }
        return $wageredCampaign;//type 1 = fun campaign, type 2 = real campaign and type 3 = wagering campaign
    }

    /**
     * @param $campaignId
     * @return mixed
     * @throws \SoapFault
     */
    public function getCampaignMaxWin($campaignId): int
    {
        $campaign['threshold_amount'] = 0;
        $query = $this->container->get('Db')->getDb(false)->prepare("SELECT c.threshold_amount FROM campaigns c WHERE id = :campaignId");
        if ($query->execute([
                ':campaignId' => $campaignId
            ]) && $query->rowCount() > 0) {
            $campaign = $query->fetch(\PDO::FETCH_ASSOC);
        } else {
            $this->container->get('Logger')->log('error', true, "Query failed! " . 'PATH: ' . __FILE__ . ' LINE: ' . __LINE__ . ' METHOD: ' . __METHOD__);
            return $campaign['threshold_amount'];
        }
        return (int)$campaign['threshold_amount'];
    }

}