<?php
/**
 * Dash
 *
 * @link      http://github.com/DASPRiD/Dash For the canonical source repository
 * @copyright 2013 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace Dash\Router\MatchResult;

/**
 * Abstrac failed match.
 */
abstract class AbstractFailedMatch implements MatchResultInterface
{
    final public function isSuccess()
    {
        return false;
    }
}
