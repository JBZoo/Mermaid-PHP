<?php

/**
 * JBZoo Toolbox - Mermaid-PHP.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Mermaid-PHP
 */

declare(strict_types=1);

namespace JBZoo\MermaidPHP\SequenceDiagram;

use JBZoo\MermaidPHP\Exception;
use JBZoo\MermaidPHP\Helper;
use JBZoo\MermaidPHP\Render;

/**
 * @psalm-suppress ClassMustBeFinal
 */
class SequenceDiagram
{
    private const int RENDER_SHIFT = 4;

    /** @var Participant[] */
    private array $participants = [];

    /** @var array<string, Participant> */
    private array $participantIndex = [];

    /** @var Box[] */
    private array $boxes = [];

    /** @var Statement[] */
    private array $statements = [];

    /** @var array<string, mixed> */
    private array $params = [
        'title'      => '',
        'autonumber' => false,
    ];

    /**
     * @param array<string, mixed> $params
     */
    public function __construct(array $params = [])
    {
        $this->setParams($params);
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function addParticipant(Participant $participant): self
    {
        $this->participants[$participant->getId()]     = $participant;
        $this->participantIndex[$participant->getId()] = $participant;

        return $this;
    }

    public function addBox(Box $box): self
    {
        $this->boxes[] = $box;

        foreach ($box->getParticipants() as $participant) {
            $this->participantIndex[$participant->getId()] = $participant;
        }

        return $this;
    }

    public function getParticipant(string $identifier): ?Participant
    {
        if (Participant::isSafeMode()) {
            $identifier = Helper::getId($identifier);
        }

        return $this->participantIndex[$identifier] ?? null;
    }

    public function addStatement(Statement $statement): self
    {
        $this->statements[] = $statement;

        return $this;
    }

    public function addMessage(Message $message): self
    {
        return $this->addStatement($message);
    }

    public function addMessageByIds(
        string $sourceId,
        string $targetId,
        string $text = '',
        ArrowType $arrow = ArrowType::SOLID_ARROW,
        bool $activateTarget = false,
        bool $deactivateSource = false,
    ): self {
        $source = $this->getParticipant($sourceId);
        if ($source === null) {
            throw new Exception("Source participant id=\"{$sourceId}\" not found");
        }

        $target = $this->getParticipant($targetId);
        if ($target === null) {
            throw new Exception("Target participant id=\"{$targetId}\" not found");
        }

        return $this->addMessage(new Message($source, $target, $text, $arrow, $activateTarget, $deactivateSource));
    }

    public function addNote(Note $note): self
    {
        return $this->addStatement($note);
    }

    public function activate(Participant $participant): self
    {
        return $this->addStatement(new Activation($participant, true));
    }

    public function deactivate(Participant $participant): self
    {
        return $this->addStatement(new Activation($participant, false));
    }

    public function create(Participant $participant): self
    {
        return $this->addStatement(new Lifecycle($participant, true));
    }

    public function destroy(Participant $participant): self
    {
        return $this->addStatement(new Lifecycle($participant, false));
    }

    public function comment(string $text): self
    {
        return $this->addStatement(new Comment($text));
    }

    /**
     * @param array<string, mixed> $params
     */
    public function setParams(array $params): self
    {
        $this->params = \array_merge($this->params, $params);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function render(): string
    {
        $spaces = \str_repeat(' ', self::RENDER_SHIFT);
        $result = [];

        $title = (string)($this->params['title'] ?? '');
        if ($title !== '') {
            $result[] = \sprintf('---%s---', \PHP_EOL . 'title: ' . $title . \PHP_EOL);
        }

        $result[] = 'sequenceDiagram';

        if (($this->params['autonumber'] ?? false) === true) {
            $result[] = $spaces . 'autonumber';
        }

        foreach ($this->participants as $participant) {
            $result[] = $spaces . $participant->getDeclaration();
        }

        foreach ($this->boxes as $box) {
            $result[] = $box->render(self::RENDER_SHIFT);
        }

        foreach ($this->collectLinks() as $link) {
            $result[] = $spaces . $link;
        }

        foreach ($this->statements as $statement) {
            $result[] = $statement->render(self::RENDER_SHIFT);
        }

        $result[] = '';

        return \implode(\PHP_EOL, $result);
    }

    /**
     * @param array<string, mixed> $params
     */
    public function renderHtml(array $params = []): string
    {
        return Render::html($this, $params);
    }

    public function getLiveEditorUrl(): string
    {
        return Helper::getLiveEditorUrl($this);
    }

    /**
     * @return string[]
     */
    private function collectLinks(): array
    {
        $lines = [];

        foreach ($this->participantIndex as $participant) {
            foreach ($participant->getLinks() as $link) {
                $lines[] = "link {$participant->getId()}: {$link['label']} @ {$link['url']}";
            }
        }

        return $lines;
    }
}
