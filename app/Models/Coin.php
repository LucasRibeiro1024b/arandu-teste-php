<?php

namespace App\Models;

use App\Contracts\GameObject;
use App\Constants\Map;

class Coin extends GameObject
{

    /**
     * Criar uma posição aleatória dentro dos limites do tabuleiro
     *
     * @return array<string,int>
     */
    public function createRandomPosition()
    {
        return [
            'x' => rand(0, Map::WIDTH - 1),
            'y' => rand(0, Map::HEIGHT - 1)
        ];
    }

    public function __construct()
    {
        [
            'x' => $x,
            'y' => $y
        ] = $this->createRandomPosition();

        parent::__construct($x, $y);
    }

    public function render()
    {
        $css = "
        .tile-{$this->x()}-{$this->y()} {
            background-color: yellow;
        }
        ";

        echo $css;
    }
}