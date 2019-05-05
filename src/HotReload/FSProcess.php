<?php

namespace SwooleTW\Http\HotReload;

use Swoole\Process as SwooleProcess;
use Symfony\Component\Process\Process as AppProcess;

/**
 * Class FSProcess
 */
class FSProcess
{
    /**
     * Fswatch options.
     *
     * @var array
     */
    protected $options;

    /**
     * Watch recursively.
     *
     * @var bool
     */
    protected $recursively;

    /**
     * Watch directory.
     *
     * @var string
     */
    protected $directory;

    /**
     * When locked event cannot do anything.
     *
     * @var bool
     */
    protected $locked;

    /**
     * Fswatch watch file event types
     * 
     * @var array
     */
    protected $eventTypes = [];

    /**
     * FSProcess constructor.
     *
     * @param array $options
     * @param bool $recursively
     * @param string $directory
     * @param array $eventTypes
     */
    public function __construct(array $options, bool $recursively, string $directory, array $eventTypes)
    {
        $this->options = $options;
        $this->recursively = $recursively;
        $this->directory = $directory;
        $this->eventTypes = $eventTypes;
        $this->locked = false;
    }

    /**
     * Make swoole process.
     *
     * @param callable|null $callback
     *
     * @return \Swoole\Process
     */
    public function make(?callable $callback = null)
    {
        $mcb = function ($type, $buffer) use ($callback) {
            $event = FSEventParser::toEvent($buffer, $this->eventTypes);
            if (! $this->locked && AppProcess::OUT === $type && $event) {
                $this->locked = true;
                ($callback) ? $callback($event) : null;
                $this->locked = false;
                unset($event);
            }
        };

        return new SwooleProcess(function () use ($mcb) {
            (new AppProcess($this->configure()))->setTimeout(0)->run($mcb);
        }, false, false);
    }

    /**
     * Configure process.
     *
     * @return array
     */
    protected function configure(): array
    {
        $configure = [
            'fswatch',
            $this->recursively ? '-rtx' : '-tx',
            '--format-time',
            '%Y-%m-%d %H:%M:%S',
        ];

        foreach($this->options as $option) {
            foreach((array)$option as $opt) {
                $configure[] = $opt;
            }
        }

        $configure[] = $this->directory;

        return $configure;
    }
}
