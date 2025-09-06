<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Attribute;
// Translation model removed
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function __construct(
        private Attribute   $attribute,
        // Translation model removed
    )
    {
    }


    /**
     * @param Request $request
     * @return Renderable
     */
    function index(Request $request): Renderable
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);

            $attributes = $this->attribute->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $attributes = $this->attribute;
        }

        $attributes = $attributes->orderBy('name')->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.attribute.index', compact('attributes', 'search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:attributes',
        ]);

        foreach ($request->name as $name) {
            if (strlen($name) > 255) {
                toastr::error(translate('Name is too long!'));
                return back();
            }
        }

        $attribute = $this->attribute;
        $attribute->name = $request->name[array_search('en', $request->lang)];
        $attribute->save();

        // Translation functionality removed - always use English

        Toastr::success(translate('Attribute added successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Renderable
     */
    public function edit($id): Renderable
    {
        $attribute = $this->attribute->withoutGlobalScopes()->with('// translations removed')->find($id);
        return view('admin-views.attribute.edit', compact('attribute'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:attributes,name,' . $id,
        ]);

        foreach ($request->name as $name) {
            if (strlen($name) > 255) {
                toastr::error(translate('Name is too long!'));
                return back();
            }
        }

        $attribute = $this->attribute->find($id);
        $attribute->name = $request->name[array_search('en', $request->lang)];
        $attribute->save();

        // Translation functionality removed - always use English

        Toastr::success(translate('Attribute updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $attribute = $this->attribute->find($request->id);
        $attribute->delete();

        Toastr::success(translate('Attribute removed!'));
        return back();
    }
}
