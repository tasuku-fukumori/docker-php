<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Logger\BatchLogger;
use Log;

class SlackNotification extends Command
{
    const SLACK_API = "https://slack.com/api/chat.postMessage";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SlackNotification {channel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Slack通知用';

    private $logger;
    private $notificationChannel;

    public function init()
    {
        // ログ出力設定
        $this->logger = new BatchLogger();
        // 通知先のチャンネル設定
        $this->notificationChannel = $this->argument('channel');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->init();
        $this->logger->info("バッチ開始");
        $this->logger->info("通知チャンネル：{$this->notificationChannel}");
        // 通知処理実行
        $this->execNotification();
        $this->logger->info("バッチ終了");

    }

    private function execNotification()
    {
        $message = "ダイレクトメッセージ送信テスト1";

        $url = self::SLACK_API;
        $post = [
            'token' => config("slack.token"),
            'channel' => "#{$this->notificationChannel}",
            'text' => $message,
        ];

        $conn = curl_init();
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($conn, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($conn, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($conn, CURLOPT_HEADER, false);
        curl_setopt($conn, CURLOPT_URL, $url);
        curl_setopt($conn, CURLOPT_POST, true);
        curl_setopt($conn, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($conn);
        curl_close($conn);
        $ret = json_decode($response);

        if ($ret->ok === false) {
            $this->logger->error($ret->error);
        } else {
            $this->logger->info("send success");
        }
    }
}
