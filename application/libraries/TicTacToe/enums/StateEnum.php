<?php
namespace libraries\TicTacToe\enums;


abstract class StateEnum
{
    const NewGame = 0;
    const InProgress = 1;
    const Player1Wins = 2;
    const Player2Wins = 3;
    const Draw = 4;
    const InvalidMove = 5;
}
