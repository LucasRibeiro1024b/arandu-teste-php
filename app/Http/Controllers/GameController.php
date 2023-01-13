<?php

namespace App\Http\Controllers;

use App\Constants\Map;
use App\Contracts\GameObject;
use App\Models\Coin;
use App\Models\Enemy;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GameController extends Controller
{

    public $score = 0;

    /** @var Player */
    public $player;

    /** @var Collection<Enemy> */
    public $enemies;

    /** @var Coin */
    public $coin;

    /**
     * Carrega as instâncias necessárias da sessão ou cria as
     * instâncias dos objetos de jogo caso a sessão esteja vazia.
     *
     * @return void
     */
    public function load()
    {

        // Instancia o jogador a partir da sessão, se a
        // sessão não existir, cria um novo jogador no
        // quadrado central
        $this->player = session(
            'player',
            new Player(
                (1 + (Map::WIDTH - 1)) / 2,
                (1 + (Map::HEIGHT - 1)) / 2
            )
        );

        // Instancia os inimigos a partir da seessão, se
        // a sessão não existir, gera uma coleção de
        // inimigos
        $this->enemies = session(
            'enemies',
            collect(Enemy::generateEnemies(Map::ENEMIES))
        );

        $this->score = session(
            'score',
            0
        );

        $this->coin = session(
            'coin',
            new Coin()
        );

    }

    /**
     * Escreve os objetos de jogo na sessão
     *
     * @return void
     */
    public function writeToSession()
    {
        session([
            'player' => $this->player,
            'enemies' => $this->enemies,
            'score' => $this->score,
            'coin' => $this->coin
        ]);
    }

    /**
     * Recebe a requisição de movimento do usuário, recalcula
     * os posicionamentos e atualiza a sessão.
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
        $this->load();

        $this->player->move($request->key);

        $this->score += 10;

        $this->enemies->each(function (Enemy $enemy) {
            $enemy->moveRandomDirection();
        });
            
        $this->writeToSession();
    }

    /**
     * Recebe a requisição para a página do tabuleiro.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function scene()
    {
        $this->load();

        if ($this->coin->isCollidingWith($this->player)) {
            $this->score += 1000;
            $this->coin = new Coin();
        }

        $this->writeToSession();

        foreach ($this->enemies as $enemy) {
            if($enemy->isCollidingWith($this->player) || $this->player->isCollidingWith($enemy)) {
                return redirect('/gameover');
            }
        }
        
        return view('game');
    }

}