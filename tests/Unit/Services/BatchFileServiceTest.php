<?php

namespace Tests\Unit\Services;

use App\Enums\BatchFileStatusEnum;
use App\Events\BatchFileUploaded;
use App\Models\BatchFile\BatchFile;
use App\Models\BatchFile\BatchFileStatus;
use App\Models\BatchFile\BatchFileType;
use App\Services\BatchFileService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BatchFileServiceTest extends TestCase
{
    use RefreshDatabase;

    private BatchFileService $service;

    public function __construct(string $name)
    {
        $this->service = new BatchFileService();
        parent::__construct($name);
    }

    public static function dataPaginate(): array
    {
        return [
            [
                'data' => [],
                'expected' => ['current_page' => 1, 'per_page' => 10],
            ],
            [
                'data' => ['page' => 1, 'per_page' => 5],
                'expected' => ['current_page' => 1, 'per_page' => 5],
            ],
        ];
    }

    /**
     * @dataProvider dataPaginate
     */
    public function test_should_return_batch_files(array $data, array $expected): void
    {
        $batchFile = BatchFile::factory()
            ->create()
            ->load(['batchFileStatus', 'batchFileType']);

        $result = $this->service->index($data)->toArray();

        $this->assertEquals($expected['current_page'], $result['current_page']);
        $this->assertEquals($expected['per_page'], $result['per_page']);
        $this->assertEquals($batchFile->toArray(), $result['data'][0]);
    }

    public function test_should_return_required_batch_file(): void
    {
        $batchFile = BatchFile::factory()
            ->create()
            ->load(['batchFileStatus', 'batchFileType']);

        $result = $this->service->show($batchFile->id);

        $this->assertEquals($batchFile->toArray(), $result->toArray());
    }

    public function test_should_throw_exception_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->show('id_not_found');
    }

    public static function dataBatchFileStatusName(): array
    {
        return [
            [BatchFileStatusEnum::PROGRESS->value],
            [BatchFileStatusEnum::ERROR->value],
        ];
    }

    /**
     * @dataProvider dataBatchFileStatusName
     */
    public function test_should_upload_file(string $statusName): void
    {
        Event::fake([BatchFileUploaded::class]);

        Storage::fake('public');

        $file = UploadedFile::fake()->create('test_file.csv');
        $type = BatchFileType::factory()->create(['name' => 'path_test']);

        $status = BatchFileStatus::factory()->create(['name' => $statusName]);

        $batchFileResult = $this->service->uploadFile($file, ['file_type_name' => $type->name]);

        $this->assertEquals($status->id, $batchFileResult->batch_file_status_id);
        $this->assertEquals($type->id, $batchFileResult->batch_file_type_id);

        $this->assertEquals('test_file.csv', $batchFileResult->name);

        $this->assertNotNull($batchFileResult->path);
    }
}
