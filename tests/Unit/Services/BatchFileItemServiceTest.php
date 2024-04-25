<?php

namespace Tests\Unit\Services;

use App\Enums\BatchFileStatusEnum;
use App\Models\BatchFile\BatchFile;
use App\Models\BatchFile\BatchFileItemError;
use App\Models\BatchFile\BatchFileStatus;
use App\Services\BatchFileItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BatchFileItemServiceTest extends TestCase
{
    //todo: create processItems method test

    use RefreshDatabase;

    private BatchFileItemService $service;

    public function __construct(string $name)
    {
        $this->service = new BatchFileItemService(BatchFile::class, null);
        parent::__construct($name);
    }

    public static function dataBatchFileStatus(): array
    {
        return [
            [
                'statusName' => BatchFileStatusEnum::ERROR->value,
                'totalItemDone' => 0,
            ],
            [
                'statusName' => BatchFileStatusEnum::DONE->value,
                'totalItemDone' => 10,
            ],
            [
                'statusName' => BatchFileStatusEnum::PARTIAL->value,
                'totalItemDone' => 5,
            ],
        ];
    }

    /**
     * @dataProvider dataBatchFileStatus
     */
    public function test_return_batch_file_status_based_on_completed_items(string $statusName, int $totalItemDone): void
    {
        $status = BatchFileStatus::factory()->create(['name' => $statusName]);
        $file = BatchFile::factory()->create(['total_items' => 10]);

        $result = $this->service->getStatus($totalItemDone, $file);

        $this->assertEquals($result->id, $status->id);
    }

    public function test_should_update_batch_file(): void
    {
        $status = BatchFileStatus::factory()->create();
        $file = BatchFile::factory()->create(['total_failed' => 0]);
        BatchFileItemError::factory(2)->create(['batch_file_id' => $file->id]);

        $serviceMock = $this->getMockBuilder(BatchFileItemService::class)
            ->onlyMethods(['getStatus'])
            ->disableOriginalConstructor()
            ->getMock();

        $serviceMock->method('getStatus')->willReturn($status);

        $serviceMock->updateBatchFile($file);

        $file->refresh();

        $this->assertEquals(2, $file->total_failed);
        $this->assertEquals($file->batch_file_status_id, $status->id);
    }

    public function test_should_return_last_row_number(): void
    {
        $data = [['row_number' => 1], ['row_number' => 2]];
        $result = $this->service->getLastRowNumber($data);

        $this->assertEquals(2, $result);
    }

    public function test_should_convert_to_snake_case(): void
    {
        $headers = ['id', 'firstName', 'RowNumber'];
        $expected = ['id', 'first_name', 'row_number'];

        $result = $this->service->convertHeadersToSnakeCase($headers);

        $this->assertEquals($expected, $result);
    }

    public function test_should_add_header_to_data(): void
    {
        Carbon::setTestNow('2024-04-24 12:34:56');

        $headers = ['id', 'first_name'];
        $data = [1, 'kobe'];
        $file = BatchFile::factory()->create();

        $result = $this->service->addHeaderInRow($headers, $data, 1, $file);

        $expected = [
            'id' => 1,
            'first_name' => 'kobe',
            'row_number' => 1,
            'batch_file_id' => $file->id,
            'created_at' => '2024-04-24 12:34:56',
            'updated_at' => '2024-04-24 12:34:56',
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_should_process_file(): void
    {
        Carbon::setTestNow('2024-04-24 12:34:56');

        $file = BatchFile::factory()->create(['path' => 'batch_file/test/test.csv']);

        $expected = [
            [
                'id' => '1',
                'name' => 'kobe',
                'row_number' => 1,
                'batch_file_id' => $file->id,
                'created_at' => '2024-04-24 12:34:56',
                'updated_at' => '2024-04-24 12:34:56',
            ],
            [
                'id' => '2',
                'name' => 'oscar',
                'row_number' => 2,
                'batch_file_id' => $file->id,
                'created_at' => '2024-04-24 12:34:56',
                'updated_at' => '2024-04-24 12:34:56',
            ],
        ];

        $chunks = $this->service->chunkFile($file);

        foreach ($chunks as $chunk) {
            $this->assertEquals($expected, $chunk);
        }
    }
}
