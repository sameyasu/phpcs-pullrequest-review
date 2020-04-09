<?php

namespace CodeReviewer;

use CodeReviewer\Logger\DefaultLoggerTrait;

class Runner
{
    use DefaultLoggerTrait;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->initLogger();
    }

    /**
     * 処理実行メソッド
     *
     * @return int exitCode
     */
    public function run(): int
    {
        $this->logger->debug('$_SERVER["argv"]', ['argv' => $_SERVER['argv']]);

        // PHPCS実行時の引数
        $argv = [
            $_SERVER['argv'][0],
            '--no-colors',
            '--report=json',
        ];
        // 第3引数以降に指定された引数を引き継ぐ
        foreach (array_slice($_SERVER['argv'], 1) as $v) {
            $argv[] = $v;
        }
        $this->logger->debug('Runtime Arguments', ['argv' => $argv]);

        // 実行時の引数を上書き
        $_SERVER['argv'] = $argv;

        ob_start();
        $runner = new \PHP_CodeSniffer\Runner();
        $exitCode = $runner->runPHPCS();
        $result = ob_get_clean();

        //$this->logger->debug('Result', ['stdout' => $result]);

        $json = json_decode($result, JSON_PRETTY_PRINT);
        $errorCode = json_last_error();
        if ($errorCode !== JSON_ERROR_NONE) {
            $this->logger->error('Failed to run PHPCS', [
                'json_last_erorr' => $errorCode,
                'json_last_error_msg' => json_last_error_msg()
            ]);
            $this->logger->info('PHPCS result', ['stdout' => $result]);
        } else {
            $this->logger->info('PHPCS ran successfully');
            $this->logger->debug('result', ['json' => $json]);
        }

        // TODO: review posting
        // https://developer.github.com/v3/pulls/reviews/#create-a-pull-request-review

        return $exitCode;
    }
}
