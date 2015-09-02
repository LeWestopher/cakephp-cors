<?php
/**
 * Created by PhpStorm.
 * User: westopher
 * Date: 9/2/15
 * Time: 12:42 PM
 */

namespace Cors\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;

/**
 * Class CorsComponent
 * @package Cors\Controller\Component
 */
class CorsComponent extends Component
{

    /**
     * @var array
     */
    public $actions = [];

    /**
     * @var bool
     */
    public $all = false;

    /**
     * @var array
     */
    public $methods = [];

    /**
     * @var array
     */
    public $headers = [];

    /**
     * @var string
     */
    public $origin = '*';


    /**
     * @param $options
     */
    public function enable($options)
    {
        if (isset($options['actions'])) {
            $this->actions = $options['actions'];
        } else {
            $this->all = true;
        }

        if (isset($options['methods'])) {
            $this->methods = $options['methods'];
        }

        if (isset($options['origin'])) {
            $this->origin = $options['origin'];
        }

        if (isset($options['headers'])) {
            $this->headers = $options['headers'];
        }
    }

    /**
     * @param Event $e
     * @return mixed
     */
    public function beforeRender(Event $e)
    {
        $request = $e->data['request'];
        $action = $request->action;

        if (isset($this->actions['*']) && is_array($this->actions['*'])) {
            // We have an associative array of actions => options
            $opts = $this->actions['*'];
            $origin = (isset($opts['origin'])) ? $opts['origin'] : $this->origin;
            $methods = (isset($opts['methods'])) ? $opts['methods'] : $this->methods;
            $headers = (isset($opts['methods'])) ? $opts['headers'] : $this->headers;
        } elseif (isset($this->actions[$action]) && is_array($this->actions[$action])) {
            // Specific action declared for this controller
            $opts = $this->actions[$action];
            $origin = (isset($opts['origin'])) ? $opts['origin'] : $this->origin;
            $methods = (isset($opts['methods'])) ? $opts['methods'] : $this->methods;
            $headers = (isset($opts['methods'])) ? $opts['headers'] : $this->headers;
        } else {
            // No Cors declared for this controller
            return $e->data['response'];
        }

        $e->data['response']->cors($request, $origin, $methods, $headers);
    }
}
