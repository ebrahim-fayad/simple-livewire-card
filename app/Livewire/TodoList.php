<?php

namespace App\Livewire;

use App\Models\Todo;
use Exception;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;
    #[Rule("required",message:"هذا الحقل مطلوب")]
    #[Rule("min:3",message:"يجب الا يقل عن 3 احروف")]
    #[Rule("max:50",message:"هذا لا يجب ان يزيد عن 50 حرف")]
    public $name;
    public $search;
    public $editingTodoId;
    #[Rule("required", message: "هذا الحقل مطلوب")]
    #[Rule("min:3", message: "يجب الا يقل عن 3 احروف")]
    #[Rule("max:50", message: "هذا لا يجب ان يزيد عن 50 حرف")]
    public $editingTodoNewName;
    public function create(){
      $validated =  $this->validateOnly('name');
        Todo::create([
            "name" => $validated["name"],
        ]);
        $this->reset(['name']);
        session()->flash("success","تم اضافة عنصر جديد");
        $this->resetPage();
    }
    public function delete($id){
        try {
            Todo::findOrFail($id)->delete();
        } catch (Exception $e) {
            session()->flash("error", 'لقد تم حذف هذا العنصر من قبل');
            return;
        }
    }
    public function toggle($id){
        $todo = Todo::findOrFail($id);
        $todo->update([
            'completed'=>!$todo->completed,
        ]);
    }
    public function edit(Todo $todo){
        $this->editingTodoId = $todo->id;
        $this->editingTodoNewName = $todo->name;
    }
    public function cancelEditing(){
        $this->reset(['editingTodoId', 'editingTodoNewName']);
    }
    public function update(Todo $todo)
    {
        $this->validateOnly('editingTodoNewName');
        $todo->update([
            'name'=> $this->editingTodoNewName,
        ]);
        $this->cancelEditing();

    }
    public function render()
    {
        return view('livewire.todo-list',[
            'todos'=>Todo::latest()->where('name','like', "%{$this->search}%")->paginate(5),
        ]);
    }

}
