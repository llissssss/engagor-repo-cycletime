<?php

namespace App\Application\Actions;

use App\Application\FetchData;
use League\Csv\Writer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

final class CycleTimeAction extends Action
{
    private const EXPORT_DATE_FORMAT = 'Y-m-d H:i:s';
    private $fetchData;

    public function __construct(LoggerInterface $logger, FetchData $fetchData)
    {
        parent::__construct($logger);
        $this->fetchData = $fetchData;
    }

    protected function action(): Response
    {
        $csv = Writer::createFromFileObject(new \SplTempFileObject());

        $csv->insertOne(['PR', 'PR Created', 'QA Needed?', 'UAT Needed?', 'QA Requested', 'QA Approved', 'UAT Requested', 'UAT Approved', 'PR Closed']);

        foreach ($this->fetchData->fetchData() as $pr) {
            $csv->insertOne([
                $pr->getNumber(),
                $pr->getCreatedAt()->format(self::EXPORT_DATE_FORMAT),
                $pr->getFirstQaRequested() || $pr->getLastQaApproved() ? 'yes' : 'no',
                $pr->getFirstUatRequested() || $pr->getLastUatApproved() ? 'yes' : 'no',
                $pr->getFirstQaRequested() ? $pr->getFirstQaRequested()->format(self::EXPORT_DATE_FORMAT) : '',
                $pr->getLastQaApproved() ? $pr->getLastQaApproved()->format(self::EXPORT_DATE_FORMAT) : '',
                $pr->getFirstUatRequested() ? $pr->getFirstUatRequested()->format(self::EXPORT_DATE_FORMAT) : '',
                $pr->getLastUatApproved() ? $pr->getLastUatApproved()->format(self::EXPORT_DATE_FORMAT) : '',
                $pr->getClosedAt()->format(self::EXPORT_DATE_FORMAT)
            ]);
        }

        $response = $this->response;

        $response = $response->withHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response = $response->withHeader('Content-Encoding', 'none');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="result.csv"');
        $response = $response->withHeader('Content-Description', 'File Transfer');

        $response->getBody()->write($csv->toString());

        return $response;
    }
}