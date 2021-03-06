<?php namespace Acme\Bookshop\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Acme\Bookshop\Models\Publisher;

/**
 * Publishers Back-end Controller
 */
class Publishers extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Acme.Bookshop', 'bookshop', 'publishers');
    }

    public function onDelete()
    {
        $checkedIds = post('checked');
        if ((is_array($checkedIds)) && (count($checkedIds) > 0)) {

            $entites = Publisher::whereIn('id', $checkedIds)->get();

            foreach ($entites as $entity) {
                if ($entity->books->count()) {
                    return \Flash::error("$entity->name has books , unable to delete!");
                }
            }
            
            $deleted = Publisher::whereIn('id', $checkedIds)->delete();
            if (!$deleted) {
                return \Flash::error('sorry publishers have\'nt  been deleted ?');
            }
        }

        \Flash::success(\Lang::get('backend::lang.list.delete_selected_success', [
            'name' => 'deleted '
        ]));


        return $this->listRefresh();
    }
}