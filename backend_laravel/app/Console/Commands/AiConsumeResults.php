<?php

namespace App\Console\Commands;

use App\Models\AiJob;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class AiConsumeResults extends Command
{
    protected $signature = 'ai:consume-results';
    protected $description = 'Consume AI results from RabbitMQ';

    public function handle(): int
    {
        $conn = new AMQPStreamConnection(
            env('RABBITMQ_HOST', '127.0.0.1'),
            (int) env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'admin'),
            env('RABBITMQ_PASSWORD', 'admin123'),
            env('RABBITMQ_VHOST', '/')
        );

        $ch = $conn->channel();

        $exchange = env('AI_RMQ_EXCHANGE', 'ai.exchange');
        $resultsQueue = env('AI_RMQ_RESULTS_QUEUE', 'ai.results');

        $ch->exchange_declare($exchange, 'direct', false, true, false);
        $ch->queue_declare($resultsQueue, false, true, false, false);
        $ch->queue_bind($resultsQueue, $exchange, 'job.result');

        $this->info("Listening on {$resultsQueue}...");

        $ch->basic_consume($resultsQueue, '', false, false, false, false, function ($msg) {
            try {
                $data = json_decode($msg->body, true, 512, JSON_THROW_ON_ERROR);

                $job = AiJob::where('job_id', $data['job_id'] ?? null)->first();

                if ($job) {
                    $job->status = ($data['status'] === 'success') ? 'success' : 'failed';
                    $job->result = $data['result'] ?? null;
                    $job->error = $data['error'] ?? null;
                    $job->finished_at = now();
                    $job->save();
                }

                $msg->ack();
            } catch (\Throwable $e) {
                logger()->error('AI result consume error', ['err' => $e->getMessage()]);
                $msg->nack(false, false);
            }
        });

        while ($ch->is_consuming()) {
            $ch->wait();
        }

        $ch->close();
        $conn->close();

        return self::SUCCESS;
    }
}
