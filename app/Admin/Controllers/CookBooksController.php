<?php

namespace App\Admin\Controllers;

use App\Models\CookBook;
use App\Http\Controllers\Controller;
use App\Models\Food;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CookBooksController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('菜谱')
            ->description('列表')
            ->body($this->grid());
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('菜谱')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('菜谱')
            ->description('创建')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CookBook);

        $grid->id('ID')->sortable();
        $grid->cover('封面')->image('', null, 40);
        $grid->name('名称');
        $grid->column('foods', '包括食材')->display(function () {
            return $this->foods->map(function ($food) {
                return $food->food->name;
            })->toArray();
        })->label();
        $grid->updated_at('更新时间');

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CookBook);

        $form->text('name', '名称')->rules('required');
        $form->image('cover', '封面')->uniqueName();
        $form->textarea('description', '描述');
        $form->textarea('tips', '提示');

        $form->hasMany('foods', '食材列表', function (Form\NestedForm $form) {
            $form->select('food_id', '食材')->ajax('/admin/api/foods');
            $form->text('number', '数量');
        });

        $form->hasMany('steps', '步骤列表', function (Form\NestedForm $form) {
            $form->image('cover', '封面')->uniqueName();
            $form->textarea('content', '内容');
            $form->number('order', '排序')->default(0);
        });

        return $form;
    }
}
