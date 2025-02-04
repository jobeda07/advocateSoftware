<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::create(['name' => 'superAdmin', 'guard_name' => 'web']);
        $managerRole = Role::create(['name' => 'manager', 'guard_name' => 'web']);

        $permissions_data = [
            [
                'module_name' => 'Dashboard',
                'guard_name' => 'web',
                'permissions' => ['dashboard-show']
            ],
            [
                'module_name' => 'Visitor', 
                'guard_name' => 'web',
                'permissions' => [
                    'visitor-create',
                    'visitor-list',
                    'visitor-edit',
                    'visitor-delete',
                ]
            ],
            [
                'module_name' => 'Client',
                'guard_name' => 'web',
                'permissions' => [
                    'client-create',
                    'client-list',
                    'client-show',
                    'client-edit',
                    'client-delete',
                    'client-report'
                ]
            ],
            [
                'module_name' => 'Case',
                'guard_name' => 'web',
                'permissions' => [
                    'case-create',
                    'case-list',
                    'case-show',
                    'case-edit',
                    'case-delete'
                ]
            ],
            [
                'module_name' => 'Hearing',
                'guard_name' => 'web',
                'permissions' => [
                    'hearing-create',
                    'hearing-list',
                    'hearing-edit',
                    'hearing-delete',
                    'hearing-inform-message',
                ]
            ],
            [
                'module_name' => 'CaseFee',
                'guard_name' => 'web',
                'permissions' => [
                    'caseFee-create',
                    'caseFee-list',
                    'caseFee-edit',
                    'caseFee-delete',
                ]
            ],
            [
                'module_name' => 'ExtraCaseFee',
                'guard_name' => 'web',
                'permissions' => [
                    'extraCaseFee-create',
                    'extraCaseFee-list',
                    'extraCaseFee-edit',
                    'extraCaseFee-delete',
                ]
            ],
            [
                'module_name' => 'Expense',
                'guard_name' => 'web',
                'permissions' => [
                    'expense-create',
                    'expense-list',
                    'expense-edit',
                    'expense-delete',
                ]
            ],
            [
                'module_name' => 'CaseTask',
                'guard_name' => 'web',
                'permissions' => [
                    'caseTask-create',
                    'caseTask-list',
                    'caseTask-edit',
                    'caseTask-delete',
                    'caseTask-show',
                    'caseTask-addProgress',
                ]
            ],
            [
                'module_name' => 'ToDoList',
                'guard_name' => 'web',
                'permissions' => [
                    'toDoList-create',
                    'toDoList-list',
                    'toDoList-edit',
                    'toDoList-delete',
                ]
            ],
            [
                'module_name' => 'Setting',
                'guard_name' => 'web',
                'permissions' => [
                    'caseStage',
                    'caseCategory',
                    'caseType',
                    'expenseCategory',
                    'clientType',
                    'caseSection',
                ]
            ],
            [
                'module_name' => 'PortFolio',
                'guard_name' => 'web',
                'permissions' => [
                    'homeAbout',
                    'service',
                    'testimonial',
                    'contactUs',
                ]
            ],
            [
                'module_name' => 'Employee',
                'guard_name' => 'web',
                'permissions' => [
                    'employee.list',
                    'employee.add',
                    'employee.edit',
                    'employee.delete',
                ]
            ],
            [
                'module_name' => 'Access-Control-Manage',
                'guard_name' => 'web',
                'permissions' => [
                    'role.list',
                    'role.add',
                    'role.edit',
                    'role.delete',
                ]
            ],
        ];
        foreach ($permissions_data as $permission_data) {
            $module_name = $permission_data['module_name'];
            $guard_name = $permission_data['guard_name'];

            foreach ($permission_data['permissions'] as $permission) {
                Permission::create([
                    'name' => $permission,
                    'guard_name' => $guard_name,
                    'module_name' => $module_name
                ]);
            }
        }


        //give all permission to superadmin
        $superAdminRole->syncPermissions(Permission::all());

        $superAdmin = User::Create(
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'phone' => '01755555555',
                'password' => Hash::make('12345678'),
               // 'username' => 'superadmin',
                'address' => 'Dhaka, Bangladesh'
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        $manager = User::Create(
            [
                'name' => 'Manager',
                'email' => 'manager@gmail.com',
                'phone' => '017555554445',
                'password' => Hash::make('12345678'),
               // 'username' => 'manager',
                'address' => 'Dhaka, Bangladesh'
            ]
        );
        $manager->assignRole($managerRole);
    }
}
