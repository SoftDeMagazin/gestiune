<?php
class Table extends Html
{
	var $attributes;
	var $table;
	var $rows;
	function __construct($options = array())
		{
		$this -> attributes = $options;
		}
	function addRow(TableRow $row)
		{
		$this -> rows[] = $row;
		}

	function getTable()
	{
		$innerHtml = '';
		if($this -> rows)
		{
		$i = 0;
		foreach ($this -> rows as $row) {
			if($i == 0) parent::append($innerHtml, '<thead>');
			parent::append($innerHtml, $row -> getRow());
			if($i == 0) parent::append($innerHtml, '</thead><tbody>');
			$i++;		
		}
		
		parent::append($innerHtml, '</tbody>');
		}
		return  parent::table($innerHtml, $this -> attributes);
	}
	
	function __toString()
	{
		return $this -> getTable();
	}
}

class TableRow extends Html
{
	var $attibutes;
	var $cells;
	
	function __construct($options = array())
		{
		$this -> attributes = $options;
		}
	
	function addCell($cell)	
	{
		$this -> cells[] = $cell;
	}
	
	function getRow()
	{
		$innerHtml = '';
		if (isset($this -> cells)) {
			foreach ($this -> cells as $cell) {
				parent::append($innerHtml, $cell -> getCell());
			}
		}
		return parent::tablerow($innerHtml, $this -> attributes);
	}
	
	function __toString()
	{
		return $this -> getRow();
	}
}

class TableCell extends Html
{
	var $attributes;
	var $innerHtml;
	
	function __construct($innerHtml, $options = array())
		{
		$this -> attributes = $options;
		$this -> innerHtml = $innerHtml;
		}
	
	function setCellAttributes($attributes = array())
		{
		$this -> attributes = $attributes;
		}
	
	function getCell()
		{
		return parent::tablecell($this -> innerHtml, $this -> attributes);
		}	
}

class TableHead extends Html
{
	var $attributes;
	var $innerHtml;
	
	function __construct($innerHtml, $options = array())
		{
		$this -> attributes = $options;
		$this -> innerHtml = $innerHtml;
		}
	
	function setCellAttributes($attributes = array())
		{
		$this -> attributes = $attributes;
		}
	
	function getCell()
		{
		return parent::tableheader($this -> innerHtml, $this -> attributes);
		}	
}

class DataGrid
{
	var $attributes;	
	var $head;
	var $headAttributes = array();
	var $data;
	var $rowAttributes;
	var $table;
	var $rowIndex=0;
	
	function __construct($attributes = array())
		{
		$this -> attributes = $attributes;
		$this -> table =  new Table($attributes);
		}
	
	function addHeadColumn($innerHtml, $attributes = array())
		{
		$this -> head[] = new TableHead($innerHtml, $attributes);
		}
	
	function setHeadAttributes($attributes = array())
		{
		$this -> headAttributes = $attributes;
		}	
	
	function addDataColumn($row, $innerHtml, $attributes = array())
		{
		$this -> data[$row][] = new TableCell($innerHtml, $attributes);
		$this -> rowIndex = $row;
		}
	
	function addColumn($innerHtml, $attributes = array())
		{
		$this -> data[$this -> rowIndex][] = new TableCell($innerHtml, $attributes);
		}
	
	function index()
		{
		$this -> rowIndex++;
		}		
	
	function setDataAttributes($row, $column, $attributes = array())
		{
		$this -> data[$row][$column] -> setCellAttributes($attributes);
		}	
	
	function addDataArray($row, $dataArray = array(), $dataStyle = array())
		{
		foreach($dataArray as $key => $value)
			{
			$this -> addDataColumn($row, $value, $dataStyle);
			}
		}
	
	function setDataFromSql($query, $mysql, $dataStyle =  array())
		{		
		$rows = $mysql -> getRows($query);
		$nr_r = count($rows);
		if($nr_r)
			{
			for($i=0; $i < $nr_r;$i++)
				{
				$this -> addDataArray($i, $rows[$i], $dataStyle);
				}
			}
		else return false; 
		}
	
	function setDataFromCollection($collection, $columns = array(), $dataStyle = array())
		{
		$nr_r = $collection -> nr_r();
		if($nr_r)
			{
			for($i=0; $i < $nr_r;$i++)
				{
				foreach($columns as $column)
					{
					$this -> addDataColumn($i, $collection -> dataRow[$i] -> $column, $dataStyle = array());
					}
				}
			}
		}		
	
	function setRowOptions($attributes = array())
		{
		$this -> rowAttributes[$this -> rowIndex] = $attributes;
		}
	
	function setRowAttributes($row, $attributes = array())
		{
		$this -> rowAttributes[$row] = $attributes;
		}
		
	function getDataGrid()
		{
		$headRow = new TableRow($this -> headAttributes);
		$headRow -> cells = $this -> head;
		$this -> table -> addRow($headRow);
		if($this -> data)
			{
			foreach($this -> data as $key => $rowData)
				{
				$row = new TableRow($this -> rowAttributes[$key]);
				$row -> cells = $rowData;
				$this -> table -> addRow($row);
				}
			}
		return $this -> table -> getTable();
		}
	
	function renew() {
		$this -> rowIndex=0;
		$this -> data = array();
		$this -> table = new Table($this -> attributes);
	}	
	
	function __toString()
		{
		return $this -> getDataGrid();
		}			
}
?>