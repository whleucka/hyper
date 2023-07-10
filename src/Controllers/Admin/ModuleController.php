<?php

namespace Nebula\Controllers\Admin;

use Exception;
use Nebula\Controllers\Controller;
use StellarRouter\{Get, Post, Delete, Patch};

class ModuleController extends Controller
{
    protected function module(string $module): mixed
    {
        $class = "Nebula\\Controllers\\Admin\\Modules\\" . ucfirst($module);
        try {
            return new $class();
        } catch (Exception $ex) {
            error_log("module not found: $module " . $ex->getMessage());
            app()->pageNotFound();
        }
    }

    /**
     * @param mixed $module
     */
    #[Get("/admin/{module}", "module.index", ["auth"])]
    public function index($module): string {
        $module = $this->module($module);
        return twig("admin/index.html", $module->getData());
    }

    /**
     * @param mixed $module
     */
    #[Get("/admin/{module}/create", "module.create", ["auth"])]
    public function create($module): string {
        $module = $this->module($module);
        return dd("wip: create");
    }

    /**
     * @param mixed $module
     */
    #[Post("/admin/{module}", "module.store", ["auth"])]
    public function store($module): string {
        $module = $this->module($module);
        return dd("wip: store");
    }

    /**
     * @param mixed $module
     * @param mixed $id
     */
    #[Get("/admin/{module}/{id}", "module.show", ["auth"])]
    public function show($module, $id): string {
        $module = $this->module($module);
        return dd("wip: show");
    }

    /**
     * @param mixed $module
     * @param mixed $id
     */
    #[Get("/admin/{module}/{id}/edit", "module.edit", ["auth"])]
    public function edit($module, $id): string {
        $module = $this->module($module);
        return dd("wip: edit");
    }

    /**
     * @param mixed $module
     * @param mixed $id
     */
    #[Patch("/admin/{module}/{id}", "module.update", ["auth"])]
    public function update($module, $id): string {
        $module = $this->module($module);
        return dd("wip: update");
    }

    /**
     * @param mixed $module
     * @param mixed $id
     */
    #[Delete("/admin/{module}/{id}", "module.destroy", ["auth"])]
    public function destroy($module, $id): string {
        $module = $this->module($module);
        return dd("wip: destroy");
    }
}
