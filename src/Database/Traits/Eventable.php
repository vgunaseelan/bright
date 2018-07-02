<?php

namespace Karla\Database\Traits;

use Illuminate\Support\Str;

trait Eventable
{
    protected $eventState   = true;
    protected $eventColumns = [];
    protected $lastId;

    public function eventState($event = false)
    {
        $this->eventState = $event;

        return $this;
    }

    public function setEvent($event)
    {
        if (is_bool($event)) {
            return $this->eventState($event);
        }

        return $this->eventColumn($event);
    }

    /**
     * Get the value of event
     */
    protected function useEvent(): bool
    {
        return $this->eventState;
    }

    public function eventColumn($name)
    {
        if (is_array($name)) {
            $this->eventColumns = array_merge($this->eventColumns, $name);
        } else {
            $this->eventColumns[] = $name;
        }

        return $this;
    }

    protected function setPrimaryKey(array $values, $id = null)
    {
        if (!isset($values['id'])) {
            $values['id'] = (string) $id;
        }

        $this->lastId = $values['id'];

        return $values;
    }

    protected function setUserId(array $values)
    {
        if (!isset($values['user_id'])) {
            $values['user_id'] = user('id');
        }

        return $values;
    }

    protected function getEventTables($type)
    {
        $tables = config('karla.tables');
        $tables = array_merge($tables['default'], $tables[$type]);
        $tables = isset($tables[$this->from]) ? $tables[$this->from] : [];

        return $tables;
    }

    protected function insertEvent($values)
    {
        if (!$this->useEvent()) {
            return $values;
        }

        if (!is_array(reset($values))) {
            $values = [$values];
        }

        $tables = $this->getEventTables('insert');
        foreach ($tables as $columns) {
            if (!is_array($columns)) {
                $columns = [$columns => $columns];
            }

            $column = key($columns);
            $field  = $columns[$column];

            foreach ($values as $key => $value) {
                if (isset($value[$column])) {
                    continue;
                }

                switch ($column) {
                    case 'id':
                        $value = $this->setPrimaryKey($value, Str::uuid());
                        break;
                    case 'uid':
                        $value = $this->setPrimaryKey($value, Str::orderedUuid());
                        break;
                    case 'user_id':
                        $value = $this->setUserId($value);
                        break;
                    case 'time':
                        $value = $this->setTimeStamps($value, true);
                        break;
                    default:
                        if (app()->has($field)) {
                            $value[$column] = app($field);
                        }
                        break;
                }

                $values[$key] = $value;
            }
        }

        foreach ($values as &$value) {
            $value = $this->setTimeStamps($value);
        }

        return $values;
    }

    protected function updateEvent($values)
    {
        $this->atomicEvent('update');
        $values = $this->setTimeStamp($values);

        return $values;
    }

    protected function atomicEvent($type = 'update')
    {
        if (!$this->useEvent()) {
            return $this;
        }

        $eventColumns = $this->getEventTables($type);
        $eventColumns = array_merge($eventColumns, $this->eventColumns);

        foreach ($eventColumns as $columns) {
            if (!is_array($columns)) {
                $columns = [$columns => $columns];
            }

            $column = key($columns);
            $field  = $columns[$column];

            $from = last(preg_split('/ as /i', $this->from));

            switch ($column) {
                case 'user_id':
                    $this->where($from . '.' . $column, user('id'));
                    break;
                case 'parent_id':
                    $this->where($from . '.' . $column, user('id'));
                    break;
                default:
                    if (app()->has($field)) {
                        $this->where($from . '.' . $column, app()->get($field));
                    }
                    break;
            }
        }

        return $this;
    }

    /**
     * Get the value of lastId
     */
    public function getLastId()
    {
        return $this->lastId;
    }

    /**
     * @{inheritdoc}
     */
    public function insert(array $values)
    {
        $values = $this->insertEvent($values);

        return parent::insert($values);
    }

    /**
     * @{inheritdoc}
     */
    public function insertGetId(array $values, $sequence = null)
    {
        $values = $this->insertEvent($values);

        $id = parent::insertGetId($values[0], $sequence);

        if (empty($id)) {
            $id = $this->getLastId();
        }

        return $id;
    }
    /**
     * @{inheritdoc}
     */
    public function delete($id = null)
    {
        $this->atomicEvent('delete');
        return parent::delete($id);
    }

    /**
     * @{inheritdoc}
     */
    public function exists()
    {
        $this->atomicEvent('select');
        return parent::exists();
    }

    public function update(array $values)
    {
        if ($this->usesTimestamps()) {
            $values['updated_at'] = $this->freshTimestamp();
        }

        $values = $this->updateEvent($values);

        return parent::update($values);
    }
}
