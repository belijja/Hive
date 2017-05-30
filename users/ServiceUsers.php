<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 16.05.2017
 * Time: 10:44
 */
declare(strict_types = 1);

namespace Users;

use Helpers\ConfigHelpers\ConfigManager;
use Helpers\ConfigHelpers\Db;

/**
 * Class ServiceUsers
 * @package Users
 */
class ServiceUsers
{
    /**
     * @param array $params
     * @return array
     * @throws \SoapFault
     */
    public function getUserData(array $params): array
    {
        list($providerId, $skinId, $userId) = $params;
        $query = Db::getInstance(ConfigManager::getDb('database', true))->prepare("SELECT u.userid, u.username, u.skinid, u.firstname, u.lastname, u.email, u.state, 
                                                          GREATEST(f.level, f.retained_level) AS level, 
                                                          f.amount AS fpp_amount, 
                                                          r.amount AS r_amount, 
                                                          cid.name AS currency, 
                                                          c.provider_id, c.skin_id AS cskinid, c.casino_id, c.extern_username, 
                                                          s.flags AS skin_flags, u.rights 
                                                          FROM fpp f JOIN casino_ids c ON f.uid = c.user_id 
                                                          JOIN realmoney r ON f.uid = r.uid 
                                                          JOIN users u ON u.userid = f.uid 
                                                          JOIN currency_ids cid ON u.curid = cid.id 
                                                          LEFT JOIN skin s ON u.skinid = s.skinid 
                                                          WHERE c.provider_id = :providerId 
                                                          AND c.skin_id = :skinId 
                                                          AND c.casino_id = :userId");
        if (!$query->execute([
                ':providerId' => $providerId,
                ':skinId'     => $skinId,
                ':userId'     => $userId
            ]) || $query->rowCount() != 1
        ) {
            throw new \SoapFault('-3', 'Query failed.');
        } else {
            $returnData = $query->fetch(\PDO::FETCH_ASSOC);
            if ($returnData['rights'] & 0x08000000) {
                throw new \SoapFault('-4', 'Access denied for user id ' . $returnData['casino_id']);
            }
        }
        return $returnData;
    }

    /**
     * @param int $providerId
     * @param int $skinId
     * @return array
     */
    public function getPokerSkinId(int $providerId, int $skinId): array
    {
        $returnData = [];
        $query = Db::getInstance(ConfigManager::getDb('database', true))->prepare("SELECT poker_skinid 
                                                          FROM provider_skin_mapping 
                                                          WHERE provider_id = :providerId 
                                                          AND provider_skinid = :skinId");
        if (!$query->execute([
                ':providerId' => $providerId,
                'skinId'      => $skinId
            ]) || $query->rowCount() != 1
        ) {
            $returnData['status'] = false;
        } else {
            $returnData = $query->fetch(\PDO::FETCH_ASSOC);
        }
        return $returnData;
    }

}