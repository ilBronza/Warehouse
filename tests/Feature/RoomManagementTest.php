<?php

namespace IlBronza\Warehouse\Tests\Feature;

use IlBronza\Clients\Models\Destination;
use IlBronza\Warehouse\Models\Room\Room;
use IlBronza\Warehouse\Models\Room\RoomCategory;
use IlBronza\Warehouse\Models\Room\RoomType;
use IlBronza\Warehouse\Models\Structure\Structure;
use IlBronza\Warehouse\WarehouseServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;

if (! function_exists('cconfig'))
	require_once dirname((new \ReflectionClass(\IlBronza\CRUD\CRUDServiceProvider::class))->getFileName()) . '/helpers.php';

foreach ([
	\App\Http\Controllers\Controller::class => 'Http/Controllers/Controller.php',
	\App\Models\User::class => 'Models/User.php',
	\App\Providers\Helpers\MenuItems::class => 'Providers/Helpers/MenuItems.php',
] as $class => $file) {
	if (! class_exists($class))
		require_once __DIR__ . '/../Fixtures/App/' . $file;
}

class RoomManagementTest extends TestCase
{
	protected function getApplicationBasePath()
	{
		$path = __DIR__ . '/../.runtime/app';
		if (! is_dir($path))
			(new \Illuminate\Filesystem\Filesystem)->copyDirectory(parent::getApplicationBasePath(), $path);

		return $path;
	}

	protected function getPackageProviders($app)
	{
		return [
			\Spatie\Permission\PermissionServiceProvider::class,
			\IlBronza\AccountManager\AccountManagerServiceProvider::class,
			\IlBronza\Ukn\UknServiceProvider::class,
			\IlBronza\Menu\MenuServiceProvider::class,
			\IlBronza\UikitTemplate\UikitTemplateServiceProvider::class,
			\IlBronza\Buttons\ButtonsServiceProvider::class,
			\IlBronza\FormField\FormFieldServiceProvider::class,
			\IlBronza\Form\FormServiceProvider::class,
			\IlBronza\Datatables\DatatablesServiceProvider::class,
			\IlBronza\CRUD\CRUDServiceProvider::class,
			WarehouseServiceProvider::class,
		];
	}

	protected function defineEnvironment($app)
	{
		$app['config']->set('clients', require dirname(__DIR__, 3) . '/Clients/config/clients.php');
		$app['config']->set('app.key', 'base64:' . base64_encode(str_repeat('r', 32)));
		$app['config']->set('app.locale', 'it');
		$app['config']->set('app.template', \IlBronza\UikitTemplate\UikitTemplate::class);
		$app['config']->set('view.compiled', $app->storagePath('framework/views'));
		$app['config']->set('menu.serviceProviders', []);
		$app['config']->set('menu.childrenPerColumn', 8);
		$app['config']->set('database.default', 'testing');
		$app['config']->set('database.connections.testing', [
			'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
		]);
		$app['config']->set('auth.providers.users.model', \IlBronza\AccountManager\Models\User::class);
		$app['config']->set('activitylog.enabled', false);
		$app['config']->set('activitylog.default_auth_driver', null);
		$app['config']->set('crud.broadcast.enabled', false);
	}

	protected function migration()
	{
		return require dirname(__DIR__, 2) . '/database/migrations/2026_10_19_120000_create_structures_and_rooms_tables.php';
	}

