<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

namespace Module\User;

use Pi;
use Pi\Application\Installer\SqlSchema;
use Pi\Db\Table\AbstractTableGateway;
use Pi\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\InputFilter;

/**
 * Abstract class for custom field handling
 *
 * {@inheritDoc}
 *
 * @author Taiwen Jiang <taiwenjiang@tsinghua.org.cn>
 */
abstract class CustomFieldHandler extends AbstractCustomHandler
{
    /**
     * {@inheritDoc}
     */
    public function getMeta()
    {
        $meta = Pi::registry('field', 'user')->read('profile');
        $result = isset($meta[$this->getName()])
            ? $meta[$this->getName()] : array();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    protected function canonize($uid, $data)
    {
        $result = array(
            'uid'   => $uid,
            'value' => $data,
        );

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function get($uid, $filter = false)
    {
        $result = null;
        if ($this->isMultiple) {
            $select = $this->getModel()->select();
            $select->order('order ASC');
            $select->where(array('uid' => $uid));
            $rowset = $this->getModel()->selectWith($select);
            foreach ($rowset as $row) {
                $result[] = $row['value'];
            }
        } else {
            $row = $this->getModel()->find($uid, 'uid');
            $result = $row ? $row['value'] : null;
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function mget($uids, $filter = false)
    {
        $result = array();
        $select = $this->getModel()->select();
        $select->where(array('uid' => $uids));

        if ($this->isMultiple) {
            $select->order('order ASC');
            $rowset = $this->getModel()->selectWith($select);
            foreach ($rowset as $row) {
                $result[(int) $row['uid']][] = $row['value'];
            }
        } else {
            $rowset = $this->getModel()->selectWith($select);
            foreach ($rowset as $row) {
                $result[(int) $row['uid']] = $row['value'];
            }
        }

        return $result;
    }
}
