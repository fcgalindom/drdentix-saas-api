<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Dentist;
use App\Models\DentistProcedure;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    private static array $branchTemplates = [
        ['Sede Principal', 'Medellín'],
        ['Sede Norte', 'Bogotá'],
        ['Sede Sur', 'Cali'],
        ['Sede Centro', 'Barranquilla'],
    ];

    private static array $procedureNames = [
        'Limpieza dental', 'Extracción simple', 'Extracción quirúrgica',
        'Endodoncia unirradicular', 'Endodoncia multirradicular',
        'Ortodoncia - control mensual', 'Implante dental unitario',
        'Corona de porcelana', 'Puente fijo (por unidad)',
        'Blanqueamiento dental', 'Carillas de porcelana',
        'Periodoncia - raspaje y alisado', 'Radiografía panorámica',
        'Radiografía periapical', 'Selladores dentales',
        'Prótesis total removible', 'Prótesis parcial removible',
        'Cirugía de terceros molares', 'Tratamiento de conducto',
        'Empaste con resina', 'Empaste con amalgama',
        'Revisión general', 'Brackets metálicos - colocación',
        'Brackets estéticos - colocación', 'Férula de descarga',
        'Gingivectomía', 'Apicectomía', 'Injerto óseo',
        'Elevación de seno maxilar', 'Fluorización', 'Calza dental',
        'Exodoncia', 'Cirugía periodontal', 'Rehabilitación oral',
        'Diseño de sonrisa', 'Perfilamiento labial', 'Toxina botulínica',
        'Ácido hialurónico', 'Guarda oclusal', 'Placa de relajación',
        'Cementación de brackets', 'Retiro de brackets',
        'Controles de ortodoncia', 'Mantenedor de espacio',
        'Pulpotomía', 'Pulpectomía', 'Coronas en acero',
        'Resinas preventivas', 'Detartraje supragingival', 'Aplicación de flúor',
    ];

    private static array $activePrinciples = [
        'Amoxicilina', 'Ibuprofeno', 'Paracetamol', 'Clorhexidina', 'Lidocaína',
        'Naproxeno', 'Diclofenaco', 'Ketorolaco', 'Tramadol', 'Metronidazol',
        'Clindamicina', 'Azitromicina', 'Doxiciclina', 'Fluoruro de sodio',
        'Povidona yodada', 'Hidróxido de calcio', 'Eugenol', 'Formocresol',
        'Hipoclorito de sodio', 'Ácido fosfórico', 'Resina compuesta',
        'Ionómero de vidrio', 'Sulfato de calcio', 'Alginato', 'Yeso dental',
    ];

    public function run(): void
    {
        $company1 = Company::create([
            'name' => 'Dentix Colombia',
            'email' => 'info@dentix.com.co',
            'phone' => '+57 601 744 1234',
            'address' => 'Carrera 43A # 1A Sur - 45, Medellín',
            'city' => 'Medellín',
            'state' => 'Activo',
        ]);

        $company2 = Company::create([
            'name' => 'Clínica Dental del Sur',
            'email' => 'info@dentaldelsur.com',
            'phone' => '+57 602 555 6789',
            'address' => 'Calle 5 # 34 - 12, Cali',
            'city' => 'Cali',
            'state' => 'Activo',
        ]);

        $this->seedCompany($company1, 0);
        $this->seedCompany($company2, 1);
    }

    private function seedCompany(Company $company, int $index): void
    {
        $cid = $company->id;

        $branches = $this->seedBranches($cid, $index);
        $usersByType = $this->seedUsers($cid);
        $dentists = $this->seedDentists($cid, $usersByType['dentists']);
        $patients = $this->seedPatients($cid, $usersByType['patients']);
        $procedures = $this->seedProcedures($cid);
        $dentistProcedures = $this->seedDentistProcedures($cid, $dentists, $procedures);
        $this->seedSchedules($cid, $dentists);
        $this->seedAppointments($cid, $branches, $patients, $dentistProcedures);
        $this->seedProducts($cid);
        $this->seedPromotions($cid);
    }

    private function seedBranches(int $cid, int $index): array
    {
        $branches = [];
        $offset = $index * 2;

        for ($i = 0; $i < 2; $i++) {
            $t = self::$branchTemplates[$offset + $i];
            $branches[] = Branch::create([
                'company_id' => $cid,
                'name' => $t[0],
                'address' => fake()->streetAddress(),
                'contact' => fake()->phoneNumber(),
                'city' => $t[1],
                'state' => 'Activo',
            ]);
        }

        return $branches;
    }

    private function seedUsers(int $cid): array
    {
        $result = ['admins' => [], 'dentists' => [], 'patients' => []];

        for ($i = 0; $i < 3; $i++) {
            $user = User::create([
                'company_id' => $cid,
                'document' => fake()->unique()->numerify('##########'),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'type_user' => 'Administrator',
                'birth' => fake()->date(),
                'photo' => '/images/default.jpg',
                'state' => 'Activo',
            ]);
            $user->assignRole('Administrador');
            $result['admins'][] = $user;
        }

        for ($i = 0; $i < 8; $i++) {
            $user = User::create([
                'company_id' => $cid,
                'document' => fake()->unique()->numerify('##########'),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'type_user' => 'Dentist',
                'birth' => fake()->date(),
                'photo' => '/images/default.jpg',
                'state' => 'Activo',
            ]);
            $user->assignRole('Dentist');
            $result['dentists'][] = $user;
        }

        for ($i = 0; $i < 14; $i++) {
            $user = User::create([
                'company_id' => $cid,
                'document' => fake()->unique()->numerify('##########'),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'type_user' => 'Patient',
                'birth' => fake()->date(),
                'photo' => '/images/default.jpg',
                'state' => 'Activo',
            ]);
            $user->assignRole('Patient');
            $result['patients'][] = $user;
        }

        return $result;
    }

    private function seedDentists(int $cid, array $users): array
    {
        $dentists = [];

        foreach ($users as $user) {
            $dentists[] = Dentist::create([
                'company_id' => $cid,
                'name' => 'Dr. '.fake()->firstName().' '.fake()->lastName(),
                'city' => fake()->randomElement(['Medellín', 'Bogotá', 'Cali', 'Barranquilla']),
                'id_user' => $user->id,
            ]);
        }

        return $dentists;
    }

    private function seedPatients(int $cid, array $users): array
    {
        $patients = [];

        foreach ($users as $user) {
            $patients[] = Patient::create([
                'company_id' => $cid,
                'name' => fake()->firstName().' '.fake()->lastName(),
                'city' => fake()->randomElement(['Medellín', 'Bogotá', 'Cali', 'Barranquilla']),
                'telephone' => fake()->phoneNumber(),
                'id_user' => $user->id,
            ]);
        }

        return $patients;
    }

    private function seedProcedures(int $cid): array
    {
        $procedures = [];
        $durations = [15, 20, 30, 30, 45, 45, 60, 60, 60, 90, 90, 120, 120];

        for ($i = 0; $i < 25; $i++) {
            $procedures[] = Procedure::create([
                'company_id' => $cid,
                'name' => self::$procedureNames[$i],
                'duration' => $durations[$i % count($durations)],
                'state' => 'Activo',
            ]);
        }

        return $procedures;
    }

    private function seedDentistProcedures(int $cid, array $dentists, array $procedures): array
    {
        $dentistProcedures = [];

        foreach ($dentists as $dentist) {
            $assigned = fake()->randomElements($procedures, fake()->numberBetween(4, 10));

            foreach ($assigned as $procedure) {
                $dentistProcedures[] = DentistProcedure::create([
                    'company_id' => $cid,
                    'dentist_id' => $dentist->id,
                    'procedure_id' => $procedure->id,
                ]);
            }
        }

        return $dentistProcedures;
    }

    private function seedSchedules(int $cid, array $dentists): void
    {
        foreach ($dentists as $dentist) {
            for ($day = 1; $day <= 6; $day++) {
                if ($day === 6 && fake()->boolean(30)) {
                    continue;
                }

                Schedule::create([
                    'company_id' => $cid,
                    'hour_start' => '08:00',
                    'hour_end' => '18:00',
                    'break' => true,
                    'break_start' => fake()->randomElement(['12:00', '13:00']),
                    'break_end' => fake()->randomElement(['13:00', '14:00']),
                    'attend' => true,
                    'day' => $day,
                    'dentist_id' => $dentist->id,
                ]);
            }
        }
    }

    private function seedAppointments(int $cid, array $branches, array $patients, array $dentistProcedures): void
    {
        $states = ['Activo', 'Activo', 'Activo', 'Recordado', 'Pagado', 'Pagado', 'Cancelado', 'No asistio'];
        $hours = ['07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30',
            '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30',
            '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', ];

        for ($i = 0; $i < 50; $i++) {
            $state = fake()->randomElement($states);
            $dp = fake()->randomElement($dentistProcedures);
            $pay = $state === 'Pagado' ? fake()->numberBetween(50_000, 2_000_000) : 0;

            $appointment = Appointment::create([
                'company_id' => $cid,
                'day' => fake()->dateTimeBetween('-6 months', '+3 months')->format('Y-m-d'),
                'hour' => fake()->randomElement($hours),
                'branch_id' => fake()->randomElement($branches)->id,
                'patient_id' => fake()->randomElement($patients)->id,
                'dentist_procedure_id' => $dp->id,
                'state' => $state,
                'pay' => $pay,
                'type_state' => fake()->numberBetween(0, 2),
            ]);

            if ($state === 'Pagado') {
                Invoice::create([
                    'company_id' => $cid,
                    'price' => $pay,
                    'procedure_id' => $dp->procedure_id,
                    'appointment_id' => $appointment->id,
                ]);
            }
        }
    }

    private function seedProducts(int $cid): void
    {
        $forms = ['Tableta', 'Cápsula', 'Jarabe', 'Suspensión', 'Solución inyectable',
            'Crema', 'Gel', 'Enjuague bucal', 'Barniz', 'Polvo'];
        $presentations = ['Caja x 10', 'Caja x 20', 'Caja x 30', 'Frasco x 60ml',
            'Frasco x 120ml', 'Tubo x 50g'];
        $concentrations = ['500mg', '200mg', '100mg', '50mg', '250mg', '0.12%', '2%', '5%'];

        for ($i = 0; $i < 25; $i++) {
            Product::create([
                'company_id' => $cid,
                'active_principle' => self::$activePrinciples[$i],
                'concentration' => fake()->randomElement($concentrations),
                'amount' => fake()->numberBetween(10, 500),
                'pharmaceutical_form' => fake()->randomElement($forms),
                'commercial_presentation' => fake()->randomElement($presentations),
                'medication_unit' => fake()->randomElement(['Unidad', 'Mililitro', 'Gramo']),
                'batch' => strtoupper(fake()->bothify('LOTE-###-??')),
                'health_register_invima' => 'INVIMA-'.fake()->numerify('####-######'),
                'expiration_date' => fake()->dateTimeBetween('+6 months', '+3 years')->format('Y-m-d'),
                'semaphore' => fake()->randomElement(['verde', 'amarillo', 'rojo']),
                'date_of_admission' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            ]);
        }
    }

    private function seedPromotions(int $cid): void
    {
        for ($i = 0; $i < 5; $i++) {
            Promotion::create([
                'company_id' => $cid,
                'date_start' => fake()->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
                'date_end' => fake()->dateTimeBetween('+1 month', '+4 months')->format('Y-m-d'),
                'details' => fake()->sentence(10),
                'discount' => fake()->numberBetween(5, 50),
                'limit_patients' => fake()->numberBetween(10, 100),
                'status' => 1,
            ]);
        }
    }
}
