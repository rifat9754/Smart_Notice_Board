<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CompareScheduling extends Command
{
    protected $signature = 'algo:compare';
    protected $description = 'Compare scheduling algorithms for the thesis Results chapter';

    public function handle()
    {

        $notices = [
            ['id'=>1,  'priority'=>'high',   'daysToDeadline'=>1,  'daysSincePosted'=>0],
            ['id'=>2,  'priority'=>'high',   'daysToDeadline'=>3,  'daysSincePosted'=>1],
            ['id'=>3,  'priority'=>'high',   'daysToDeadline'=>7,  'daysSincePosted'=>2],
            ['id'=>4,  'priority'=>'medium', 'daysToDeadline'=>2,  'daysSincePosted'=>0],
            ['id'=>5,  'priority'=>'medium', 'daysToDeadline'=>5,  'daysSincePosted'=>1],
            ['id'=>6,  'priority'=>'medium', 'daysToDeadline'=>10, 'daysSincePosted'=>3],
            ['id'=>7,  'priority'=>'medium', 'daysToDeadline'=>14, 'daysSincePosted'=>5],
            ['id'=>8,  'priority'=>'low',    'daysToDeadline'=>4,  'daysSincePosted'=>1],
            ['id'=>9,  'priority'=>'low',    'daysToDeadline'=>8,  'daysSincePosted'=>2],
            ['id'=>10, 'priority'=>'low',    'daysToDeadline'=>15, 'daysSincePosted'=>4],
            ['id'=>11, 'priority'=>'low',    'daysToDeadline'=>20, 'daysSincePosted'=>6],
            ['id'=>12, 'priority'=>'low',    'daysToDeadline'=>30, 'daysSincePosted'=>8],
        ];

        $slots = 100; 

        $this->info("Simulating {$slots} display slots over 12 notices\n");

  
        $roundRobin = $this->simulateRoundRobin($notices, $slots);
        $static     = $this->simulateStatic($notices, $slots);
        $dynamic    = $this->simulateDynamic($notices, $slots);

     
        $this->printTable($notices, $roundRobin, $static, $dynamic, $slots);

        return 0;
    }

    private function simulateRoundRobin($notices, $slots)
    {
        $shown = array_fill_keys(array_column($notices, 'id'), 0);
        $n = count($notices);
        for ($i = 0; $i < $slots; $i++) {
            $shown[$notices[$i % $n]['id']]++;
        }
        return $shown;
    }

    private function simulateStatic($notices, $slots)
    {
        $shown = array_fill_keys(array_column($notices, 'id'), 0);
        $groups = ['high'=>[], 'medium'=>[], 'low'=>[]];
        foreach ($notices as $x) $groups[$x['priority']][] = $x['id'];

        $quota = ['high'=>0.6, 'medium'=>0.3, 'low'=>0.1];
        foreach ($quota as $pri => $frac) {
            $count = (int) round($slots * $frac);
            $ids = $groups[$pri];
            if (empty($ids)) continue;
            for ($i = 0; $i < $count; $i++) {
                $shown[$ids[$i % count($ids)]]++;
            }
        }
        return $shown;
    }

private function simulateDynamic($notices, $slots)
    {
        $shown = array_fill_keys(array_column($notices, 'id'), 0);
        $lastShown = array_fill_keys(array_column($notices, 'id'), 0);
        $priorityWeight = ['high'=>1.0, 'medium'=>0.6, 'low'=>0.3];

        for ($i = 1; $i <= $slots; $i++) {
            $best = null; $bestScore = -1;
            foreach ($notices as $x) {
                $p = $priorityWeight[$x['priority']];
                $u = 1 / ($x['daysToDeadline'] + 1);
                
                $wait = $i - $lastShown[$x['id']];
                $starve = min(1.0, $wait / 12);
                $boost = $wait > 18 ? 0.5 : 0.0;
                $score = 0.35*$p + 0.25*$u + 0.30*$starve + $boost;
                if ($score > $bestScore) { $bestScore = $score; $best = $x['id']; }
            }
            $shown[$best]++;
            $lastShown[$best] = $i;
        }
        return $shown;
    }

    private function printTable($notices, $rr, $st, $dy, $slots)
    {
        $rows = [];
        foreach ($notices as $x) {
            $rows[] = [
                $x['id'], $x['priority'], $x['daysToDeadline'],
                $rr[$x['id']], $st[$x['id']], $dy[$x['id']],
            ];
        }
        $this->table(
            ['ID', 'Priority', 'Deadline(d)', 'RoundRobin', 'Static', 'Dynamic'],
            $rows
        );

  
        $this->info("\n=== METRICS ===\n");
        $metrics = [];
        foreach (['RoundRobin'=>$rr, 'Static'=>$st, 'Dynamic'=>$dy] as $name => $shown) {
            $metrics[] = [
                $name,
                $this->highExposure($notices, $shown, $slots) . '%',
                $this->urgentExposure($notices, $shown, $slots) . '%',
                $this->jainFairness($shown),
                min($shown),
            ];
        }
        $this->table(
            ['Method', 'High-Priority Exposure', 'Urgent Exposure', 'Fairness (Jain)', 'Min Shown'],
            $metrics
        );

        $this->info("\nInterpretation: Dynamic balances high-priority & urgent exposure");
        $this->info("while keeping fairness high (no notice starved).\n");
    }


    private function highExposure($notices, $shown, $slots)
    {
        $sum = 0;
        foreach ($notices as $x) if ($x['priority']==='high') $sum += $shown[$x['id']];
        return round(100 * $sum / $slots, 1);
    }


    private function urgentExposure($notices, $shown, $slots)
    {
        $sum = 0;
        foreach ($notices as $x) if ($x['daysToDeadline'] <= 3) $sum += $shown[$x['id']];
        return round(100 * $sum / $slots, 1);
    }

    private function jainFairness($shown)
    {
        $vals = array_values($shown);
        $sum = array_sum($vals);
        $sumSq = 0;
        foreach ($vals as $v) $sumSq += $v * $v;
        $n = count($vals);
        return $sumSq > 0 ? round(($sum*$sum) / ($n * $sumSq), 3) : 0;
    }
}