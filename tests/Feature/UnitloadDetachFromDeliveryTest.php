<?php

namespace IlBronza\Warehouse\Tests\Feature;

use IlBronza\Warehouse\Models\Delivery\ContentDelivery;
use IlBronza\Warehouse\Models\Unitload\Unitload;
use IlBronza\Warehouse\WarehouseServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;
use RuntimeException;

if (! function_exists('cconfig'))
	require_once dirname((new \ReflectionClass(\IlBronza\CRUD\CRUDServiceProvider::class))->getFileName()) . '/helpers.php';

if (! class_exists(\App\Http\Controllers\Controller::class))
	require_once __DIR__ . '/../Fixtures/App/Http/Controllers/Controller.php';

class UnitloadDetachFromDeliveryTest extends TestCase
{
	protected function getPackageProviders($app)
	{
		return [WarehouseServiceProvider::class];
	}

	protected function defineEnvironment($app)
	{
		$app['config']->set('app.locale', 'it');
		$app['config']->set('database.default', 'testing');
		$app['config']->set('database.connections.testing', [
			'driver' => 'sqlite',
			'database' => ':memory:',
			'prefix' => '',
		]);
		$app['config']->set('warehouse.models.unitload.class', DetachableUnitload::class);
		$app['config']->set('warehouse.models.contentDelivery.class', DetachableContentDelivery::class);
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->withoutMiddleware();

		Schema::create('warehouse__content_deliveries', function (Blueprint $table) {
			$table->string('id')->primary();
			$table->boolean('partial')->default(false);
			$table->timestamps();
		});
		Schema::create('warehouse__unitloads', function (Blueprint $table) {
			$table->string('id')->primary();
			$table->string('content_delivery_id')->nullable();
			$table->softDeletes();
			$table->timestamps();
		});

		DB::table('warehouse__content_deliveries')->insert([
			['id' => 'delivery-a', 'partial' => false],
			['id' => 'delivery-b', 'partial' => false],
		]);
		DB::table('warehouse__unitloads')->insert([
			['id' => 'selected-a', 'content_delivery_id' => 'delivery-a'],
			['id' => 'selected-b', 'content_delivery_id' => 'delivery-b'],
			['id' => 'unselected', 'content_delivery_id' => 'delivery-a'],
			['id' => 'unassociated', 'content_delivery_id' => null],
		]);
	}

	protected function detachUrl(): string
	{
		return app('warehouse')->route('unitloads.detachSelectedFromDelivery');
	}

	public function test_it_detaches_only_selected_unitloads_and_marks_their_deliveries_partial(): void
	{
		$this->post($this->detachUrl(), [
			'unitloads' => ['selected-a', 'selected-b'],
		], ['X-Requested-With' => 'XMLHttpRequest'])
			->assertOk()
			->assertJson(['success' => true, 'detached_count' => 2]);

		$this->assertDatabaseHas('warehouse__unitloads', ['id' => 'selected-a', 'content_delivery_id' => null]);
		$this->assertDatabaseHas('warehouse__unitloads', ['id' => 'selected-b', 'content_delivery_id' => null]);
		$this->assertDatabaseHas('warehouse__unitloads', ['id' => 'unselected', 'content_delivery_id' => 'delivery-a']);
		$this->assertDatabaseHas('warehouse__content_deliveries', ['id' => 'delivery-a', 'partial' => true]);
		$this->assertDatabaseHas('warehouse__content_deliveries', ['id' => 'delivery-b', 'partial' => true]);
	}

	public function test_it_ignores_unassociated_unitloads_and_duplicate_selections(): void
	{
		$this->post($this->detachUrl(), [
			'unitloads' => ['selected-a', 'selected-a', 'unassociated'],
		], ['X-Requested-With' => 'XMLHttpRequest'])
			->assertOk()
			->assertJson(['success' => true, 'detached_count' => 1]);

		$this->assertDatabaseHas('warehouse__unitloads', ['id' => 'selected-a', 'content_delivery_id' => null]);
		$this->assertDatabaseHas('warehouse__unitloads', ['id' => 'selected-b', 'content_delivery_id' => 'delivery-b']);
		$this->assertDatabaseHas('warehouse__content_deliveries', ['id' => 'delivery-b', 'partial' => false]);
	}

