<?php
/**
 * Created by PhpStorm.
 * User: Branislav Malidzan
 * Date: 18.05.2017
 * Time: 15:01
 */
declare(strict_types = 1);

namespace BackOffice;

class Core extends AbstractBackOffice
{
    public function getGameShortData($gameId)
    {
        $query = $this->db->prepare("
			SELECT 
  			pro.provider_id,
	  		game.aams_game_id_desktop,
		  	game.aams_game_id_mobile,
  			game.provider_game_id_desktop AS game_id,
	  		cat.type_id as aams_game_type,
		  	cat.id as category_id,
  			game.is_slot
			FROM
	  		hg_game game
			JOIN
		  	hg_provider pro ON game.provider_id = pro.id
	  		JOIN
			hg_category cat ON game.category_id=cat.id
			WHERE
			game.internal_game_id = :gameId");
        $result = $query->execute([
            'gameId' => $gameId
        ]);
        if ($result && $query->rowCount() > 0) {
            $gameShortData = $query->fetch(\PDO::FETCH_ASSOC);
        } else {
            $gameShortData['status'] = false;
        }
        return $gameShortData;
    }
}