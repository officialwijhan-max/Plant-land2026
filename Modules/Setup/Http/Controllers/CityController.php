<?php

namespace Modules\Setup\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\Setup\Entities\City;
use Modules\Setup\Entities\Country;
use Modules\Setup\Entities\State;
use Modules\Setup\Repositories\CityRepository;

class CityController extends Controller
{
    protected $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function list_select_option(Request $request)
    {
        $data = $this->cityRepository->listForSelect($request->search,$request->state_id);
        return response()->json($data);
    }

    public function index(Request $request)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;

            $data['items'] = $this->cityRepository->withPaginate($row_count,$quick_search,$sort,$column);
            if ($request->ajax()) {
                return view('setup::city.paginates.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                if ($request->import_as == "csv") {
                    $this->cityRepository->csvDownload();
                    $filePath = public_path("uploads/csv/city.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-city.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
                $data['items'] = $this->cityRepository->withPaginate("all",$quick_search,$sort,$column);
                if ($request->import_as == "print") {
                    return view('setup::city.paginates.print', $data);
                }
            }
            return view('setup::city.index', $data);
        } catch (\Throwable $th) {

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        $country_id = request()->country_id;
        $countries = Country::all()->pluck('name', 'id');
        if (!$country_id){
            $country_id = array_key_first($countries->toArray());
        }
        $states = State::where('country_id', $country_id)->pluck('name', 'id');
        $state_id = request()->state_id;
        if (!$state_id){
            $state_id = array_key_first($states->toArray());
        }
        $countries = $countries->prepend(__('contact.Select Country'), '');

        return view('setup::city.create', compact('countries','states', 'country_id', 'state_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    public function store(Request $request) {
        if (!$request->json()) {
            abort(404);
        }
        $validate_rules = [
            'country_id' => ['required', Rule::exists('countries', 'id')],
            'state_id' => ['required', Rule::exists('states', 'id')],
            'name' => 'required|max:191|string',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        $model = new City();
        $model->name = $request->name;
        $model->state_id = $request->state_id;
        $model->save();

        $response = [
            'model' => $model,
            'message' => __('setup.City Added Successfully'),
        ];

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id) {
        abort(4040);
        $model = City::findOrFail($id);
        return view('setup::city.show', compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id) {
        $model = City::findOrFail($id);
        $countries = Country::all()->pluck('name', 'id')->prepend(__('Select country'), '');
        $states = State::where('country_id', $model->state->country_id)->pluck('name', 'id')->prepend(__('contact.Select Country'), '');
        return view('setup::city.edit', compact('model', 'countries', 'states'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws ValidationException
     */
    public function update(Request $request, $id) {
        if (!$request->json()) {
            abort(404);
        }

        $validate_rules = [
            'country_id' => ['required', Rule::exists('countries', 'id')],
            'state_id' => ['required', Rule::exists('states', 'id')],
            'name' => 'required|max:191|string',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));


        $model = City::find($id);
        if (!$model) {
            throw ValidationException::withMessages(['message' => __('setup.City Not Found')]);
        }

        $model->name = $request->name;
        $model->state_id = $request->state_id;
        $model->save();

        $response = [
            'message' => __('setup.City Updated Successfully'),
            'goto' => route('setup.city.index', ['country_id' => $request->country_id, 'state_id' => $request->state_id]),
        ];

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     * @throws ValidationException
     */
    public function destroy(Request $request, $id) {
        if (!$request->json()) {
            abort(404);
        }

        $model = City::find($id);
        if (!$model) {
            throw ValidationException::withMessages(['message' => __('setup.City Not Found')]);
        }

        if ($model->states) {
            throw ValidationException::withMessages(['message' => __('setup.City Has States. For delete state, first delete states')]);
        }

        $model->delete();

        return response()->json(['message' => __('setup.City Deleted Successfully'), 'goto' => route('setup.city.index')]);
    }
}