	public function test_it_detaches_two_unitloads_from_the_same_delivery(): void
	{
		DB::table('warehouse__unitloads')->insert([
			'id' => 'another-selected', 'content_delivery_id' => 'delivery-a',
		]);

		$this->post($this->detachUrl(), [
			'unitloads' => ['selected-a', 'another-selected'],
		], ['X-Requested-With' => 'XMLHttpRequest'])
			->assertOk()
			->assertJson(['success' => true, 'detached_count' => 2]);

		$this->assertDatabaseHas('warehouse__unitloads', ['id' => 'selected-a', 'content_delivery_id' => null]);
		$this->assertDatabaseHas('warehouse__unitloads', ['id' => 'another-selected', 'content_delivery_id' => null]);
		$this->assertDatabaseHas('warehouse__unitloads', ['id' => 'unselected', 'content_delivery_id' => 'delivery-a']);
		$this->assertDatabaseHas('warehouse__content_deliveries', ['id' => 'delivery-a', 'partial' => true]);
	}

	public function test_it_reports_when_all_selected_unitloads_are_already_unassociated(): void
	{
		$this->post($this->detachUrl(), [
			'unitloads' => ['unassociated'],
		], ['X-Requested-With' => 'XMLHttpRequest'])
			->assertOk()
			->assertJson(['success' => false, 'detached_count' => 0]);
	}

	public function test_it_rejects_an_empty_selection(): void
	{
		$this->postJson($this->detachUrl(), ['unitloads' => []])
			->assertUnprocessable()
			->assertJsonValidationErrors('unitloads');
	}

	public function test_it_rejects_invalid_ids_before_detaching_any_unitload(): void
	{
		$this->postJson($this->detachUrl(), ['unitloads' => ['selected-a', 'missing']])
			->assertUnprocessable()
			->assertJsonValidationErrors('unitloads.1');

		$this->assertDatabaseHas('warehouse__unitloads', ['id' => 'selected-a', 'content_delivery_id' => 'delivery-a']);
	}

	public function test_it_returns_to_the_popup_after_a_form_submission(): void
	{
		$this->from('/order-deliveries-popup')->post($this->detachUrl(), [
			'unitloads' => ['selected-a'],
		])->assertRedirect('/order-deliveries-popup')->assertSessionHas('UiKitNotifications.success');
	}

	public function test_it_rolls_back_all_detachments_when_a_save_fails(): void
	{
		$this->withoutExceptionHandling();
		DetachableUnitload::saving(function ($unitload) {
			if ($unitload->getKey() === 'selected-b')
				throw new RuntimeException('Cannot save selected-b');
		});

		try {
			$this->post($this->detachUrl(), ['unitloads' => ['selected-a', 'selected-b']]);
			$this->fail('The failing save should interrupt the request.');
		} catch (RuntimeException $exception) {
			$this->assertSame('Cannot save selected-b', $exception->getMessage());
		}

		$this->assertDatabaseHas('warehouse__unitloads', ['id' => 'selected-a', 'content_delivery_id' => 'delivery-a']);
		$this->assertDatabaseHas('warehouse__unitloads', ['id' => 'selected-b', 'content_delivery_id' => 'delivery-b']);
		$this->assertDatabaseHas('warehouse__content_deliveries', ['id' => 'delivery-a', 'partial' => false]);
	}
}

// Keep production packing events and external activity logging out of these database tests.
class DetachableUnitload extends Unitload
{
	protected $touches = [];

	protected static function booted() {}
	public static function bootLogsActivity(): void {}
	public static function bootBroadcastCrudModelEventsTrait(): void {}
}

class DetachableContentDelivery extends ContentDelivery
{
	protected $touches = [];

	public static function bootLogsActivity(): void {}
}
