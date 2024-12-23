<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Department\Models\Department;
use App\Modules\Equipement\Models\Equipement;
use App\Modules\ProfileGroup\Models\ProfileGroup;
use App\Modules\Role\Models\Role;
use App\Modules\Shift\Models\Shift;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $department_it = new Department();
        $department_it->name = "IT";
        $department_it->department_id = null;
        $department_it->save();

        $department_ops = new Department();
        $department_ops->name = "OPERATIONS";
        $department_ops->department_id = null;
        $department_ops->save();

        $department_qhsse = new Department();
        $department_qhsse->name = "QHSSE";
        $department_qhsse->department_id = null;
        $department_qhsse->save();

        $department_tech = new Department();
        $department_tech->name = "TECHNICAL";
        $department_tech->department_id = null;
        $department_tech->save();

        $department_finance = new Department();
        $department_finance->name = "FINANCE";
        $department_finance->department_id = null;
        $department_finance->save();

        $department_hr = new Department();
        $department_hr->name = "HR";
        $department_hr->department_id = null;
        $department_hr->save();




        $role_admin = new Role();
        $role_admin->name = "admin";
        $role_admin->department_id = $department_it->id;
        $role_admin->save();




        $role_driver = new Role();
        $role_driver->name = "driver";
        $role_driver->department_id = $department_ops->id;
        $role_driver->save();


        $role_am = new Role();
        $role_am->name = "am";
        $role_am->department_id = $department_ops->id;
        $role_am->save();


        $role_foremane = new Role();
        $role_foremane->name = "fm";
        $role_foremane->department_id = $department_ops->id;
        $role_foremane->save();


        $role_shiftManager = new Role();
        $role_shiftManager->name = "sm";
        $role_shiftManager->department_id = $department_ops->id;
        $role_shiftManager->save();


        $role_shiftManager = new Role();
        $role_shiftManager->name = "opsm";
        $role_shiftManager->department_id = $department_ops->id;
        $role_shiftManager->save();


        $role_shiftManager = new Role();
        $role_shiftManager->name = "em";
        $role_shiftManager->department_id = $department_ops->id;
        $role_shiftManager->save();




        $shift_a = new Shift();
        $shift_a->category = "A";
        $shift_a->save();



        $shift_b = new Shift();
        $shift_b->category = "B";
        $shift_b->save();


        $shift_c = new Shift();
        $shift_c->category = "C";
        $shift_c->save();


        $shift_d = new Shift();
        $shift_d->category = "D";
        $shift_d->save();


        $user = new User();
        $user->matricule="admin";
        $user->firstname="Fayssal";
        $user->lastname="OUREZZOUQ";
        $user->email="fayssal.ourezzouq@tangeralliance.com";
        $user->password="Initial123";
        $user->role_id=$role_admin->id;
        $user->save();


        $profileGroup_rtg = new ProfileGroup();
        $profileGroup_rtg->type = "rtg";
        $profileGroup_rtg->save();



        $profileGroup_sts = new ProfileGroup();
        $profileGroup_sts->type = "sts";
        $profileGroup_sts->save();


        $profileGroup_rs = new ProfileGroup();
        $profileGroup_rs->type = "rs";
        $profileGroup_rs->save();


        $profileGroup = new ProfileGroup();
        $profileGroup->type = "am";
        $profileGroup->save();




        $equipment = new Equipement();
        $equipment->matricule = "RTG01";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG02";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG03";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG04";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG05";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG06";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG07";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG08";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG09";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG10";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG11";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG12";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG13";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG14";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG15";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG16";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG17";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG18";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG19";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG20";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG21";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RTG22";
        $equipment->profile_group_id = $profileGroup_rtg->id;
        $equipment->status=1;
        $equipment->save();


        $equipment = new Equipement();
        $equipment->matricule = "STS01";
        $equipment->profile_group_id = $profileGroup_sts->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "STS01";
        $equipment->profile_group_id = $profileGroup_sts->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "STS02";
        $equipment->profile_group_id = $profileGroup_sts->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "STS03";
        $equipment->profile_group_id = $profileGroup_sts->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "STS04";
        $equipment->profile_group_id = $profileGroup_sts->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "STS05";
        $equipment->profile_group_id = $profileGroup_sts->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "STS06";
        $equipment->profile_group_id = $profileGroup_sts->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "STS07";
        $equipment->profile_group_id = $profileGroup_sts->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "STS08";
        $equipment->profile_group_id = $profileGroup_sts->id;
        $equipment->status=1;
        $equipment->save();


        $equipment = new Equipement();
        $equipment->matricule = "RS01";
        $equipment->profile_group_id = $profileGroup_rs->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RS02";
        $equipment->profile_group_id = $profileGroup_rs->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "RS03";
        $equipment->profile_group_id = $profileGroup_rs->id;
        $equipment->status=1;
        $equipment->save();

        $equipment = new Equipement();
        $equipment->matricule = "SBY";
        $equipment->profile_group_id = $profileGroup_rs->id;
        $equipment->status=1;
        $equipment->save();




    }
}
