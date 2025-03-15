<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionMethod;

class FindRouteDetails extends Command
{
    protected $signature = 'route:info {url}';
    protected $description = 'استخراج معلومات الكونترولر والموديل والفيو لرابط معين في المشروع';

    public function handle()
    {
        $inputUrl = $this->argument('url');
        $parsedUrl = parse_url($inputUrl);
        $path = $parsedUrl['path'] ?? '';

        $route = collect(Route::getRoutes())->first(function ($route) use ($path) {
            return trim($route->uri(), '/') === trim($path, '/');
        });

        if (!$route) {
            $this->error("❌  No found Route");
            return;
        }

        // استخراج بيانات الكونترولر
        $action = $route->getAction();
        $controllerAction = $action['controller'] ?? null;

        if (!$controllerAction) {
            $this->error("❌  No found Controller");
            return;
        }

        [$controller, $method] = explode('@', $controllerAction);
        $this->info("✅ Controller: $controller");
        $this->info("✅ Method: $method");

        // البحث عن الموديل داخل الكونترولر
        if (class_exists($controller)) {
            $reflection = new ReflectionClass($controller);
            if ($reflection->hasMethod($method)) {
                $methodReflection = $reflection->getMethod($method);
                $this->findModelInController($controller, $methodReflection);
            }
        }

        // البحث عن الفيو في الكود
        $this->findViewInController($controller, $method);
    }

    private function findModelInController($controller, ReflectionMethod $method)
    {
        $modelNamespace = "App\\Modules\\User\\Models\\"; // يجب تعديله حسب مكان الموديلات لديك
        $modelUsed = null;

        $controllerContent = File::get((new ReflectionClass($controller))->getFileName());
        preg_match_all('/(\w+)::/', $controllerContent, $matches);

        foreach ($matches[1] as $class) {
            if (class_exists($modelNamespace . $class)) {
                $modelUsed = $modelNamespace . $class;
                break;
            }
        }

        if ($modelUsed) {
            $this->info("✅ Model: $modelUsed");
        } else {
            $this->warn("⚠️  No found Model");
        }
    }

    private function findViewInController($controller, $method)
    {
        $controllerContent = File::get((new ReflectionClass($controller))->getFileName());

        preg_match('/return view\(\'([a-zA-Z0-9._-]+)\'/', $controllerContent, $matches);

        if (!empty($matches[1])) {
            $viewPath = resource_path('views/' . str_replace('.', '/', $matches[1]) . '.blade.php');
            $this->info("✅ View: $viewPath");
        } else {
            $this->warn("⚠️  No found View");
        }
    }
}
