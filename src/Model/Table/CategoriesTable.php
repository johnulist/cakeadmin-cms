<?php
/**
 * CakeManager (http://cakemanager.org)
 * Copyright (c) http://cakemanager.org
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) http://cakemanager.org
 * @link          http://cakemanager.org CakeManager Project
 * @since         1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cms\Model\Table;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cms\Model\Entity\CmsData;
use Cms\Utility\PathManager;

/**
 * CmsData Model
 *
 */
class CategoriesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('cms_categories');
        $this->displayField('title');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');

        $this->hasMany('Cms.Blogs', [
            'foreignKey' => 'category_id',
        ]);

        $this->addBehavior('Utils.Stateable', [
            'field' => 'state',
        ]);

        $this->addBehavior('Utils.WhoDidIt', [
            'contain' => false,
            'fields' => [
                'id',
                'email'
            ]
        ]);

    }

    public function postType()
    {
        return [
            'menuWeight' => 27,
            'tableColumns' => [
                'id',
                'title',
                'path',
                'view' => [
                    'before' => '<a target="_blank" href="' . Configure::read('App.fullBaseUrl'),
                    'get' => 'path',
                    'after' => '">View category</a>',
                ],
                'created'
            ],
            'formFields' => [
                '_create' => [
                    'type' => 'file'
                ],
                'id',
                'title',
                'slug',
            ]
        ];
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('title');

        $validator
            ->allowEmpty('body');

        $validator
            ->allowEmpty('image');

        $validator
            ->add('state', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('state');

        return $validator;
    }

    /**
     * beforeFind event
     *
     * @param $event
     * @param $query
     * @param $options
     * @param $primary
     */
    public function beforeFind($event, $query, $options, $primary)
    {
    }

    /**
     * beforeSave event
     *
     * @param \Cake\Event\Event $event Event.
     * @param \Cake\ORM\Entity $entity Entity.
     * @param array $options Options.
     * @return void
     */
    public function beforeSave($event, $entity, $options)
    {
        PathManager::instance()->removeRoutes('categories');
    }

    public function getRoutes()
    {
        $list = $this->find('list', [
            'keyField' => 'id',
            'valueField' => 'slug'
        ])->toArray();

        $routes = [];

        foreach ($list as $id => $slug) {
            $routes[$id] = '/' . $slug;
        }

        return $routes;
    }
}
