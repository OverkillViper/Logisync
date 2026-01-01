<?php

namespace Database\Seeders;

use App\Models\Batch;
use Illuminate\Database\Seeder;

class BatchSeeder extends Seeder
{
    public function run(): void
    {
        $batches = [
            ['name' => 'May 2017'      ],
            ['name' => 'July 2019'     ],
            ['name' => 'November 2021' ],
            ['name' => 'February 2022' ],
            ['name' => 'November 2022' ],
            ['name' => 'July 2023'     ],
            ['name' => 'August 2024'   ],
            ['name' => 'December 2024' ],
            ['name' => 'March 2025'    ],
            ['name' => 'May 2025'      ],
            ['name' => 'August 2025'   ],
        ];

        foreach ($batches as $batch) {
            Batch::create($batch);
        }
    }
}