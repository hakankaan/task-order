<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddPrerequisitiesRequest;
use App\Http\Requests\CreateTaskRequest;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class TaskController extends Controller
{



    public function __construct()
    {
        try {
            Storage::disk('local')->get('tasks.json');
        } catch (FileNotFoundException $fe) {
            Storage::disk('local')->put('tasks.json', json_encode([]));
        }
    }

    public function all()
    {
        try {
            $tasks = self::getTasks();

            return response()->json(['tasks' => $tasks], 200);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function taskOrder()
    {
        try {
            $tasks = collect(self::getTasks());

            $tmpTasks = $tasks->filter(function ($q) {
                return count($q['prerequisities']) == 0;
            });


            $tmpTasks = $tmpTasks->map(function ($q) use ($tasks) {
                $q['next'] =  self::hasPrerequisity($q, $tasks);
                return $q;
            });



            return response()->json(["tasks" => $tmpTasks->values()->toArray()], 200);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function createTask(CreateTaskRequest $request)
    {
        try {
            $tasks = self::getTasks();
            $new_task = [];
            $new_task["id"] = "task_" . count($tasks);
            $new_task = array_merge($new_task, $request->all());
            $new_task["prerequisities"] = [];
            array_push($tasks, $new_task);
            Storage::disk('local')->put('tasks.json', json_encode($tasks));
            return response()->json(['added_task' => $new_task], 200);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function addPrerequisities(AddPrerequisitiesRequest $request)
    {
        $task_id = $request->task_id;
        $prerequisities = $request->prerequisities;
        $tasks = self::getTasks();
        $key = array_search($task_id, array_column($tasks, "id"));
        $task = $tasks[$key];

        // check if tasks of new prerequisities has the task as a prerequisity
        $failed_prerequisities = [];
        foreach ($prerequisities as $k => $v) {
            $tmpKey = array_search($v, array_column($tasks, "id"));
            if (!$key) {
                array_push($failed_prerequisities, $v);
                continue;
            }

            $tmpKey = array_search($v, array_column($tasks, "id"));
            $tmpTask = $tasks[$tmpKey];
            if (!self::checkPrerequisity($tasks, $tmpTask, $task_id) or $task_id == $v) {
                array_push($failed_prerequisities, $v);
            } else {
                array_push($tasks[$key]['prerequisities'], $v);
            }
        }


        Storage::disk('local')->put('tasks.json', json_encode($tasks));
        return response()->json(['result' => "success", 'failed_prerequisities' =>  $failed_prerequisities], 200);
    }





    static function getTasks()
    {
        $json = Storage::disk('local')->get('tasks.json');
        $json = json_decode($json, true);
        $tasks = $json ? $json : [];
        return (array)$tasks;
    }

    static function checkPrerequisity($tasks, $task, $pre)
    {
        $success = true;
        foreach ($task['prerequisities'] as $v) {
            if ($v == $pre) {
                $success = false;
                break;
            }
            $key = array_search($v, array_column($tasks, "id"));
            $task = $tasks[$key];
            $success = self::checkPrerequisity($tasks, $task, $pre);
            if ($success == false) break;
        }
        return $success;
    }

    static function hasPrerequisity($task, $tasks)
    {
        $tasks = collect($tasks->values()->toArray());
        $tmpHasPrerequisities = $tasks->filter(function ($q) use ($task) {
            return in_array($task['id'], $q['prerequisities']);
        });
        error_log(json_encode($tmpHasPrerequisities));
        if ($tmpHasPrerequisities->count() > 0) {

            $tmpHasPrerequisities = $tmpHasPrerequisities->map(function ($q) use ($tasks) {
                $q['next'] =  self::hasPrerequisity($q, $tasks);
                return $q;
            });
        }
        return $tmpHasPrerequisities->values()->toArray();
    }
}
