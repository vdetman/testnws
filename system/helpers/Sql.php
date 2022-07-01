<?php namespace Helper;

class Sql
{
    /**
	 * @return array
	 */
	public static function insertOrUpdateQuery($data, $tableName = "tableName", $insertSet = [], $updateSet = [], $partLength = 2000, $ignore = false)
	{
		if (!is_array($data)) return [];
		
		$totalRecords = count($data);
		$fileRows = [];

		// OnDuplicate set
		$onDuplicate = "";
		if ($updateSet) {
			$onDuplicate = " ON DUPLICATE KEY UPDATE ";
			$parts = [];
			foreach ($updateSet as $f) $parts[] = "`{$f}` = VALUES(`{$f}`)";
			$onDuplicate .= implode(",", $parts);
		}
		$ignore = $ignore ? "IGNORE" : "";
		$sqlFields = "INSERT {$ignore} INTO `{$tableName}` (`" . implode("`,`", $insertSet) . "`) VALUES ";
		$count = $countTotal = 0;
		$values = [];
		foreach ($data as $r) {
			if (count($r) != count($insertSet)) continue;

			$_values = [];
			foreach ($r as $v) {
				if (is_null($v)) $_values[] = "NULL";
				elseif (is_string($v)) $_values[] = "'{$v}'";
				else $_values[] = "{$v}";
			}

			$values[] = "(" . implode(",", $_values) . ")";
			$countTotal++;
			if (++$count >= $partLength || $countTotal == $totalRecords) {
				$fileRows[] = $sqlFields . implode(",", $values) . "{$onDuplicate};\n";
				$count = 0; $values = [];
			}
		}
		if ($values) $fileRows[] = $sqlFields . implode(",", $values) . "{$onDuplicate};\n";

		return $fileRows;
	}

	/**
	 * @return array
	 */
	public static function updateQuery($data, $tableName = "tableName", $keyField = false, $fieldsSet = [], $partLength = 2000)
	{
		$fileRows = [];
		$parts = array_chunk($data, $partLength, true);
		foreach ($parts as $partData) {
			foreach ($fieldsSet as $field) {
				$sql = "UPDATE `{$tableName}` SET `{$field}` = CASE ";
				foreach ($partData as $id => $values) $sql .= "WHEN `{$keyField}` = {$id} THEN " . (is_null($values[$field]) ? "NULL" : "'{$values[$field]}'") . " ";
				$sql .= "END WHERE `{$keyField}` IN (" . implode(",", array_keys($partData)) . ");";
				$fileRows[] = $sql . "\n";
			}
		}
		return $fileRows;
	}

}