	protected function setUp() : void
	{
		parent::setUp();
		$this->withoutMiddleware();

		Schema::create(config('clients.models.destination.table'), function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->string('name');
			$table->string('slug');
			$table->softDeletes();
			$table->timestamps();
		});
		$this->migration()->up();

		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('email');
			$table->string('password');
			$table->boolean('active')->default(true);
			$table->timestamps();
		});
		Schema::create('users__userdata', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->unsignedBigInteger('user_id');
			$table->string('name')->nullable();
			$table->string('surname')->nullable();
			$table->softDeletes();
			$table->timestamps();
		});
		$permissionRoot = dirname((new \ReflectionClass(\Spatie\Permission\PermissionServiceProvider::class))->getFileName(), 2);
		(require $permissionRoot . '/database/migrations/create_permission_tables.php.stub')->up();
		Schema::create('datatabledata', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('user_id');
			$table->string('key');
			$table->json('data');
			$table->timestamps();
		});

		$user = \IlBronza\AccountManager\Models\User::create([
			'name' => 'Tester', 'email' => 'rooms@example.com', 'password' => 'secret', 'active' => true,
		]);
		$user->assignRole(\Spatie\Permission\Models\Role::findOrCreate('superadmin', 'web'));
		$this->actingAs($user);
	}

	protected function url(string $resource, string $action, array $parameters = []) : string
	{
		return app('warehouse')->route($resource . '.' . $action, $parameters);
	}

	protected function fixtures() : array
	{
		$category = RoomCategory::create(['name' => 'Direzionale']);
		$type = $category->roomTypes()->create(['name' => 'Ufficio']);
		$structure = Structure::create(['name' => 'Sede Milano']);

		return [$category, $type, $structure];
	}

	protected function roomData(RoomType $type, Structure $structure) : array
	{
		return ['name' => 'Ufficio 01', 'room_type_id' => $type->getKey(), 'structure_id' => $structure->getKey()];
	}

	public function test_catalogs_can_be_created_through_the_crud() : void
	{
		$this->post($this->url('structures', 'store'), ['name' => 'Sede Milano'])->assertRedirect();
		$this->post($this->url('roomCategories', 'store'), ['name' => 'Direzionale'])->assertRedirect();
		$category = RoomCategory::firstOrFail();
		$this->post($this->url('roomTypes', 'store'), [
			'name' => 'Ufficio', 'room_category_id' => $category->getKey(),
		])->assertRedirect();

		$this->assertSame('Direzionale', RoomType::firstOrFail()->roomCategory->name);
		$this->assertDatabaseCount('warehouse__structures', 1);
	}

	public function test_rooms_share_a_generic_type_across_structures_without_destinations() : void
	{
		[$category, $type, $firstStructure] = $this->fixtures();
		$secondStructure = Structure::create(['name' => 'Sede Roma']);
		foreach ([$firstStructure, $secondStructure] as $structure)
			$this->post($this->url('rooms', 'store'), $this->roomData($type, $structure))->assertRedirect();

		$this->assertSame(2, $type->rooms()->count());
		$this->assertSame(2, $category->rooms()->count());
		$this->assertSame(1, $firstStructure->rooms()->count());
		$this->assertSame(1, $secondStructure->rooms()->count());
		$this->assertNull(Room::firstOrFail()->getDestination());
		$this->assertDatabaseCount('warehouse__room_types', 1);
	}

	public function test_a_destination_can_have_multiple_rooms_and_can_be_cleared_on_update() : void
	{
		[, $type, $structure] = $this->fixtures();
		$destination = Destination::create(['name' => 'Sede cliente']);
		$data = $this->roomData($type, $structure) + ['destination_id' => $destination->getKey(), 'surface_sqm' => 42.5];
		foreach (['Ufficio 01', 'Ufficio 02'] as $name)
			$this->post($this->url('rooms', 'store'), array_replace($data, ['name' => $name]))->assertRedirect();

		$this->assertSame(2, $destination->rooms()->count());
		$room = Room::firstOrFail();
		$this->assertTrue($room->getDestination()->is($destination));
		$this->assertSame('42.50', $room->surface_sqm);
		$this->put($this->url('rooms', 'update', [$room]), array_replace($data, [
			'name' => 'Ufficio aggiornato', 'destination_id' => null,
		]))->assertRedirect();
		$this->assertNull($room->fresh()->getDestination());
		$this->assertSame(1, $destination->rooms()->count());
	}

	public function test_required_and_invalid_room_links_are_rejected() : void
	{
		[, $type, $structure] = $this->fixtures();
		$data = $this->roomData($type, $structure);
		foreach (['structure_id', 'room_type_id', 'destination_id'] as $field)
			$this->postJson($this->url('rooms', 'store'), array_replace($data, [$field => (string) Str::uuid()]))
				->assertUnprocessable()->assertJsonValidationErrors($field);

		$this->postJson($this->url('rooms', 'store'), ['name' => 'Senza relazioni'])
			->assertUnprocessable()->assertJsonValidationErrors(['structure_id', 'room_type_id']);
		$this->postJson($this->url('rooms', 'store'), $data + ['surface_sqm' => -1])
			->assertUnprocessable()->assertJsonValidationErrors('surface_sqm');
		$this->assertDatabaseCount('warehouse__rooms', 0);
	}

	public function test_soft_deleted_destinations_and_types_cannot_be_assigned() : void
	{
		[, $type, $structure] = $this->fixtures();
		$destination = Destination::create(['name' => 'Destinazione archiviata']);
		$destination->delete();
		$data = $this->roomData($type, $structure);
		$this->postJson($this->url('rooms', 'store'), $data + ['destination_id' => $destination->getKey()])
			->assertUnprocessable()->assertJsonValidationErrors('destination_id');
		$type->delete();
		$this->postJson($this->url('rooms', 'store'), $data)
			->assertUnprocessable()->assertJsonValidationErrors('room_type_id');
	}

	public function test_type_requires_an_existing_active_category() : void
	{
		$this->postJson($this->url('roomTypes', 'store'), ['name' => 'Ufficio'])
			->assertUnprocessable()->assertJsonValidationErrors('room_category_id');
		$category = RoomCategory::create(['name' => 'Categoria archiviata']);
		$category->delete();
		$this->postJson($this->url('roomTypes', 'store'), ['name' => 'Ufficio', 'room_category_id' => $category->getKey()])
			->assertUnprocessable()->assertJsonValidationErrors('room_category_id');
	}

	public function test_used_structures_types_and_categories_cannot_be_deleted() : void
	{
		[$category, $type, $structure] = $this->fixtures();
		$room = Room::create($this->roomData($type, $structure));
		foreach ([['structures', $structure], ['roomTypes', $type], ['roomCategories', $category]] as [$resource, $model])
			$this->deleteJson($this->url($resource, 'destroy', [$model]))->assertUnprocessable();

		$this->assertFalse($room->fresh()->trashed());
		$this->delete($this->url('rooms', 'destroy', [$room]))->assertRedirect();
		$this->assertSoftDeleted('warehouse__rooms', ['id' => $room->getKey()]);
		$this->delete($this->url('roomTypes', 'destroy', [$type]))->assertRedirect();
		$this->delete($this->url('roomCategories', 'destroy', [$category]))->assertRedirect();
		$this->delete($this->url('structures', 'destroy', [$structure]))->assertRedirect();
	}

	public function test_force_deleting_a_destination_preserves_its_rooms() : void
	{
		[, $type, $structure] = $this->fixtures();
		$destination = Destination::create(['name' => 'Sede cliente']);
		$room = Room::create($this->roomData($type, $structure) + ['destination_id' => $destination->getKey()]);
		// Exercise the foreign key independently of Clients' media cleanup observers.
		DB::table($destination->getTable())->where('id', $destination->getKey())->delete();

		$this->assertNotNull($room->fresh());
		$this->assertNull($room->fresh()->destination_id);
	}

	public function test_crud_pages_render_and_missing_models_return_404() : void
	{
		[$category, $type, $structure] = $this->fixtures();
		$room = Room::create($this->roomData($type, $structure));
		foreach ([['structures', $structure], ['roomCategories', $category], ['roomTypes', $type], ['rooms', $room]] as [$resource, $model]) {
			foreach (['index', 'create'] as $action)
				$this->get($this->url($resource, $action))->assertOk();
			foreach (['show', 'edit'] as $action)
				$this->get($this->url($resource, $action, [$model]))->assertOk();
			$this->get($this->url($resource, 'show', [(string) Str::uuid()]))->assertNotFound();
		}
	}

	public function test_migration_rolls_back_and_respects_configured_table_names() : void
	{
		$this->migration()->down();
		foreach (['structure', 'roomCategory', 'roomType', 'room'] as $model) {
			$this->assertFalse(Schema::hasTable(config("warehouse.models.{$model}.table")));
			config(["warehouse.models.{$model}.table" => 'custom_' . Str::snake($model)]);
		}
		$this->migration()->up();
		[$category, $type, $structure] = $this->fixtures();
		$room = Room::create($this->roomData($type, $structure));

		$this->assertTrue($room->structure->is($structure));
		$this->assertTrue($room->roomType->roomCategory->is($category));
		$this->assertSame(1, $category->rooms()->count());
	}

	public function test_new_models_are_merged_into_existing_published_configuration() : void
	{
		config(['warehouse.models' => [
			'delivery' => ['table' => 'existing_deliveries'],
			'room' => ['table' => 'custom_rooms'],
		]]);
		(new WarehouseServiceProvider($this->app))->register();

		$this->assertSame('existing_deliveries', config('warehouse.models.delivery.table'));
		$this->assertSame('custom_rooms', config('warehouse.models.room.table'));
		$this->assertSame(Room::class, config('warehouse.models.room.class'));
		$this->assertSame(Structure::class, config('warehouse.models.structure.class'));
		$this->assertNotNull(config('warehouse.models.room.controllers.store'));
	}

	public function test_relations_respect_configured_project_models() : void
	{
		config([
			'warehouse.models.room.class' => ProjectRoom::class,
			'clients.models.destination.class' => ProjectDestination::class,
		]);
		[$category, $type, $structure] = $this->fixtures();
		$destination = ProjectDestination::create(['name' => 'Sede cliente']);
		$room = ProjectRoom::create($this->roomData($type, $structure) + ['destination_id' => $destination->getKey()]);

		$this->assertInstanceOf(ProjectDestination::class, $room->destination);
		foreach ([$structure, $type, $category, $destination] as $parent)
			$this->assertInstanceOf(ProjectRoom::class, $parent->rooms()->firstOrFail());
	}
}

class ProjectRoom extends Room {}
class ProjectDestination extends Destination {}
