<?php

namespace Seat\EveQueues\Partial;

use Carbon\Carbon;
use Seat\EveApi;

class CorporationWallet {

    public function fire($job, $data) {

        $keyID = $data['keyID'];
        $vCode = $data['vCode'];

		$job_record = \SeatQueueInformation::where('jobID', '=', $job->getJobId())->first();

        // Check that we have a valid jobid
        if (!$job_record) {

            // Sometimes the jobs get picked up faster than the submitter could write a
            // database entry about it. So, just wait 5 seconds before we come back and
            // try again
            $job->release(5);
            return;
        }

        // We place the actual API work in our own try catch so that we can report
        // on any critical errors that may have occurred.

        // By default Laravel will requeue a failed job based on --tries, but we
        // dont really want failed api jobs to continually poll the API Server
        try {

            $job_record->status = 'Working';
            $job_record->save();

            $job_record->output = 'Started WalletJournal Update';
            $job_record->save();
            EveApi\Corporation\WalletJournal::Update($keyID, $vCode);

            $job_record->output = 'Started WalletTransactions Update';
            $job_record->save();
            EveApi\Corporation\WalletTransactions::Update($keyID, $vCode);

            $job_record->status = 'Done';
            $job_record->output = null;
            $job_record->save();

            $job->delete();

        } catch (\Seat\EveApi\Exception\APIServerDown $e) {

            // The API Server is down according to \Seat\EveApi\bootstrap().
            // Due to this fact, we can simply take this job and put it
            // back in the queue to be processed later.
            $job_record->status = 'Queued';
            $job_record->output = 'The API Server appears to be down. Job has been re-queued.';
            $job_record->save();

            // Re-queue the job to try again in 10 minutes
            $job->release(60 * 10);

        } catch (\Exception $e) {

            $job_record->status = 'Error';
            $job_record->output = 'Last status: ' . $job_record->output . PHP_EOL .
                'Error: ' . $e->getCode() . ': ' . $e->getMessage() . PHP_EOL .
                'File: ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL .
                'Trace: ' . $e->getTraceAsString() . PHP_EOL .
                'Previous: ' . $e->getPrevious();
            $job_record->save();

            $job->delete();
        }
    }
}