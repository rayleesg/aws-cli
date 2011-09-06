<?php
namespace Lagged\AWS\Cli;

abstract class Command
{
    /**
     * @var string
     */
    protected $key, $secret;

    /**
     * __construct
     *
     * Uses a Config, or the defined constants.
     *
     * @param \Lagged\AWS\Config $config
     *
     * @return $this
     * @throws \LogicException
     */
    public function __construct(\Lagged\AWS\Config $config = null)
    {
        if ($config !== null) {
            if (!isset($config->aws)) {
                throw new \LogicException("No AWS configuration found.");
            }
            if (!isset($config->aws->key) || !isset($config->aws->secret)) {
                throw new \LogicException("Missing 'key' or 'secret'.");
            }
            $this->key    = $config->aws->key;
            $this->secret = $config->aws->secret;

        } else {
            if (!defined('AWS_KEY')) {
                throw new \LogicException("'AWS_KEY' is not defined.");
            }
            if (!defined('AWS_SECRET_KEY')) {
                throw new \LogicException("'AWS_SECRET_KEY' is not defined.");
            }
            $this->key    = AWS_KEY;
            $this->secret = AWS_SECRET_KEY;
        }
        if (empty($this->key)) {
            throw new \RuntimeException("Key missing: no config file or constant found.");
        }
        if (empty($this->secret)) {
            throw new \RuntimeException("Secret missing: no config file or constant found.");
        }
    }

    public function execute($method)
    {
        if (empty($method)) {
            throw new \LogicException("Can't execute an empty method.");
        }

        if (!is_callable(array($this, $method))) {
            throw new \RuntimeException("Invalid task: {$method}");
        }
        return call_user_func(array($this, $method));
    }

    abstract function Help();
}