<?php

namespace Nebula\Controllers\Admin;

use Exception;
use Nebula\Controllers\Controller;
use StellarRouter\{Get, Post, Delete, Patch};

class ModuleController extends Controller
{
    protected function module(string $module, ?string $id = null): mixed
    {
        $class = "Nebula\\Admin\\Modules\\" . ucfirst($module);
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
            "create_enabled" => $module->create_enabled,
            "create_route" => $module->routeName("create"),
            ...$module->data(),
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
            "create_enabled" => $module->create_enabled,
            "create_route" => $module->routeName("create"),
            ...$module->data(),
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
            "id" => $id,
            "create_enabled" => $module->create_enabled,
            "create_route" => $module->routeName("create"),
            "edit_route" => $module->routeName("edit"),
            ...$module->data(),
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
        $module_name = $module;
        $module = $this->module($module);
        if ($this->validate($module->validationArray("modify"))) {
            $id = $module->insert();
            if ($id !== false) {
                // Insert successful
                app()->redirectUrl(app()->moduleRoute($module->routeName("edit"), $id));
            }
        }
        return $this->create($module_name);
    }
    /**
     * POST update existing module
     * @param mixed $module
     * @param mixed $id
     */
    #[Post("/admin/{module}/{id}", "module.modify", ["auth"])]
    public function modify($module, $id): string {
        $module_name = $module;
        $module = $this->module($module, $id);
        if ($this->validate($module->validationArray("modify"))) {
            if ($module->update()) {
                // Update successful
            }
        }
        return $this->edit($module_name, $id);
    }
    /**
     * POST delete existing module
     * @param mixed $module
     * @param mixed $id
     */
    #[Post("/admin/{module}/{id}/delete", "module.destroy", ["auth"])]
    public function destroy($module, $id): string {
        $module_name = $module;
        $module = $this->module($module, $id);
        if ($module->delete()) {
            // Delete successful
            app()->redirectUrl(app()->moduleRoute($module->routeName("index")));
        }
        return $this->edit($module_name, $id);
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
