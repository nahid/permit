<?php

namespace Nahid\Permit;

abstract class BaseRepository
{
    protected $model;


    function __construct()
    {
        $model = $this->setModel();
        $this->model = new $model;
    }

    abstract protected function setModel();

    public function find($id, $select = ['*'])
    {
        return $this->model->find($id, $select);
    }

    public function findBy($column, $value, $select = ['*'])
    {
        return $this->model->where($column, $value)->first($select);
    }

    public function update($id, array $data)
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

}