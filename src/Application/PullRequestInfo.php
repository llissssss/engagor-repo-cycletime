<?php

namespace App\Application;

use DateTimeImmutable;

final class PullRequestInfo
{
    private $number;
    private $title;
    private $url;
    /**
     * @var Label[]
     */
    private $labeled = array();

    /**
     * @var Label[]
     */
    private $unlabeled = array();
    private $closedAt;
    private $createdAt;

    public function __construct(
        string $number,
        string $title,
        string $url,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $closedAt
    ) {
        $this->number = $number;
        $this->title = $title;
        $this->url = $url;
        $this->closedAt = $closedAt;
        $this->createdAt = $createdAt;
    }

    public function labeled(Label $label) {
        $this->labeled[] = $label;
    }

    public function unlabeled(Label $label) {
        $this->unlabeled[] = $label;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getClosedAt()
    {
        return $this->closedAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getLastUatApproved()
    {
        /** @var Label $label */
        foreach (array_reverse($this->labeled) as $label) {
            if ($label->getLabel() === 'PR - User Acceptance Test Approved' || $label->getLabel() === 'PR - Acceptance Test Approved') {
                return $label->getDateTimeImmutable();
            }
        }

        return null;
    }

    public function getFirstQaRequested()
    {
        /** @var Label $label */
        foreach ($this->labeled as $label) {
            if ($label->getLabel() === 'PR - Awaiting QA') {
                return $label->getDateTimeImmutable();
            }
        }

        return null;
    }

    public function getLastQaApproved()
    {
        /** @var Label $label */
        foreach (array_reverse($this->labeled) as $label) {
            if ($label->getLabel() === 'PR - QA Approved') {
                return $label->getDateTimeImmutable();
            }
        }

        return null;
    }

    public function getFirstUatRequested()
    {
        /** @var Label $label */
        foreach (array_reverse($this->labeled) as $label) {
            if ($label->getLabel() === 'PR - Awaiting User Acceptance Test' || $label->getLabel() === 'PR - Awaiting Acceptance Test') {
                return $label->getDateTimeImmutable();
            }
        }

        return null;
    }
}