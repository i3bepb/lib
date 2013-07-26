<?php
namespace i3bepb;

class Console {

    public static function strip($text) {
        return preg_replace('/\033\[(\d+)(;\d+)*m/', '', $text);
    }

    public static function stdin($raw = false) {
        return $raw ? fgets(STDIN) : rtrim(fgets(STDIN), PHP_EOL);
    }

    public static function stdout($text) {
        return fwrite(STDOUT, $text);
    }

    public static function stderr($text) {
        return fwrite(STDERR, $text);
    }

    public static function error($text = null) {
        return static::stderr($text . PHP_EOL);
    }

    /**
     * Asks the user for input. Ends when the user types a PHP_EOL. Optionally
     * provide a prompt.
     *
     * @param string $prompt String prompt (optional)
     *
     * @return string User input
     */
    public static function input($prompt = null) {
        if(isset($prompt)) {
            static::stdout($prompt);
        }
        return static::stdin();
    }

    /**
     * Prints text to STDOUT appended with a PHP_EOL.
     *
     * @param string $text
     * @param bool   $raw
     *
     * @return int|false Number of bytes printed or false on error
     */
    public static function output($text = null) {
        return static::stdout($text . PHP_EOL);
    }

    /**
     * Prompts the user for input
     *
     * @param string $text    Prompt string
     * @param array $defaultAnswers Set of options
     *
     * @return string
     */
    public static function ask($text, $defaultAnswers = false) {
        $input = '';
        if(!$defaultAnswers) $defaultAnswers = array('y', 'n');
        $defaultAnswersText = '[' . implode('/', $defaultAnswers) . ']';

        while(!in_array($input, $defaultAnswers)) {
            $input = static::input($text . ' ' . $defaultAnswersText . ':');
        }
        return $input;
    }

    public static function confirm($text) {
        $input = static::ask($text);
        return (!strncasecmp($input, 'y', 1) ? true : false);
    }
}