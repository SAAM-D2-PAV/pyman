<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class MessageGenerator
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getHappyMessage()
    {
        $messages = [
            'You did it! You updated the system! Amazing!',
            'That was one of the coolest updates I\'ve seen all day!',
            'Great work! Keep going!',
            'It\'s done! Good job',
            'Easy Peasy!',
            'That was a big one, well done',
            'Easy, right? ;)'
        ];

        $index = array_rand($messages);

        $this->logger->info('Generated message', [$messages[$index]]);

        return $messages[$index];
    }
}