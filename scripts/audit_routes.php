<?php
/** Audit non-destruktif: php scripts/audit_routes.php */
require dirname(__DIR__).'/vendor/autoload.php';
$root = dirname(__DIR__);
$command = 'php '.escapeshellarg($root.'/artisan').' route:list --json 2>&1';
exec($command, $lines, $exit);
if ($exit !== 0) { fwrite(STDERR, implode("\n", $lines)."\n"); exit(2); }
$routes = json_decode(implode("\n", $lines), true, 512, JSON_THROW_ON_ERROR);
$names = []; $issues = []; $counts = ['admin'=>0,'coach'=>0,'customer'=>0];
foreach ($routes as $route) {
    $name = $route['name'] ?? null;
    if ($name !== null) {
        if (isset($names[$name])) $issues[] = "Duplicate route name: $name";
        $names[$name] = true;
    }
    $action = $route['action'];
    if (str_contains($action, '@')) {
        [$class, $method] = explode('@', $action, 2);
        if (!class_exists($class) || !method_exists($class, $method)) $issues[] = "Missing action: $action";
    }
    foreach (array_keys($counts) as $role) {
        if (str_starts_with($route['uri'], "$role/")) {
            $counts[$role]++;
            if (!in_array('auth', $route['middleware'], true) || !in_array("role:$role", $route['middleware'], true)) {
                $issues[] = "Role protection missing: {$route['uri']} ($role)";
            }
        }
    }
}
$views = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/resources/views'));
$references = 0;
foreach ($views as $view) {
    if (!$view->isFile() || !str_ends_with($view->getFilename(), '.blade.php')) continue;
    $contents = file_get_contents($view->getPathname());
    preg_match_all('/\broute\(\s*[\'\"]([^\'\"]+)[\'\"]/', $contents, $matches);
    foreach ($matches[1] as $routeName) {
        $references++;
        if (!isset($names[$routeName])) $issues[] = "Undefined Blade route $routeName in ".substr($view->getPathname(), strlen($root)+1);
    }
}
echo 'Registered routes: '.count($routes)."\n";
echo 'Named routes: '.count($names)."\n";
echo "Literal Blade route references: $references\n";
foreach ($counts as $role=>$count) echo "$role routes: $count\n";
echo 'Issues: '.count($issues)."\n";
foreach ($issues as $issue) echo "- $issue\n";
exit($issues ? 1 : 0);
