<?php

namespace Modules\Project\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Project\Http\Requests\ProjectRequest;
use Modules\Project\Services\ProjectService;
use Modules\Project\Services\TeamService;
use Modules\Project\Entities\Project;
use Modules\Project\Entities\FieldTask;
use Modules\Project\Entities\TaskComment;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Pagination\LengthAwarePaginator;
class ProjectController extends Controller
{
    public $service, $request, $team;

    public function __construct(
        ProjectService $service,
        Request $request,
        TeamService $team
    )
    {
        $this->service = $service;
        $this->request = $request;
        $this->team = $team;
    }

    

    public function followUp(Request $request, $id)
    {
        try {
            $row_count = $request->get('row', 10);
            $sort = $request->get('sort', 'asc');
            $column = $request->get('col', 'id');
            $quick_search = $request->get('quick_search');
            $name = $request->get('name');

            // Base query for projects by team ID
            $projectsQuery = Project::where('team_id', $id);

            if ($quick_search) {
                $projectsQuery->where('name', 'like', '%' . $quick_search . '%');
            }

            if ($name) {
                $projectsQuery->where('name', $name);
            }

            // Fetch projects and related data
            $projects = $projectsQuery->with(['tasks', 'comments', 'users'])->get();

            // Flatten tasks into individual records
            $tasksData = $projects->flatMap(function ($project) {
                return $project->tasks->map(function ($task) use ($project) {
                    $users = FieldTask::where('task_id', $task->id)->get();

                    $assignedUsers = $users->map(function ($fieldTask) {
                        return optional($fieldTask->assinge)->name;
                    })->toArray();

                    $lastComment = TaskComment::where('task_id', $task->id)->latest()->first();
                    $comment = $lastComment ? $lastComment->comment : "---";

                    $taskDate = FieldTask::where('date', '!=', null)->where('task_id', $task->id)->latest()->first();
                    $dueDate = $taskDate ? $taskDate->date : null;

                    return [
                        'id' => $task->id,
                        'project_name' => $project->name ?: "---",
                        'task_name' => $task->name ?: "---",
                        'assigned_users' => $assignedUsers,
                        'due_date' => $dueDate ? \Carbon\Carbon::parse($dueDate)->format('d M, Y') : 'No due date',
                        'last_comment' => $comment,
                        'completed' => $task->completed,
                    ];
                });
            });

            // Apply sorting based on column and order
            $sortedTasks = $tasksData->sortBy($column, SORT_REGULAR, $sort === 'desc')->values();

            // Manual pagination
            $total = $sortedTasks->count();
            $currentPage = $request->get('page', 1);
            $pagedTasks = $sortedTasks->slice(($currentPage - 1) * $row_count, $row_count)->values();

            // Create a LengthAwarePaginator instance
            $paginator = new LengthAwarePaginator(
                $pagedTasks,
                $total,
                $row_count,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            // Prepare data to send to the view
            $data['tasks'] = $paginator;

            // Handle AJAX or full-page view
            if ($request->ajax()) {
                return view('project::project.paginates.index', $data);
            }

            return view('project::project.index', $data);

        } catch (\Exception $e) {
            \LogActivity::errorLog($e->getMessage());
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }






    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($teamId=null)
    {
        $team_id = $teamId??null;
        $teams = $this->team->teamListByCurrentUserWorkspace()->pluck('name', 'id')->toArray();
        return view('project::project.create', compact('team_id','teams'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \App\Http\Controllers\Response
     */
    public function store(ProjectRequest $request)
    {
        if(!$request->ajax()){
            abort(404);
        }

        $project = $this->service->storeProject($request);
        return $this->success([
            'message' => trans('project::project.created_successfully'),
            'goto' => route('project.show', $project->uuid)
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id, $view=null)
    {

        $model = $this->service->findByUuid($id);

        $blade = '';
        if(!$view){
            $blade = $model->default_view;
        } else{
            if(in_array($view, ['board', 'files', 'conversation' ])){
                $blade = $view;
            }
        }
        if($blade == 'list'){
            $blade = '';
        }
        // dd($blade);
        return view('project::project.show'.$blade, compact('model', 'blade'));
    }


    public function shareProject(Request $request)
    {
        $validate_rules = [
            'members' => 'required',
            'project_id' => 'required'
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        $members = $request->members;
        $project_id = $request->project_id;

        $project = $this->service->shareProject($members, $project_id);

        return response()->json([
            'project' => $project,
            'success' => true,
            'message' => trans('project::project.share_successfull')
        ]);


    }


    public function removeUser(Request $request)
    {
        $user_id = $request->user_id;
        $project_id = $request->project_id;

        $this->service->removeUser($project_id, $user_id);

        return response()->json([
            'success' => true,
            'message' => trans('project::project.user_removed')
        ]);

    }

    public function projectUser($id)
    {
        return $this->service->projectUser($id);
    }

    public function updateColor(Request $request)
    {
        $color = $request->color;
        $project_id = $request->project_id;

        return $this->service->updateProjectElement($project_id,$color,'color');
    }

    public function updateIcon(Request $request)
    {
        $icon = $request->icon;
        $project_id = $request->project_id;

        return $this->service->updateProjectElement($project_id,$icon,'icon');
    }

    public function updateFavorite(Request $request)
    {
        $fav = $request->favorite;
        $project_id = $request->project_id;
        return $this->service->updateProjectElement($project_id,$fav,'favourite');

    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request)
    {
        return $this->service->update($request->all());
    }


    public function setFieldVisibility(){
        $this->service->setFieldVisibility($this->request->all());
    }

    public function defaultView(){
        return $this->service->defaultView($this->request->all());
    }

    public function getImages(){
        return $this->service->getImages($this->request->all());
    }

    public function deleteProject(){
        $team_id = $this->service->delete($this->request->all());

        return $this->success([
            'message' => trans('project::project.delete_successfull'),
            'goto' => route('team.show', $team_id)
        ]);
    }

    public function comment(){
        $project = $this->service->comment($this->request->all());

        return $this->success([
            'message' => trans('project::project.comment_added_successfull'),
            'project' => $project
        ]);
    }

    public function editComment(){
        $project = $this->service->editComment($this->request->all());
        return $this->success([
            'message' => trans('project::project.comment_edited_successfull'),
            'project' => $project
        ]);
    }

    public function deleteComment(){
        $project = $this->service->deleteComment($this->request->all());
        return $this->success([
            'message' => trans('project::project.comment_deleted_successfull'),
            'project' => $project
        ]);
    }
}
