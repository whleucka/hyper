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

    /************************************************************************
     * VIEWS
     ************************************************************************/
    /**
     * Module(s) table view
     * @param mixed $module
     */
    #[Get("/admin/{module}", "module.index", ["auth"])]
    public function index($module): string {
        $module = $this->module($module);
        return twig("admin/index.html", [
            "mode" => "index",
            "create_enabled" => $module->create_enabled ?? false,
            "create_route" => $module->getRoute("create"),
            ...$module->getData(),
        ]);
    }
    /**
     * Create new module view
     * @param mixed $module
     */
    #[Get("/admin/{module}/create", "module.create", ["auth"])]
    public function create($module): string {
        $module = $this->module($module);
        return twig("admin/index.html", [
            "mode" => "create",
            ...$module->getData(),
        ]);
    }
    /**
     * Edit existing module view
     * @param mixed $module
     * @param mixed $id
     */
    #[Get("/admin/{module}/{id}/edit", "module.edit", ["auth"])]
    public function edit($module, $id): string {
        $module = $this->module($module, $id);
        return twig("admin/index.html", [
            "mode" => "edit",
            "create_route" => "/",
            ...$module->getData(),
        ]);
    }

    /************************************************************************
     * REQUESTS
     ************************************************************************/
    /**
     * POST store new module
     * @param mixed $module
     */
    #[Post("/admin/{module}", "module.store", ["auth"])]
    public function store($module): string {
        $module = $this->module($module);
        return dd("wip: store");
    }
    /**
     * POST update existing module
     * @param mixed $module
     * @param mixed $id
     */
    #[Post("/admin/{module}/{id}", "module.modify", ["auth"])]
    public function modify($module, $id): string {
        $module = $this->module($module, $id);
        return dd("wip: modify");
    }
    /**
     * POST delete existing module
     * @param mixed $module
     * @param mixed $id
     */
    #[Post("/admin/{module}/{id}/delete", "module.destroy", ["auth"])]
    public function destroy($module, $id): string {
        $module = $this->module($module, $id);
        return dd("wip: destroy");
    }

    /************************************************************************
     * API ENDPOINTS
     ************************************************************************/
    /**
     * API GET module(s)
     * @param mixed $module
     */
    #[Get("/api/admin/{module}", "module.api.index", ["auth", "api"])]
    public function api_index($module): array {
        $module = $this->module($module);
        return $module->tableData();
    }
    /**
     * API POST store new module
     * @param mixed $module
     */
    #[Post("/api/admin/{module}", "module.api.store", ["auth"])]
    public function api_store($module): string {
        die("wip api store");
    }
    /**
     * API GET existing module
     * @param mixed $module
     * @param mixed $id
     */
    #[Get("/api/admin/{module}/{id}", "module.api.show", ["auth"])]
    public function api_show($module, $id): string {
        die("wip api show");
    }
    /**
     * API PATCH update existing module
     * @param mixed $module
     * @param mixed $id
     * @return void
     */
    #[Patch("/api/admin/{module}/{id}", "module.api.modify", ["auth"])]
    public function api_modify($module, $id): array {
        die("wip api modify");
    }
    /**
     * API DELETE delete existing module
     * @param mixed $module
     * @param mixed $id
     * @return void
     */
    #[Delete("/api/admin/{module}/{id}", "module.api.destroy", ["auth"])]
    public function api_destroy($module, $id): array {
        die("wip api destroy");
    }
}
