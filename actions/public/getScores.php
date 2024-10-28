<?php

function getScores_ALL(Web $w) {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, FETCH");
    $p = $w->pathMatch("hash");
    
    if (empty($p['hash'])) {
        //return null;
        $w->error('no key provided', '/leaderboard');
    } 

    $game = LeaderboardService::getInstance($w)->getGameForHash($p['hash']);
    if (empty($game)) {
        $w->error('game not found for key');
    }

    $scores = LeaderboardService::getInstance($w)->getScoresForGameId($game->id);
    
    usort($scores, function($a, $b){
        if (intVal($a->player_score) < intVal($b->player_score)){
            return 1;
        } else {
            return 0;
        }
    });

    $results = [];
    foreach($scores as $score) {
        $results[] = [
            "name" => $score->player_name,
            "score" => $score->player_score
        ];
    }
    $payload = ["all_scores" => $results];
    $w->setLayout(null);
    $w->out(json_encode($payload));
}
