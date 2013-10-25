<?php

namespace Kpacha\Datastructure\Queue;

/**
 * Description of PriorityQueue
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class PriorityQueue extends \SplPriorityQueue
{

    public function compare($p1, $p2)
    {
        if ($p1 === $p2)
            return 0;
        return ($p1 < $p2) ? 1 : -1;
    }

}
