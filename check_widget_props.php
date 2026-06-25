<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$props = ['heading', 'sort', 'columnSpan'];
$classes = [
    'Filament\Widgets\ChartWidget',
    'Filament\Widgets\TableWidget',
    'Filament\Widgets\StatsOverviewWidget',
];

foreach ($classes as $class) {
    echo "$class:\n";
    foreach ($props as $prop) {
        try {
            $ref = new ReflectionProperty($class, $prop);
            echo "  $prop: static=" . ($ref->isStatic() ? 'yes' : 'no') . ' type=' . $ref->getType() . "\n";
        } catch (Exception $e) {
            echo "  $prop: not found\n";
        }
    }
}
