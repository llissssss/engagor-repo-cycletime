<?php

namespace App\Application;

use DateTimeImmutable;

final class FetchData
{
    private $client;

    public function __construct(GithubClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return PullRequestInfo[]
     */
    public function fetchData()
    {
        $qa = $this->fetchAllQAApproved();
        $uat = $this->fetchAllUATApproved();

        $all = array_merge($qa, $uat);

        return $this->fetchPRDetails($all);
    }

    private function fetchAllByLabels(string $labels)
    {
        $page = 1;

        $allResults = [];
        do {
            $result = $this->client->list($page, $labels);
            $fetchedResults = json_decode($result, true);
            $allResults = array_merge($allResults, $fetchedResults);
            $page++;
        } while (!empty($fetchedResults));

        return $allResults;
    }

    private function fetchAllQAApproved()
    {
        return $this->fetchAllByLabels('PR - Merge Request,PR - QA approved');
    }

    private function fetchAllUATApproved()
    {
        return $this->fetchAllByLabels('PR - Merge Request,PR - User Acceptance Test Approved');
    }

    private function fetchPRDetails(array $all)
    {
        $result = [];

        foreach ($all as $issue) {
            if (array_key_exists($issue['number'], $result)) {
                continue;
            }

            $pull = new PullRequestInfo(
                $issue['number'],
                $issue['title'],
                $issue['html_url'],
                new DateTimeImmutable($issue['created_at']),
                new DateTimeImmutable($issue['closed_at'])
            );

            $timelineEvents = $this->fetchAllTimeline($issue['number']);
            foreach ($timelineEvents as $event) {
                if ($event['event'] === 'labeled') {
                    $label = new Label($event['label']['name'], new DateTimeImmutable($event['created_at']));
                    $pull->labeled($label);
                }
                if ($event['event'] === 'unlabeled') {
                    $label = new Label($event['label']['name'], new DateTimeImmutable($event['created_at']));
                    $pull->unlabeled($label);
                }
            }

            $result[$pull->getNumber()] = $pull;
        }

        return $result;
    }

    private function fetchAllTimeline(string $number)
    {
        $page = 1;

        $allResults = [];
        do {
            $result = $this->client->timeline($number, $page);
            $fetchedResults = json_decode($result, true);
            $allResults = array_merge($allResults, $fetchedResults);
            $page++;
        } while (!empty($fetchedResults));

        return $allResults;
    }
}
