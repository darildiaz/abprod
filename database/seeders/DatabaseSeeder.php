<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\user;
use App\Models\Category;
use App\Models\Center;
use App\Models\Customer;
use App\Models\Operator;
use App\Models\QuestionCategory;
use App\Models\Question;
use App\Models\Line;
use App\Models\Size;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $users = [
            ['name' => 'daril diaz', 'email' => 'darildiaz29@gmail.com', 'password' => Hash::make('12345678')],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
        $categories = [
            ['name' => 'Camiseta', 'order' => 1, 'is_important' => true],
            ['name' => 'Short', 'order' => 3, 'is_important' => true],
            ['name' => 'Camisilla', 'order' => 2, 'is_important' => true],
            ['name' => 'Media', 'order' => 4, 'is_important' => true],
            ['name' => 'Camiseta Manga largas', 'order' => 5, 'is_important' => true],
            ['name' => 'Botinera', 'order' => 6, 'is_important' => true],
            ['name' => 'Blusa Allegra', 'order' => 8, 'is_important' => false],
            ['name' => 'Camisa', 'order' => 9, 'is_important' => false],
            ['name' => 'Remera', 'order' => 10, 'is_important' => false],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
        $lines = [
            ['code' => 'C','name' => 'Eco'],
            ['code' => 'B','name' => 'Basic'],
            ['code' => 'S','name' => 'Estandar'],
            ['code' => 'F','name' => 'Oficial'],
            ['code' => 'A','name' => 'Profesional'],
            ['code' => 'R','name' => 'Semi Profesional'],
            ['code' => 'PR','name' => 'Premium'],
            ['code' => 'E','name' => 'Elite'],

        ];
        foreach ($lines as $line) {
            Line::create($line);
        }
        $centers = [
            ['name' => 'Diagramacion', 'level' => 1],
            ['name' => 'Impresion', 'level' => 2],
            ['name' => 'Sublimacion', 'level' => 3],
            ['name' => 'Corte', 'level' => 4],
            ['name' => 'Taller', 'level' => 5],
            ['name' => 'Plancha', 'level' => 6],
            ['name' => 'Vinilo y terminado', 'level' => 7],
            ['name' => 'Bordado', 'level' => 8],
            ['name' => 'Empaque', 'level' => 9],
        ];

        foreach ($centers as $center) {
            Center::create($center);
        }
        $customers = [
            ['nif' => '5192306', 'name' => 'Daril Diaz', 'address' => 'Horqueta', 'phone' => '0972813605', 'user_id' => 1],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
        $operators = [
            ['name' => 'Tobias', 'position' => 'Diagramador', 'user_id' => 1, 'center_id' => 1],
        ];

        foreach ($operators as $operator) {
            Operator::create($operator);
        }
        $questionCategories = [
            ['name' => 'Deportivo'],
            ['name' => 'Escolar'],
            ['name' => 'Empresarial'],
        ];

        foreach ($questionCategories as $category) {
            QuestionCategory::create($category);
        }
        $questions = [
            ['text' => 'Color base', 'type' => 'string', 'is_required' => true, 'category_id' => 1],
            ['text' => 'Va tener Nombre?', 'type' => 'list', 'options' => 'Si\nNo', 'is_required' => false, 'category_id' => 1],
            ['text' => 'Auspicio delantero', 'type' => 'integer', 'is_required' => true, 'category_id' => 1],
            ['text' => 'Tipo de logo', 'type' => 'list', 'options' => 'Bordado\nUV\nSublimado', 'is_required' => true, 'category_id' => 2],
        ];

        foreach ($questions as $question) {
            Question::create($question);
        }
        $sizes = [
            ['name' => 'NORMAL'],
            ['name' => '1A-CAB'], ['name' => '1A-DAM'],
            ['name' => '2A-CAB'], ['name' => '2A-DAM'],
            ['name' => '4A-CAB'], ['name' => '4A-DAM'],
            ['name' => '6A-CAB'], ['name' => '6A-DAM'],
            ['name' => '8A-CAB'], ['name' => '8A-DAM'],
            ['name' => '10A-CAB'], ['name' => '10A-DAM'],
            ['name' => '14A-CAB'], ['name' => '14A-DAM'],
            ['name' => '16A-CAB'], ['name' => '16A-DAM'],
            ['name' => 'XP-CAB'], ['name' => 'XP-DAM'],
            ['name' => 'P-CAB'], ['name' => 'P-DAM'],
            ['name' => 'M-CAB'], ['name' => 'M-DAM'],
            ['name' => 'G-CAB'], ['name' => 'G-DAM'],
            ['name' => 'XG-CAB'], ['name' => 'XG-DAM'],
            ['name' => '2XG-CAB'], ['name' => '2XG-DAM'],
            ['name' => '3XG-CAB'], ['name' => '3XG-DAM'],
            ['name' => '4XG-CAB'], ['name' => '4XG-DAM'],
            ['name' => '5XG-CAB'], ['name' => '5XG-DAM'],
            ['name' => '6XG-CAB'], ['name' => '6XG-DAM'],
            
        ];

        foreach ($sizes as $size) {
            Size::create($size);
        }
    }
    
}
