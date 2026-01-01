<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EvaluationCriteria;

class EvaluationCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $criterias = [
            [ 'name' => 'Coding Skill'                    , 'type' => 'technical'     ],
            [ 'name' => 'Debugging Skill'                 , 'type' => 'technical'     ],
            [ 'name' => 'Overall Technical Understanding' , 'type' => 'technical'     ],
            [ 'name' => 'Analytical Skill'                , 'type' => 'technical'     ],
            [ 'name' => 'Corporate Ettiquette and Manners', 'type' => 'non-technical' ],
            [ 'name' => 'Sincerity'                       , 'type' => 'non-technical' ],
            [ 'name' => 'Effort'                          , 'type' => 'non-technical' ],
            [ 'name' => 'Follow Instruction'              , 'type' => 'non-technical' ],
            [ 'name' => 'Communication'                   , 'type' => 'non-technical' ],
            [ 'name' => 'Timeliness'                      , 'type' => 'non-technical' ],
            [ 'name' => 'Quality of Work/ Efficiency'     , 'type' => 'non-technical' ],
        ];

        foreach ($criterias as $criteria) {
            EvaluationCriteria::create([
                'name' => $criteria['name'],
                'type' => $criteria['type'],
            ]);
            sleep(1); 
        }
    }
}
