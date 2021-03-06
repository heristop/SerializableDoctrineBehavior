<?php

/**
 * Base2Serializable Listener
 *
 * @subpackage  Listener
 * @author Alexandre Mogère
 */
class Doctrine_Template_Listener_Base2Serializable extends Doctrine_Template_Listener_Serializable
{
  /**
   * Normalizes relationship(s) 
   *
   * @param array $objects
   * @param string $filter
   * @return array
   */
  protected function normalize($objects, $filter)
  {
    $relations = $this->_options['relations'];
    
    // transforms base2 value to array
    $matches = array();
    foreach ($objects as $result)
    {
      $primaryKey = isset($relation['primary_key']) ? $relation['primary_key'] : 'id';
      
      // if related table contains a column with filter 
      if (isset($relation['column_filter']))
      {
        $result['code_filter'] = $result[$relation['column_filter']];
      }
      // computes the filter from primary key
      else
      {
        $result['code_filter'] = pow(2, (int) $result[$primaryKey]);
      }
    
      // checks if primary key is present in filter
      if (((int) $filter & (int) $result['code_filter']) != 0)
      {
        // builds an array composed of primary keys
        array_push($matches, $result[$primaryKey]);
      }
    }
    
    return $matches;
  }
  
  /**
   * Denormalizes relationship(s) 
   *
   * @param Doctrine_Record $invoker
   * @return void
   */
  protected function denormalize($invoker)
  {
    $relations = $this->_options['relations'];
    
    foreach ($relations as $relation)
    {
      $column = Doctrine_Template_Serializable::getColumnName(
        $relation,
        $this->_options['column_suffix']
      );
      if ("" !== $invoker->$column)
      {
        if (! is_array($invoker->$column))
        {
          $invoker->$column = explode($this->_options['post_separator'], $invoker->$column);
        }
        
        $invoker->$column = $this->getSerializedIds($invoker->$column);
      }
    }
  }
  
  /**
   * Generates code_filter from a list of Ids
   *
   * @param array $ids
   * @return int
   */
  protected function getSerializedIds($ids)
  {
    sort($ids);
    
    $binary = 0;
    foreach ($ids as $id)
    {
      if ("" === $id) continue;
      
      $binary = $binary + pow(2, (int) $id);
    }

    return $binary;
  }
  
}
