<?php
/**
 * Dash
 *
 * @link      http://github.com/DASPRiD/Dash For the canonical source repository
 * @copyright 2013 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace Dash\Router\Http\Route;

use Dash\Router\Http\RouteMatch;
use Zend\Http\Request as HttpRequest;
use Zend\Uri\Http as HttpUri;

/**
 * Interface every HTTP route must implement.
 */
interface RouteInterface
{
    /**
     * Matches a request at a given path offset.
     *
     * @param  HttpRequest $request
     * @param  int         $pathOffset
     * @return null|RouteMatch
     */
    public function match(HttpRequest $request, $pathOffset);

    /**
     * Assembles a URL.
     *
     * @param  array       $params
     * @param  null|string $childName
     * @return AssemblyResult
     */
    public function assemble(array $params, $childName = null);
}
