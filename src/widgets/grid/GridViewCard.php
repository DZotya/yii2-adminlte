<?php

namespace p4it\adminlte\widgets\grid;

use pappco\yii2\grid\widgets\GridView;
use yii\base\Widget;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * Created by PhpStorm.
 * User: papppeter
 * Date: 12/05/2019
 * Time: 12:59
 */
class GridViewCard extends GridView
{
    public const FILTER_POS_BOX_HEADER = 'box-header';

    public $filterPosition = self::FILTER_POS_BOX_HEADER;
    public $bordered = false;
    public $striped = false;
    public $condensed = true;

    public array $actionButtons = [];

    public array $cardOptions = [];
    public array $cardDefaultOptions = [
        'bodyOptions' => [
            'class' => 'p-0',
        ],
        'footer' => '{summary} <div class="pull-right">{pager}</div>',
        'body' => '{items}',
    ];

    public $filterRowOptions = ['class' => 'mb-1'];

    public string $actionButtonsView = '@vendor/p4it-kft/yii2-adminlte/src/widgets/grid/views/action-buttons';
    public string $emptyLayoutView = '@vendor/p4it-kft/yii2-adminlte/src/widgets/grid/views/empty-layout';
    public string $filtersView = '@vendor/p4it-kft/yii2-adminlte/src/widgets/grid/views/filters';
    public string $layoutView = '@vendor/p4it-kft/yii2-adminlte/src/widgets/grid/views/layout';

    public function __construct(View $view, $config = [])
    {
        parent::__construct($config);
    }

    public function renderActionButtons() {
        if(!$this->actionButtons) {
            return '';
        }

        $actionButtons = array_map(static function ($button){
            if(isset($button['class']) && is_subclass_of($button['class'], Widget::class)) {
                $button = $button['class']::widget($button);
            }

            return $button;
        }, $this->actionButtons);

        return $this->render($this->actionButtonsView, [
            'buttons' => $actionButtons
        ]);
    }

    public function renderFilters()
    {
        if($this->filterModel === null) {
            return '';
        }

        if($this->filterPosition !== self::FILTER_POS_BOX_HEADER) {
            return parent::renderFilters();
        }

        $filters = [];
        foreach ($this->columns as $column) {
            /* @var $column DataColumn */
            $filter = AutoFilterRender::createFromColumn($column)->render();
            if($filter === $this->emptyCell) {
                continue;
            }
            $filters[] = $filter;
        }

        return $this->render($this->filtersView, [
            'containerAttributes' => Html::renderTagAttributes($this->filterRowOptions),
            'filters' => $filters,
        ]);
    }

    public function init()
    {
        Html::addCssClass($this->pager['options']['class'], 'pagination m-0');
        Html::addCssClass($this->tableOptions, 'm-0');

        parent::init(); // TODO: Change the autogenerated stub
    }

    protected function initLayout()
    {
        $filters = [];
        if($this->filterPosition === self::FILTER_POS_BOX_HEADER) {
            $filters = $this->renderFilters();
        }

        if($this->dataProvider->getCount() > 0) {
            $this->layout = $this->render($this->layoutView, [
                'cardOptions' => ArrayHelper::merge($this->cardDefaultOptions, $this->cardOptions),
                'actionButtons' => $this->renderActionButtons(),
                'filters' => $filters
            ]);
        } else {
            $cardOptions = $this->cardOptions;
            $cardOptions['body'] = $this->emptyText;

            $this->layout = $this->render($this->emptyLayoutView, [
                'cardOptions' => $cardOptions,
                'actionButtons' => $this->renderActionButtons(),
            ]);
        }

        parent::initLayout(); // TODO: Change the autogenerated stub
    }
}