<?php

namespace App\Events\Lesson;

class LessonStatusUpdatedEvent extends \App\Events\BaseEvent
{

    public function __construct(
        public string $lessonId,
    ) {
    }

    public function getChannelName(): string
    {
        return \sprintf('lesson.%s', $this->getLessonId());
    }

    /**
     * @return string
     */
    public function getLessonId(): string
    {
        return $this->lessonId;
    }
}