<?php

namespace App\Http\Services\PoliceOfficer;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitPublisher
{
    public function publish(string $routingKey, array $data): void
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
        $jobsQueue = env('AI_RMQ_JOBS_QUEUE', 'ai.jobs');

        $ch->exchange_declare($exchange, 'direct', false, true, false);
        $ch->queue_declare($jobsQueue, false, true, false, false);
        $ch->queue_bind($jobsQueue, $exchange, 'job.create');

        $msg = new AMQPMessage(
            json_encode($data, JSON_UNESCAPED_UNICODE),
            [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'correlation_id' => $data['correlation_id'] ?? null,
            ]
        );

        $ch->basic_publish($msg, $exchange, $routingKey);

        $ch->close();
        $conn->close();
    }
}
