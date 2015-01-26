<?php

/**
 * @package   Enginer
 * @author    Ray Lawlor http://www.zoomodsplus.com
 * @copyright Copyright (C) 2015 zoomodsplus.com
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemEnginer extends JPlugin
{

    public function onAfterInitialise()
    {
        //init vars
        $engine = $this->params->get('engine');
        $opposite = $this->_getOpposite($engine);

        //set database query
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('TABLE_NAME');
        $query->from('INFORMATION_SCHEMA.TABLES');

        $query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '"
                . JFactory::getConfig()->get("db")
                . "' AND ENGINE = '" . $opposite . "'";

        if ($db->setQuery($query))
        {
            $results = $db->loadObjectList();
        }
        else
        {
            return;
        }

        if (!empty($results))
        {
            //cycle through tables and reset the Engine
            foreach ($results as $result)
            {
                $tableName = $result->TABLE_NAME;
                $newQuery = "ALTER TABLE " . $tableName . " ENGINE=" . $engine;

                try
                {
                    $db->setQuery($newQuery);
                    $db->execute();
                } catch (Exception $e)
                {
                    /* echo $e->getMessage(); */
                }
            }
        }
    }
    
    
    //helps set init vars
    protected function _getOpposite($engine)
    {
        if ($engine == 'MyISAM')
        {
            return 'InnoDB';
        }
        else
        {
            return 'MyISAM';
        }
    }

}
