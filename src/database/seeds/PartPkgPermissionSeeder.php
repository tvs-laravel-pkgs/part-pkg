<?php
namespace Abs\PartPkg\Database\Seeds;

use App\Permission;
use Illuminate\Database\Seeder;

class PartPkgPermissionSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$permissions = [
			
			//Parts
			[
				'display_order' => 99,
				'parent' => null,
				'name' => 'parts',
				'display_name' => 'Parts',
			],
			[
				'display_order' => 1,
				'parent' => 'parts',
				'name' => 'add-part',
				'display_name' => 'Add',
			],
			[
				'display_order' => 2,
				'parent' => 'parts',
				'name' => 'edit-part',
				'display_name' => 'Edit',
			],
			[
				'display_order' => 3,
				'parent' => 'parts',
				'name' => 'delete-part',
				'display_name' => 'Delete',
			],

			
		];
		Permission::createFromArrays($permissions);
	}
}