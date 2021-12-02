<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class DayOne extends Command
{
    /** @var string */
    protected $signature = 'Advent:day-one';

    /** @var string */
    protected $description = 'Advent day-one ';

    private ?int $last = null;
    private int $partOneCount = 0;
    private int $partTwoCount = 0;
    private Collection $measurements;

    public function __construct()
    {
        $file = File::get(__DIR__ . '/../data/day-one.csv');
        $this->measurements = collect(explode(PHP_EOL, $file));

        parent::__construct();
    }

    public function handle(): void
    {
        $this->runPartOne();
        $this->runPartTwo();

        $this->output->success("Part One: $this->partOneCount! Part Two: $this->partTwoCount");
    }

    private function checkNumber(int $measurement, string $countType)
    {
        if ($measurement <= $this->last) {
            return;
        }

        $this->$countType++;
    }

    private function runPartOne(string $countType = 'partOneCount'): void
    {
        $this->measurements->each(function (string $measurement) use ($countType): void {
            if ($this->last !== null) {
                $this->checkNumber((int) $measurement, $countType);
            }
            $this->last = (int) $measurement;
        });
    }

    private function runPartTwo(): void
    {
        $this->last = null;

        $this->measurements = $this->measurements->map(function (string $measurement, int $index): ?int {
            $groupedMeasurements = collect([
                (int) $measurement,
                (int) data_get($this->measurements, $index + 1),
                (int) data_get($this->measurements, $index + 2),
            ]);

            return $groupedMeasurements->count() === 3 ? $groupedMeasurements->sum() : null;
        })->filter();

        $this->runPartOne('partTwoCount');
    }
}
