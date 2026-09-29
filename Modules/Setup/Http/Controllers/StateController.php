<?php


namespace Modules\Setup\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\Setup\Entities\Country;
use Modules\Setup\Entities\State;
use Modules\Setup\Repositories\StateRepository;

class StateController extends Controller
{
    protected $cityRepository;

    public function __construct(StateRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        try {
            $row_count = ($request->has('row')) ? $request->row : 10 ;
            $sort = ($request->has('sort')) ? $request->sort : 'asc' ;
            $column = ($request->has('col')) ? $request->col : null ;
            $quick_search = ($request->has('quick_search')) ? $request->quick_search : null ;

            $data['items'] = $this->cityRepository->withPaginate($row_count,$quick_search,$sort,$column);
            if ($request->ajax()) {
                return view('setup::state.paginates.list', $data);
            }
            if ($request->has("import_as")) {
                set_time_limit(-1);
                if ($request->import_as == "csv") {
                    $this->cityRepository->csvDownload();
                    $filePath = public_path("uploads/csv/states.xlsx");
                    $headers = ['Content-Type: text/csv'];
                    $fileName = time().'-states.xlsx';

                    return response()->download($filePath, $fileName, $headers);
                }
                $data['items'] = $this->cityRepository->withPaginate("all",$quick_search,$sort,$column);
                if ($request->import_as == "print") {
                    return view('setup::state.paginates.print', $data);
                }
            }
            return view('setup::state.index', $data);
        } catch (\Throwable $th) {

        }
    }

    public function list_select_option(Request $request)
    {
        $data = $this->cityRepository->listForSelect($request->search,$request->country_id);
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $country_id = request()->country_id;
        $countries = Country::all()->pluck('name', 'id');
        if (!$country_id) {
            $country_id = array_key_first($countries->toArray());
        }
        $countries = $countries->prepend(__('contact.Select Country'), '');
        return view('setup::state.create', compact('countries', 'country_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        if (!$request->json()) {
            abort(404);
        }

        $validate_rules = [
            'country_id' => ['required', Rule::exists('countries', 'id')],
            'name' => 'required|max:191|string',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        $model = new State();
        $model->name = $request->name;
        $model->country_id = $request->country_id;
        $model->save();

        $response = [
            'model' => $model,
            'message' => __('setup.State Added Successfully'),
            'reload' => true
        ];

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $model = State::findOrFail($id);
        return view('setup::state.show', compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $model = State::findOrFail($id);
        $countries = Country::all()->pluck('name', 'id')->prepend(__('contact.Select Country'), '');
        return view('setup::state.edit', compact('model', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        if (!$request->json()) {
            abort(404);
        }

        $validate_rules = [
            'country_id' => ['required', Rule::exists('countries', 'id')],
            'name' => 'required|max:191|string',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));


        $model = State::find($id);
        if (!$model) {
            throw ValidationException::withMessages(['message' => __('setup.State Not Found')]);
        }

        $model->name = $request->name;
        $model->country_id = $request->country_id;
        $model->save();

        $response = [
            'message' => __('setup.State Updated Successfully'),
            'goto' => route('setup.state.index', ['country_id' => $model->country_id]),
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
    public function destroy(Request $request, $id)
    {
        if (!$request->json()) {
            abort(404);
        }

        $model = State::find($id);
        if (!$model) {
            throw ValidationException::withMessages(['message' => __('setup.State Not Found')]);
        }

        if ($model->cities) {
            throw ValidationException::withMessages(['message' => __('setup.State Has Cities. For delete state, first delete city')]);
        }

        $model->delete();

        return response()->json(['message' => __('setup.State Deleted Successfully'), 'goto' => route('setup.state.index')]);
    }
}
