<?php

namespace Nebula\Controllers\Admin;

use Exception;
use Nebula\Controllers\Controller;
use StellarRouter\{Get, Post, Delete, Patch};

class ModuleController extends Controller
{
    protected function module(string $module, ?string $id = null): mixed
    {
        $class = "Nebula\\Controllers\\Admin\\Modules\\" . ucfirst($module);
        try {
            if (!class_exists($class)) {
                app()->pageNotFound();
            }
            return new $class($id);
        } catch (Exception $ex) {
            app()->serverError();
            error_log("module not found: $class " . $ex->getMessage());
        }
    }

    /**
     * @param mixed $module
     */
    #[Get("/admin/{module}", "module.index", ["auth"])]
    public function index($module): string {
        $module = $this->module($module);
        return twig("admin/index.html", [
            'mode' => 'index',
            ...$module->getData()
        ]);
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
        $module = $this->module($module, $id);
        return dd("wip: show");
    }

    /**
     * @param mixed $module
     * @param mixed $id
     */
    #[Get("/admin/{module}/{id}/edit", "module.edit", ["auth"])]
    public function edit($module, $id): string {
        $module = $this->module($module, $id);
        return twig("admin/index.html", [
            'mode' => 'edit',
            ...$module->getData(),
        ]);
    }

    /**
     * @param mixed $module
     * @param mixed $id
     */
    #[Post("/admin/{module}/{id}/update", "module.modify", ["auth"])]
    #[Patch("/admin/{module}/{id}", "module.update", ["auth"])]
    public function update($module, $id): string {
        $module = $this->module($module, $id);
        return dd("wip: update");
    }

    /**
     * @param mixed $module
     * @param mixed $id
     */
    #[Post("/admin/{module}/{id}/delete", "module.delete", ["auth"])]
    #[Delete("/admin/{module}/{id}", "module.destroy", ["auth"])]
    public function destroy($module, $id): string {
        $module = $this->module($module, $id);
        return dd("wip: destroy");
    }
}
