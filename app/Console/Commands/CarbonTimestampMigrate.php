<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use MongoDB;

class CarbonTimestampMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db_migrate:Carbon {collection_name}{fields}{max_in_batch=1000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'timestamp -> Carbon';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $collectionName = $this->argument('collection_name');
        $fields = explode(',', $this->argument('fields'));
        $maxInBatch = intval($this->argument('max_in_batch'));

        $connectStr = sprintf('mongodb://%s:%s', env('MG_HOST'), env('MG_PORT'));
        $databaseName = env('MG_DATABASE');
        $client = new MongoDB\Client($connectStr);
        $db = $client->selectDatabase($databaseName);
        $collection = $db->{$collectionName};
        $findCondition = [];

        $findStartAtDt = Carbon::now();

        $numberTypes = [1, 18];
        foreach ($fields as $field) {
//            $findCondition['$or'][] = [
//                //新版本支持number，老版本只支持整数类型的type
//                $field => [
//                    '$exists' => true,
//                    '$type' => 'number'
//                ]
//            ];
            foreach ($numberTypes as $type) {
                $findCondition['$or'][] = [
                    $field => [
                        '$exists' => true,
                        '$type' => $type
                    ]
                ];
            }
        }

        $result = $collection->find($findCondition);
        $bulks = [];
        $count = 0;
        $totalCount = 0;
        $bulk = null;
        foreach ($result as $entry) {
            if ($count == 0) {
                $bulk = new MongoDB\Driver\BulkWrite(['ordered' => false]);
            }

            $setCondition = [];
            foreach ($fields as $field) {
                if (isset($entry->{$field}) && is_numeric($entry->{$field})) {
                    $setCondition[$field] = new MongoDB\BSON\UTCDateTime($entry->{$field} * 1000);
                }
            }
            if (empty($setCondition)) continue;
            $bulk->update(['_id' => $entry['_id']], ['$set' => $setCondition], ['multi' => false, 'upsert' => false]);
            $count += 1;
            $totalCount += 1;

            if ($count == $maxInBatch) {
                $bulks[] = $bulk;
                $count = 0;
            }
        }

        if ($count > 0) {
            $bulks[] = $bulk;
        }

        $findEndAtDt = Carbon::now();

        print_r(sprintf('Total record: %d', $totalCount) . PHP_EOL);
        print_r(sprintf('Reading uses %d seconds', $findEndAtDt->diffInSeconds($findStartAtDt)) . PHP_EOL);

        $manager = $db->getManager();
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $namespace = sprintf('%s.%s', $databaseName, $collectionName);

        $updateStartAtDt = Carbon::now();
        $totalUpdatedCount = 0;

        $bar = $this->output->createProgressBar(count($bulks));
        foreach ($bulks as $bulk) {
            try {
                $result = $manager->executeBulkWrite($namespace, $bulk, $writeConcern);
            } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
                $result = $e->getWriteResult();

                // Check if the write concern could not be fulfilled
                if ($writeConcernError = $result->getWriteConcernError()) {
                    printf("%s (%d): %s\n",
                        $writeConcernError->getMessage(),
                        $writeConcernError->getCode(),
                        var_export($writeConcernError->getInfo(), true)
                    );
                }

                // Check if any write operations did not complete at all
                foreach ($result->getWriteErrors() as $writeError) {
                    printf("Operation#%d: %s (%d)\n",
                        $writeError->getIndex(),
                        $writeError->getMessage(),
                        $writeError->getCode()
                    );
                }
            } catch (MongoDB\Driver\Exception\Exception $e) {
                printf("Other error: %s\n", $e->getMessage());
                exit;
            }

//            printf("Inserted %d document(s)\n", $result->getInsertedCount());
//            printf("Updated  %d document(s)\n", $result->getModifiedCount());
            $totalUpdatedCount += $result->getModifiedCount();

            $bar->advance();
        }

        $bar->finish();
        print_r(PHP_EOL);
        $updateEndAtDt = Carbon::now();
        print_r(sprintf('Total updated %d records', $totalUpdatedCount) . PHP_EOL);
        print_r(sprintf('Updating uses %d seconds', $updateEndAtDt->diffInSeconds($updateStartAtDt)) . PHP_EOL);
    }
}
