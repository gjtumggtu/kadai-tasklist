<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $tasks = Task::orderBy('id', 'desc')->paginate(25);

        // View側で呼び出すtasksに、$tasksを渡しておく
        // return view('tasks.index', ['tasks' => $tasks,]);
        if (\Auth::check()) {
            $user  = \Auth::user();
            $tasks = $user->tasks()->orderBy('id', 'desc')->paginate(25);

            // View側で呼び出すtasksに、$tasksを渡しておく
            return view('tasks.index', ['tasks' => $tasks,]);
        }
        // ログインしていなかったら、task取得しないでindexへ
        return view('tasks.index');
        // return redirect('/');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $task = new Task;

        // メッセージ作成ビューを表示
        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         
        // ステータスが入力されていて、10文字以上でない場合のみOK
        // タスクが入力されていて、191文字以上でない場合のみOK
        $this->validate($request, ['status'  => 'required|max:10',
                                   'content' => 'required|max:191',]);

         // フォームから送られてきたcontentはrequestに入っているので、requestから取り出して登録
        $task = new Task;
        $task->status  = $request->status;
        $task->content = $request->content;
        $task->user_id = auth()->id();
        $task->save();
        
        // $request->user()->tasks()->create([
        //     'status'  => $request->status,
        //     'content' => $request->content,
        // ]);
        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // idの値でメッセージを検索して取得
        // $task = Task::find($id);
        $task = \App\Task::find($id);

        // return view('tasks.show', ['task' => $task,]);
         // ログインユーザー = タスク作成者なら表示画面へ
        if (\Auth::id() === $task->user_id) {
            return view('tasks.show', ['task' => $task,]);
        }
        // 編集画面へ入れなかった場合はトップページへ
        return redirect('/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = Task::find($id);

        // return view('tasks.edit', ['task' => $task,]);
        // ログインユーザー = タスク作成者なら編集画面へ
        if (\Auth::id() === $task->user_id) {
            return view('tasks.edit', ['task' => $task,]);
        }
        // 編集画面へ入れなかった場合はトップページへ
        return redirect('/');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // ステータスが入力されていて、10文字以上でない場合のみOK
        // タスクが入力されていて、191文字以上でない場合のみOK
        $this->validate($request, ['status'  => 'required|max:10',
                                   'content' => 'required|max:191',]);
        // フォームから送られてきたcontentはrequestに入っているので、requestから取り出して登録
        $task = Task::find($id);
        // $task->status  = $request->status;
        // $task->content = $request->content;
        // $task->save();
        
        $task = \App\Task::find($id);

        // ログインユーザー = タスク作成者なら編集処理へ
        if (\Auth::id() === $task->user_id) {
            $task->status  = $request->status;
            $task->content = $request->content;
            $task->save();
        }

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $task = Task::find($id);
        // $task->delete();
        
        $task = \App\Task::find($id);
        if (\Auth::id() === $task->user_id) {
            $task->delete();
        }

        // トップページへリダイレクトさせる
        return redirect('/');
    }
}
