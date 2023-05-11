<?php

namespace CodeIgniter\Database;

class Backup
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * 
     * 
     * Generate a backup of the database schema.
     *
     *
     *
    */

    public function backup($fileName)
    {
        // Open the backup file for writing
        $file = fopen($fileName, 'w');

        // Retrieve a list of all the tables .Write the SQL code for each table to the backup file
        $tables = $this->db->listTables();
        foreach ($tables as $table) {
            $sql = $this->db->query('SELECT * FROM '.$table)->getResult('array');
            if (count($sql) > 0) {
                fwrite($file, "--\n-- Table structure for table `{$table}`\n--\n\n");
                fwrite($file, $this->db->query('SHOW CREATE TABLE '.$table)->getRow()->{'Create Table'}.";\n\n");
                fwrite($file, "--\n-- Dumping data for table `{$table}`\n--\n\n");
                foreach ($sql as $row) {
                    $row = array_map(function($value) {
                        if (is_null($value)) {
                            return 'NULL';
                        } else {
                            return "'".addslashes($value)."'";
                        }
                    }, $row);
                    fwrite($file, "INSERT INTO `{$table}` VALUES (".implode(',', $row).");\n");
                }
                fwrite($file, "\n");
            }
        }

        // Close the backup file
        fclose($file);
    }
    
}
