<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);

        // // USUARIOS - DAR DE ALTA
        // // 1er USUARIO --- CONTADOR
        // \DB::table('users')->insert([
        //     0 => [
        //         'role'      => 'administrator',
        //         'name'      => 'Genaro Perez', 
        //         'user_name' => 'genaro28',
        //         'email'     => 'genaro@gmail.com',
        //         'password'  => bcrypt('436276G')
        //     ],
        //     1 => [
        //         'role'      => 'administrator',
        //         'name'      => 'Jorge', 
        //         'user_name' => 'jorge25',
        //         'email'     => 'jorge@gmail.com',
        //         'password'  => bcrypt('J567433')
        //     ],
        //     2 => [
        //         'role'      => 'manager',
        //         'name'      => 'Yoselyn', 
        //         'user_name' => 'yoselyn25',
        //         'email'     => 'yoselyn@gmail.com',
        //         'password'  => bcrypt('Y9673N7')
        //     ],
        //     3 => [
        //         'role'      => 'reviewer',
        //         'name'      => 'Usuario', 
        //         'user_name' => 'usuario',
        //         'email'     => 'usuario@gmail.com',
        //         'password'  => bcrypt('usuario')
        //     ]
        // ]);

        // // ESCUELAS - DAR DE ALTA
        // \DB::table('schools')->insert([
        //     0 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE CAMPECHE' ],
        //     1 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE CANCUN' ],
        //     2 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE CIUDAD JUAREZ' ],
        //     3 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE CUAUTLA' ],
        //     4 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE CULIACAN' ],
        //     5 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE ESTUDIOS SUP. DE COACALCO' ],
        //     6 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE ESTUDIOS SUP. DE LOS CABOS' ],
        //     7 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE HUIMANGUILLO' ],
        //     8 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE IZTAPALAPA' ],
        //     9 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE LINARES' ],
        //     10 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE MERIDA' ],
        //     11 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE MILPA ALTA' ],
        //     12 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE MINATITLAN' ],
        //     13 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE OAXACA' ],
        //     14 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE REYNOSA' ],
        //     15 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE ROQUE' ],
        //     16 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE VILLAHERMOSA' ],
        //     17 => [ 'name'      => 'INSTITUTO TECNOLOGICO DE ZITACUARO' ],
        //     18 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE ACAYUCAN' ],
        //     19 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE APATZINGAN' ],
        //     20 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE CENTLA' ],
        //     21 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE COALCOMAN' ],
        //     22 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE ESCARCEGA' ],
        //     23 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE LA COSTA CHICA' ],
        //     24 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE SALVATIERRA' ],
        //     25 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE SAN ANDRES TUXTLA' ],
        //     26 => [ 'name'      => 'INSTITUTO TECNOLOGICO SUP. DE ZACAPOAXTLA' ],
        //     27 => [ 'name'      => 'TECNOLOGICO DE ESTUDIOS SUPERIORES DE CUAUTITLAN IZCALLI' ],
        //     28 => [ 'name'      => 'VOCACIONAL 7' ]
        // ]);

        // \DB::table('users')->insert([
        //     0 => [
        //         'role'      => 'reviewer',
        //         'name'      => 'Ana Iturbe', 
        //         'user_name' => 'ana-102',
        //         'email'     => 'ana-iturbe@gmail.com',
        //         'password'  => bcrypt('4n4262')
        //     ],
        //     1=> [
        //         'role'      => 'reviewer',
        //         'name'      => 'Cecilia', 
        //         'user_name' => 'cecilia-11',
        //         'email'     => 'cecilia@gmail.com',
        //         'password'  => bcrypt('c3c1112')
        //     ]
        // ]);

        // \DB::table('editoriales')->insert([
        //     0 => [ 'name'      => 'CAMBRIDGE' ],
        //     1 => [ 'name'      => 'CENGAGE' ],
        //     2 => [ 'name'      => 'EXPRESS PUBLISHING' ],
        //     3 => [ 'name'      => 'MAJESTIC EDUCATION' ],
        //     4 => [ 'name'      => 'MCGRAW HILL' ],
        //     5 => [ 'name'      => 'RICHMOND' ],
        // ]);

        // \DB::table('users')->insert([
        //     0 => [
        //         'role'      => 'reviewer',
        //         'name'      => 'Berenice', 
        //         'user_name' => 'bere-815',
        //         'email'     => 'berenice@gmail.com',
        //         'password'  => bcrypt('b3r368')
        //     ]
        // ]);

        // \DB::table('users')->insert([
        //     // 0 => [
        //     //     'role'      => 'capturist',
        //     //     'name'      => 'Armando', 
        //     //     'user_name' => 'armando-6',
        //     //     'email'     => 'armando@gmail.com',
        //     //     'password'  => bcrypt('armando-42')
        //     // ]
        //     0 => [
        //         'role'      => 'capturist',
        //         'name'      => 'Axel', 
        //         'user_name' => 'axel-8',
        //         'email'     => 'axel@gmail.com',
        //         'password'  => bcrypt('axel-32')
        //     ],
        //     1 => [
        //         'role'      => 'capturist',
        //         'name'      => 'Angel', 
        //         'user_name' => 'angel-4',
        //         'email'     => 'angel@gmail.com',
        //         'password'  => bcrypt('angel-20')
        //     ]
        // ]);

        // \DB::table('users')->insert([
        //     0 => [
        //         'role'      => 'reviewer',
        //         'name'      => 'Gabriela Zamora', 
        //         'user_name' => 'gabriela-825',
        //         'email'     => 'gabriela@gmail.com',
        //         'password'  => bcrypt('648r1314')
        //     ]
        // ]);

        // \DB::table('users')->insert([
        //     0 => [
        //         'role'      => 'reviewer',
        //         'name'      => 'Maricela Hernandez', 
        //         'user_name' => 'maricela-5',
        //         'email'     => 'maricela@gmail.com',
        //         'password'  => bcrypt('m4r1-25')
        //     ]
        // ]);

        // \DB::table('users')->insert([
        //     2 => [
        //         'role'      => 'manager',
        //         'name'      => 'Jenny', 
        //         'user_name' => 'jenny02',
        //         'email'     => 'jenny@gmail.com',
        //         'password'  => bcrypt('6J3nNy-2')
        //     ]
        // ]);

        // \DB::table('users')->insert([
        //     0 => [
        //         'role'      => 'reviewer',
        //         'name'      => 'Claudia', 
        //         'user_name' => 'claudia-7',
        //         'email'     => 'claudia@gmail.com',
        //         'password'  => bcrypt('c14ud14-14')
        //     ]
        // ]);

        // \DB::table('users')->insert([
        //     0 => [
        //         'role'      => 'reviewer',
        //         'name'      => 'Aida', 
        //         'user_name' => 'aida-3',
        //         'email'     => 'aida@gmail.com',
        //         'password'  => bcrypt('41d4-60')
        //     ]
        // ]);

        // \DB::table('users')->insert([
        //     0 => [
        //         'role'      => 'reviewer',
        //         'name'      => 'Ximena', 
        //         'user_name' => 'ximena-7',
        //         'email'     => 'ximena@gmail.com',
        //         'password'  => bcrypt('x1m3n4-275')
        //     ]
        // ]);

        // \DB::table('users')->insert([
        //     0 => [
        //         'role'      => 'reviewer',
        //         'name'      => 'Adriana', 
        //         'user_name' => 'adriana-8',
        //         'email'     => 'adriana@gmail.com',
        //         'password'  => bcrypt('4dr14n4-828')
        //     ]
        // ]);

        // \DB::table('users')->insert([
        //     0 => [
        //         'role'      => 'reviewer',
        //         'name'      => 'Karen', 
        //         'user_name' => 'karen-9',
        //         'email'     => 'karen@gmail.com',
        //         'password'  => bcrypt('K4r3N-268')
        //     ]
        // ]);

        // \DB::table('users')->insert([
        //     0 => [
        //         'role'      => '',
        //         'name'      => '', 
        //         'user_name' => '',
        //         'email'     => '',
        //         'password'  => bcrypt('')
        //     ]
        // ]);

        // \DB::table('users')->insert([
        //     0 => [ 'role'      => 'reviewer', 'name'      => 'Berenice', 'user_name' => 'berenice-ra', 'email'     => 'berenice@me-registers.com', 'password'  => bcrypt('xJ1631%') ],
        //     1 => [ 'role'      => 'reviewer', 'name'      => 'Gabriela', 'user_name' => 'gabriela-ra', 'email'     => 'gabriela@me-registers.com', 'password'  => bcrypt('lH1715&') ],
        //     2 => [ 'role'      => 'administrator', 'name'      => 'Genaro', 'user_name' => 'genaro-ra', 'email'     => 'genaro@me-registers.com', 'password'  => bcrypt('tR4195&') ],
        //     3 => [ 'role'      => 'manager', 'name'      => 'Jenny', 'user_name' => 'jenny-ra', 'email'     => 'jenny@me-registers.com', 'password'  => bcrypt('nF8954$') ],
        //     4 => [ 'role'      => 'manager', 'name'      => 'Yoselyn', 'user_name' => 'yoselyn-ra', 'email'     => 'yoselyn@me-registers.com', 'password'  => bcrypt('wN7558&') ],
        //     5 => [ 'role'      => 'reviewer', 'name'      => 'Ximena', 'user_name' => 'ximena-ra', 'email'     => 'ximena@me-registers.com', 'password'  => bcrypt('eW1053#') ]
        // ]);

        // \DB::table('users')->insert([
        //     0 => [ 
        //         'id'        => 18,
        //         'role'      => 'reviewer', 
        //         'name'      => 'Adriana', 
        //         'user_name' => 'adriana-ra', 
        //         'email'     => 'adriana@me-registers.com', 
        //         'password'  => bcrypt('zF8721%') ],
        // ]);

        \DB::table('users')->insert([
            0 => [
                'role'      => 'manager',
                'name'      => 'Yoselyn CM', 
                'user_name' => 'yoselyn-dtec',
                'email'     => 'yoselynmajestice@gmail.com',
                'password'  => bcrypt('pP2621&')
            ]
        ]);
    }
}
