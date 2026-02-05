<?php

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use App\Domain\Task\TaskStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tasks')]
final class TaskEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'string', length: 32)]
    private string $status;

    #[ORM\Column(type: 'string', length: 36)]
    private string $assigneeId;

    public function __construct(string $id, string $title, string $description, TaskStatus $status, string $assigneeId)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->status = $status->value;
        $this->assigneeId = $assigneeId;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function status(): TaskStatus
    {
        return TaskStatus::from($this->status);
    }

    public function assigneeId(): string
    {
        return $this->assigneeId;
    }

    public function update(string $title, string $description, TaskStatus $status, string $assigneeId): void
    {
        $this->title = $title;
        $this->description = $description;
        $this->status = $status->value;
        $this->assigneeId = $assigneeId;
    }
}
