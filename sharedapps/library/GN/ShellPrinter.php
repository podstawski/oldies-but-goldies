<?php
/**
 * @author Radosław Szczepaniak <radoslaw.szczepaniak@gammanet.pl>
 *
 * Class for nice command line text formatting.
 * Call 'demo' method for available templates with examples.
 */

class ShellPrinter
{
    const BLACK   = "\033[30m";
    const RED     = "\033[31m";
    const GREEN   = "\033[32m";
    const YELLOW  = "\033[33m";
    const BLUE    = "\033[34m";
    const PURPULE = "\033[35m";
    const CYAN    = "\033[36m";

    const DEFAULT_COLOR  = "\033[37m";

    const PLAIN      = 0;
    const INFO       = 1;
    const WARN       = 2;
    const ERROR      = 3;
    const APPEND     = 4;
    const STAR       = 5;
    const STAR_WHITE = 6;
    const PLUS       = 7;
    const MINUS      = 8;

    /**
     * @var int
     */
    protected static $DEFAULT_TEMPLATE = self::PLAIN;

    /**
     * @var array
     */
    protected static $prefixes = array();

    /**
     * @var array
     */
    protected static $suffixes = array();

    public function __construct()
    {
        self::$prefixes = array(
            self::PLAIN      => self::DEFAULT_COLOR,
            self::INFO       => self::DEFAULT_COLOR . ">>> ",
            self::WARN       => self::RED . "!! " . self::DEFAULT_COLOR,
            self::ERROR      => self::RED . "!! ",
            self::APPEND     => " " . self::DEFAULT_COLOR,
            self::STAR       => " " . self::GREEN . "*" . self::DEFAULT_COLOR . " ",
            self::STAR_WHITE => " " . self::DEFAULT_COLOR . "* ",
            self::PLUS       => " " . self::GREEN . "+" . self::DEFAULT_COLOR . " ",
            self::MINUS      => " " . self::RED . "-" . self::DEFAULT_COLOR . " ",
        );

        self::$suffixes = array(
            self::INFO  => " ...",
            self::WARN  => " " . self::RED . "!!",
            self::ERROR => " !!",
            self::STAR  => " ...",
            self::PLUS  => " ...",
            self::MINUS => " ...",
        );
    }

    /**
     * @param int $template
     */
    public function setDefaultTemplate($template)
    {
        self::$DEFAULT_TEMPLATE = $template;
    }

    /**
     * Prints new line.
     */
    public function eol()
    {
        echo PHP_EOL;
    }

    /**
     * Prints formatted text.
     * @param string $text
     * @param null $template
     * @param bool $isEndOfLine
     */
    public function slog($text, $template = null, $isEndOfLine = true)
    {
        if ($template === null) {
            $template = self::$DEFAULT_TEMPLATE;
        }
        $text = self::$prefixes[$template] . $text;
        if (array_key_exists($template, self::$suffixes)) {
            $text .= self::$suffixes[$template];
        }
        $text .= self::DEFAULT_COLOR;
        if ($isEndOfLine) {
            $text .= PHP_EOL;
        }
        echo $text;
    }

    /**
     * Prints available templates with example.
     */
    public function demo()
    {
        $this->plain('List of available templates:');
        $this->eol();
        $r = new ReflectionObject($this);
        foreach ($r->getConstants() as $name => $value)
        {
            if (is_int($value) && $name !== 'DEFAULT_LEVEL') {
                $method = strtolower($name);
                call_user_func(array($this, $method), $method, $value);
            }
        }
        die();
    }

    /**
     * @param string $method
     * @param array $args
     */
    public function __call($method, $args)
    {
        $r = new ReflectionObject($this);
        $constantName = strtoupper($method);
        if ($r->hasConstant($constantName) && is_int($template = $r->getConstant($constantName))) {
            if (!empty($args)) {
                @list ($text, $isEndOfLine) = $args;
                $this->slog($text, $template, is_bool($isEndOfLine) ? $isEndOfLine : true);
            } else {
                throw new Exception('Please privide text parameter');
            }
        } else {
            throw new Exception('Could not find level "' . $method . '"');
        }
    }
}

