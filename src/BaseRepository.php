<?php

namespace Nahid\Permit;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    /**
     * Model instance
     * @var Model $model
     */
    protected $model;

    public function __construct()
    {
        $model = $this->setModel();
        if (class_exists($model)) {
            $this->model = new $model();
        }
    }

    /**
     * take model namespace
     *
     * @return mixed
     */
    abstract protected function setModel();

    /**
     * find model by id
     *
     * @param       $id
     * @param array $select
     * @return mixed
     */
    public function find($id, $select = ['*'])
    {
        return $this->model->find($id, $select);
    }

    /**
     * find model by custom field
     *
     * @param       $column
     * @param       $value
     * @param array $select
     * @return mixed
     */
    public function findBy($column, $value, $select = ['*'])
    {
        return $this->model->where($column, $value)->first($select);
    }

    /**
     * update model
     *
     * @param       $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data)
    {
        return $this->model->where('id', $id)->update($data);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $this->model->fill($data);

        return $this->model->save();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function insert(array $data)
    {
        return $this->model->insert($data);
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }
}
