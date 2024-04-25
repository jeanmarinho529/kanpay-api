<?php

namespace Tests\Unit\Services;

use App\Jobs\BatchFilePersistItemErrorsJob;
use App\Models\BatchFile\BatchFile;
use App\Services\BatchFileItemErrorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\MessageBag;
use Tests\TestCase;

class BatchFileItemErrorServiceTest extends TestCase
{
    use RefreshDatabase;

    private BatchFileItemErrorService $service;

    public function __construct(string $name)
    {
        $this->service = new BatchFileItemErrorService();
        parent::__construct($name);
    }

    public function test_should_validate(): void
    {
        $file = BatchFile::factory()->create();
        $data = [['row_number' => 1]];

        $result = $this->service->validate($data, $file);
        $this->assertEquals($data, $result);
    }

    public function test_should_build_row_errors(): void
    {
        Carbon::setTestNow('2024-04-24 12:34:56');

        $indexErrors = ['1' => 100];
        $errors = ['error message 1', 'error message 2'];
        $file = BatchFile::factory()->create();

        $result = $this->service->rowErrors($errors, $indexErrors, 1, $file);

        $expected = [
            [
                'batch_file_id' => $file->id,
                'row_number' => 100,
                'error' => 'error message 1',
                'created_at' => '2024-04-24 12:34:56',
                'updated_at' => '2024-04-24 12:34:56',
            ],
            [
                'batch_file_id' => $file->id,
                'row_number' => 100,
                'error' => 'error message 2',
                'created_at' => '2024-04-24 12:34:56',
                'updated_at' => '2024-04-24 12:34:56',
            ],
        ];

        $this->assertEquals($result, $expected);
    }

    public function test_should_process_and_validate_errors(): void
    {
        $file = BatchFile::factory()->create();

        Queue::fake();

        $validateErrors = new MessageBag();
        $validateErrors->add('1.field', 'The 1.field field is required.');
        $validateErrors->add('1.field', 'The 1.field field must be at least 3 characters.');

        $serviceMock = $this->getMockBuilder(BatchFileItemErrorService::class)
            ->onlyMethods(['rowErrors'])
            ->disableOriginalConstructor()
            ->getMock();

        $serviceMock->method('rowErrors')->willReturn([]);

        $data = [
            ['filed' => 'field', 'row_number' => 2],
            ['row_number' => 1],
        ];

        $result = $this->service->processValidationErrors($validateErrors, $data, $file);

        $this->assertEquals([['filed' => 'field', 'row_number' => 2]], $result);

        Queue::assertPushed(BatchFilePersistItemErrorsJob::class);
    }
}
