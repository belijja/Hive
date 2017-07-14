<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 18.05.2017
 * Time: 15:01
 */
declare(strict_types = 1);

namespace BackOffice;

use Helpers\ConfigHelpers\Db;

class Core
{
    private $db;

    /**
     * Core constructor.
     * @param Db $db
     */
    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function getGameShortData(int $gameId): array
    {
        $query = $this->db->getDb(false)->prepare("
                SELECT 
				pro.provider_id,
				game.aams_game_id_desktop,
				game.aams_game_id_mobile,
				game.provider_game_id_desktop AS game_id,
				game.aams_type_id as aams_game_type,
				game.is_slot
			FROM
				hg_game game
					JOIN
				hg_provider pro ON game.provider_id = pro.id
			WHERE
				game.internal_game_id = :gameId");
        $result = $query->execute([
            ':gameId' => $gameId
        ]);
        if (!$result || $query->rowCount() < 1) {
            throw new \SoapFault('-1', 'Invalid game ID passed.');
        } else {
            $gameShortData = $query->fetch(\PDO::FETCH_ASSOC);
            if (empty($gameShortData['provider_id']) || empty($gameShortData['game_id'])) {
                throw new \SoapFault('-1', 'Invalid game ID passed.');
            }
            return $gameShortData;
        }
    }
}